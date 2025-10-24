<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$asiento = $_GET['codigo'];
$iduser = $_GET['iduser'];



//INFORMACION PARA EL ASIENTO CONTABLE

$query_asiento_contable = mysqli_query($conection, "SELECT * FROM contabilidad_asientos_contables
   WHERE contabilidad_asientos_contables.iduser ='$iduser'  AND contabilidad_asientos_contables.estatus = '1' AND contabilidad_asientos_contables.id = '$asiento' ");
$data_asiento_contable  = mysqli_fetch_array($query_asiento_contable);




$query = mysqli_query($conection, "SELECT SUM(debito) AS total_debito, SUM(credito) AS total_credito FROM contabilidad_datos_asiento_contable WHERE asiento = '$asiento' AND estatus = 1 AND iduser = '$iduser'");

if ($query && mysqli_num_rows($query) > 0) {
    $result = mysqli_fetch_assoc($query);
    $total_debito = $result['total_debito'];
    $total_credito = $result['total_credito'];
    $descuadre = abs($total_debito - $total_credito);
    $monto_asiento = max($total_debito, $total_credito);
} else {
    $total_debito = 0;
    $total_credito = 0;
    $descuadre = 0;
    $monto_asiento = 0;
}



//INFORMACION DENTRO DEL ASIENTO CONTABLE

$query_consulta = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id,contabilidad_plan_cuentas.nombre_cuenta,
  contabilidad_plan_cuentas.nivel_1,contabilidad_plan_cuentas.nivel_2,contabilidad_plan_cuentas.nivel_3,contabilidad_plan_cuentas.nivel_4,
  contabilidad_plan_cuentas.nivel_5,contabilidad_plan_cuentas.nivel_6,contabilidad_plan_cuentas.codigo,contabilidad_datos_asiento_contable.concepto,
  contabilidad_datos_asiento_contable.debito,contabilidad_datos_asiento_contable.credito
   FROM contabilidad_datos_asiento_contable
  INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
   WHERE contabilidad_datos_asiento_contable.iduser ='$iduser' AND contabilidad_datos_asiento_contable.estatus = '1' AND
   contabilidad_datos_asiento_contable.asiento ='$asiento'
   ORDER BY contabilidad_datos_asiento_contable.fecha DESC   ");


   //INFORMACION DEL USUARIO

   //CODIGO PARA SACAR INFORMACION DEL USUARIO
   $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
   $result_documentos = mysqli_fetch_array($query_doccumentos);
   $email_empresa_emisor  = $result_documentos['email'];
   $celular_empresa_emisor  = $result_documentos['celular'];
   $telefono_empresa_emisor   = $result_documentos['telefono'];
   $nombre_empresa   = $result_documentos['nombre_empresa'];
   $regimen   = $result_documentos['regimen'];
   $contabilidad   = $result_documentos['contabilidad'];
   $url_img_upload   = $result_documentos['url_img_upload'];
   $img_facturacion   = $result_documentos['img_facturacion'];
   $id_e   = $result_documentos['id_e'];

   $facebook   = $result_documentos['facebook'];
   $instagram   = $result_documentos['instagram'];
   $whatsapp   = $result_documentos['whatsapp'];
   $pagina_web   = $result_documentos['pagina_web'];


   $fecha_actual = date("d-m-Y H:i:s");


$data_consulta = mysqli_fetch_array($query_consulta);
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Asiento Contable</title>
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
  margin-top: 10px;
}

