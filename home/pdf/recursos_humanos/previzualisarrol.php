<?php
require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

use Dompdf\Options;
include "../../../coneccion.php";

mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
session_start();
$iduser= $_SESSION['id'];
$empresa = $_COOKIE['empresa_id'];


 $codigo = $_GET['codigo'];


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 $gg = "$url2/home/pdf/recursos_humanos/generar_previzualizar_rol_pdf.php?iduser=$iduser&codigo=$codigo&empresa=$empresa";

 $html=file_get_contents_curl("$url2/home/pdf/recursos_humanos/generar_previzualizar_rol_pdf.php?iduser=$iduser&codigo=$codigo&empresa=$empresa");

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


$query_rol_pagos = mysqli_query($conection, "SELECT * FROM rol_pagos
    WHERE rol_pagos.iduser = '$iduser' AND rol_pagos.estatus = '1' AND rol_pagos.id = '$codigo'");


$data_rol_pagos = mysqli_fetch_array($query_rol_pagos);

$recurso_humano = $data_rol_pagos['recurso_humano'];

$query_recurso_humano = mysqli_query($conection,"SELECT recursos_humanos.id,DATE_FORMAT(recursos_humanos.fecha, '%W  %d de %b %Y %H:%i:%s') as 'fecha_f',recursos_humanos.foto,recursos_humanos.nombres,recursos_humanos.identificacion,
recursos_humanos.celular
FROM `recursos_humanos`
WHERE  recursos_humanos.estatus = '1' AND recursos_humanos.id = '$recurso_humano'");

$data_recursos_humanos =mysqli_fetch_array($query_recurso_humano);
$nombre_usuario = $data_recursos_humanos['nombres'];


$pdf->stream('Previzualisar-'.$nombre_usuario.'-'.$fecha_actual.'.pdf');




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
