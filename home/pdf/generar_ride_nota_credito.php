<?php

require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
$iduser= $_SESSION['id'];


 $clave_acceso_nota_credito = $_POST['clave_acceso_nota_credito'];


 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 $gg = "$url2/home/pdf/ride_nota_credito.php?iduser=$iduser&clave_acceso_nota_credito=$clave_acceso_nota_credito";


 $html=file_get_contents("$url2/home/pdf/ride_nota_credito.php?iduser=$iduser&clave_acceso_nota_credito=$clave_acceso_nota_credito");

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

 $archivo = '../facturacion/facturacionphp/comprobantes/nota-credito/pdf/'.$clave_acceso_nota_credito.'.pdf';
 file_put_contents($archivo, $pdf->output());

 $arrayName = array('noticia' =>'ride_exitoso','clave_acceso_nota_credito' =>$clave_acceso_nota_credito);
 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
