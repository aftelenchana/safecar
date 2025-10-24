<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";

require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
$iduser= $_SESSION['id'];

 $codigo = $_GET['codigo'];

 $query_lista = mysqli_query($conection,"SELECT mensajes_masivos_wsp.nombre_campana,
   mensajes_masivos_wsp.modo_tiempo,
   mensajes_masivos_wsp.fecha_hora_envio,
   mensajes_masivos_wsp.fecha,
   mensajes_masivos_wsp.intervalo_tiempo
    FROM mensajes_masivos_wsp
   INNER JOIN numeros_extras ON numeros_extras.id = mensajes_masivos_wsp.code_numero
    WHERE mensajes_masivos_wsp.estatus = '1' AND mensajes_masivos_wsp.id = '$codigo'");

    $data_consulta = mysqli_fetch_array($query_lista);

    $nombre_camp = $data_consulta['nombre_campana'];



 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 $gg = "$url2/home/pdf/mensajeria/campanas_out.php?codigo=$codigo&iduser=$iduser";


 $html=file_get_contents_curl("$url2/home/pdf/mensajeria/campanas_out.php?codigo=$codigo&iduser=$iduser");

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

$pdf->stream('Campaña-'.$nombre_camp.'.pdf');




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
