<?php



$idrecursos_humanos= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
}

$user_in= $_SESSION['user_in'];
$idrecursos_humanos= $_SESSION['id'];


$id_generacion =  $idrecursos_humanos;
$rol = $_SESSION['rol'];

$query_recursos_humanos = mysqli_query($conection, "SELECT * FROM recursos_humanos     WHERE recursos_humanos.id ='$idrecursos_humanos'");
$data_recursos_humanos  =mysqli_fetch_array($query_recursos_humanos);
$nombres_usuarios_punto_venta    = $data_recursos_humanos['nombres'];
$direccion_usuario_venta         = $data_recursos_humanos['direccion'];
$mail_usuario_venta              = $data_recursos_humanos['mail'];
$iduser                          = $data_recursos_humanos['iduser'];
$cambio_password_usuarios_punto_venta   = $data_recursos_humanos['cambio_password'];
$foto_usuarios_punto_venta       = $data_recursos_humanos['foto'];
$fecha_registro_usuario_venta    = $data_recursos_humanos['fecha'];
$url_img_upload_usuario_venta    = $data_recursos_humanos['url_img_upload'];
$identificacion_usuario_venta    = $data_recursos_humanos['identificacion'];
$foto_usuario_venta              = $data_recursos_humanos['foto'];
$ciudad_usuario_venta            = $data_recursos_humanos['ciudad'];
$telefono_usuario_venta          = $data_recursos_humanos['telefono'];
$celular_usuario_venta           = $data_recursos_humanos['celular'];
$cliente_rol                     = $data_recursos_humanos['cliente'];
$cobrador_rol                    = $data_recursos_humanos['cobrador'];




$query = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
$result=mysqli_fetch_array($query);
$nombres           = $result['nombres'];
$firma_electronica = $result['firma_electronica'];
$direccion         = $result['direccion'];
$codigo_sri        = $result['codigo_sri'];
$estableciminento        = $result['estableciminento_f'];
$punto_emision        = $result['punto_emision_f'];
$porcentaje_iva       = $result['porcentaje_iva_f'];
$apellidos         = $result['apellidos'];
$img_logo          = $result['img_facturacion'];
$url_img_upload           = $result['url_img_upload'];

$email_user           = $result['email'];
$fecha                = $result['fecha_creacion'];
$ciudad_user          = $result['ciudad'];
$telefono_user        = $result['telefono'];
$celular_user         = $result['celular'];
$contabilidad         = $result['contabilidad'];
$regimen              = $result['regimen'];
$contribuyente_especial             = $result['contribuyente_especial'];
$resolucion_contribuyente_especial  = $result['resolucion_contribuyente_especial'];
$agente_retencion                   = $result['agente_retencion'];
$resolucion_retencion               = $result['resolucion_retencion'];

$nombre_empresa       = $result['nombre_empresa'];
$razon_social         = $result['razon_social'];
$numero_identidad     = $result['numero_identidad'];

$whatsapp             = $result['whatsapp'];
$instagram            = $result['instagram'];
$facebook             = $result['facebook'];
$pagina_web           = $result['pagina_web'];
$descripcion_usuerio  = $result['descripcion'];
$latitud              = $result['latitud'];
$longitud             = $result['longitud'];
$carpeta_drive        = $result['carpeta_drive'];
$json_google_drive    = $result['json_google_drive'];
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
