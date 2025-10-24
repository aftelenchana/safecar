<?php
// Conexión a la base de datos
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api = $result_configuracion['cuota_sms_api'];

// Reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Cabeceras HTTP
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
$JSONData = file_get_contents("php://input");
$dataObject = json_decode($JSONData, true);

// Si viene anidado en JSONDocumento
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

// ====== Validar KEY → obtener iduser ======
$query_busqueda_usuario = mysqli_query(
    $conection,
    "SELECT * FROM `aplicaciones_api` WHERE key_api = '".mysqli_real_escape_string($conection, $KEY)."' LIMIT 1"
);
$existencia_usuario = $query_busqueda_usuario ? mysqli_num_rows($query_busqueda_usuario) : 0;

if ($existencia_usuario === 0) {
    echo json_encode(['error' => 'usuario no encontrado.']);
    exit();
}

$data_usuarios = mysqli_fetch_array($query_busqueda_usuario);
$iduser = (int)($data_usuarios['iduser'] ?? 0);
if ($iduser <= 0) {
    echo json_encode(['error' => 'iduser inválido para la KEY.']);
    exit();
}

/* ====== Producto (ESTÁTICO) que define el costo por consulta ====== */
$idProductoApi = 13657; // fijo como pediste
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

// Contar consultas del usuario en el mes (iduser + rango de fecha)
$sql_count = "
    SELECT COUNT(*) AS c
    FROM busquedaapiruc
    WHERE iduser = '$iduser'
      AND DATE(fecha) BETWEEN '$monthStartDate' AND '$monthEndDate'
";
$rs_count = mysqli_query($conection, $sql_count);
$rowCount = $rs_count ? mysqli_fetch_assoc($rs_count) : ['c' => 0];
$usadas_mes_antes = (int)($rowCount['c'] ?? 0);

// ¿Esta consulta es de pago (superó las gratis)?
$es_pago = ($usadas_mes_antes >= $FREE_MONTHLY) ? 1 : 0;

// ====== Normalizar entrada para consultas locales ======
$parametro_busqueda_sql = mysqli_real_escape_string($conection, $parametro_busqueda);

/* ====== Helper de INSERT de tracking ====== */


function guibis_insert_busqueda($con, $iduser, $key, $busqueda, $metodo){

  $client_ip   = $_SERVER['REMOTE_ADDR'] ?? '';

    $sql = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo,client_ip)
            VALUES('".$iduser."','".mysqli_real_escape_string($con, $busqueda)."','1','".mysqli_real_escape_string($con, $key)."','".mysqli_real_escape_string($con, $metodo)."','".$client_ip."')";
    return mysqli_query($con, $sql);
}

/* ===========================================================
   HELPERS SRI
   =========================================================== */

/**
 * Consulta SRI → Deudas por Denominación (simulando navegador)
 * Retorna array: ['http_code'=>int, 'data'=>array|null, 'raw'=>string, 'url'=>string, 'error'=>string|null]
 */
function sri_deudas_por_denominacion($termino, $tipoPersona = 'N', $resultados = 50) {
    $denominacion = rawurlencode($termino); // importante para PATH
    $url = "https://srienlinea.sri.gob.ec/movil-servicios/api/v1.0/deudas/porDenominacion/{$denominacion}?tipoPersona={$tipoPersona}&resultados={$resultados}";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTPGET        => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_ENCODING       => '', // gzip/deflate/br
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: es-EC,es;q=0.9',
            'Origin: https://srienlinea.sri.gob.ec',
            'Referer: https://srienlinea.sri.gob.ec/',
            'X-Requested-With: XMLHttpRequest',
        ],
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ]);

    $raw = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['http_code'=>0, 'data'=>null, 'raw'=>'', 'url'=>$url, 'error'=>"cURL: $err"];
    }

    $json = json_decode($raw, true);
    return ['http_code'=>$http, 'data'=>$json, 'raw'=>$raw, 'url'=>$url, 'error'=>null];
}

/** Detección de tipoPersona según longitud (10=N, 13=R; si texto → N). */
function detectar_tipo_persona($valor) {
    if (is_numeric($valor)) {
        $len = strlen($valor);
        if ($len === 13) return 'R';
        if ($len === 10) return 'N';
    }
    return 'N';
}

/** Extrae cédula/RUC probable de un item del JSON devuelto por Deudas */
function extraer_identificacion_de_item($item) {
    $candidatos = [
        'numeroIdentificacion', 'numIdentificacion', 'identificacion',
        'ruc', 'numeroRuc', 'numero_ruc', 'cedula'
    ];
    foreach ($candidatos as $k) {
        if (isset($item[$k]) && is_string($item[$k]) && $item[$k] !== '') {
            $soloDigitos = preg_replace('/\D+/', '', $item[$k]);
            if ($soloDigitos !== '') return $soloDigitos;
        }
    }
    return null;
}

/* ===========================================================
   LÓGICA PRINCIPAL NUEVA (SRI Deudas + Encapsulado; 1 solo insert)
   =========================================================== */

