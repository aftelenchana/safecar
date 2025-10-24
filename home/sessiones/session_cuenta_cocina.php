<?php



$iduser= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
}
$user_in= $_SESSION['user_in'];


$id_cocina= $_SESSION['id'];

$id_generacion =  $id_cocina;
$rol = $_SESSION['rol'];

$query_cocina = mysqli_query($conection, "SELECT * FROM cocina    WHERE cocina.id =$id_cocina");
$data_cocina =mysqli_fetch_array($query_cocina);
$nombres_cocina    = $data_cocina['nombres'];
$direccion_cocina         = $data_cocina['direccion'];
$mail_cocina              = $data_cocina['mail'];
$iduser                     =     $data_cocina['iduser'];
$cambio_password_cocina   = $data_cocina['cambio_password'];
$foto_cocina       = $data_cocina['foto'];
$fecha_registro_cocina    = $data_cocina['fecha'];
$url_img_upload_cocina    = $data_cocina['url_img_upload'];
$identificacion_cocina    = $data_cocina['identificacion'];
$foto_cocina              = $data_cocina['foto'];
$ciudad_cocina            = $data_cocina['ciudad'];
$celular_cocina           = $data_cocina['celular'];




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
