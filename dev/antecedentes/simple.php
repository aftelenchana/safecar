<?php
// ===============================
// API FJ – Defensor Penal por cédula (+ cálculo de peligrosidad Ecuador)
// Entrada JSON: {
//   "KEY": "...",
//   "parametro_busqueda": "1309022935",
//   "paginaInicial"?:1,
//   "paginaFinal"?:10,
//   "agg_mode"?: "union"|"max",   // opcional (default: union)
//   "alpha"?: 0.05                 // opcional (para modo max)
// }
// Respuesta: proxy del JSON remoto + tracking + billing + peligrosidad por caso y global
// ===============================

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['error' => 'Método de solicitud no permitido. Solo POST.']);
  exit();
}

// ====== INPUT (JSON o JSONDocumento anidado) ======
$raw        = file_get_contents("php://input");
$dataObject = json_decode($raw, true);
if (isset($dataObject['JSONDocumento'])) {
  $dataObject = json_decode($dataObject['JSONDocumento'], true);
}

$KEY = trim($dataObject['KEY'] ?? '');
if ($KEY === '') {
  echo json_encode(['error' => 'KEY vacía.']);
  exit();
}

$cedula = trim($dataObject['parametro_busqueda'] ?? '');
if ($cedula === '') {
  echo json_encode(['error' => 'El parámetro de búsqueda está vacío.']);
  exit();
}

// Paginación opcional (defaults: 1 a 10)
$paginaInicial = (int)($dataObject['paginaInicial'] ?? 1);
$paginaFinal   = (int)($dataObject['paginaFinal']   ?? 10);
if ($paginaInicial <= 0) $paginaInicial = 1;
if ($paginaFinal   <= 0) $paginaFinal   = 10;

// Params de agregación de peligrosidad (opcionales)
$AGG_MODE = strtolower(trim($dataObject['agg_mode'] ?? 'union')); // 'union' | 'max'
if (!in_array($AGG_MODE, ['union','max'], true)) { $AGG_MODE = 'union'; }
$ALPHA = is_numeric($dataObject['alpha'] ?? null) ? (float)$dataObject['alpha'] : 0.05; // solo para 'max'

// ====== Conexión BD ======
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api        =  (int)($result_configuracion['cuota_sms_api'] ?? 0);

// ====== Validar KEY → obtener iduser ======
$KEY_ESC = mysqli_real_escape_string($conection, $KEY);
$qUser   = mysqli_query($conection, "SELECT * FROM aplicaciones_api WHERE key_api = '$KEY_ESC' LIMIT 1");
if (!$qUser || mysqli_num_rows($qUser) === 0) {
  echo json_encode(['error' => 'usuario no encontrado.']);
  exit();
}
$apiRow = mysqli_fetch_assoc($qUser);
$iduser = (int)($apiRow['iduser'] ?? 0);
if ($iduser <= 0) {
  echo json_encode(['error' => 'iduser inválido para la KEY.']);
  exit();
}

/* ====== Producto (ESTÁTICO) que define el costo por consulta ====== */
$idProductoApi = 13603; // fijo
$qProd = mysqli_query(
  $conection,
  "SELECT precio FROM producto_venta WHERE idproducto = {$idProductoApi} LIMIT 1"
);
if (!$qProd || mysqli_num_rows($qProd) === 0) {
  echo json_encode(['error' => 'producto_api_no_encontrado', 'id_producto_api' => $idProductoApi]);
  exit();
}
$rowProd        = mysqli_fetch_assoc($qProd);
$precioProducto = (float)($rowProd['precio'] ?? 0.0);
if ($precioProducto <= 0) {
  echo json_encode(['error' => 'precio_producto_invalido', 'id_producto_api' => $idProductoApi, 'precio' => $precioProducto]);
  exit();
}

/* ====== Config de cuota mensual (del 1 al último día) ====== */
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

// ====== Sanitizar parámetro para trazabilidad ======
$busqEsc = mysqli_real_escape_string($conection, $cedula);

// ====== Construir request remoto ======
$base = "https://consultas.funcionjudicial.gob.ec";
$path = "/informacionjudicialindividual/api/defensorPenal/buscarPorNombreCedula/{$cedula}/{$paginaInicial}/{$paginaFinal}/cedula";
$url  = $base . $path;

$payload = [
  "origen"       => "cedula",
  "paginaFinal"  => $paginaFinal,
  "paginaIncial" => $paginaInicial,
  "parametro"    => $cedula
];

// ====== cURL POST ======
$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => [
    "Content-Type: application/json; charset=utf-8",
    "Accept: application/json",
    "Origin: https://consultas.funcionjudicial.gob.ec",
    "Referer: https://consultas.funcionjudicial.gob.ec/"
  ],
  CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT        => 40,
  // CURLOPT_SSL_VERIFYPEER => false, // bajo propio riesgo
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
  echo json_encode(['error' => "cURL error: $curlErr"]);
  exit();
}

