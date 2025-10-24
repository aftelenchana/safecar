<?php

require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;


 $clave_acceso_factura = $_POST['clave_acceso_factura'];


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 //$gg = "$url2/home/pdf/ride_factura.php?iduser=$iduser&clave_acceso_factura=$clave_acceso_factura";



 $html=file_get_contents("$url2/home/pdf/ride_factura.php?clave_acceso_factura=$clave_acceso_factura");

 // Instanciamos un objeto de la clase DOMPDF.
 $options = new Options();
 $options  -> set('isRemoteEnabled', TRUE);
 // Instanciamos un objeto de la clase DOMPDF.
 $pdf = new DOMPDF($options);
 // Definimos el tamaño y orientación del papel que queremos.
 $pdf->set_paper("letter", "portrait");
 //$pdf->set_paper(array(0,0,104,250));ntenido HTML.
 $pdf->load_html($html,'UTF-8');
 $pdf->setPaper('A4', 'portrait');
 // Renderizamos el documento PDF.
 $pdf->render();

 //CODIGO PARA ENVIAR AL FRONT DE QUE YA SE HA CREADO EL RIDE CORRECTAMENTE

 $archivo = '../facturacion/facturacionphp/comprobantes/pdf/'.$clave_acceso_factura.'.pdf';
 file_put_contents($archivo, $pdf->output());

 $arrayName = array('noticia' =>'ride_exitoso','clave_acceso_factura' =>$clave_acceso_factura);
 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
