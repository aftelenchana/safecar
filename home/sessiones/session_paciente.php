<?php



$idpaciente= $_SESSION['id'];
if (empty($_SESSION['active'])) {
  header('location:/');
}

$user_in= $_SESSION['user_in'];
$idrecursos_humanos= $_SESSION['id'];


$id_generacion =  $idrecursos_humanos;
$rol = $_SESSION['rol'];

$query_recursos_humanos = mysqli_query($conection, "SELECT * FROM clientes     WHERE clientes.id ='$idpaciente'");
$data_recursos_humanos  =mysqli_fetch_array($query_recursos_humanos);
$nombres_usuarios_punto_venta    = $data_recursos_humanos['nombres'];
$direccion_usuario_venta         = $data_recursos_humanos['direccion'];
$mail_usuario_venta              = $data_recursos_humanos['mail'];
$iduser                          = $data_recursos_humanos['iduser'];
$foto_usuarios_punto_venta       = $data_recursos_humanos['foto'];
$fecha_registro_usuario_venta    = $data_recursos_humanos['fecha'];
$url_img_upload_usuario_venta    = $data_recursos_humanos['url_img_upload'];
$identificacion_usuario_venta    = $data_recursos_humanos['identificacion'];
$foto_usuario_venta              = $data_recursos_humanos['foto'];
$ciudad_usuario_venta            = $data_recursos_humanos['ciudad'];
$telefono_usuario_venta          = $data_recursos_humanos['telefono'];
$celular_usuario_venta           = $data_recursos_humanos['celular'];




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
