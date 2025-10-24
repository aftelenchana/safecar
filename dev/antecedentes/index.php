<?php
// ======================================================
// API SIAF – Casos y “peligrosidad” por cédula (estilo ANT)
// Entrada JSON: { "KEY":"...", "parametro_busqueda":"1309022935" }
// Opcional: también acepta { "cedula": "..." } por compatibilidad
// Respuesta: { meta, config, casos, personas_riesgo, quota, billing?, httpCode? }
// ======================================================

/* ====== Init/Headers ====== */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Origin, X-Requested-With, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }
header('Content-Type: application/json; charset=UTF-8');
mb_internal_encoding('UTF-8');
@set_time_limit(0);

/* ====== Entrada (JSON o JSONDocumento) ====== */
$raw = file_get_contents('php://input');
$in  = json_decode($raw ?: '{}', true);
if (isset($in['JSONDocumento'])) {
  $in = json_decode($in['JSONDocumento'], true);
}
if (!is_array($in)) { $in = $_POST; }

$KEY      = trim($in['KEY'] ?? '');
$cedula   = trim($in['parametro_busqueda'] ?? ($in['cedula'] ?? ''));
$cedula   = preg_replace('/\D+/', '', $cedula);

if ($KEY === '')    { echo json_encode(['error'=>'KEY vacía.']); exit(); }
if ($cedula === '' || strlen($cedula) < 8) {
  echo json_encode(['error'=>'Parámetro "cedula" inválido.']); exit();
}

/* ====== BD / Validación de KEY ====== */
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api          =  $result_configuracion['cuota_sms_api'];

$KEY_ESC = mysqli_real_escape_string($conection, $KEY);
$qUser   = mysqli_query($conection, "SELECT * FROM aplicaciones_api WHERE key_api = '$KEY_ESC' LIMIT 1");
if (!$qUser || mysqli_num_rows($qUser) === 0) {
  echo json_encode(['error'=>'usuario no encontrado.']); exit();
}
$apiRow = mysqli_fetch_assoc($qUser);
$iduser = (int)($apiRow['iduser'] ?? 0);
if ($iduser <= 0) { echo json_encode(['error'=>'iduser inválido para la KEY.']); exit(); }

/* ====== Producto (ESTÁTICO) que define el costo por consulta ====== */
$idProductoApi = 13604; // fijo
$qProd = mysqli_query(
  $conection,
  "SELECT precio FROM producto_venta WHERE idproducto = {$idProductoApi} LIMIT 1"
);
if (!$qProd || mysqli_num_rows($qProd) === 0) {
  echo json_encode(['error' => 'producto_api_no_encontrado', 'id_producto_api' => $idProductoApi]); exit();
}
$rowProd        = mysqli_fetch_assoc($qProd);
$precioProducto = (float)($rowProd['precio'] ?? 0.0);
if ($precioProducto <= 0) {
  echo json_encode(['error' => 'precio_producto_invalido', 'id_producto_api' => $idProductoApi, 'precio' => $precioProducto]); exit();
}

/* ====== Cuota mensual (del 1 al último día) ====== */
$FREE_MONTHLY = $cuota_sms_api; // máximo gratis por mes

$monthStartDate = date('Y-m-01');
$monthEndDate   = date('Y-m-t');

$sql_count = "
  SELECT COUNT(*) AS c
  FROM busquedaapiruc
  WHERE iduser = '$iduser'
    AND DATE(fecha) BETWEEN '$monthStartDate' AND '$monthEndDate'
";
$rs_count         = mysqli_query($conection, $sql_count);
$rowCount         = $rs_count ? mysqli_fetch_assoc($rs_count) : ['c' => 0];
$usadas_mes_antes = (int)($rowCount['c'] ?? 0);
$es_pago          = ($usadas_mes_antes >= $FREE_MONTHLY) ? 1 : 0;

$busqEsc = mysqli_real_escape_string($conection, $cedula);

/* =========================
   CONFIGURACIÓN DE RIESGO
   ========================= */
