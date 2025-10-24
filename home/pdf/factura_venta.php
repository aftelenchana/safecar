<?php
  $venta = $_GET['venta'];
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;


session_start();
$iduser= $_SESSION['id'];

// Introducimos HTML de prueba


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;

 $gg = "$url/home/pdf/factura_venta_generar.php?venta=$venta?iduser=$iduser";


  $html=file_get_contents_curl("$url/home/pdf/factura_venta_generar.php?venta=$venta?iduser=$iduser");


  // Instanciamos un objeto de la clase DOMPDF.
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

// Enviamos el fichero PDF al navegador.
$pdf->stream('Venta'.$venta.'.pdf');




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
