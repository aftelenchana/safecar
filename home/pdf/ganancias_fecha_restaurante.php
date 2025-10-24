<?php

include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
      $iduser = $_GET['iduser'];

        $fecha1 = $_GET['fecha1'];
        $fecha2 = $_GET['fecha2'];

 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ganancias Fecha </title>
  </head>
  <style media="screen">
  .img_alumno{
    text-align: center;
  }
   .contenedor_informacion_alumno{
     padding: 10px;
     margin: 10px;
   }
  .contenedor_accion_descarga{
    text-align: center;
    padding: 15px;
    margin: 10px;
  }

  .negrita_fila{
    font-weight: bold;
  }
  .primer_bloque_fila{
    background:  #ebf2bd ;
  }

  .segundo_bloque_fila{
    background:  #c1fa9f ;
  }
  .contenedor_informacion_paciente table{
    margin: 0 auto;
  }
  .contenedor_informacion_paciente td{
    width: 310px;
    padding: 10px;
  }
  .cabezera{
    background: #263238;
    color: #fff;
    width: 800PX;
    height: 70px;
    margin-top: -60PX;
    text-align: center;
    margin-left: -48px;


  }
.cabezera h4{
  margin: auto;
  padding: 15px;

}
  </style>
  <body>
    <?php
$query = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
$result=mysqli_fetch_array($query);
$nombres           = $result['nombres'];
$firma_electronica = $result['firma_electronica'];
$direccion         = $result['direccion'];
$codigo_sri        = $result['codigo_sri'];
$estableciminento        = $result['estableciminento_f'];
$punto_emision        = $result['punto_emision_f'];
$porcentaje_iva       = $result['porcentaje_iva_f'];
$apellidos         = $result['apellidos'];
$img_facturacion          = $result['img_facturacion'];
  $url_img_upload  = $result['url_img_upload'];