// Pesos por nombre de delito (contiene)
$DELITO_PESOS = [
  'ASESINATO' => 100, 'HOMICIDIO' => 90, 'TENTATIVA DE ASESINATO' => 90,
  'VIOLACION' => 100, 'ABUSO SEXUAL' => 90, 'ROBO' => 60, 'HURTO' => 45,
  'AMENAZA' => 30, 'ESTAFA' => 40, 'LESIONES' => 40, 'VIOLENCIA INTRAFAMILIAR' => 50,
  'TRAFICO ILICITO' => 70, 'TENENCIA ILEGAL DE ARMAS' => 50, 'EXTORSION' => 70, 'SECUESTRO' => 95,
];
// Pesos por código (si viene “DELITO (####)”)
$DELITO_CODIGOS = [
  '3068' => 100, // ASESINATO
  '1045' => 60,  // ROBO
  '2289' => 30,  // AMENAZA...
];
// Factor multiplicador por rol
$FACTOR_POR_ROL = [
  'PROCESADO'=>1.00,'SOSPECHOSO'=>0.70,'INVESTIGADO'=>0.60,'IMPUTADO'=>0.80,
  'APREHENDIDO'=>0.90,'PRIVADO DE LA LIBERTAD'=>1.00,
  'DENUNCIANTE'=>0.00,'PERJUDICADO'=>0.00,'PERJUDICADO NO RECONOCIDO'=>0.00,
  'SOSPECHOSO NO RECONOCIDO'=>0.40,
];

/* =========================
   UTILIDADES HTTP/HTML
   ========================= */
