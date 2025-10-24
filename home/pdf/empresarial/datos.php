<?php
include "../../../coneccion.php";

require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
$iduser= $_SESSION['id'];

$iduser = isset($_GET['iduser']) ? trim($_GET['iduser']) : '';
$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : '';
$fecha_final = isset($_GET['fecha_final']) ? trim($_GET['fecha_final']) : '';
$identificacion = isset($_GET['identificacion']) ? trim($_GET['identificacion']) : '';
$facturas_emitidas = isset($_GET['facturas_emitidas']) ? trim($_GET['facturas_emitidas']) : '';
$facturas_recibidas = isset($_GET['facturas_recibidas']) ? trim($_GET['facturas_recibidas']) : '';
$credito_emitidas = isset($_GET['credito_emitidas']) ? trim($_GET['credito_emitidas']) : '';
$credito_recibidas = isset($_GET['credito_recibidas']) ? trim($_GET['credito_recibidas']) : '';
$retenciones_emitidas = isset($_GET['retenciones_emitidas']) ? trim($_GET['retenciones_emitidas']) : '';
$retenciones_recibidas = isset($_GET['retenciones_recibidas']) ? trim($_GET['retenciones_recibidas']) : '';



 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 $gg = "$url2/home/pdf/empresarial/datos_out.php?iduser=$iduser&fecha_inicio=$fecha_inicio&fecha_final=$fecha_final&identificacion=$identificacion&facturas_emitidas=$facturas_emitidas&retenciones_emitidas=$retenciones_emitidas&retenciones_recibidas=$retenciones_recibidas&facturas_recibidias=$facturas_recibidas&credito_emitidas=$credito_emitidas&credito_recibidas=$credito_recibidas";


 $html=file_get_contents("$url2/home/pdf/empresarial/datos_out.php?iduser=$iduser&fecha_inicio=$fecha_inicio&fecha_final=$fecha_final&identificacion=$identificacion&facturas_emitidas=$facturas_emitidas&retenciones_emitidas=$retenciones_emitidas&retenciones_recibidas=$retenciones_recibidas&facturas_recibidias=$facturas_recibidas&credito_emitidas=$credito_emitidas&credito_recibidas=$credito_recibidas");




$options = new Options();
$options  -> set('isRemoteEnabled', TRUE);
// Instanciamos un objeto de la clase DOMPDF.
$pdf = new DOMPDF($options);
// Definimos el tamaño y orientación del papel que queremos.
$pdf->set_paper("letter", "landscape");
//$pdf->set_paper(array(0,0,104,250));

$pdf->load_html($html,'UTF-8');
$pdf->setPaper('A4', 'landscape');
// Renderizamos el documento PDF.
$pdf->render();

$fecha_actual = date("Y-m-d H:i:s");

$pdf->stream('dd.pdf');


function file_get_contents_curl($url) {
    $crl = curl_init();
    $timeout = 10; // Aumenta el tiempo de espera
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($crl);
    if ($ret === false) {
        echo 'Error de cURL: ' . curl_error($crl);
    }
    curl_close($crl);
    return $ret;
}
