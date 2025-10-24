<?php
// ===============================
// API ANT (código persona + citaciones)
// Entrada JSON: { "KEY": "...", "parametro_busqueda": "..." }
// ===============================

// Errores (ajusta en prod)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Encabezados
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=utf-8');

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['error' => 'Método de solicitud no permitido. Solo se permite POST.']);
  exit();
}

// ====== INPUT (JSON o JSONDocumento anidado) ======
$JSONData    = file_get_contents("php://input");
$dataObject  = json_decode($JSONData, true);
if (isset($dataObject['JSONDocumento'])) {
  $dataObject = json_decode($dataObject['JSONDocumento'], true);
}

$parametro_busqueda = trim($dataObject['parametro_busqueda'] ?? '');
if ($parametro_busqueda === '') {
  echo json_encode(['error' => 'El parámetro de búsqueda está vacío.']);
  exit();
}
$KEY = $dataObject['KEY'] ?? '';
if ($KEY === '') {
  echo json_encode(['error' => 'KEY vacía.']);
  exit();
}

// ====== Conexión BD ======
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api          =  $result_configuracion['cuota_sms_api'];


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
$idProductoApi = 13602; // fijo
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

// Rango del mes actual
$monthStartDate = date('Y-m-01'); // primer día
$monthEndDate   = date('Y-m-t');  // último día

// Contar consultas del usuario en el mes (iduser + rango de fecha)
$sql_count = "
  SELECT COUNT(*) AS c
  FROM busquedaapiruc
  WHERE iduser = '$iduser'
    AND DATE(fecha) BETWEEN '$monthStartDate' AND '$monthEndDate'
";
$rs_count         = mysqli_query($conection, $sql_count);
$rowCount         = $rs_count ? mysqli_fetch_assoc($rs_count) : ['c' => 0];
$usadas_mes_antes = (int)($rowCount['c'] ?? 0);

// ¿Esta consulta es de pago (superó las gratis)?
$es_pago = ($usadas_mes_antes >= $FREE_MONTHLY) ? 1 : 0;

// ====== Sanitizar parámetro para trazabilidad / inserts ======
$busqEsc = mysqli_real_escape_string($conection, $parametro_busqueda);

// ====== Funciones ANT ======
function ant_hacer_get($url, $headers = []) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $res = curl_exec($ch);
  if ($res === false) {
    $err = curl_error($ch);
    curl_close($ch);
    return ['error' => $err];
  }
  curl_close($ch);
  $json = json_decode($res, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    return ['error' => 'Respuesta no válida de ANT'];
  }
  return $json;
}

// Código persona
function ant_consultar_codigo_persona($identificacion_busqueda) {
  // Identifica si es cédula (10 dígitos) o placa (lo demás)
  $tipo = (strlen($identificacion_busqueda) == 10) ? 'CED' : 'PLA';
  $url  = "https://consultaweb.ant.gob.ec/PortalWEB/paginas/clientes/clp_json_consulta_persona.jsp?"
        . "ps_tipo_identificacion={$tipo}"
        . "&ps_identificacion=" . urlencode($identificacion_busqueda)
        . "&ps_placa=";

  $headers = [
    "Content-Type: application/json",
    "Authorization: Bearer TU_TOKEN_AQUI", // <-- Reemplaza por tu token real si aplica
    "Accept: application/json",
    "User-Agent: PHP-cURL",
    "Connection: keep-alive"
  ];

  $resp = ant_hacer_get($url, $headers);
  return [$resp, $tipo];
}

// Citaciones
function ant_consultar_citaciones($id_contrato, $id_persona, $placa, $identificacion, $tipo_identificacion) {
  $url = "https://consultaweb.ant.gob.ec/PortalWEB/paginas/clientes/clp_json_citaciones.jsp?"
       . "ps_opcion=P"
       . "&ps_id_contrato=" . urlencode($id_contrato)
       . "&ps_id_persona=" . urlencode($id_persona)
       . "&ps_placa=" . urlencode($placa)
       . "&ps_identificacion=" . urlencode($identificacion)
       . "&ps_tipo_identificacion=" . urlencode($tipo_identificacion)
       . "&_search=false&nd=" . time() . "&rows=50&page=1&sidx=fecha_emision&sord=desc";

  $headers = [
    "Content-Type: application/json",
    "Authorization: Bearer TU_TOKEN_AQUI", // <-- Reemplaza por tu token real si aplica
    "Accept: application/json",
    "User-Agent: PHP-cURL",
    "Connection: keep-alive"
  ];

  return ant_hacer_get($url, $headers);
}

// ====== Llamadas a ANT ======
// 1) Código persona
list($personaResp, $tipoIdentificacion) = ant_consultar_codigo_persona($parametro_busqueda);

if (isset($personaResp['error'])) {
  echo json_encode(['error' => 'Error consultando código persona: ' . $personaResp['error']]);
  exit();
}

$id_contrato = $personaResp['id_contrato'] ?? "";
$placa       = $personaResp['placa'] ?? "";
$id_persona  = $personaResp['id_persona'] ?? "";

// 2) Citaciones
$citResp    = ant_consultar_citaciones($id_contrato, $id_persona, $placa, $parametro_busqueda, $tipoIdentificacion);
$citaciones = $citResp['rows'] ?? [];

// ====== Armar respuesta ======
$response = [
  "datos_persona" => [
    "id_contrato"         => $id_contrato,
    "id_persona"          => $id_persona,
    "placa"               => $placa,
    "tipo_identificacion" => $tipoIdentificacion
  ],
  "citaciones"   => []
];

if (!empty($citaciones) && is_array($citaciones)) {
  foreach ($citaciones as $citacion) {
    $cell = $citacion['cell'] ?? [];
    $response["citaciones"][] = [
      "id"                 => $cell[1]  ?? "",
      "entidad"            => $cell[2]  ?? "",
      "codigo"             => $cell[3]  ?? "",
      "fecha_infraccion"   => $cell[6]  ?? "",
      "fecha_notificacion" => $cell[7]  ?? "",
      "valor_multa"        => $cell[16] ?? "",
      "detalle"            => $cell[17] ?? ""
    ];
  }
}

// ====== Tracking de la consulta ======
$metodo = 'ant-citaciones';
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
  // ---- DESCONTAR SALDO (transacción real) ----
  mysqli_begin_transaction($conection);
  try {
    // Leer saldo con bloqueo
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
        '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API-ANT', '0', 'Ninguno'
      )
    ");

    mysqli_commit($conection);

  } catch (Throwable $e) {
    mysqli_rollback($conection);
    echo json_encode(['error' => 'error_cobro', 'detalle' => $e->getMessage()]);
    exit();
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

// ====== Respuesta final ======
echo json_encode([
  'status' => 'success',
  'data'   => $response,
  'quota'  => [
    'mensual_max'      => $FREE_MONTHLY,
    'usadas_mes'       => $usadas_mes_despues,
    'gratis_restantes' => $gratis_restantes,
    'es_pago'          => $es_pago, // 0 = gratis, 1 = cobrada
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
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