.informacion_titulos_cabezera h5{
  padding: 0px;
  margin: 0px;
  color: #000;
  font-weight: bold;
}
    </style>




    <div class="cabezera">
      <br>
      <div class="bloque_gene">
        <br>
          <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
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
      <p><?php echo $data_asiento_contable['tipo'] ?> - <?php echo $data_asiento_contable['referencia'] ?></p>
    </div>

   <style media="screen">
   .informacion_comprobante{
     font-size: 13px;
   }

   .negrita{
     font-weight: bold;
   }

   .bloque_gene_comproban{
     width: 50%;
     display: inline-block;
     vertical-align: top; /* Añadir alineación vertical */
   }

   /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
   .bloque_gene_comproban table {
     width: 100%;
   }

   </style>


   <?php if ($data_asiento_contable['tipo'] == 'COMPROBANTE DE COMPRA'): ?>
     <?php

     $funcion =$data_asiento_contable['funcion'];

     $codigo_compra = $data_asiento_contable['codigo_compra'];

     switch ($funcion) {
       case 'ingreso_activo':

       $fecha_emision_factura = $data_asiento_contable['fecha'];
         // code...
         break;

      case 'facturacion':
      $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM compras_facturacion WHERE compras_facturacion.id ='$codigo_compra' ");
      $data_existencia       = mysqli_fetch_array($query_comprobacion_existencia);
      $fecha_emision_factura = $data_existencia['fecha_emision_factura'];

        // code...
        break;

       default:
       $fecha_emision_factura = '';
         // code...
         break;
     }




      ?>


         <div class="informacion_comprobante">
           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Fecha de Asiento</td>
                 <td><?php echo $data_asiento_contable['fecha_asiento'] ?></td>
               </tr>
               <tr>
                 <td class="negrita">Fecha Documento</td>
                 <td><?php echo $fecha_emision_factura?></td>
               </tr>
               <tr>
                 <td class="negrita">Descripción</td>
                 <td><?php echo $data_asiento_contable['descripcion'] ?></td>
               </tr>
             </table>
           </div>

           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Estado Asiento</td>
                 <td><?php echo $data_asiento_contable['estado'] ?></td>
               </tr>
               <tr>
                   <td class="negrita">Proveedor</td>
                   <td><?php echo $data_asiento_contable['razon_social_vendedor'] ?></td>
               </tr>
             </table>
           </div>
         </div>


         <style media="screen">
         .subtitulo{
           font-size: 13px;
           padding: 0px;
           margin: 0px;
         }

         </style>

         <div class="subtitulo">
           <h4>Detalle del Asiento</h4>
         </div>

         <style media="screen">
           .parte_productos{
             padding: 1px;
             font-size: 11px;
             border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
             border-radius: 8px;
           }

           .parte_productos .parte_productos_solo_productos{
             width: 100%;
             padding: 0px;
             margin: 0px;
           }

           .parte_productos .parte_productos_solo_productos {
             border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
         }

         .texto_asiento{
           text-align: left;
         }
         .valores_asiento{
           text-align: right;
         }

         .negrita{
           font-weight: bold;
           font-size: 12px;
         }


         </style>

         <div class="parte_productos">
           <div class="">
             <table class="parte_productos_solo_productos">
               <thead>
                 <tr>
                   <th class="th_productos texto_asiento">Cuenta</th>
                   <th class="th_productos texto_asiento">Descripción</th>
                   <th class="th_productos texto_asiento">Concepto</th>
                   <th class="th_productos valores_asiento">Debe</th>
                   <th class="th_productos valores_asiento">Haber</th>
                 </tr>
               </thead>
               <tbody>

         <?php

         $query_consulta = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id,contabilidad_plan_cuentas.nombre_cuenta,
           contabilidad_plan_cuentas.nivel_1,contabilidad_plan_cuentas.nivel_2,contabilidad_plan_cuentas.nivel_3,contabilidad_plan_cuentas.nivel_4,
           contabilidad_plan_cuentas.nivel_5,contabilidad_plan_cuentas.nivel_6,contabilidad_plan_cuentas.id as 'codigo_plan_cuentas',contabilidad_datos_asiento_contable.concepto,
           contabilidad_datos_asiento_contable.debito,contabilidad_datos_asiento_contable.credito
            FROM contabilidad_datos_asiento_contable
           INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
            WHERE contabilidad_datos_asiento_contable.iduser ='$iduser' AND contabilidad_datos_asiento_contable.estatus = '1' AND
            contabilidad_datos_asiento_contable.asiento ='$asiento'
            ORDER BY contabilidad_plan_cuentas.nivel_6, contabilidad_plan_cuentas.nivel_5, contabilidad_plan_cuentas.nivel_4, contabilidad_plan_cuentas.nivel_3, contabilidad_plan_cuentas.nivel_2, contabilidad_plan_cuentas.nivel_1   ");



      while ($data_datos = mysqli_fetch_assoc($query_consulta)) {

        $plan_cuentas = $data_datos['codigo_plan_cuentas'];

        $query_consulta_jerarquia = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
                                      WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
                                      AND contabilidad_plan_cuentas.id = '$plan_cuentas'

                                      ORDER BY nivel_6, nivel_5, nivel_4, nivel_3, nivel_2, nivel_1 ");
        $data = array();
        while ($row = mysqli_fetch_assoc($query_consulta_jerarquia)) {
            $data[] = $row;
        }
        foreach ($data as $row) {
            $jerarquia = '';
            $nivel_maximo = 0;
            for ($i = 1; $i <= 6; $i++) {
                if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                    $jerarquia .= $row["nivel_{$i}"];
                    $nivel_maximo = $i;
                    if ($i < 6) {
                        $jerarquia .= '.';
                    }
                }
            }
            $tiene_hijos = false;
            if ($nivel_maximo < 6) {
                $query_hijos = "SELECT * FROM contabilidad_plan_cuentas
                                        WHERE iduser ='$iduser' AND estatus = '1' AND (";
                for ($i = 1; $i <= $nivel_maximo; $i++) {
                    $query_hijos .= "nivel_$i = '{$row["nivel_$i"]}' AND ";
                }
                $query_hijos .= "nivel_" . ($nivel_maximo + 1) . " <> '0')";

                $resultado_hijos = mysqli_query($conection, $query_hijos);
                $tiene_hijos = mysqli_num_rows($resultado_hijos) > 0;
            }

            if (!$tiene_hijos) {
                $opcionValor = $row['id'];


                $nombre_cuenta = $row['nombre_cuenta'];

                if ($nombre_cuenta == 'cuentas_bancarias_factu') {

                  $codigo_columna = $row['codigo_columna'];

                  $query_cuenta_bancaria = mysqli_query($conection, "SELECT * FROM cuentas_bancarias_factu
                     WHERE cuentas_bancarias_factu.iduser ='$iduser'  AND cuentas_bancarias_factu.estatus = '1' AND cuentas_bancarias_factu.id = '$codigo_columna' ");
                  $data_cuenta_bancaria = mysqli_fetch_array($query_cuenta_bancaria);

                  $result_lista= mysqli_num_rows($query_cuenta_bancaria);

                  if ($result_lista > 0) {
                    $nombre_cuenta_bancaria  = $data_cuenta_bancaria['nombre_cuenta'];
                    $tipo_cuenta  = $data_cuenta_bancaria['tipo_cuenta'];
                    $numero_cuenta  = $data_cuenta_bancaria['numero_cuenta'];
                    $titular_cuenta  = $data_cuenta_bancaria['titular_cuenta'];
                    $saldo_inicial  = $data_cuenta_bancaria['saldo_inicial'];
                    $row['nombre_cuenta'] = $nombre_cuenta_bancaria.'-'.$numero_cuenta.'-'.$tipo_cuenta;
                    // code...
                  }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                  }

                }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                }

                $nombre_cuenta = $row['nombre_cuenta'];
                $opcionTexto = rtrim($jerarquia, '.'); // Quitamos el último punto.

            }


        }


           ?>
             <tr>
               <td class="td_productos"><?php echo $opcionTexto ?></td>
               <td class="td_productos"><?php echo $nombre_cuenta ?></td>
               <td class="td_productos"><?php echo $data_datos['concepto'] ?></td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['debito'] > 0 ? '$' . number_format($data_datos['debito'], 2) : ''; ?>
               </td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['credito'] > 0 ? '$' . number_format($data_datos['credito'], 2) : ''; ?>
               </td>



             </tr>

          <?php

           }
           ?>

           <tr>
             <td class="td_productos"></td>
             <td class="td_productos"></td>
             <td class="td_productos negrita">TOTALES</td>
             <td class="td_productos valores_asiento negrita">
                 $<?php echo round($total_debito,2) ?>
             </td>
             <td class="td_productos valores_asiento negrita">
               $$<?php echo round($total_credito,2) ?>
             </td>
           </tr>
         </tbody>
       </table>
     </div>
     </div>
     <br>


     <style media="screen">

     .cont_formas_pago{
       border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
       border-radius: 8px;

     }
     .formas_pago_table  {
       border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
       text-align: center;
       font-size: 12px;
       width: 100%;

     }


     </style>

     <div class="cont_formas_pago">

              <?php

              $query_lista = mysqli_query($conection," SELECT *
                 FROM formas_pago_compras_contabilidad
                WHERE formas_pago_compras_contabilidad.compra = '$codigo_compra'
                  AND formas_pago_compras_contabilidad.estatus = '1'
            ORDER BY `formas_pago_compras_contabilidad`.`fecha` desc");
                  $result_lista= mysqli_num_rows($query_lista);
                if ($result_lista > 0) {
                      while ($data_lista=mysqli_fetch_array($query_lista)) {
               ?>

               <table class="formas_pago_table">

                   <thead>
                     <tr>
                       <th class="th_productos">Forma de Pago</th>
                       <th class="th_productos">Cantidad</th>
                     </tr>
                   </thead>
                   <tbody>
                       <tr>
                         <td class="td_productos"><?php echo htmlspecialchars($data_lista['nombre_formas_pago']); ?></td>
                         <td class="td_productos">$<?php echo number_format($data_lista['total'], 2); ?></td>
                       </tr>

                   </tbody>

               </table>


               <?php
             }
             }

                ?>

     </div>
   <?php endif; ?>





   <?php if ($data_asiento_contable['tipo'] == 'COMPROBANTE DE VENTA'): ?>
     <?php

     $codigo_venta = $data_asiento_contable['codigo_venta'];

     $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM ventas_documentos WHERE ventas_documentos.id ='$codigo_venta' ");
     $data_existencia       = mysqli_fetch_array($query_comprobacion_existencia);
     $fecha_emision_factura = $data_existencia['fecha_emision_factura'];
      ?>
         <div class="informacion_comprobante">
           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Fecha de Asiento</td>
                 <td><?php echo $data_asiento_contable['fecha_asiento'] ?></td>
               </tr>
               <tr>
                 <td class="negrita">Fecha Documento</td>
                 <td><?php echo $fecha_emision_factura?></td>
               </tr>
               <tr>
                 <td class="negrita">Descripción</td>
                 <td><?php echo $data_asiento_contable['descripcion'] ?></td>
               </tr>
             </table>
           </div>

           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Estado Asiento</td>
                 <td><?php echo $data_asiento_contable['estado'] ?></td>
               </tr>
               <tr>
                   <td class="negrita">Cliente</td>
                   <td><?php echo $data_asiento_contable['razon_social_comprador'] ?></td>
               </tr>

               <tr>
                   <td class="negrita">codInt</td>
                   <td><?php echo $data_asiento_contable['id'] ?></td>
               </tr>
             </table>
           </div>
         </div>


         <style media="screen">
         .subtitulo{
           font-size: 13px;
           padding: 0px;
           margin: 0px;
         }

         </style>

         <div class="subtitulo">
           <h4>Detalle del Asiento</h4>
         </div>

         <style media="screen">
           .parte_productos{
             padding: 1px;
             font-size: 11px;
             border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
             border-radius: 8px;
           }

           .parte_productos .parte_productos_solo_productos{
             width: 100%;
             padding: 0px;
             margin: 0px;
           }

           .parte_productos .parte_productos_solo_productos {
             border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
         }

         .texto_asiento{
           text-align: left;
         }
         .valores_asiento{
           text-align: right;
         }

         .negrita{
           font-weight: bold;
           font-size: 12px;
         }


         </style>

         <div class="parte_productos">
           <div class="">
             <table class="parte_productos_solo_productos">
               <thead>
                 <tr>
                   <th class="th_productos texto_asiento">Cuenta</th>
                   <th class="th_productos texto_asiento">Descripción</th>
                   <th class="th_productos texto_asiento">Concepto</th>
                   <th class="th_productos valores_asiento">Debe</th>
                   <th class="th_productos valores_asiento">Haber</th>
                 </tr>
               </thead>
               <tbody>

         <?php

         $query_consulta = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id,contabilidad_plan_cuentas.nombre_cuenta,
           contabilidad_plan_cuentas.nivel_1,contabilidad_plan_cuentas.nivel_2,contabilidad_plan_cuentas.nivel_3,contabilidad_plan_cuentas.nivel_4,
           contabilidad_plan_cuentas.nivel_5,contabilidad_plan_cuentas.nivel_6,contabilidad_plan_cuentas.id as 'codigo_plan_cuentas',contabilidad_datos_asiento_contable.concepto,
           contabilidad_datos_asiento_contable.debito,contabilidad_datos_asiento_contable.credito
            FROM contabilidad_datos_asiento_contable
           INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
            WHERE contabilidad_datos_asiento_contable.iduser ='$iduser' AND contabilidad_datos_asiento_contable.estatus = '1' AND
            contabilidad_datos_asiento_contable.asiento ='$asiento'
            ORDER BY contabilidad_plan_cuentas.nivel_6, contabilidad_plan_cuentas.nivel_5, contabilidad_plan_cuentas.nivel_4, contabilidad_plan_cuentas.nivel_3, contabilidad_plan_cuentas.nivel_2, contabilidad_plan_cuentas.nivel_1   ");



      while ($data_datos = mysqli_fetch_assoc($query_consulta)) {

        $plan_cuentas = $data_datos['codigo_plan_cuentas'];

        $query_consulta_jerarquia = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
                                      WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
                                      AND contabilidad_plan_cuentas.id = '$plan_cuentas'
                                      ORDER BY nivel_6, nivel_5, nivel_4, nivel_3, nivel_2, nivel_1 ");
        $data = array();
        while ($row = mysqli_fetch_assoc($query_consulta_jerarquia)) {
            $data[] = $row;
        }
        foreach ($data as $row) {
            $jerarquia = '';
            $nivel_maximo = 0;
            for ($i = 1; $i <= 6; $i++) {
                if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                    $jerarquia .= $row["nivel_{$i}"];
                    $nivel_maximo = $i;
                    if ($i < 6) {
                        $jerarquia .= '.';
                    }
                }
            }
            $tiene_hijos = false;
            if ($nivel_maximo < 6) {
                $query_hijos = "SELECT * FROM contabilidad_plan_cuentas
                                        WHERE iduser ='$iduser' AND estatus = '1' AND (";
                for ($i = 1; $i <= $nivel_maximo; $i++) {
                    $query_hijos .= "nivel_$i = '{$row["nivel_$i"]}' AND ";
                }
                $query_hijos .= "nivel_" . ($nivel_maximo + 1) . " <> '0')";

                $resultado_hijos = mysqli_query($conection, $query_hijos);
                $tiene_hijos = mysqli_num_rows($resultado_hijos) > 0;
            }

            if (!$tiene_hijos) {
                $opcionValor = $row['id'];


                $nombre_cuenta = $row['nombre_cuenta'];

                if ($nombre_cuenta == 'cuentas_bancarias_factu') {

                  $codigo_columna = $row['codigo_columna'];

                  $query_cuenta_bancaria = mysqli_query($conection, "SELECT * FROM cuentas_bancarias_factu
                     WHERE cuentas_bancarias_factu.iduser ='$iduser'  AND cuentas_bancarias_factu.estatus = '1' AND cuentas_bancarias_factu.id = '$codigo_columna' ");
                  $data_cuenta_bancaria = mysqli_fetch_array($query_cuenta_bancaria);

                  $result_lista= mysqli_num_rows($query_cuenta_bancaria);

                  if ($result_lista > 0) {
                    $nombre_cuenta_bancaria  = $data_cuenta_bancaria['nombre_cuenta'];
                    $tipo_cuenta  = $data_cuenta_bancaria['tipo_cuenta'];
                    $numero_cuenta  = $data_cuenta_bancaria['numero_cuenta'];
                    $titular_cuenta  = $data_cuenta_bancaria['titular_cuenta'];
                    $saldo_inicial  = $data_cuenta_bancaria['saldo_inicial'];
                    $row['nombre_cuenta'] = $nombre_cuenta_bancaria.'-'.$numero_cuenta.'-'.$tipo_cuenta;
                    // code...
                  }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                  }

                }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                }

                $nombre_cuenta = $row['nombre_cuenta'];
                $opcionTexto = rtrim($jerarquia, '.'); // Quitamos el último punto.

            }


        }


           ?>
             <tr>
               <td class="td_productos"><?php echo $opcionTexto ?></td>
               <td class="td_productos"><?php echo $nombre_cuenta ?></td>
               <td class="td_productos"><?php echo $data_datos['concepto'] ?></td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['debito'] > 0 ? '$' . number_format($data_datos['debito'], 2) : ''; ?>
               </td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['credito'] > 0 ? '$' . number_format($data_datos['credito'], 2) : ''; ?>
               </td>



             </tr>

          <?php

           }
           ?>

           <tr>
             <td class="td_productos"></td>
             <td class="td_productos"></td>
             <td class="td_productos negrita">TOTALES</td>
             <td class="td_productos valores_asiento negrita">
                  $<?php echo round($total_debito,2) ?>
             </td>
             <td class="td_productos valores_asiento negrita">
                  $<?php echo round($total_credito,2) ?>
             </td>
           </tr>

         </tbody>
       </table>
     </div>
     </div>
     <br>


     <style media="screen">

     .cont_formas_pago{
       border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
       border-radius: 8px;

     }
     .formas_pago_table  {
       border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
       text-align: center;
       font-size: 12px;
       width: 100%;

     }


     </style>

     <div class="cont_formas_pago">
       <table class="formas_pago_table">

           <thead>
             <tr>
               <th class="th_productos">Forma de Pago</th>
               <th class="th_productos">Cantidad</th>
             </tr>
           </thead>
           <tbody>

              <?php

              $query_lista = mysqli_query($conection," SELECT *
                 FROM formas_pago_ventas_contabilidad
                WHERE formas_pago_ventas_contabilidad.compra = '$codigo_venta'
                  AND formas_pago_ventas_contabilidad.estatus = '1'
            ORDER BY `formas_pago_ventas_contabilidad`.`fecha` desc");
                  $result_lista= mysqli_num_rows($query_lista);
                if ($result_lista > 0) {
                      while ($data_lista=mysqli_fetch_array($query_lista)) {
               ?>
                       <tr>
                         <td class="td_productos"><?php echo htmlspecialchars($data_lista['nombre_formas_pago']); ?></td>
                         <td class="td_productos">$<?php echo number_format($data_lista['total'], 2); ?></td>
                       </tr>


               <?php
             }
             }

                ?>
              </tbody>

          </table>

     </div>
   <?php endif; ?>





   <?php if ($data_asiento_contable['tipo'] == 'COMPROBANTE DE EGRESO'): ?>
     <?php



      ?>
         <div class="informacion_comprobante">
           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Fecha de Asiento</td>
                 <td><?php echo $data_asiento_contable['fecha_asiento'] ?></td>
               </tr>
               <tr>
                 <td class="negrita">Fecha Documento</td>
                 <td><?php echo $data_asiento_contable['fecha']?></td>
               </tr>
               <tr>
                 <td class="negrita">Descripción</td>
                 <td><?php echo $data_asiento_contable['descripcion'] ?></td>
               </tr>
             </table>
           </div>

           <div class="bloque_gene_comproban">
             <table>
               <tr>
                 <td class="negrita">Estado Asiento</td>
                 <td><?php echo $data_asiento_contable['estado'] ?></td>
               </tr>
               <tr>
                   <td class="negrita">Empleado</td>
                   <td><?php echo $data_asiento_contable['razon_social_recurso_humano'] ?></td>
               </tr>

               <tr>
                   <td class="negrita">codInt</td>
                   <td><?php echo $data_asiento_contable['id'] ?></td>
               </tr>
             </table>
           </div>
         </div>


         <style media="screen">
         .subtitulo{
           font-size: 13px;
           padding: 0px;
           margin: 0px;
         }

         </style>

         <div class="subtitulo">
           <h4>Detalle del Asiento</h4>
         </div>

         <style media="screen">
           .parte_productos{
             padding: 1px;
             font-size: 11px;
             border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
             border-radius: 8px;
           }

           .parte_productos .parte_productos_solo_productos{
             width: 100%;
             padding: 0px;
             margin: 0px;
           }

           .parte_productos .parte_productos_solo_productos {
             border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
         }

         .texto_asiento{
           text-align: left;
         }
         .valores_asiento{
           text-align: right;
         }

         .negrita{
           font-weight: bold;
           font-size: 12px;
         }


         </style>

         <div class="parte_productos">
           <div class="">
             <table class="parte_productos_solo_productos">
               <thead>
                 <tr>
                   <th class="th_productos texto_asiento">Cuenta</th>
                   <th class="th_productos texto_asiento">Descripción</th>
                   <th class="th_productos texto_asiento">Concepto</th>
                   <th class="th_productos valores_asiento">Debe</th>
                   <th class="th_productos valores_asiento">Haber</th>
                 </tr>
               </thead>
               <tbody>

         <?php

         $query_consulta = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id,contabilidad_plan_cuentas.nombre_cuenta,
           contabilidad_plan_cuentas.nivel_1,contabilidad_plan_cuentas.nivel_2,contabilidad_plan_cuentas.nivel_3,contabilidad_plan_cuentas.nivel_4,
           contabilidad_plan_cuentas.nivel_5,contabilidad_plan_cuentas.nivel_6,contabilidad_plan_cuentas.id as 'codigo_plan_cuentas',contabilidad_datos_asiento_contable.concepto,
           contabilidad_datos_asiento_contable.debito,contabilidad_datos_asiento_contable.credito
            FROM contabilidad_datos_asiento_contable
           INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
            WHERE contabilidad_datos_asiento_contable.iduser ='$iduser' AND contabilidad_datos_asiento_contable.estatus = '1' AND
            contabilidad_datos_asiento_contable.asiento ='$asiento'
            ORDER BY contabilidad_plan_cuentas.nivel_6, contabilidad_plan_cuentas.nivel_5, contabilidad_plan_cuentas.nivel_4, contabilidad_plan_cuentas.nivel_3, contabilidad_plan_cuentas.nivel_2, contabilidad_plan_cuentas.nivel_1   ");



      while ($data_datos = mysqli_fetch_assoc($query_consulta)) {

        $plan_cuentas = $data_datos['codigo_plan_cuentas'];

        $query_consulta_jerarquia = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
                                      WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
                                      AND contabilidad_plan_cuentas.id = '$plan_cuentas'
                                      ORDER BY nivel_6, nivel_5, nivel_4, nivel_3, nivel_2, nivel_1 ");
        $data = array();
        while ($row = mysqli_fetch_assoc($query_consulta_jerarquia)) {
            $data[] = $row;
        }
        foreach ($data as $row) {
            $jerarquia = '';
            $nivel_maximo = 0;
            for ($i = 1; $i <= 6; $i++) {
                if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                    $jerarquia .= $row["nivel_{$i}"];
                    $nivel_maximo = $i;
                    if ($i < 6) {
                        $jerarquia .= '.';
                    }
                }
            }
            $tiene_hijos = false;
            if ($nivel_maximo < 6) {
                $query_hijos = "SELECT * FROM contabilidad_plan_cuentas
                                        WHERE iduser ='$iduser' AND estatus = '1' AND (";
                for ($i = 1; $i <= $nivel_maximo; $i++) {
                    $query_hijos .= "nivel_$i = '{$row["nivel_$i"]}' AND ";
                }
                $query_hijos .= "nivel_" . ($nivel_maximo + 1) . " <> '0')";

                $resultado_hijos = mysqli_query($conection, $query_hijos);
                $tiene_hijos = mysqli_num_rows($resultado_hijos) > 0;
            }

            if (!$tiene_hijos) {
                $opcionValor = $row['id'];


                $nombre_cuenta = $row['nombre_cuenta'];

                if ($nombre_cuenta == 'cuentas_bancarias_factu') {

                  $codigo_columna = $row['codigo_columna'];

                  $query_cuenta_bancaria = mysqli_query($conection, "SELECT * FROM cuentas_bancarias_factu
                     WHERE cuentas_bancarias_factu.iduser ='$iduser'  AND cuentas_bancarias_factu.estatus = '1' AND cuentas_bancarias_factu.id = '$codigo_columna' ");
                  $data_cuenta_bancaria = mysqli_fetch_array($query_cuenta_bancaria);

                  $result_lista= mysqli_num_rows($query_cuenta_bancaria);

                  if ($result_lista > 0) {
                    $nombre_cuenta_bancaria  = $data_cuenta_bancaria['nombre_cuenta'];
                    $tipo_cuenta  = $data_cuenta_bancaria['tipo_cuenta'];
                    $numero_cuenta  = $data_cuenta_bancaria['numero_cuenta'];
                    $titular_cuenta  = $data_cuenta_bancaria['titular_cuenta'];
                    $saldo_inicial  = $data_cuenta_bancaria['saldo_inicial'];
                    $row['nombre_cuenta'] = $nombre_cuenta_bancaria.'-'.$numero_cuenta.'-'.$tipo_cuenta;
                    // code...
                  }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                  }

                }else {
                    $row['nombre_cuenta'] = $nombre_cuenta;
                }

                $nombre_cuenta = $row['nombre_cuenta'];
                $opcionTexto = rtrim($jerarquia, '.'); // Quitamos el último punto.

            }


        }


           ?>
             <tr>
               <td class="td_productos"><?php echo $opcionTexto ?></td>
               <td class="td_productos"><?php echo $nombre_cuenta ?></td>
               <td class="td_productos"><?php echo $data_datos['concepto'] ?></td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['debito'] > 0 ? '$' . number_format($data_datos['debito'], 2) : ''; ?>
               </td>
               <td class="td_productos valores_asiento">
                   <?php echo $data_datos['credito'] > 0 ? '$' . number_format($data_datos['credito'], 2) : ''; ?>
               </td>



             </tr>

          <?php

           }
           ?>

           <tr>
             <td class="td_productos"></td>
             <td class="td_productos"></td>
             <td class="td_productos negrita">TOTALES</td>
             <td class="td_productos valores_asiento negrita">
                  $<?php echo round($total_debito,2) ?>
             </td>
             <td class="td_productos valores_asiento negrita">
                  $<?php echo round($total_credito,2) ?>
             </td>
           </tr>

         </tbody>
       </table>
     </div>
     </div>
     <br>




   <?php endif; ?>


   <br><br>

     <style media="screen">
     .informacion_firmado{
       font-size: 13px;
       text-align: center;
     }

     .negrita{
       font-weight: bold;
     }

     .bloque_gene_firmado{
       width: 33%;
       display: inline-block;
       vertical-align: top; /* Añadir alineación vertical */
     }

     /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
     .bloque_gene_firmado table {
       text-align: center;
       width: 100%;
     }

     </style>

   <br><br>

     <div class="informacion_firmado">
       <div class="bloque_gene_firmado">
         <table>
           <tr>
             <td class="negrita">_____________________</td>
           </tr>
           <tr>
             <td class="negrita">Elaborador Por</td>
           </tr>
         </table>
       </div>


       <div class="bloque_gene_firmado">
         <table>
           <tr>
             <td class="negrita">_____________________</td>
           </tr>
           <tr>
             <td class="negrita">Aprobado por</td>
           </tr>
         </table>
       </div>

       <div class="bloque_gene_firmado">
         <table>
           <tr>
             <td class="negrita">_____________________</td>
           </tr>
           <tr>
             <td class="negrita">Recibido</td>
           </tr>
         </table>
       </div>
     </div>


  </body>
</html>