$fjData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  echo json_encode(['error' => 'Respuesta remota no es JSON válido', 'httpCode' => $httpCode, 'raw' => $response]);
  exit();
}

// ====== Insert tracking ======
$metodo = 'fj-defensor-penal';
$sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
           VALUES('$iduser', '$busqEsc', '1', '$KEY_ESC', '$metodo')";
mysqli_query($conection, $sqlIns);

// ====== Post-insert: métricas y cobro condicional ======
$usadas_mes_despues = $usadas_mes_antes + 1; // asumimos insert correcto
$gratis_restantes   = max(0, $FREE_MONTHLY - $usadas_mes_despues);

$cantidad       = 1; // siempre 1 por consulta
$es_pago_bool   = ($es_pago === 1);
$costo_consulta = $es_pago_bool ? ($precioProducto * $cantidad) : 0.00;

$saldo_inicio   = null;
$saldo_restante = null;

if ($es_pago_bool) {
  mysqli_begin_transaction($conection);
  try {
    $qSaldo = mysqli_query($conection, "SELECT cantidad FROM saldo_total_leben WHERE idusuario = {$iduser} LIMIT 1 FOR UPDATE");
    if (!$qSaldo || mysqli_num_rows($qSaldo) === 0) {
      mysqli_rollback($conection);
      echo json_encode(['error' => 'saldo_no_encontrado']);
      exit();
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

    $saldo_restante = $saldo_inicio - $costo_consulta;
    $okSaldo = mysqli_query($conection, "
      UPDATE saldo_total_leben
      SET cantidad = '{$saldo_restante}'
      WHERE idusuario = '{$iduser}'
      LIMIT 1
    ");
    if (!$okSaldo) { throw new Exception('error_update_saldo'); }

    mysqli_query($conection, "
      INSERT INTO historial_bancario(
        precio_unidad, cantidad_producto, idp, cantidad_parcial, cantidad_comision,
        id_usuario, cantidad, accion, metodo, id_admin, id_accionado
      ) VALUES(
        '{$precioProducto}', '{$cantidad}', '{$idProductoApi}', '{$costo_consulta}', '0',
        '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API-FJ', '0', 'Ninguno'
      )
    ");

    mysqli_commit($conection);

  } catch (Throwable $e) {
    mysqli_rollback($conection);
    echo json_encode(['error' => 'error_cobro', 'detalle' => $e->getMessage()]);
    exit();
  }
} else {
  $qSaldo = mysqli_query($conection, "SELECT cantidad FROM saldo_total_leben WHERE idusuario = {$iduser} LIMIT 1");
  if ($qSaldo && mysqli_num_rows($qSaldo) > 0) {
    $rowSaldo      = mysqli_fetch_assoc($rowSaldo ?? $qSaldo); // robustez
    $rowSaldo      = is_array($rowSaldo) ? $rowSaldo : mysqli_fetch_assoc($qSaldo);
    $saldo_inicio  = (float)$rowSaldo['cantidad'];
    $saldo_restante= $saldo_inicio;
  }
}

/* ==========================================================
   PELIGROSIDAD ECUADOR: mapa, keywords y cálculo (mixto)
   ========================================================== */

function normalizar_delito($texto) {
  $t = preg_replace('/^\s*\d+\s*/', '', (string)$texto); // quitar códigos "### "
  $t = mb_strtoupper($t, 'UTF-8');
  $t = strtr($t, [
    'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U',
    'À'=>'A','È'=>'E','Ì'=>'I','Ò'=>'O','Ù'=>'U',
    'Ñ'=>'N'
  ]);
  $t = trim(preg_replace('/\s+/', ' ', $t));
  return $t;
}

// Mapa extensible: palabra clave (normalizada) => % base
$peligrosidad_map = [
  'ASESINATO'                => 95,
  'SICARIATO'                => 95,
  'HOMICIDIO'                => 90,
  'SECUESTRO'                => 90,
  'VIOLACION'                => 90,
  'DELINCUENCIA ORGANIZADA'  => 85,
  'ASOCIACION ILICITA'       => 75,
  'EXTORSION'                => 70,
  'TRAFICO DE DROGAS'        => 80,
  'TRAFICO DE ARMAS'         => 80,
  'ROBO'                     => 60,
  'HURTO'                    => 30,
  'ESTAFA'                   => 40,
  'PECULADO'                 => 30,
  // agrega aquí nuevas claves => %
];

// Palabras clave críticas (fallback si no matchea mapa)
$keywords_altas = [
  'ASESINATO' => 95,
  'VIOLACION' => 90,
  'FEMICIDIO' => 95,
  'SECUESTRO' => 90,
  'TORTURA'   => 90,
];

/* --- detectar dónde viene 'respuesta' en la FJ ---
   - Caso A (más común): $fjData['respuesta'] es array
   - Caso B: $fjData['data']['respuesta'] es array
*/
$pathCase = null;
$respuestas = [];

if (isset($fjData['respuesta']) && is_array($fjData['respuesta'])) {
  $respuestas = $fjData['respuesta'];
  $pathCase = 'root'; // raíz
} elseif (isset($fjData['data']['respuesta']) && is_array($fjData['data']['respuesta'])) {
  $respuestas = $fjData['data']['respuesta'];
  $pathCase = 'nested'; // dentro de data
} else {
  // Si no hay lista, devolver igual estructura con peligro 0
  $respuestas = [];
  $pathCase = 'none';
}

// Paso A: contar ocurrencias por delito normalizado
$crimeCounts = [];
foreach ($respuestas as $caso) {
  $nombreDelito = $caso['nombreDelito'] ?? '';
  $norm = normalizar_delito($nombreDelito);
  if ($norm !== '') {
    $crimeCounts[$norm] = ($crimeCounts[$norm] ?? 0) + 1;
  }
}

// Paso B: calcular peligrosidad por caso (base + repeticiones + keywords)
$total_raw = 0.0;
$casos_num = count($respuestas);

foreach ($respuestas as $idx => $caso) {
  $nombreDelito = $caso['nombreDelito'] ?? '';
  $norm = normalizar_delito($nombreDelito);

  // 1) base por mapa
  $danger = 0;
  if ($norm !== '') {
    foreach ($peligrosidad_map as $clave => $porc) {
      if (strpos($norm, $clave) !== false) {
        $danger = $porc;
        break;
      }
    }
  }
  // 2) fallback por keywords
  if ($danger === 0 && $norm !== '') {
    foreach ($keywords_altas as $kw => $porc) {
      if (strpos($norm, $kw) !== false) {
        $danger = $porc;
        break;
      }
    }
  }
  // 3) default si no clasifica
  if ($danger === 0) { $danger = 50; }

  // 4) ajuste por repeticiones
  if ($norm !== '' && isset($crimeCounts[$norm]) && $crimeCounts[$norm] > 1) {
    $reps  = $crimeCounts[$norm];
    $extra = 5 * ($reps - 1);           // +5% por repetición adicional
    $danger = min(100, $danger + $extra);
  }

  $respuestas[$idx]['peligrosidad'] = $danger;
  $total_raw += $danger;
}

// Paso C: peligrosidad global con agregador no-diluyente
$global_percent = 0.0;
if ($casos_num > 0) {
  if ($AGG_MODE === 'union') {
    // Unión probabilística: 1 - Π(1 - pi/100)
    $prod = 1.0;
    foreach ($respuestas as $caso) {
      $pi = max(0, min(100, (float)($caso['peligrosidad'] ?? 0)));
      $prod *= (1.0 - $pi / 100.0);
    }
    $global_percent = round((1.0 - $prod) * 100.0, 2);
  } else { // 'max'
    $maxp = 0.0;
    $sumOtros = 0.0;
    foreach ($respuestas as $caso) {
      $pi = max(0, min(100, (float)($caso['peligrosidad'] ?? 0)));
      $maxp = max($maxp, $pi);
    }
    foreach ($respuestas as $caso) {
      $pi = max(0, min(100, (float)($caso['peligrosidad'] ?? 0)));
      if ($pi < $maxp) { $sumOtros += $pi; }
    }
    $global_percent = round(min(100.0, $maxp + max(0.0, $ALPHA) * $sumOtros), 2);
  }
}

// Reinyectar respuestas modificadas en el mismo path de origen (sin inventar "data")
if ($pathCase === 'root') {
  $fjData['respuesta'] = $respuestas;
} elseif ($pathCase === 'nested') {
  $fjData['data']['respuesta'] = $respuestas;
} else {
  // nada: mantenemos fjData tal cual vino si no había lista
}

// ====== Respuesta final ======
echo json_encode([
  'status'   => 'success',
  'httpCode' => $httpCode,
  'data'     => $fjData, // JSON FJ (con peligrosidad por caso en su mismo path)
  'peligrosidad_consulta' => [
    'porcentaje'      => $global_percent,  // 0–100
    'modo'            => $AGG_MODE,        // 'union' o 'max'
    'alpha'           => ($AGG_MODE === 'max' ? $ALPHA : null),
    'casos'           => $casos_num,
    'suma_individual' => $total_raw,       // suma de % individuales
    'criterios'       => [
      'base_manual'           => 'Mapa extensible por delito',
      'ajuste_repeticiones'   => '+5% por cada repetición adicional (tope 100% por caso)',
      'palabras_clave'        => array_keys($keywords_altas),
      'default_si_desconocido'=> 50
    ]
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
