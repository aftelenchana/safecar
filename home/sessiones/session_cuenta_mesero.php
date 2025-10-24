<?php



$iduser= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
}
$user_in= $_SESSION['user_in'];

$id_mesero= $_SESSION['id'];

$id_generacion =  $id_mesero;
$rol = $_SESSION['rol'];

$query_mesero = mysqli_query($conection, "SELECT * FROM mesero    WHERE mesero.id =$id_mesero");
$data_mesero =mysqli_fetch_array($query_mesero);
$nombres_mesero    = $data_mesero['nombres'];
$direccion_mesero         = $data_mesero['direccion'];
$mail_mesero              = $data_mesero['mail'];
$iduser                     =     $data_mesero['iduser'];
$cambio_password_mesero   = $data_mesero['cambio_password'];
$foto_mesero       = $data_mesero['foto'];
$fecha_registro_mesero    = $data_mesero['fecha'];
$url_img_upload_mesero    = $data_mesero['url_img_upload'];
$identificacion_mesero    = $data_mesero['identificacion'];
$foto_mesero              = $data_mesero['foto'];
$ciudad_mesero            = $data_mesero['ciudad'];
$celular_mesero           = $data_mesero['celular'];




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
