<?php



$iduser= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
  $user_in= $_SESSION['user_in'];
}

$id_usuario_venta= $_SESSION['id'];


$id_generacion =  $id_usuario_venta;
$rol = $_SESSION['rol'];

$query_usuario_venta = mysqli_query($conection, "SELECT * FROM usuarios_punto_venta    WHERE usuarios_punto_venta.id =$id_usuario_venta");
$data_usuario_venta =mysqli_fetch_array($query_usuario_venta);
$nombres_usuarios_punto_venta    = $data_usuario_venta['nombres'];
$direccion_usuario_venta         = $data_usuario_venta['direccion'];
$mail_usuario_venta              = $data_usuario_venta['mail'];
$iduser                     =     $data_usuario_venta['iduser'];
$cambio_password_usuarios_punto_venta   = $data_usuario_venta['cambio_password'];
$foto_usuarios_punto_venta       = $data_usuario_venta['foto'];
$fecha_registro_usuario_venta    = $data_usuario_venta['fecha'];
$url_img_upload_usuario_venta    = $data_usuario_venta['url_img_upload'];
$identificacion_usuario_venta    = $data_usuario_venta['identificacion'];
$foto_usuario_venta              = $data_usuario_venta['foto'];
$ciudad_usuario_venta            = $data_usuario_venta['ciudad'];
$telefono_usuario_venta          = $data_usuario_venta['telefono'];
$celular_usuario_venta           = $data_usuario_venta['celular'];




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

$nombre_empresa                   = $result['nombre_empresa'];
$razon_social               = $result['razon_social'];
$numero_identidad               = $result['numero_identidad'];

$whatsapp             = $result['whatsapp'];
$instagram            = $result['instagram'];
$facebook             = $result['facebook'];
$pagina_web             = $result['pagina_web'];

$descripcion_usuerio             = $result['descripcion'];

$latitud             = $result['latitud'];
$longitud             = $result['longitud'];

 ?>