$contabilidad         = $result['contabilidad'];
$regimen          = $result['regimen'];
$nombre_empresa          = $result['nombre_empresa'];
     ?>
   <div class="cabezera">
     <br>
     <h4>GANANCIAS POR FECHA  DE  <?php echo $nombre_empresa ?> ENTRE <?php echo $fecha1 ?> Y <?php echo $fecha2 ?></h4>
   </div>
   <style media="screen">
     .imagen_empresa{
       text-align: center;
     }
     .imagen_empresa img{
       width: 200px;
     }
   </style>

   <div class="imagen_empresa">
     <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
   </div>

   <?php


   $query_ganancias_factura = mysqli_query($conection,"SELECT SUM(comprobante_factura_final.subtotal) as 'subtotal',SUM(comprobante_factura_final.iva) as 'iva',SUM(comprobante_factura_final.total) as 'total'
   FROM comprobante_factura_final
   WHERE comprobante_factura_final.fecha BETWEEN '$fecha1' AND '$fecha2'  AND comprobante_factura_final.id_emisor = '$iduser'");
   $result_ganancia_factura = mysqli_fetch_array($query_ganancias_factura);
   $subtotal_factura  = $result_ganancia_factura['subtotal'];
   $iva_factura       = $result_ganancia_factura['iva'];
   $total_factura     = $result_ganancia_factura['total'];

   $query_ganancias_tiket = mysqli_query($conection,"SELECT SUM(tikets.subtotal) as 'subtotal',SUM(tikets.iva) as 'iva',SUM(tikets.total) as 'total'
   FROM tikets
   WHERE tikets.fecha BETWEEN '$fecha1' AND '$fecha2'
    AND tikets.id_emisor = '$iduser'");
   $result_ganancia_tiket = mysqli_fetch_array($query_ganancias_tiket);
   $subtotal_tiket  = $result_ganancia_tiket['subtotal'];
   $iva_tiket       = $result_ganancia_tiket['iva'];
   $total_tiket     = $result_ganancia_tiket['total'];

    ?>
    <style media="screen">
      .informacion_usuario{
        text-align: center;
        font-size: 18px;
        font-weight: bold;
      }
      .bd_jhg{
        font-size: 20px;
        color: #263238;

      }
    </style>

   <div class="informacion_usuario">
     <h5>Ganancias Generales  de <span class="bd_jhg">$<?php echo number_format(($total_factura+$total_tiket ),2)?></span> entre <?php echo $fecha1 ?> Y <?php echo $fecha2 ?> </h5>
   </div>
<style media="screen">
.table-responsive tr,td,th  {
border: solid 1px #c1c1c1;
text-align: center;

}

.table-responsive th{
width: 95px;
  font-size: 11px;
}
.table-responsive td{
width: 95px;
  font-size: 11px;
}

.table-responsive table{
   border-collapse: collapse;
   margin: 0 auto;
     font-size: 11px;
}
</style>
   <main role="main" class="container">
     <h5>Facturación electrónica </h5>
       <div class="row">
           <div class="col-12">
               <div class="table-responsive">
                   <table class="table table-bordered">
                       <thead>
                           <tr>
                               <th>Código</th>
                               <th>Descripción</th>
                               <th>Comprador</th>
                               <th>Subtotal</th>
                               <th>Descuento</th>
                               <th>Iva</th>
                               <th>Total</th>
                           </tr>
                       </thead>
                       <tbody>
                         <?php
                         mysqli_query($conection,"SET lc_time_names = 'es_ES'");
                         $query_lista = mysqli_query($conection,"SELECT comprobante_factura_final.fecha,comprobante_factura_final.codigo_factura,comprobante_factura_final.nombres_receptor,
                           comprobante_factura_final.cedula_receptor,comprobante_factura_final.email_receptor,comprobante_factura_final.clave_acceso,comprobante_factura_final.descripcion,
                           comprobante_factura_final.id_producto,comprobante_factura_final.total,comprobante_factura_final.id,comprobante_factura_final.estado,
                           comprobante_factura_final.precio_neto,comprobante_factura_final.subtotal,comprobante_factura_final.iva,comprobante_factura_final.total,comprobante_factura_final.descuento_general
                            FROM `comprobante_factura_final`
                   WHERE comprobante_factura_final.fecha BETWEEN '$fecha1' AND '$fecha2'
                  AND  comprobante_factura_final.id_emisor  = '$iduser' AND  (comprobante_factura_final.secuencia != '00000000' || comprobante_factura_final.secuencia IS NOT NULL) AND comprobante ='factura'
                 GROUP BY comprobante_factura_final.codigo_factura
                     ORDER BY `comprobante_factura_final`.`fecha` DESC");
                     $result_lista= mysqli_num_rows($query_lista);
                   if ($result_lista > 0) {
                         while ($data_lista=mysqli_fetch_array($query_lista)) {
                           $descuento = $data_lista['descuento_general'];
                           if ($descuento == '') {
                             $descuento = 0;
                             // code...
                           }
                          ?>
                           <tr>
                                <td><?php echo $data_lista['id']; ?></td>
                               <td><?php echo $data_lista['descripcion']; ?> </td>
                               <td><?php echo $data_lista['nombres_receptor']; ?></td>
                               <td>$<?php echo $data_lista['subtotal']; ?></td>
                               <td>$<?php echo $descuento ?></td>
                               <td>$<?php echo $data_lista['iva']; ?></td>
                               <td>$<?php echo $data_lista['total']; ?></td>
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
                     FROM comprobante_factura_final
                      WHERE comprobante_factura_final.fecha BETWEEN '$fecha1' AND '$fecha2'
                      AND comprobante_factura_final.id_emisor = '$iduser'");
                     $result_ganancia_factura = mysqli_fetch_array($query_ganancias_factura);
                     $subtotal_factura  = $result_ganancia_factura['subtotal'];
                     $iva_factura       = $result_ganancia_factura['iva'];
                     $total_factura     = $result_ganancia_factura['total'];

                      ?>

                     <table>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Subtotal</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_factura,) ?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Iva</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($iva_factura,2) ?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Ganancias Generales</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($total_factura,2) ?></td>
                       </tr>
                     </table>

                   </div>
               </div>
           </div>
       </div>
   </main>


      <style type="text/css">


          .page-break {
              page-break-before: always;
              margin-top: 0;
              padding-top: 0;
          }

          .titulo_consiguiente {
              margin-top: 0;
              padding-top: 0;
          }

      </style>




   <div class="page-break"></div>

   <div class="cabezera titulo_conisguiente">
     <br>
     <h4>GANANCIAS GENERALES  DE  <?php echo $nombre_empresa ?> <?php echo $fechaActual ?> </h4>
   </div>




      <main role="main" class="container">
        <h5>Nota de Venta Autorizada</h5>
          <div class="row">
              <div class="col-12">
                  <div class="table-responsive">
                      <table class="table table-bordered">
                          <thead>
                              <tr>
                                <th>Descripción</th>
                                <th>Comprador</th>
                                <th>Subtotal</th>
                                <th>Iva</th>
                                <th>Total</th>
                              </tr>
                          </thead>
                          <tbody>
                            <?php
                            $query_lista = mysqli_query($conection,"SELECT * FROM `nota_venta_autorizada`
                              WHERE nota_venta_autorizada.fecha BETWEEN '$fecha1' AND '$fecha2'
                               and nota_venta_autorizada.id_emisor  = '$iduser' ORDER BY `nota_venta_autorizada`.`fecha`  DESC");
                                $result_lista= mysqli_num_rows($query_lista);
                              if ($result_lista > 0) {
                                    while ($data_lista=mysqli_fetch_array($query_lista)) {
                             ?>
                              <tr>
                                <td><?php echo $data_lista['descripcion']; ?> </td>
                                <td><?php echo $data_lista['nombres_receptor']; ?></td>
                                <td>$<?php echo $data_lista['subtotal']; ?></td>
                                <td>$<?php echo $data_lista['iva']; ?></td>
                                <td>$<?php echo $data_lista['total']; ?></td>
                              </tr>
                              <?php
                              }
                              }
                          ?>

                          </tbody>
                      </table>
                      <div class="resumen_ganancias_dia">
                        <?php

                        $fecha_inicio = date('Y-m-d H:i:s', strtotime('midnight'));
                        $fecha_fin = date('Y-m-d H:i:s', strtotime('tomorrow midnight'));

                        $query_ganancias_tiket = mysqli_query($conection,"SELECT SUM(nota_venta_autorizada.subtotal) as 'subtotal',SUM(nota_venta_autorizada.iva) as 'iva',SUM(nota_venta_autorizada.total) as 'total'
                        FROM nota_venta_autorizada
                         WHERE nota_venta_autorizada.fecha BETWEEN '$fecha1' AND '$fecha2'
                       AND  nota_venta_autorizada.id_emisor = '$iduser'");
                        $result_ganancia_tiket = mysqli_fetch_array($query_ganancias_tiket);
                        $subtotal_tiket  = $result_ganancia_tiket['subtotal'];
                        $iva_tiket       = $result_ganancia_tiket['iva'];
                        $total_tiket     = $result_ganancia_tiket['total'];

                         ?>

                        <table>
                          <tr class="tabla_resumen_tr">
                            <td class="tabla_resumen_tr">Subtotal</td>
                            <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_tiket,) ?></td>
                          </tr>
                          <tr class="tabla_resumen_tr">
                            <td class="tabla_resumen_tr">Iva</td>
                            <td class="tabla_resumen_tr">$<?php echo number_format($iva_tiket,2) ?></td>
                          </tr>
                          <tr class="tabla_resumen_tr">
                            <td class="tabla_resumen_tr">Ganancias Generales</td>
                            <td class="tabla_resumen_tr">$<?php echo number_format($total_tiket,2) ?></td>
                          </tr>
                        </table>

                      </div>
                  </div>
              </div>
          </div>
      </main>


         <style type="text/css">


             .page-break {
                 page-break-before: always;
                 margin-top: 0;
                 padding-top: 0;
             }

             .titulo_consiguiente {
                 margin-top: 0;
                 padding-top: 0;
             }

         </style>




      <div class="page-break"></div>

      <div class="cabezera titulo_conisguiente">
        <br>
        <h4>GANANCIAS GENERALES  DE  <?php echo $nombre_empresa ?> <?php echo $fechaActual ?> </h4>
      </div>

   <main role="main" class="container">
     <h5>Tiekts de Venta </h5>
       <div class="row">
           <div class="col-12">
               <div class="table-responsive">
                   <table class="table table-bordered">
                       <thead>
                           <tr>
                             <th>Descripción</th>
                             <th>Comprador</th>
                             <th>Subtotal</th>
                             <th>Iva</th>
                             <th>Total</th>
                           </tr>
                       </thead>
                       <tbody>
                         <?php
                         $query_lista = mysqli_query($conection,"SELECT * FROM `tikets`
                           WHERE tikets.fecha BETWEEN '$fecha1' AND '$fecha2'
                            and tikets.id_emisor  = '$iduser' ORDER BY `tikets`.`fecha`  DESC");
                             $result_lista= mysqli_num_rows($query_lista);
                           if ($result_lista > 0) {
                                 while ($data_lista=mysqli_fetch_array($query_lista)) {
                          ?>
                           <tr>
                             <td><?php echo $data_lista['descripcion']; ?> </td>
                             <td><?php echo $data_lista['nombres_receptor']; ?></td>
                             <td>$<?php echo $data_lista['subtotal']; ?></td>
                             <td>$<?php echo $data_lista['iva']; ?></td>
                             <td>$<?php echo $data_lista['total']; ?></td>
                           </tr>
                           <?php
                           }
                           }
                       ?>

                       </tbody>
                   </table>
                   <div class="resumen_ganancias_dia">
                     <?php

                     $fecha_inicio = date('Y-m-d H:i:s', strtotime('midnight'));
                     $fecha_fin = date('Y-m-d H:i:s', strtotime('tomorrow midnight'));

                     $query_ganancias_tiket = mysqli_query($conection,"SELECT SUM(tikets.subtotal) as 'subtotal',SUM(tikets.iva) as 'iva',SUM(tikets.total) as 'total'
                     FROM tikets
                      WHERE tikets.fecha BETWEEN '$fecha1' AND '$fecha2'
                    AND  tikets.id_emisor = '$iduser'");
                     $result_ganancia_tiket = mysqli_fetch_array($query_ganancias_tiket);
                     $subtotal_tiket  = $result_ganancia_tiket['subtotal'];
                     $iva_tiket       = $result_ganancia_tiket['iva'];
                     $total_tiket     = $result_ganancia_tiket['total'];

                      ?>

                     <table>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Subtotal</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($subtotal_tiket,) ?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Iva</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($iva_tiket,2) ?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Ganancias Generales</td>
                         <td class="tabla_resumen_tr">$<?php echo number_format($total_tiket,2) ?></td>
                       </tr>
                     </table>

                   </div>
               </div>
           </div>
       </div>
   </main>



  </body>
</html>
