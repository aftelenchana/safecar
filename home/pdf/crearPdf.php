<?php
session_start();
$iduser= $_SESSION['id'];
$clave_acc_guardar = '1502202301171685757600120010010000003171234567814';
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Introducimos HTML de prueba
 $html=file_get_contents_curl("http://localhost/home/pdf/factura.php?id=$iduser&clave_acc_guardar=$clave_acc_guardar");



// Instanciamos un objeto de la clase DOMPDF.
$pdf = new DOMPDF();



// Definimos el tamaño y orientación del papel que queremos.
$pdf->set_paper("letter", "portrait");
//$pdf->set_paper(array(0,0,104,250));

// Cargamos el contenido HTML.
$pdf->load_html($html,'UTF-8');
$pdf->setPaper('A4', 'portrait');
// Renderizamos el documento PDF.
$pdf->render();

$archivo = 'pdf/'.$clave_acc_guardar.'';
file_put_contents($archivo, $pdf->output());


// Enviamos el fichero PDF al navegador.
$pdf->stream(''.$clave_acc_guardar .'.pdf');
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