function http_get_stream($url, $cookies = [], $referer = null) {
  $headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: es-EC,es;q=0.9,en;q=0.8',
    'Cache-Control: no-cache','Pragma: no-cache','Connection: keep-alive'
  ];
  if (!empty($cookies)) {
    $cookieLine=[]; foreach ($cookies as $k=>$v){ $cookieLine[]=$k.'='.$v; }
    $headers[]='Cookie: '.implode('; ',$cookieLine);
  }
  if ($referer) $headers[]='Referer: '.$referer;

  $context = stream_context_create([
    'http'=>['method'=>'GET','header'=>implode("\r\n",$headers)."\r\n",'ignore_errors'=>true],
    'ssl'=>['verify_peer'=>true,'verify_peer_name'=>true,'SNI_enabled'=>true]
  ]);
  $body = @file_get_contents($url,false,$context);
  $respHeaders = isset($http_response_header) ? $http_response_header : [];
  return [$body,$respHeaders];
}
function parse_set_cookies($headers){ $out=[]; foreach($headers as $h){ if(stripos($h,'Set-Cookie:')===0){ $kv=trim(substr($h,strlen('Set-Cookie:'))); $parts=explode(';',$kv,2); $pair=trim($parts[0]); $eq=strpos($pair,'='); if($eq!==false){ $n=substr($pair,0,$eq); $v=substr($pair,$eq+1); if($n!=='') $out[$n]=$v; } } } return $out; }
function extract_iframe_src($html){
  if(!preg_match('#<iframe[^>]+src="([^"]+)"#i',$html,$m)) return null;
  $src = html_entity_decode($m[1], ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
  if(preg_match('#^https?://#i',$src)) return $src;
  $base='https://www.gestiondefiscalias.gob.ec';
  if(strpos($src,'/')===0) return $base.$src;
  return $base.'/'.ltrim($src,'/');
}

/* ====== HTML helpers ====== */
function normalize_ws($s){ if($s===null)return null; $s=str_replace("\xC2\xA0",' ',$s); $s=html_entity_decode($s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); $s=preg_replace('/\s+/u',' ',trim($s)); return $s===''?null:$s; }
function parse_table_node(DOMElement $table): array {
  $rows=[]; foreach($table->getElementsByTagName('tr') as $tr){ $row=[];
    foreach($tr->childNodes as $td){ if($td->nodeType===XML_ELEMENT_NODE){ $name=strtolower($td->nodeName); if($name==='td'||$name==='th'){ $row[]=normalize_ws($td->textContent ?? ''); } } }
    if (count(array_filter($row, fn($v)=>$v!==null))>0) $rows[]=$row;
  } return $rows;
}
function table_text(DOMElement $tbl): string{ $txt=''; foreach($tbl->getElementsByTagName('*') as $n){ if($n->nodeType===XML_ELEMENT_NODE) $txt.=' '.$n->textContent; } return mb_strtoupper(normalize_ws($txt)??'','UTF-8'); }
function next_element_sibling(?DOMNode $node, string $tagWanted='table'): ?DOMElement {
  if(!$node) return null; $n=$node->nextSibling; while($n){ if($n->nodeType===XML_ELEMENT_NODE){ if(strtolower($n->tagName)===strtolower($tagWanted)) return $n; } $n=$n->nextSibling; } return null;
}
function rows_to_kv(array $rows): array { $kv=[]; foreach($rows as $r){ for($i=0;$i+1<count($r);$i+=2){ $k=rtrim($r[$i],':'); $v=$r[$i+1]; if($k!==null && $v!==null) $kv[$k]=$v; } } return $kv; }
function find_key_in_rows(array $rows, string $keyWanted): ?string {
  $keyWanted = mb_strtoupper($keyWanted,'UTF-8');
  foreach($rows as $r){ for($i=0;$i<count($r);$i++){ $cell=mb_strtoupper($r[$i]??'','UTF-8'); $cell=rtrim($cell,':'); if($cell===$keyWanted){ $val=$r[$i+1]??null; return $val?normalize_ws($val):null; } } }
  return null;
}

/* ====== Descarga HTML SIAF ====== */
$len        = strlen($cedula);
$serialized = 'a:1:{i:0;s:'.$len.':"'.$cedula.'";}';
$businfo    = str_replace('"','%22',$serialized);
$url        = 'https://www.gestiondefiscalias.gob.ec/siaf/comunes/noticiasdelito/info_mod.php?businfo='.$businfo;

$start = hrtime(true);
list($body1,$hdr1) = http_get_stream($url);
$final   = $body1 ?: '';
$lastHdr = $hdr1;

$blocked = ($final && (stripos($final,'Incapsula')!==false || stripos($final,'_Incapsula_Resource')!==false));
if ($blocked) {
  $cookies = parse_set_cookies($hdr1);
  $iframe  = extract_iframe_src($final);
  if ($iframe) {
    list($ib,$ih) = http_get_stream($iframe,$cookies);
    $cookies = array_merge($cookies, parse_set_cookies($ih));
    list($body2,$hdr2) = http_get_stream($url,$cookies,'https://www.gestiondefiscalias.gob.ec/siaf/');
    if ($body2) { $final=$body2; $lastHdr=$hdr2; }
  }
}
$duration_ms = (int) round((hrtime(true) - $start) / 1e6);
$final_utf8  = ($final!=='') ? mb_convert_encoding($final,'UTF-8','UTF-8, ISO-8859-1, Windows-1252') : '';

/* ====== Parseo de casos ====== */
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$ok  = $dom->loadHTML($final_utf8, LIBXML_NOERROR|LIBXML_NOWARNING|LIBXML_NONET);
libxml_clear_errors();

$casos = [];
if ($ok) {
  $allTables = $dom->getElementsByTagName('table');
  for ($i=0; $i<$allTables->length; $i++) {
    $t0 = $allTables->item($i);
    if (!$t0 instanceof DOMElement) continue;

    $t0Text = table_text($t0);
    if ($t0Text && mb_strpos($t0Text,'NOTICIA DEL DELITO',0,'UTF-8') !== false) {
      $encabezado = parse_table_node($t0);
      $headPlain=''; foreach($encabezado as $r){ $headPlain.=' '.implode(' ', array_filter($r)); }
      $headPlain = normalize_ws($headPlain);
      $numero = null;
      if ($headPlain && preg_match('/NOTICIA\s+DEL\s+DELITO\s+N(?:RO|ÚM|UM|O)?\.?\s*([0-9\-]+)/iu',$headPlain,$m)) {
        $numero = $m[1];
      }
      $t1 = next_element_sibling($t0,'table');              // personas
      $t2 = $t1 ? next_element_sibling($t1,'table') : null; // detalle
      $tabla_personas = $t1 ? parse_table_node($t1) : [];
      $tabla_detalle  = $t2 ? parse_table_node($t2) : [];
      $detalle_kv     = rows_to_kv($tabla_detalle);
      if (!isset($detalle_kv['DELITO']) || !$detalle_kv['DELITO']) {
        $delEnc = find_key_in_rows($encabezado,'DELITO'); if ($delEnc) $detalle_kv['DELITO'] = $delEnc;
      }
      $casos[] = [
        'numero_noticia'=>$numero,
        'encabezado'=>$encabezado,
        'tabla_personas'=>$tabla_personas,
        'tabla_detalle'=>$tabla_detalle,
        'detalle_kv'=>$detalle_kv
      ];
    }
  }
}

/* ====== Scoring personas ====== */
function norm_delito_label($s){ $s=mb_strtoupper($s??'','UTF-8'); $s=strtr($s,['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N']); $s=preg_replace('/\s+/u',' ',trim($s)); return $s; }
function parse_delito_y_codigo($s){ $label = norm_delito_label($s); $codigo=null; if(preg_match('/^(.*?)[\s]*\(([0-9]+)\)\s*$/u',$label,$m)){ $label=trim($m[1]); $codigo=$m[2]; } return [$label,$codigo]; }
function peso_por_delito($rawDelito,$DELITO_PESOS,$DELITO_CODIGOS){
  if(!$rawDelito) return 0; list($label,$codigo)=parse_delito_y_codigo($rawDelito);
  if($codigo && isset($DELITO_CODIGOS[$codigo])) return (int)$DELITO_CODIGOS[$codigo];
  $best=0; foreach($DELITO_PESOS as $k=>$peso){ $k=norm_delito_label($k); if($k!=='' && mb_strpos($label,$k,0,'UTF-8')!==false) $best=max($best,(int)$peso); }
  return $best>0 ? $best : 25;
}
function factor_por_rol($rol,$FACTOR_POR_ROL){ $rolN=norm_delito_label($rol); return $FACTOR_POR_ROL[$rolN] ?? 0.0; }
function clasifica_categoria($score){ if($score>=80) return 'CRITICO'; if($score>=50) return 'ALTO'; if($score>=20) return 'MEDIO'; if($score>=1) return 'BAJO'; return 'NULO'; }

$personas = [];
foreach ($casos as $c) {
  $delitoRaw = $c['detalle_kv']['DELITO'] ?? null;
  $pesoBase  = peso_por_delito($delitoRaw,$DELITO_PESOS,$DELITO_CODIGOS);

  foreach ($c['tabla_personas'] as $row) {
    if (count($row) < 3) continue;
    $hdr = mb_strtoupper(implode('|',$row),'UTF-8');
    if (strpos($hdr,'CEDULA')!==false && strpos($hdr,'NOMBRES')!==false && strpos($hdr,'ESTADO')!==false) continue;

    $ced = preg_replace('/\D+/', '', (string)$row[0]);
    $nom = $row[1] ?? null;
    $rol = $row[2] ?? null;
    if (!$ced || $ced==='0000000000') continue;

    $fact = factor_por_rol($rol,$FACTOR_POR_ROL);
    if ($fact <= 0) continue;

    $aporte = (int) round($pesoBase * $fact);

    if (!isset($personas[$ced])) {
      $personas[$ced] = ['cedula'=>$ced,'nombre'=>$nom,'peligrosidad'=>0,'aportes'=>[],'roles'=>[],'casos_contados'=>0];
    }
    $personas[$ced]['nombre'] = $nom ?: $personas[$ced]['nombre'];
    $personas[$ced]['roles'][$rol] = ($personas[$ced]['roles'][$rol] ?? 0) + 1;
    $personas[$ced]['casos_contados']++;
    $personas[$ced]['aportes'][] = [
      'numero_noticia'=>$c['numero_noticia'],'delito'=>$delitoRaw,'peso_base'=>$pesoBase,
      'rol'=>$rol,'factor_rol'=>$fact,'aporte'=>$aporte
    ];
    $personas[$ced]['peligrosidad'] = min(100, $personas[$ced]['peligrosidad'] + $aporte);
  }
}
$personas_riesgo = array_values($personas);
usort($personas_riesgo, fn($a,$b)=> $b['peligrosidad'] <=> $a['peligrosidad']);
foreach ($personas_riesgo as &$p) { $p['categoria'] = clasifica_categoria($p['peligrosidad']); }

/* ====== Meta HTTP del último request ====== */
$httpCode = null; $contentType = null;
if (!empty($lastHdr)) {
  if (preg_match('#HTTP/\d\.\d\s+(\d+)#', $lastHdr[0], $m)) $httpCode = (int)$m[1];
  foreach ($lastHdr as $h) { if (stripos($h,'Content-Type:')===0){ $contentType = trim(substr($h,strlen('Content-Type:'))); break; } }
}

/* ====== Tracking/Insert ====== */
$metodo = 'siaf-casos-riesgo';
$sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
           VALUES('$iduser', '$busqEsc', '1', '$KEY_ESC', '$metodo')";
mysqli_query($conection, $sqlIns);

/* ====== Post-insert: métricas y COBRO CONDICIONAL ====== */
$usadas_mes_despues = $usadas_mes_antes + 1;
$gratis_restantes   = max(0, $FREE_MONTHLY - $usadas_mes_despues);

$cantidad       = 1; // siempre 1 por consulta
$es_pago_bool   = ($es_pago === 1);
$costo_consulta = $es_pago_bool ? ($precioProducto * $cantidad) : 0.00;

$saldo_inicio   = null;
$saldo_restante = null;

if ($es_pago_bool) {
  // ---- DESCONTAR SALDO (transacción real) ----
  mysqli_begin_transaction($conection);
  try {
    // Leer saldo con bloqueo
    $qSaldo = mysqli_query($conection, "SELECT cantidad FROM saldo_total_leben WHERE idusuario = {$iduser} LIMIT 1 FOR UPDATE");
    if (!$qSaldo || mysqli_num_rows($qSaldo) === 0) {
      mysqli_rollback($conection);
      echo json_encode(['error' => 'saldo_no_encontrado']); exit();
    }
    $rowSaldo     = mysqli_fetch_assoc($qSaldo);
    $saldo_inicio = (float)$rowSaldo['cantidad'];

    if ($saldo_inicio < $costo_consulta) {
      mysqli_rollback($conection);
      echo json_encode([
        'error'      => 'saldo_insuficiente',
        'necesario'  => round($costo_consulta, 2),
        'disponible' => round($saldo_inicio, 2)
      ]);
      exit();
    }

    // Descontar
    $saldo_restante = $saldo_inicio - $costo_consulta;
    $okSaldo = mysqli_query($conection, "
      UPDATE saldo_total_leben
      SET cantidad = '{$saldo_restante}'
      WHERE idusuario = '{$iduser}'
      LIMIT 1
    ");
    if (!$okSaldo) { throw new Exception('error_update_saldo'); }

    // Registrar historial del cargo
    mysqli_query($conection, "
      INSERT INTO historial_bancario(
        precio_unidad, cantidad_producto, idp, cantidad_parcial, cantidad_comision,
        id_usuario, cantidad, accion, metodo, id_admin, id_accionado
      ) VALUES(
        '{$precioProducto}', '{$cantidad}', '{$idProductoApi}', '{$costo_consulta}', '0',
        '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API-SIAF', '0', 'Ninguno'
      )
    ");

    mysqli_commit($conection);

  } catch (Throwable $e) {
    mysqli_rollback($conection);
    echo json_encode(['error' => 'error_cobro', 'detalle' => $e->getMessage()]); exit();
  }
} else {
  // Gratis → leer saldo solo para informar (opcional)
  $qSaldo = mysqli_query($conection, "SELECT cantidad FROM saldo_total_leben WHERE idusuario = {$iduser} LIMIT 1");
  if ($qSaldo && mysqli_num_rows($qSaldo) > 0) {
    $rowSaldo      = mysqli_fetch_assoc($qSaldo);
    $saldo_inicio  = (float)$rowSaldo['cantidad'];
    $saldo_restante= $saldo_inicio; // sin cambios
  }
}

/* ====== Respuesta ====== */
echo json_encode([
  'status'        => 'success',
  'meta'          => [
    'cedula'       => $cedula,
    'duration_ms'  => $duration_ms,
    'timestamp'    => date('c'),
    'total_casos'  => count($casos),
    'nota'         => 'Peligrosidad es un indicador heurístico; no es determinación judicial.'
  ],
  'config'        => [
    'delito_pesos'   => $DELITO_PESOS,
    'delito_codigos' => $DELITO_CODIGOS,
    'factor_por_rol' => $FACTOR_POR_ROL,
    'max_score'      => 100
  ],
  'casos'           => $casos,
  'personas_riesgo' => $personas_riesgo,
  'quota'           => [
    'mensual_max'      => $FREE_MONTHLY,
    'usadas_mes'       => $usadas_mes_despues,
    'gratis_restantes' => $gratis_restantes,
    'es_pago'          => $es_pago, // 0=gratis, 1=cobrada
    'rango_mes'        => [$monthStartDate, $monthEndDate],
  ],
  'billing' => [
    'id_producto_api' => $idProductoApi,
    'cantidad'        => $cantidad,
    'costo_unitario'  => round($precioProducto, 2),
    'costo_total'     => round($costo_consulta, 2),
    'saldo_inicio'    => is_null($saldo_inicio) ? null : round($saldo_inicio, 2),
    'saldo_restante'  => is_null($saldo_restante) ? null : round($saldo_restante, 2),
  ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
exit();
