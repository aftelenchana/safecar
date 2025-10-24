<?php
// ======================================================
// API Rebotadora → Firmador (XML + .p12)
// Recibe por POST (multipart/form-data):
//   - KEY        : tu llave interna para validar y controlar cuota
//   - xmlfile    : archivo XML a firmar
//   - firmap12   : archivo .p12
//   - clave      : contraseña del .p12
// También acepta JSON con base64: { KEY, clave, xmlfile_b64, firmap12_b64 }
// Reenvía al endpoint remoto (multipart):
//   https://guibis.com/home/facturacion/facturacionphp/controladores/firmar2
// Control de cuota (gratis por mes) + cobro real por producto 13601 cuando aplica
// ======================================================

/* --- Ajustes para cargas grandes --- */
@ini_set('post_max_size', '64M');
@ini_set('upload_max_filesize', '64M');
@ini_set('max_input_time', '300');
@ini_set('max_execution_time', '300');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Origin, X-Requested-With, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }
header('Content-Type: application/json; charset=utf-8');

@ignore_user_abort(true);
@set_time_limit(300);

/* ========= INPUT ========= */
$isMultipart = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;

if ($isMultipart) {
  $KEY      = trim($_POST['KEY']   ?? '');
  $clave    = (string)($_POST['clave'] ?? '');
  $xmlFile  = $_FILES['xmlfile']   ?? null;
  $p12File  = $_FILES['firmap12']  ?? null;
} else {
  $raw = file_get_contents('php://input');
  $in  = json_decode($raw ?: '{}', true);
  if (isset($in['JSONDocumento'])) $in = json_decode($in['JSONDocumento'], true);
  $KEY     = trim($in['KEY'] ?? '');
  $clave   = (string)($in['clave'] ?? '');
  $xml_b64 = $in['xmlfile_b64']  ?? null;
  $p12_b64 = $in['firmap12_b64'] ?? null;
}

/* ========= Validaciones básicas ========= */
if ($KEY === '') { echo json_encode(['error' => 'KEY vacía.']); exit(); }
if ($clave === '') { echo json_encode(['error' => 'El campo "clave" es obligatorio.']); exit(); }
if ($isMultipart) {
  if (!$xmlFile || ($xmlFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Falta el archivo "xmlfile" o hubo error al subirlo.']); exit();
  }
  if (!$p12File || ($p12File['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Falta el archivo "firmap12" o hubo error al subirlo.']); exit();
  }
} else {
  if (!$xml_b64 || !$p12_b64) {
    echo json_encode(['error' => 'Para JSON debes enviar xmlfile_b64 y firmap12_b64.']); exit();
  }
}

/* ========= BD / Validación de KEY ========= */
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api          =  $result_configuracion['cuota_sms_api'];

$KEY_ESC = mysqli_real_escape_string($conection, $KEY);
$qUser   = mysqli_query($conection, "SELECT * FROM aplicaciones_api WHERE key_api = '$KEY_ESC' LIMIT 1");
if (!$qUser || mysqli_num_rows($qUser) === 0) { echo json_encode(['error' => 'usuario no encontrado.']); exit(); }
$apiRow = mysqli_fetch_assoc($qUser);
$iduser = (int)($apiRow['iduser'] ?? 0);
if ($iduser <= 0) { echo json_encode(['error' => 'iduser inválido para la KEY.']); exit(); }

/* ========= Producto (ESTÁTICO) que define el costo por consulta ========= */
$idProductoApi = 13605; // fijo
$qProd = mysqli_query($conection, "SELECT precio FROM producto_venta WHERE idproducto = {$idProductoApi} LIMIT 1");
if (!$qProd || mysqli_num_rows($qProd) === 0) {
  echo json_encode(['error' => 'producto_api_no_encontrado', 'id_producto_api' => $idProductoApi]); exit();
}
$rowProd        = mysqli_fetch_assoc($qProd);
$precioProducto = (float)($rowProd['precio'] ?? 0.0);
if ($precioProducto <= 0) {
  echo json_encode(['error' => 'precio_producto_invalido', 'id_producto_api' => $idProductoApi, 'precio' => $precioProducto]); exit();
}

/* ========= Cuota mensual (del 1 al último día) ========= */
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

/* ========= Preparar archivos para reenviar ========= */
$tmpFiles = []; // para cleanup

function make_curl_file_from_upload($arr, $fallbackName) {
  $name = $arr['name'] ?: $fallbackName;
  $mime = $arr['type'] ?: 'application/octet-stream';
  return new CURLFile($arr['tmp_name'], $mime, $name);
}
function write_temp_from_b64($b64, $suggestedName) {
  $bin = base64_decode(preg_replace('#^data:.*?;base64,#', '', $b64), true);
  if ($bin === false) return [null, 'Base64 inválido'];
  $tmp = tempnam(sys_get_temp_dir(), 'up_');
  $ext = pathinfo($suggestedName, PATHINFO_EXTENSION);
  if ($ext) { $new = $tmp . "." . $ext; rename($tmp, $new); $tmp = $new; }
  if (file_put_contents($tmp, $bin) === false) return [null, 'No se pudo escribir archivo temporal'];
  return [$tmp, null];
}

if ($isMultipart) {
  $curlXml = make_curl_file_from_upload($xmlFile, 'documento.xml');
  $curlP12 = make_curl_file_from_upload($p12File, 'certificado.p12');
} else {
  list($xmlTmp, $e1) = write_temp_from_b64($xml_b64, 'documento.xml');
  if ($e1) { echo json_encode(['error' => $e1]); exit(); }
  list($p12Tmp, $e2) = write_temp_from_b64($p12_b64, 'certificado.p12');
  if ($e2) { @unlink($xmlTmp); echo json_encode(['error' => $e2]); exit(); }

  $tmpFiles[] = $xmlTmp;
  $tmpFiles[] = $p12Tmp;

  $curlXml = new CURLFile($xmlTmp, 'application/xml', 'documento.xml');
  $curlP12 = new CURLFile($p12Tmp, 'application/x-pkcs12', 'certificado.p12');
}

/* ========= Envío multipart al firmador (con retry) ========= */
$endpoint = "https://guibis.com/home/facturacion/facturacionphp/controladores/firmar2";

$postFields = [
  'xmlfile'  => $curlXml,
  'firmap12' => $curlP12,
  'clave'    => $clave,
];

function post_multipart_with_retry($url, $fields, $timeout = 240, $connectTO = 30, $retries = 2) {
  $attempt = 0; $last = ['ok'=>false,'error'=>'unknown'];
  while ($attempt <= $retries) {
    $attempt++;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => $fields,
      CURLOPT_CONNECTTIMEOUT => $connectTO,
      CURLOPT_TIMEOUT        => $timeout,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_MAXREDIRS      => 3,
      // CURLOPT_SSL_VERIFYPEER => false, // solo si tu CA falla
    ]);
    $resp     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($resp !== false && $httpCode >= 200 && $httpCode < 500) {
      return ['ok'=>true,'httpCode'=>$httpCode,'body'=>$resp,'attempt'=>$attempt];
    }
    $last = ['ok'=>false,'httpCode'=>$httpCode,'error'=>$err,'attempt'=>$attempt];
    sleep(min(10, $attempt * 3)); // backoff simple
  }
  return $last;
}

