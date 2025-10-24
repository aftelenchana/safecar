<?php
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar


$iduser               = $_GET['iduser'];
$fecha_inicio         = $_GET['fecha_inicio'];
$fecha_final          = $_GET['fecha_final'];
$identificacion       = $_GET['identificacion'];
$facturas_emitidas    = $_GET['facturas_emitidas'];
$retenciones_emitidas = $_GET['retenciones_emitidas'];
$retenciones_recibidas= $_GET['retenciones_recibidas'];

$facturas_recibidas = $_GET['facturas_recibidias'];
//$credito_emitidas    = $_GET['credito_emitidas'];
//$credito_recibidas   = $_GET['credito_recibidas'];

$nombre_archivo = "Datos-" . $identificacion . "-" . $fecha_inicio . "-".$fecha_final.".xls";

 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title><?php echo $nombre_archivo ?> </title>
   </head>
   <body>
     <style media="screen">

     .cabezera{
       width: 1125PX;
       height: 100px;
       margin-top: -50PX;
     }
   .cabezera h4{
     margin: auto;
     padding: 15px;

   }

   .cabezera img {
     width: 100px;
   }

 .bloque_gene{
   display: inline-block;
   width: 500px;
 }
 .informacion_titulos_cabezera{
   margin-top: -40px;
 }

 .informacion_titulos_cabezera h5{
   padding: 0px;
   margin: 0px;
   color: #000;
   font-weight: bold;
 }
     </style>


     <?php

     $query_consulta_usuario = mysqli_query($conection, "SELECT * FROM usuarios_empresariales
        WHERE usuarios_empresariales.iduser ='$iduser'  AND usuarios_empresariales.estatus = '1' AND usuarios_empresariales.identificacion = '$identificacion' ");
     $data_consulta_usuario = mysqli_fetch_array($query_consulta_usuario);

     $nombre_empresa = $data_consulta_usuario['nombre_empresa'];
     $imagen         = $data_consulta_usuario['imagen'];
     $codigo_usuario         = $data_consulta_usuario['id'];

        $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;
        $fecha_actual = date("d-m-Y H:i:s");

      ?>

     <div class="cabezera">
       <br>
       <div class="bloque_gene">
         <br>
           <img src="<?php echo $url2 ?>/home/img/uploads/<?php echo $imagen ?>" alt="">
       </div>
       <div class="bloque_gene informacion_titulos_cabezera">
         <h5><?php echo $nombre_empresa?></h5>
         <h5><?php echo $fecha_actual ?></h5>
       </div>
     </div>

     <hr>
     <style media="screen">
       .contenedor_titu_asiento{
         text-align: center;
       }
     </style>
     <div class="contenedor_titu_asiento">
       <p><?php echo $nombre_archivo?></p>
     </div>

     <style media="screen">
     .table-responsive tr,td,th  {
     border: solid 1px #c1c1c1;
     text-align: center;

     }

     .table-responsive th{
     width: 80px;
         font-size: 11px;
     }
     .table-responsive td{
     width: 80px;
         font-size: 11px;
     }

     .table-responsive table{
        border-collapse: collapse;
        margin: 0 auto;
            font-size: 11px;
     }
     </style>
     <?php
     $facturas_emitidas         = $_GET['facturas_emitidas'];
     $facturas_recibidias         = $_GET['facturas_recibidias'];
     $notas_credito_emitidas         = $_GET['credito_emitidas'];
     $notas_credito_recibidas         = $_GET['credito_recibidas'];
     $retenciones_emitidas         = $_GET['retenciones_emitidas'];
     $retenciones_recibidas         = $_GET['retenciones_recibidas'];


      ?>

      <?php if ($facturas_emitidas == 'on'): ?>
     <main role="main" class="container">
       <h5>Facturación electrónica <?php echo $facturas_emitidas ?> Fecha de incicio <?php echo $fecha_inicio ?> Fecha Final <?php echo $fecha_final ?> </h5>
         <div class="row">
             <div class="col-12">
                 <div class="table-responsive">
                     <table class="table table-bordered">
                         <thead>
                             <tr>
                                 <th>RUC EMISOR</th>
                                 <th>RAZON SOCIAL EMISOR</th>
                                 <th>SERIE COMPROBANTE</th>
                                 <th>CLAVE ACCESO</th>
                                 <th>FECHA EMISIÓN</th>
                                 <th>IDENTIFICACION RECEPTOR</th>
                                 <th>VALOR SIN IMPUESTOS</th>
                                 <th>IVA</th>
                                 <th>IMPORTE TOTAL</th>
                             </tr>
                         </thead>
                         <tbody>
                           <?php

                       $query_lista = mysqli_query($conection," SELECT *
                         FROM datos_documentos_recibidos_txt
                         INNER JOIN usuarios_empresariales ON usuarios_empresariales.id = datos_documentos_recibidos_txt.usuario
                         WHERE STR_TO_DATE(datos_documentos_recibidos_txt.FECHA_EMISION, '%d/%m/%Y')
                         BETWEEN STR_TO_DATE('$fecha_inicio', '%Y-%m-%d') AND STR_TO_DATE('$fecha_final', '%Y-%m-%d')
                         AND  datos_documentos_recibidos_txt.usuario = '$codigo_usuario'
                         AND datos_documentos_recibidos_txt.proceso = 'Enviado'
                         AND datos_documentos_recibidos_txt.TIPO_COMPROBANTE = 'Factura'
                         ORDER BY `datos_documentos_recibidos_txt`.`fecha` desc");

                       $result_lista= mysqli_num_rows($query_lista);
                     if ($result_lista > 0) {
                           while ($data_lista=mysqli_fetch_array($query_lista)) {
                            ?>
                             <tr>
                                <td><?php echo $data_lista['RUC_EMISOR']; ?> </td>
                                <td><?php echo $data_lista['RAZON_SOCIAL_EMISOR']; ?> </td>
                                <td><?php echo $data_lista['SERIE_COMPROBANTE']; ?> </td>
                                <td><?php echo $data_lista['CLAVE_ACCESO']; ?> </td>
                                <td><?php echo $data_lista['FECHA_EMISION']; ?> </td>
                                <td><?php echo $data_lista['IDENTIFICACION_RECEPTOR']; ?> </td>
                                <td><?php echo $data_lista['VALOR_SIN_IMPUESTOS']; ?></td>
                                <td>$<?php echo $data_lista['IVA']; ?></td>
                                <td>$<?php echo $data_lista['IMPORTE_TOTAL']; ?></td>
                             </tr>
                             <?php
                             }
                             }
                         ?>
                         </tbody>
                     </table>
                     <style media="screen">
                     .tabla_resumen_tr{
                       width: 100px;
                       height: 60px
                       padding: 5px;
                     }
                     .resumen_ganancias_dia{
                       padding: 5px;
                       margin: 3px;
                     }

                     </style>
                     <div class="resumen_ganancias_dia">
                       <?php

                       $query_ganancias_factura = mysqli_query($conection,"SELECT SUM(comprobante_factura_final.subtotal) as 'subtotal',SUM(comprobante_factura_final.iva) as 'iva',SUM(comprobante_factura_final.total) as 'total'
                       FROM comprobante_factura_final WHERE comprobante_factura_final.id_emisor = '$iduser' AND estado = 'COMPLETADO'");
                       $result_ganancia_factura = mysqli_fetch_array($query_ganancias_factura);
                       $subtotal_factura  = $result_ganancia_factura['subtotal'];
                       $iva_factura       = $result_ganancia_factura['iva'];
                       $total_factura     = $result_ganancia_factura['total'];


                       $query_ganancias_factura_anulado = mysqli_query($conection,"SELECT SUM(comprobante_factura_final.subtotal) as 'subtotal',SUM(comprobante_factura_final.iva) as 'iva',SUM(comprobante_factura_final.total) as 'total'
                       FROM comprobante_factura_final WHERE comprobante_factura_final.id_emisor = '$iduser' AND estado = 'ANULADO'");
                       $result_ganancia_factura_anulado = mysqli_fetch_array($query_ganancias_factura_anulado);
                       $subtotal_factura_anulado  = $result_ganancia_factura_anulado['subtotal'];
                       $iva_factura_anulado       = $result_ganancia_factura_anulado['iva'];
                       $total_factura_anulado     = $result_ganancia_factura_anulado['total'];
                        ?>

                       <table>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Subtotal Base Imponible COMPLETADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_factura,) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Impuesto Iva Completado</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($iva_factura,2) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Subtotal  Base Imponible ANULADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_factura_anulado,) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Impuesto Iva ANULADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($iva_factura_anulado,2) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Ganancias Generales</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($total_factura-$total_factura_anulado,2) ?></td>
                         </tr>
                       </table>

                     </div>
                 </div>
             </div>
         </div>
     </main>
      <?php endif; ?>


      <?php if ($facturas_recibidas == 'on'): ?>
      <main role="main" class="container">
       <h5>Facturas Recibidas <?php echo $facturas_emitidas ?> Fecha Inicio <?php echo $fecha_inicio ?> - Fecha Final <?php echo $fecha_final ?> </h5>
         <div class="row">
             <div class="col-12">
                 <div class="table-responsive">
                     <table class="table table-bordered">
                         <thead>
                             <tr>
                                 <th>RUC EMISOR</th>
                                 <th>RAZON SOCIAL EMISOR</th>
                                 <th>SERIE COMPROBANTE</th>
                                 <th>CLAVE ACCESO</th>
                                 <th>FECHA EMISIÓN</th>
                                 <th>IDENTIFICACION RECEPTOR</th>
                                 <th>VALOR SIN IMPUESTOS</th>
                                 <th>IVA</th>
                                 <th>IMPORTE TOTAL</th>
                             </tr>
                         </thead>
                         <tbody>
                           <?php

                           $query_lista_enviado = mysqli_query($conection," SELECT *
                             FROM datos_documentos_recibidos_txt
                             INNER JOIN usuarios_empresariales ON usuarios_empresariales.id = datos_documentos_recibidos_txt.usuario
                             WHERE STR_TO_DATE(datos_documentos_recibidos_txt.FECHA_EMISION, '%d/%m/%Y')
                             BETWEEN STR_TO_DATE('$fecha_inicio', '%Y-%m-%d') AND STR_TO_DATE('$fecha_final', '%Y-%m-%d')
                             AND  datos_documentos_recibidos_txt.usuario = '$codigo_usuario'
                             AND datos_documentos_recibidos_txt.proceso = 'Recibido'
                             AND datos_documentos_recibidos_txt.TIPO_COMPROBANTE = 'Factura'
                             ORDER BY `datos_documentos_recibidos_txt`.`fecha` desc");

                       $result_lista_enviado= mysqli_num_rows($query_lista_enviado);
                     if ($result_lista_enviado > 0) {
                           while ($data_lista_enviado=mysqli_fetch_array($query_lista_enviado)) {
                            ?>
                             <tr>
                                <td><?php echo $data_lista_enviado['RUC_EMISOR']; ?> </td>
                                <td><?php echo $data_lista_enviado['RAZON_SOCIAL_EMISOR']; ?> </td>
                                <td><?php echo $data_lista_enviado['SERIE_COMPROBANTE']; ?> </td>
                                <td><?php echo $data_lista_enviado['CLAVE_ACCESO']; ?> </td>
                                <td><?php echo $data_lista_enviado['FECHA_EMISION']; ?> </td>
                                <td><?php echo $data_lista_enviado['IDENTIFICACION_RECEPTOR']; ?> </td>
                                <td><?php echo $data_lista_enviado['VALOR_SIN_IMPUESTOS']; ?></td>
                                <td>$<?php echo $data_lista_enviado['IVA']; ?></td>
                                <td>$<?php echo $data_lista_enviado['IMPORTE_TOTAL']; ?></td>
                             </tr>
                             <?php
                             }
                             }
                         ?>
                         </tbody>
                     </table>
                     <style media="screen">
                     .tabla_resumen_tr{
                       width: 100px;
                       height: 60px
                       padding: 5px;
                     }
                     .resumen_ganancias_dia{
                       padding: 5px;
                       margin: 3px;
                     }

                     </style>
                     <div class="resumen_ganancias_dia">
                       <?php

                       $fecha_inicio = date('Y-m-d H:i:s', strtotime('midnight'));
                       $fecha_fin = date('Y-m-d H:i:s', strtotime('tomorrow midnight'));

                       $query_ganancias_factura = mysqli_query($conection,"SELECT SUM(comprobante_factura_final.subtotal) as 'subtotal',SUM(comprobante_factura_final.iva) as 'iva',SUM(comprobante_factura_final.total) as 'total'
                       FROM comprobante_factura_final WHERE comprobante_factura_final.id_emisor = '$iduser' AND estado = 'COMPLETADO'");
                       $result_ganancia_factura = mysqli_fetch_array($query_ganancias_factura);
                       $subtotal_factura  = $result_ganancia_factura['subtotal'];
                       $iva_factura       = $result_ganancia_factura['iva'];
                       $total_factura     = $result_ganancia_factura['total'];


                       $query_ganancias_factura_anulado = mysqli_query($conection,"SELECT SUM(comprobante_factura_final.subtotal) as 'subtotal',SUM(comprobante_factura_final.iva) as 'iva',SUM(comprobante_factura_final.total) as 'total'
                       FROM comprobante_factura_final WHERE comprobante_factura_final.id_emisor = '$iduser' AND estado = 'ANULADO'");
                       $result_ganancia_factura_anulado = mysqli_fetch_array($query_ganancias_factura_anulado);
                       $subtotal_factura_anulado  = $result_ganancia_factura_anulado['subtotal'];
                       $iva_factura_anulado       = $result_ganancia_factura_anulado['iva'];
                       $total_factura_anulado     = $result_ganancia_factura_anulado['total'];
                        ?>

                       <table>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Subtotal Base Imponible COMPLETADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_factura,) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Impuesto Iva Completado</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($iva_factura,2) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Subtotal  Base Imponible ANULADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_factura_anulado,) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Impuesto Iva ANULADO</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($iva_factura_anulado,2) ?></td>
                         </tr>
                         <tr class="tabla_resumen_tr">
                           <td class="tabla_resumen_tr">Ganancias Generales</td>
                           <td class="tabla_resumen_tr">$<?php echo number_format($total_factura-$total_factura_anulado,2) ?></td>
                         </tr>
                       </table>

                     </div>
                 </div>
             </div>
         </div>
      </main>
      <?php endif; ?>




   </body>
 </html>
