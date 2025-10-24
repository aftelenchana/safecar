<?php


require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
$iduser= $_SESSION['id'];
$empresa = $_COOKIE['empresa_id'];

 $codigo_factura = $_GET['codigo'];

 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;


 $gg = "$url2/home/pdf/generar_previzualizar_nota_credito_emitido.php?iduser=$iduser&codigo_factura=$codigo_factura";


 $html=file_get_contents_curl("$url2/home/pdf/generar_previzualizar_nota_credito_emitido.php?iduser=$iduser&codigo_factura=$codigo_factura");

 //$html=file_get_contents_curl("$url2/home/pdf/generar_previzualizar_pdf.php?iduser=$iduser&documento_electronico=$documento_electronico&codigo_factura=$codigo_factura&razon_social_cliente=$razon_social_cliente&direccion_reeptor=$direccion_reeptor&email_reeptor=$email_reeptor&celular_receptor=$celular_receptor&idcliente=$idcliente&identificacion_cliente=$identificacion_cliente");

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

$fecha_actual = date("Y-m-d H:i:s");

$pdf->stream('Previzualisar -'.$codigo_factura.'-'.$fecha_actual.'.pdf');




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
