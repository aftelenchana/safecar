<?php
// ======================================================
// API Rebotadora → SRI Render (login/consulta)
// Recibe:  KEY (interna) + usuario/clave + key_sistem + filtros
// Reenvía: POST JSON a https://srirender.onrender.com/sri/login
// Tiempo:  soporta hasta 4 min (timeout 240s) con reintentos
// Tracking: registra en busquedaapiruc + control de cuota + cobro real por producto 13601
// ======================================================

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Origin, X-Requested-With, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit(); }
header('Content-Type: application/json; charset=utf-8');

@ignore_user_abort(true);
@set_time_limit(300); // margen extra por si el remoto demora

// ========= INPUT =========
$raw = file_get_contents('php://input');
$in  = json_decode($raw ?: '{}', true);
if (isset($in['JSONDocumento'])) {
  $in = json_decode($in['JSONDocumento'], true);
}
if (!is_array($in)) { $in = $_POST; }

// KEY interna para validar contra tu BD
$KEY = trim($in['KEY'] ?? '');

// Campos requeridos por el endpoint remoto
$usuario     = trim((string)($in['usuario'] ?? ''));
$clave       = (string)($in['clave'] ?? '');
$key_sistem  = trim((string)($in['key_sistem'] ?? '')); // → se reenvía como "key"
$tipo        = trim((string)($in['tipo'] ?? ''));
$modo        = trim((string)($in['modo'] ?? ''));
$fechaInicio = trim((string)($in['fechaInicio'] ?? ''));
$fechaFinal  = trim((string)($in['fechaFinal'] ?? ''));
$documentos  = $in['documentos'] ?? [];

// Validaciones base
if ($KEY === '') { echo json_encode(['error' => 'KEY vacía.']); exit(); }

$req_missing = [];
foreach ([
  'usuario' => $usuario, 'clave' => $clave, 'key_sistem' => $key_sistem,
  'tipo' => $tipo, 'modo' => $modo, 'fechaInicio' => $fechaInicio, 'fechaFinal' => $fechaFinal
] as $k => $v) { if ($v === '') $req_missing[] = $k; }

if (!is_array($documentos) || !count($documentos)) {
  // si vino como string "facturas,notas_de_credito" => array
  if (is_string($documentos) && trim($documentos) !== '') {
    $documentos = array_map('trim', explode(',', $documentos));
  } else {
    $req_missing[] = 'documentos[]';
  }
}

if ($req_missing) {
  echo json_encode(['error' => 'Faltan campos: '.implode(', ', $req_missing)]); exit();
}

// Validación de fechas YYYY-MM-DD
$reDate = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($reDate, $fechaInicio) || !preg_match($reDate, $fechaFinal)) {
  echo json_encode(['error' => 'Formato de fecha inválido. Use YYYY-MM-DD en fechaInicio/fechaFinal.']); exit();
}
if (strtotime($fechaInicio) === false || strtotime($fechaFinal) === false) {
  echo json_encode(['error' => 'Fechas no válidas.']); exit();
}
if (strtotime($fechaInicio) > strtotime($fechaFinal)) {
  echo json_encode(['error' => 'fechaInicio no puede ser mayor que fechaFinal.']); exit();
}

// ========= BD / KEY =========
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

// ========= Producto (ESTÁTICO) que define el costo por consulta =========
$idProductoApi = 13607; // fijo
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

// ========= Cuota mensual (del 1 al último día) =========
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

// Para el campo 'busqueda' en tracking (sin registrar claves)
$busqueda_txt = sprintf(
  'usuario=%s; tipo=%s; modo=%s; rango=%s..%s; docs=%s',
  mysqli_real_escape_string($conection, $usuario),
  mysqli_real_escape_string($conection, $tipo),
  mysqli_real_escape_string($conection, $modo),
  mysqli_real_escape_string($conection, $fechaInicio),
  mysqli_real_escape_string($conection, $fechaFinal),
  mysqli_real_escape_string($conection, implode('|', (array)$documentos))
);

