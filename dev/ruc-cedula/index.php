<?php
// Conexión a la base de datos
require "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$cuota_sms_api          =  $result_configuracion['cuota_sms_api'];

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
// En aplicaciones_api el dueño está en iduser
$iduser = (int)($data_usuarios['iduser'] ?? 0);
if ($iduser <= 0) {
    echo json_encode(['error' => 'iduser inválido para la KEY.']);
    exit();
}

/* ====== Producto (ESTÁTICO) que define el costo por consulta ====== */
$idProductoApi = 13601; // fijo como pediste
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

// ====== Normalizar / escapar entrada para consultas locales ======
$parametro_busqueda = mysqli_real_escape_string($conection, $parametro_busqueda);

// Buscar en tu BD local (por ejemplo, recursos_humanos)
$query_cliente = "
    SELECT *
    FROM recursos_humanos
    WHERE identificacion = '$parametro_busqueda'
    ORDER BY recursos_humanos.fecha DESC
    LIMIT 1
";
$result_cliente = mysqli_query($conection, $query_cliente);

$response = [];
$longitud_caracteres = strlen($parametro_busqueda);

// ====== Helper de INSERT de tracking ======
function guibis_insert_busqueda($con, $iduser, $key, $busqueda, $metodo){
    // Mantenemos key_api y metodo para trazabilidad
    $sql = "INSERT INTO busquedaapiruc(iduser, busqueda, estado, key_api, metodo)
            VALUES('".$iduser."','".mysqli_real_escape_string($con, $busqueda)."','1','".mysqli_real_escape_string($con, $key)."','".mysqli_real_escape_string($con, $metodo)."')";
    return mysqli_query($con, $sql);
}

// ====== Lógica principal (conservando tus funciones externas) ======
if ($result_cliente && mysqli_num_rows($result_cliente) > 0) {
    $data_cliente = mysqli_fetch_assoc($result_cliente);

    switch ($longitud_caracteres) {
        case 10: // CÉDULA
            $cedula_data = consultar_cedula($parametro_busqueda);
            $response = array_merge(is_array($cedula_data) ? $cedula_data : [], [
                'mail'    => $data_cliente['mail'] ?? '',
                'celular' => $data_cliente['celular'] ?? '',
            ]);
            guibis_insert_busqueda($conection, $iduser, $KEY, $parametro_busqueda, 'ruc-cedula');
            break;

        case 13: // RUC / SRI
            $sri_data = obtenerDatosSRI($parametro_busqueda);
            if ($sri_data === false) {
                echo json_encode(['error' => 'datos_inexistentes']);
                exit();
            }
            $response = array_merge($sri_data, [
                'mail'    => $data_cliente['mail'] ?? '',
                'celular' => $data_cliente['celular'] ?? '',
            ]);
            guibis_insert_busqueda($conection, $iduser, $KEY, $parametro_busqueda, 'ruc');
            break;

        default:
            echo json_encode(['error' => 'datos_inexistentes']);
            exit();
    }

} else {
    // No existe en tu BD local, se responde solo con fuentes externas
    switch ($longitud_caracteres) {
        case 10: // CÉDULA
            $cedula_data = consultar_cedula($parametro_busqueda);
            $response = array_merge(is_array($cedula_data) ? $cedula_data : [], [
                'mail'    => '',
                'celular' => '',
            ]);
            guibis_insert_busqueda($conection, $iduser, $KEY, $parametro_busqueda, 'ruc-cedula');
            break;

        case 13: // RUC / SRI
            $sri_data = obtenerDatosSRI($parametro_busqueda);
            if ($sri_data === false) {
                echo json_encode(['error' => 'datos_inexistentes']);
                exit();
            }
            $response = array_merge($sri_data, [
                'mail'    => '',
                'celular' => '',
            ]);
            guibis_insert_busqueda($conection, $iduser, $KEY, $parametro_busqueda, 'ruc');
            break;

        default:
            echo json_encode(['error' => 'datos_inexistentes']);
            exit();
    }
}

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
], JSON_UNESCAPED_UNICODE);
exit();

/* ============================
   Función para obtener datos del SRI
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
            // Ejemplo de modificación puntual
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