$res = post_multipart_with_retry($endpoint, $postFields, 240, 30, 2);

/* ========= Limpieza de temporales ========= */
foreach ($tmpFiles as $f) { @is_file($f) && @unlink($f); }

/* ========= Tracking + respuesta en fallo ========= */
$metodo      = 'firmador-rebote';
$busquedaLog = "firmar XML; size_xml=" .
  ($isMultipart ? (int)$xmlFile['size'] : (int)@filesize($tmpFiles[0] ?? '')) .
  "; size_p12=" .
  ($isMultipart ? (int)$p12File['size'] : (int)@filesize($tmpFiles[1] ?? ''));

if (!($res['ok'] ?? false)) {
  $sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
             VALUES('$iduser', '".mysqli_real_escape_string($conection,$busquedaLog)."', '0', '$KEY_ESC', '$metodo')";
  mysqli_query($conection, $sqlIns);

  echo json_encode([
    'status'   => 'error',
    'httpCode' => $res['httpCode'] ?? null,
    'error'    => $res['error'] ?? 'sin detalle',
    'message'  => 'Fallo al contactar el firmador o timeout.',
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  exit();
}

/* ========= Tracking ok ========= */
$sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
           VALUES('$iduser', '".mysqli_real_escape_string($conection,$busquedaLog)."', '1', '$KEY_ESC', '$metodo')";
mysqli_query($conection, $sqlIns);

/* ========= Post-insert: cuota + COBRO CONDICIONAL ========= */
$usadas_mes_despues = $usadas_mes_antes + 1;
$gratis_restantes   = max(0, $FREE_MONTHLY - $usadas_mes_despues);

$cantidad       = 1; // 1 consulta
$es_pago_bool   = ($es_pago === 1);
$costo_consulta = $es_pago_bool ? ($precioProducto * $cantidad) : 0.00;

$saldo_inicio   = null;
$saldo_restante = null;

if ($es_pago_bool) {
  // Descontar saldo real con transacción
  mysqli_begin_transaction($conection);
  try {
    // Saldo con bloqueo
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
      ]); exit();
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

    // Historial bancario
    mysqli_query($conection, "
      INSERT INTO historial_bancario(
        precio_unidad, cantidad_producto, idp, cantidad_parcial, cantidad_comision,
        id_usuario, cantidad, accion, metodo, id_admin, id_accionado
      ) VALUES(
        '{$precioProducto}', '{$cantidad}', '{$idProductoApi}', '{$costo_consulta}', '0',
        '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API-Firmador', '0', 'Ninguno'
      )
    ");

    mysqli_commit($conection);

  } catch (Throwable $e) {
    mysqli_rollback($conection);
    echo json_encode(['error' => 'error_cobro', 'detalle' => $e->getMessage()]); exit();
  }
} else {
  // Gratis: opcionalmente leer saldo para informar
  $qSaldo = mysqli_query($conection, "SELECT cantidad FROM saldo_total_leben WHERE idusuario = {$iduser} LIMIT 1");
  if ($qSaldo && mysqli_num_rows($qSaldo) > 0) {
    $rowSaldo      = mysqli_fetch_assoc($qSaldo);
    $saldo_inicio  = (float)$rowSaldo['cantidad'];
    $saldo_restante= $saldo_inicio;
  }
}

/* ========= Devolver respuesta del firmador ========= */
$httpCode = (int)($res['httpCode'] ?? 0);
$remote   = json_decode($res['body'] ?? '', true);
if (json_last_error() !== JSON_ERROR_NONE) {
  $remote = ['raw' => ($res['body'] ?? ''), 'note' => 'Respuesta no-JSON del firmador.'];
}

echo json_encode([
  'status'   => 'success',
  'httpCode' => $httpCode,
  'data'     => $remote,
  'relay'    => [
    'attempt'   => $res['attempt'] ?? 1,
    'timeout_s' => 240
  ],
  'quota' => [
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