// ========= Rebotar al servicio Render =========
$endpoint = "https://srirender.onrender.com/sri/login";
$payloadArr = [
  "usuario"     => $usuario,
  "clave"       => $clave,
  "key"         => $key_sistem,    // ← se envía como "key" al Render
  "tipo"        => $tipo,
  "modo"        => $modo,
  "fechaInicio" => $fechaInicio,
  "fechaFinal"  => $fechaFinal,
  "documentos"  => array_values($documentos),
];
$payload = json_encode($payloadArr, JSON_UNESCAPED_UNICODE);

// Función con reintentos y timeout grande
function post_json_with_retry($url, $jsonBody, $maxSeconds = 240, $connectTO = 30, $retries = 3) {
  $attempt = 0; $last = ['error' => 'unknown']; $start = microtime(true);
  while ($attempt < $retries) {
    $attempt++;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER    => true,
      CURLOPT_POST              => true,
      CURLOPT_HTTPHEADER        => [
        'Content-Type: application/json; charset=utf-8',
        'Accept: application/json',
      ],
      CURLOPT_POSTFIELDS        => $jsonBody,
      CURLOPT_CONNECTTIMEOUT    => $connectTO,   // 30s para conectar
      CURLOPT_TIMEOUT           => $maxSeconds,  // hasta 240s totales
      CURLOPT_TCP_KEEPALIVE     => 1,
      CURLOPT_TCP_KEEPIDLE      => 30,
      CURLOPT_TCP_KEEPINTVL     => 15,
      CURLOPT_FOLLOWLOCATION    => true,
      CURLOPT_MAXREDIRS         => 3,
      // CURLOPT_SSL_VERIFYPEER => false, // si tu CA falla (no recomendado)
    ]);
    $resp     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($resp !== false && $httpCode >= 200 && $httpCode < 500) {
      return ['ok' => true, 'httpCode' => $httpCode, 'body' => $resp, 'attempt' => $attempt];
    }

    $last = ['ok' => false, 'httpCode' => $httpCode, 'error' => $err, 'attempt' => $attempt];
    if ((microtime(true) - $start) > ($maxSeconds + 30)) break;
    $sleep = [1, 3, 7, 15][$attempt - 1] ?? 15; usleep($sleep * 1000000);
  }
  return $last;
}

$res = post_json_with_retry($endpoint, $payload, 240, 30, 3);

// ========= Manejo de respuesta =========
$metodo = 'sri-login';
if (!($res['ok'] ?? false)) {
  // Log de intento fallido
  $sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
             VALUES('$iduser', '$busqueda_txt', '0', '$KEY_ESC', '$metodo')";
  mysqli_query($conection, $sqlIns);

  echo json_encode([
    'status'   => 'error',
    'httpCode' => $res['httpCode'] ?? null,
    'error'    => $res['error'] ?? 'sin detalle',
    'message'  => 'Fallo al contactar el servicio remoto o timeout.'
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  exit();
}

$httpCode = (int)($res['httpCode'] ?? 0);
$rawBody  = $res['body'] ?? '';
$remote   = json_decode($rawBody, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  $remote = ['raw' => $rawBody, 'note' => 'Respuesta no-JSON del servicio remoto.'];
}

// ========= Tracking OK =========
$sqlIns = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
           VALUES('$iduser', '$busqueda_txt', '1', '$KEY_ESC', '$metodo')";
mysqli_query($conection, $sqlIns);

// ========= Post-insert: cuota + COBRO CONDICIONAL =========
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

    // Registrar historial del cargo
    mysqli_query($conection, "
      INSERT INTO historial_bancario(
        precio_unidad, cantidad_producto, idp, cantidad_parcial, cantidad_comision,
        id_usuario, cantidad, accion, metodo, id_admin, id_accionado
      ) VALUES(
        '{$precioProducto}', '{$cantidad}', '{$idProductoApi}', '{$costo_consulta}', '0',
        '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API-SRI', '0', 'Ninguno'
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

// ========= Respuesta =========
echo json_encode([
  'status'   => 'success',
  'httpCode' => $httpCode,
  'data'     => $remote,        // JSON del servicio remoto (o raw)
  'relay'    => [
    'url'        => $endpoint,
    'attempt'    => $res['attempt'] ?? 1,
    'timeout_s'  => 240
  ],
  'quota'    => [
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
