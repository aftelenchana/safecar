<?php
include "../../coneccion.php";
  session_start();
$iduser= $_SESSION['id'];
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Introducimos HTML de prueba

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
$img_facturacion          = $result['img_facturacion'];
  $url_img_upload  = $result['url_img_upload'];
$contabilidad         = $result['contabilidad'];
$regimen          = $result['regimen'];
$nombre_empresa          = $result['nombre_empresa'];

 if (empty($nombre_empresa)) {
   $nombre_salida = $result['nombres'];
   // code...
 }else {
   $nombre_salida = $nombre_empresa;
 }


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;


  $html=file_get_contents_curl("$url/home/pdf/ganancias_diario_restaurante.php?iduser=$iduser");


  $options = new Options();
  $options  -> set('isRemoteEnabled', TRUE);
  // Instanciamos un objeto de la clase DOMPDF.
  $pdf = new DOMPDF($options);
  // Definimos el tamaño y orientación del papel que queremos.
  $pdf->set_paper("letter", "portrait");
  //$pdf->set_paper(array(0,0,104,250));

  $pdf->load_html($html,'UTF-8');
  $pdf->setPaper('A4', 'portrait');
  // Renderizamos el documento PDF.
  $pdf->render();

  $fechaActual = date("d-m-Y H:i:s");


  $pdf->stream('Ganancias_dia'.$nombre_salida.'-'.$fechaActual.'.pdf');



function file_get_contents_curl($url) {
	$crl = curl_init();
	$timeout = 1;
	curl_setopt($crl, CURLOPT_URL, $url);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$ret = curl_exec($crl);
	curl_close($crl);
	return $ret;
}
