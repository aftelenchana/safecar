<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";

require_once '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
$iduser= $_SESSION['id'];

 $fecha_inicio = $_GET['fecha_inicio'];
 $fecha_final = $_GET['fecha_final'];
 $codigo_plan_cuentas = $_GET['codigo_plan_cuentas'];
 $empresa = $_COOKIE['empresa_id'];




 mysqli_query($conection,"SET lc_time_names = 'es_ES'");
  $query_consulta_datos = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id, contabilidad_plan_cuentas.nombre_cuenta,
      contabilidad_plan_cuentas.nivel_1, contabilidad_plan_cuentas.nivel_2, contabilidad_plan_cuentas.nivel_3, contabilidad_plan_cuentas.nivel_4,
      contabilidad_plan_cuentas.nivel_5, contabilidad_plan_cuentas.nivel_6, contabilidad_plan_cuentas.codigo, contabilidad_datos_asiento_contable.concepto,
      contabilidad_datos_asiento_contable.debito, contabilidad_datos_asiento_contable.credito, contabilidad_plan_cuentas.id as 'identificador_plan_cuentas',
      DATE_FORMAT(contabilidad_datos_asiento_contable.fecha, '%W  %d de %b %Y %h:%m:%s') as 'fecha'
      FROM contabilidad_datos_asiento_contable
      INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
      WHERE contabilidad_datos_asiento_contable.iduser ='$iduser'
      AND contabilidad_datos_asiento_contable.estatus = '1'
      AND contabilidad_plan_cuentas.id = '$codigo_plan_cuentas'
      ORDER BY contabilidad_datos_asiento_contable.fecha DESC");

      $data_lista=mysqli_fetch_array($query_consulta_datos);


      $query_consulta = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
         WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
         AND contabilidad_plan_cuentas.id = '$codigo_plan_cuentas'
         ORDER BY nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6 ");
                $data = array();
             while ($row = mysqli_fetch_assoc($query_consulta)) {
                 $data[] = $row;
             }
      foreach ($data as $row) {
            $jerarquia = '';
            for ($i = 1; $i <= 6; $i++) {
                if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                    $jerarquia .= $row["nivel_{$i}"];
                    if ($i < 6 && $row["nivel_" . ($i + 1)] && $row["nivel_" . ($i + 1)] !== '0') {
                        $jerarquia .= '.';
                    }
                }
            }
            // Asumo que 'id' es el identificador único para usar como valor del option.
            // Ajusta esta parte si deseas usar otro campo como el valor del option.
            $opcionValor = $row['id'];
            $nombre_cuenta = $row['nombre_cuenta'];
            // Usamos la jerarquía construida como el texto del option.
            $opcionTexto = $jerarquia;

            $dtlle_plan_cuentas =  "$opcionTexto - $nombre_cuenta";
        }



 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;

 $gg = "$url2/home/pdf/contable/mayorizacion_out.php?fecha_inicio=$fecha_inicio&fecha_final=$fecha_final&codigo_plan_cuentas=$codigo_plan_cuentas&empresa=$empresa&iduser=$iduser";

 $html=file_get_contents_curl("$url2/home/pdf/contable/mayorizacion_out.php?fecha_inicio=$fecha_inicio&fecha_final=$fecha_final&codigo_plan_cuentas=$codigo_plan_cuentas&empresa=$empresa&iduser=$iduser");

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

$pdf->stream('Mayorización-'.$dtlle_plan_cuentas.'-'.$fecha_inicio.'-'.$fecha_final.'.pdf');




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