$RESULTADOS_FIJOS = 50; // SIEMPRE 50 como pediste
$tipoPersona_sugerido = detectar_tipo_persona($parametro_busqueda);

// 1) Consultar SRI Deudas por Denominación (siempre porDenominacion)
$respDeudas = sri_deudas_por_denominacion($parametro_busqueda, $tipoPersona_sugerido, $RESULTADOS_FIJOS);

if (!empty($respDeudas['error'])) {
    echo json_encode([
        'status' => 'error',
        'message'=> $respDeudas['error'],
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

if (!($respDeudas['http_code'] >= 200 && $respDeudas['http_code'] < 300) || empty($respDeudas['data'])) {
    echo json_encode([
        'status' => 'error',
        'message'=> "HTTP {$respDeudas['http_code']} o respuesta vacía",
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Normalizar items
$items = [];
if (is_array($respDeudas['data'])) {
    if (isset($respDeudas['data']['data']) && is_array($respDeudas['data']['data'])) {
        $items = $respDeudas['data']['data'];
    } else {
        $items = $respDeudas['data'];
    }
}
$coincidencias = is_array($items) ? count($items) : 0;

$modo       = ($coincidencias === 1) ? 'unico' : 'multiple';
$es_unico   = ($coincidencias === 1);

// Variables para salida e insert único
$detalle = null;
$identificacion_detectada = null;
$tipoPersona_detectado    = null;
$ruc_base10               = null;
$busqueda_final           = $parametro_busqueda;  // lo que se registrará
$metodo_final             = 'sri-deudas';         // un solo insert por proceso

// Si es único, intentar detalle por cédula/RUC
if ($es_unico) {
    $item = $items[0];
    $identificacion_detectada = extraer_identificacion_de_item($item);
    if (!$identificacion_detectada) {
        $identificacion_detectada = is_numeric($parametro_busqueda) ? preg_replace('/\D+/', '', $parametro_busqueda) : null;
    }

    if ($identificacion_detectada) {
        $len = strlen($identificacion_detectada);

        if ($len === 10) {
            // CÉDULA
            $tipoPersona_detectado = 'N';
            $detalle_cedula = consultar_cedula($identificacion_detectada);
            $detalle = [
                'cedula'     => $detalle_cedula,
                'ruc'        => null,
                'ruc_base10' => null
            ];
            // Insert único: se mantiene como 'sri-deudas'
            $busqueda_final = $parametro_busqueda;
            $metodo_final   = 'nombres-unico-cedula'; // etiqueta informativa (igual 1 solo insert)

        } elseif ($len === 13) {
            // RUC → además consultar cédula con base10 (quita 001)
            $tipoPersona_detectado = 'R';
            $ruc_full   = $identificacion_detectada;
            $ruc_base10 = substr($ruc_full, 0, 10);

            $detalle_ruc    = obtenerDatosSRI($ruc_full);
            $detalle_cedula = consultar_cedula($ruc_base10);

            $detalle = [
                'ruc'        => $detalle_ruc,
                'cedula'     => $detalle_cedula,
                'ruc_base10' => $ruc_base10
            ];
            // Insert único
            $busqueda_final = $parametro_busqueda;
            $metodo_final   = 'nombres-unico-ruc+cedula';

        } else {
            // Longitud inesperada: sin detalle
            $tipoPersona_detectado = detectar_tipo_persona($identificacion_detectada);
            $detalle = null;
            $busqueda_final = $parametro_busqueda;
            $metodo_final   = 'nombres-unico-sin-id';
        }
    } else {
        // No se pudo extraer identificación → solo original
        $detalle = null;
        $busqueda_final = $parametro_busqueda;
        $metodo_final   = 'nombres-unico-sin-id';
    }
} else {
    // Múltiples → devolver original
    $detalle = null;
    $busqueda_final = $parametro_busqueda;
    $metodo_final   = 'nombres-multiple';
}


// ====== ÚNICO INSERT DE TRACKING (una sola vez por proceso) ======
guibis_insert_busqueda($conection, $iduser, $KEY, $busqueda_final, $metodo_final);

/* ====== Post-tracking: métricas y COBRO CONDICIONAL ====== */
$usadas_mes_despues = $usadas_mes_antes + 1;      // asumimos insert correcto
$gratis_restantes   = max(0, $FREE_MONTHLY - $usadas_mes_despues);

$cantidad       = 1; // siempre 1 por consulta
$costo_consulta = $es_pago ? ($precioProducto * $cantidad) : 0.00;

$saldo_inicio   = null;
$saldo_restante = null;

if ($es_pago) {
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
                '{$iduser}', '{$costo_consulta}', 'API Consulta', 'API', '0', 'Ninguno'
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

/* ====== RESPUESTA FINAL (ENCAPSULADO) ====== */
$salida = [
    'status' => 'success',
    'busqueda' => [
        'parametro'     => $parametro_busqueda,
        'tipoPersona'   => $tipoPersona_sugerido,
        'resultados'    => 50,              // siempre 50
        'coincidencias' => $coincidencias,  // cantidad devuelta por SRI
        'es_unico'      => $es_unico,       // true/false
        'modo'          => $modo,           // "unico" o "multiple"
        'metodo_log'    => $metodo_final    // para debug/seguimiento
    ],
    'data' => null, // se rellena abajo
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
];

// Modo múltiple: devolver JSON original del SRI
if (!$es_unico) {
    $salida['data'] = [
        'original' => $respDeudas['data'], // el JSON tal cual
    ];
} else {
    // Modo único: devolver detalle y el item original
    // Reconstruimos $items por seguridad (igual ya está)
    if (is_array($respDeudas['data'])) {
        if (isset($respDeudas['data']['data']) && is_array($respDeudas['data']['data'])) {
            $items = $respDeudas['data']['data'];
        } else {
            $items = $respDeudas['data'];
        }
    }
    $itemOriginal = is_array($items) && count($items) ? $items[0] : null;

    $salida['data'] = [
        'identificacion_detectada' => $identificacion_detectada,
        'tipoPersona_detectado'    => $tipoPersona_detectado,
        'ruc_base10'               => $ruc_base10,   // null si no aplica
        'detalle'                  => $detalle,       // ['ruc'=>..., 'cedula'=>..., 'ruc_base10'=>...]
        'original'                 => $itemOriginal   // el item del listado original
    ];
}

echo json_encode($salida, JSON_UNESCAPED_UNICODE);
exit();

/* ============================
   Función para obtener datos del SRI (RUC)
   ============================ */
function obtenerDatosSRI($ruc) {
    $url1 = "https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/ConsolidadoContribuyente/obtenerPorNumerosRuc?ruc=" . $ruc;
    $url2 = "https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/Establecimiento/consultarPorNumeroRuc?numeroRuc=" . $ruc;

    // cURL #1
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, $url1);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
    $response1 = curl_exec($ch1);
    curl_close($ch1);

    // cURL #2
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $url2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $response2 = curl_exec($ch2);
    curl_close($ch2);

    $data1 = json_decode($response1, true);
    $data2 = json_decode($response2, true);

    if (!empty($data1) && is_array($data1) && isset($data1[0]['numeroRuc'])) {
        $datos = [
            'numeroRuc'                   => $data1[0]['numeroRuc'] ?? '',
            'razonSocial'                 => $data1[0]['razonSocial'] ?? '',
            'estadoContribuyenteRuc'      => $data1[0]['estadoContribuyenteRuc'] ?? '',
            'actividadEconomicaPrincipal' => $data1[0]['actividadEconomicaPrincipal'] ?? '',
            'tipoContribuyente'           => $data1[0]['tipoContribuyente'] ?? '',
            'regimen'                     => $data1[0]['regimen'] ?? '',
            'categoria'                   => $data1[0]['categoria'] ?? null,
            'obligadoLlevarContabilidad'  => $data1[0]['obligadoLlevarContabilidad'] ?? '',
            'agenteRetencion'             => $data1[0]['agenteRetencion'] ?? '',
            'contribuyenteEspecial'       => $data1[0]['contribuyenteEspecial'] ?? '',
            'informacionFechasContribuyente' => $data1[0]['informacionFechasContribuyente'] ?? [],
            'representantesLegales'       => $data1[0]['representantesLegales'] ?? [],
            'motivoCancelacionSuspension' => $data1[0]['motivoCancelacionSuspension'] ?? null,
            'contribuyenteFantasma'       => $data1[0]['contribuyenteFantasma'] ?? '',
            'transaccionesInexistente'    => $data1[0]['transaccionesInexistente'] ?? ''
        ];

        if (!empty($data2) && is_array($data2)) {
            $datos['establecimientos'] = $data2;
            foreach ($data2 as $establecimiento) {
                if (isset($establecimiento['matriz']) && $establecimiento['matriz'] === 'SI') {
                    $datos['direccionMatriz']       = $establecimiento['direccionCompleta'] ?? '';
                    $datos['nombreComercialMatriz'] = $establecimiento['nombreFantasiaComercial'] ?? '';
                    break;
                }
            }
        } else {
            $datos['establecimientos'] = [];
        }

        return $datos;
    } else {
        return false;
    }
}

/* ============================
   Consulta cédula con cURL
   ============================ */
function consultar_cedula($cedula) {
    $url = "https://sib.ambiente.gob.ec/registro/registrosib/identidad";

    $headers = [
        "Accept: application/json, text/javascript, */*; q=0.01",
        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "X-Requested-With: XMLHttpRequest",
        "Origin: https://sib.ambiente.gob.ec",
        "Referer: https://sib.ambiente.gob.ec/registro/registrosib/crear"
    ];

    $data = http_build_query([
        "ced_id" => $cedula
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ["error" => $error_msg];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $json_response = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Placeholder opcional
            if ($cedula === "1804843900") {
                $json_response['FechaNacimiento'] = "07/05/2001";
            }
            return $json_response;
        } else {
            return ["error" => "Respuesta no válida: No se pudo decodificar el JSON"];
        }
    } else {
        return ["error" => "Respuesta HTTP {$http_code}"];
    }
}
