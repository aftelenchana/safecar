<?php



$iduser= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
}

$iduser= $_SESSION['id'];

$user_in= $_SESSION['user_in'];

$id_generacion =  $iduser;
$rol = $_SESSION['rol'];

$query = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
$result=mysqli_fetch_array($query);
$nombres           = $result['nombres'];

$direccion         = $result['direccion'];
$codigo_sri        = $result['codigo_sri'];


$img_logo             = $result['img_facturacion'];
$url_img_upload       = $result['url_img_upload'];
$email_user           = $result['email'];
$fecha                = $result['fecha_creacion'];
$ciudad_user          = $result['ciudad'];
$telefono_user        = $result['telefono'];
$celular_user         = $result['celular'];

$nombre_empresa       = $result['nombre_empresa'];
$razon_social         = $result['razon_social'];
$numero_identidad     = $result['numero_identidad'];

$whatsapp             = $result['whatsapp'];
$instagram            = $result['instagram'];
$facebook             = $result['facebook'];
$pagina_web           = $result['pagina_web'];

$descripcion_usuerio = $result['descripcion'];

$latitud             = $result['latitud'];
$longitud            = $result['longitud'];
$key_user            = $result['codigo_registro'];
$key_desarrollador   = $result['id_e'];
$carpeta_drive       = $result['carpeta_drive'];
$json_google_drive   = $result['json_google_drive'];
$capital_inicio      = $result['capital_inicio'];


$query_cantidad_creditos = mysqli_query($conection,"SELECT COUNT(*) as  cantidad_creditos
FROM creditos_guibis
WHERE creditos_guibis.iduser  = '$iduser'
AND  creditos_guibis.estatus = '1'");
$data_cantidad_creditod = mysqli_fetch_array($query_cantidad_creditos);
$cantidad_creditos = $data_cantidad_creditod['cantidad_creditos'];


$query_suma_activos_otorgados = mysqli_query($conection,"SELECT SUM(creditos_guibis.monto_credito) as 'monto_credito'
FROM `creditos_guibis`
WHERE creditos_guibis.iduser = '$iduser'
AND creditos_guibis.estatus = '1'
");
$data_query_otorgados =mysqli_fetch_array($query_suma_activos_otorgados);
$monto_creditos = round(($data_query_otorgados['monto_credito']),2);

//ANALISIS DE LAS CUTOAS PAGADAS PARA AGREGAR EL ANALISIS DE LAS GANANCIAS TOTALES

$query_cantidad_cuotas_pagadas = mysqli_query($conection,"SELECT COUNT(*) as  cantidad_cuotas_pagadas
FROM cuotas_creditos_guibis
WHERE cuotas_creditos_guibis.iduser  = '$iduser'
AND  cuotas_creditos_guibis.estatus = '1'
AND cuotas_creditos_guibis.estado = 'Pagado'
");
$data_cantidad_cuotas_pagadas = mysqli_fetch_array($query_cantidad_cuotas_pagadas);
$cantidad_cuotas_pagadas = $data_cantidad_cuotas_pagadas['cantidad_cuotas_pagadas'];

$query_suma_cuotas_pagadas = mysqli_query($conection,"SELECT SUM(cuotas_creditos_guibis.cuota_fija) as 'cuotas_pagadas'
FROM `cuotas_creditos_guibis`
WHERE cuotas_creditos_guibis.iduser = '$iduser'
AND cuotas_creditos_guibis.estatus = '1'
AND cuotas_creditos_guibis.estado = 'Pagado'
");
$data_cuotas_pagadas =mysqli_fetch_array($query_suma_cuotas_pagadas);
$cuotas_pagadas = round(($data_cuotas_pagadas['cuotas_pagadas']),2);

$ganancias_totales = $capital_inicio - $monto_creditos + $cuotas_pagadas;


 ?>
