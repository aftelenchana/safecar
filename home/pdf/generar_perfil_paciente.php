<?php
  $paciente = $_GET['paciente'];
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Introducimos HTML de prueba


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;


  $html=file_get_contents_curl("$url/home/pdf/paciente.php?paciente=$paciente");


// Instanciamos un objeto de la clase DOMPDF.
$pdf = new DOMPDF();



// Definimos el tamaño y orientación del papel que queremos.
$pdf->set_paper("letter", "portrait");
//$pdf->set_paper(array(0,0,104,250));

$pdf->load_html($html,'UTF-8');
$pdf->setPaper('A4', 'portrait');
// Renderizamos el documento PDF.
$pdf->render();

// Enviamos el fichero PDF al navegador.
$pdf->stream('Paciente_'.$paciente.'.pdf');




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
