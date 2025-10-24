<?php
include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$iduser = $_GET['iduser'];
$clave_acceso_nota_credito = $_GET['clave_acceso_nota_credito'];


new barCodeGenrator(''.$clave_acceso_nota_credito.'', 1, 'barra.gif', 455, 60, false);


$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$ambito_area          =  $result_configuracion['ambito'];

//sacamos informacion de la  nota de credito

$query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM comprobante_nota_credito  WHERE comprobante_nota_credito.clave_acceso ='$clave_acceso_nota_credito' ");
$data_existencia = mysqli_fetch_array($query_comprobacion_existencia);
$ininterno = $data_existencia['id'];
$url_file_upload_nota_credito = $data_existencia['url_file_upload'];
$clave_acceso_factura = $data_existencia['clave_acceso_factura'];

if ($ambito_area == 'prueba') {
  $ruta_nota_credito = 'C:\\xampp\\htdocs\\home\\facturacion\\facturacionphp\\comprobantes\\nota-credito\\no_firmados\\'.$clave_acceso_nota_credito.'.xml';

}else {
  $ruta_nota_credito = ''.$url_file_upload_nota_credito.'/home/facturacion/facturacionphp/comprobantes/nota-credito/no_firmados/'.$clave_acceso_nota_credito.'.xml';
}



$query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
$result_documentos = mysqli_fetch_array($query_doccumentos);
$regimen = $result_documentos['regimen'];
$contabilidad             = $result_documentos['contabilidad'];
$email_empresa_emisor     = $result_documentos['email'];
$celular_empresa_emisor   = $result_documentos['celular'];
$telefono_empresa_emisor  = $result_documentos['telefono'];
$direccion_emisor          = $result_documentos['direccion'];
$whatsapp                 = $result_documentos['whatsapp'];
$nombres                  = $result_documentos['nombres'];
$apellidos                = $result_documentos['apellidos'];
$numero_identificacion_emisor  = $result_documentos['numero_identidad'];
$contribuyente_especial   = $result_documentos['contribuyente_especial'];
$contabilidad            = $result_documentos['contabilidad'];
$img_facturacion         = $result_documentos['img_facturacion'];
$contabilidad         = $result_documentos['contabilidad'];
$regimen         = $result_documentos['regimen'];

$nombre_empresa         = $result_documentos['nombre_empresa'];
$razon_social_usuario          = $result_documentos['razon_social'];

$facebook                = $result_documentos['facebook'];
$pagina_web                = $result_documentos['pagina_web'];
$instagram           = $result_documentos['instagram'];
$whatsapp             = $result_documentos['whatsapp'];
$url_img_upload             = $result_documentos['url_img_upload'];




//de qui empezamos a sacar la informacion de la factura
if ($ambito_area == 'prueba') {
  $ruta_factura = 'C:\\xampp\\htdocs\\home\\facturacion\\facturacionphp\\comprobantes\\no_firmados\\'.$clave_acceso_factura.'.xml';

}else {

  $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM comprobante_factura_final WHERE comprobante_factura_final.clave_acceso ='$clave_acceso_factura' ");
  $data_existencia = mysqli_fetch_array($query_comprobacion_existencia);
  $ininterno = $data_existencia['id'];
  $url_file_upload = $data_existencia['url_file_upload'];
  $ruta_factura = ''.$url_file_upload.'/home/facturacion/facturacionphp/comprobantes/no_firmados/'.$clave_acceso_factura.'.xml';
}

$acceso_factura = simplexml_load_file($ruta_factura);
$codDocModificado                = $acceso_factura->infoTributaria->codDoc;

 //para crear el numero dl documento necesito de 4 partes
  $estab                       = $acceso_factura->infoTributaria->estab;
  $ptoEmi                      = $acceso_factura->infoTributaria->ptoEmi;
  $secuencial                  = $acceso_factura->infoTributaria->secuencial;
$numDocModificado                = ''.$estab.'-'.$ptoEmi.'-'.$secuencial.'';

//informacion del comprador



  $identificacion_comprador             = $acceso_factura->infoFactura->identificacionComprador;
  $tipo_identificacion_comprador         = $acceso_factura->infoFactura->tipoIdentificacionComprador;
  $razon_social_comprador                = $acceso_factura->infoFactura->razonSocialComprador;
  $obligadoContabilidad                = $acceso_factura->infoFactura->obligadoContabilidad;
  $fechaEmision                = $acceso_factura->infoFactura->fechaEmision;
  $totalSinImpuestos_general                = $acceso_factura->infoFactura->totalSinImpuestos;
  $totalDescuento_general                = $acceso_factura->infoFactura->totalDescuento;
  $totalDescuento                = $acceso_factura->infoFactura->totalDescuento;

  $dirEstablecimiento                = $acceso_factura->infoFactura->dirEstablecimiento;

  $rrr= ($acceso_factura->infoAdicional->campoAdicional);
 foreach($rrr as $Item){
   $atrinuto = (string)$acceso_factura->infoAdicional->campoAdicional[0];
   $posicion_coincidencia = strpos($atrinuto, '@');
   if ($posicion_coincidencia === false) {
     $email_receptor = 'vacio';

   } else {
   $email_receptor =$atrinuto;
   }
   }

   //vamos a sacr la informacion del usuario si es el caso de que tenga en la base de datos un registro de clientes

   $query_veri_ruc = mysqli_query($conection, "SELECT * FROM clientes WHERE identificacion= '$identificacion_comprador' ORDER BY id DESC");
   $result_lista_ruc = mysqli_num_rows($query_veri_ruc);

   if ($result_lista_ruc > 0) {
       // Cliente encontrado en la base de datos
       $data_cliente = mysqli_fetch_array($query_veri_ruc);
       $direccion_comprador = $data_cliente['direccion'];
       $celular_comprador = $data_cliente['celular'];
       $email_comprador = $data_cliente['mail'];
       $id_comprador = $data_cliente['id'];
   } else {
       // Cliente no encontrado en la base de datos, buscar información externa
       $query_user_in = mysqli_query($conection, "SELECT * FROM usuarios WHERE id = '$user_in'");
       $data_user_in = mysqli_fetch_array($query_user_in);
       $key_user_in = $data_user_in['id_e'];
       $datos_ruc = json_decode(file_get_contents('https://www.guibis.com/dev/ruc?key=' . $key_user_in . '&callback=all&CIRUC=' . $identificacion_comprador), true);

       if ($datos_ruc['noticia'] == 'consulta_exitosa') {
           // Datos obtenidos de la consulta externa
           $direccion_comprador = $datos_ruc['DIRECCION'];
           $celular_comprador = $datos_ruc['CELULAR'];
           $email_comprador = $datos_ruc['EMAIL'];
       } else {
           // No se encontraron datos ni en la base de datos ni en la consulta externa
           $direccion_comprador = '';
           $celular_comprador = '';
           $email_comprador = '';
       }
   }




 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nota de Crédito</title>
  </head>
  <style media="screen">

.img_logo_empresa{
  text-align: center;
}

  .img_logo_empresa img{
    width: 100px;
  }
  .parte_superior{
    padding: 2px;
    margin: 2px;
    height: 250px;
  }
  .td_bld{
    font-weight: bold;
  }
  .informacion_emisor th,td{
    padding: 0;
    margin: 0;
  }

  .informacion_emisor{
    padding: 10px;
    margin-bottom: 30px;
    margin-right: 5px;
    display: inline-block;
    width: 300px;
    border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
  }
  .informacion_factura{
    padding: 10px;
    border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
    margin-bottom: 30px;
    width: 340px;
    display: inline-block;
  }
  .informacion_factura table{
    margin: 0 auto;
  }


  .numero_autorzaxion{
    font-size: 11px;
  }
  .informacion_ghd{
    display: inline-block;
  }

  .informacion_financiero_bancario{
    display: inline-block;
      font-size: 11px;
  }


  .parte_superior .bloque_superior_row{
    font-size: 11px;

  }


  .clave_ed_acces{
    font-size: 11px;
    text-align: center;
  }


  </style>
  <body>
    <div class="parte_superior">
      <div class="bloque_superior_row informacion_emisor">
        <div class="img_logo_empresa">
          <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
        </div>
        <table>

          <?php

          $acceso_nota_credito = simplexml_load_file($ruta_nota_credito);
          $razonSocial_nota_credito    = $acceso_nota_credito->infoTributaria->razonSocial;

          $ruc_nota_credito    = $acceso_nota_credito->infoTributaria->ruc;
          $claveAcceso_nota_credito    = $acceso_nota_credito->infoTributaria->claveAcceso;
          $codDoc_nota_credito    = $acceso_nota_credito->infoTributaria->codDoc;
          $estab_nota_credito    = $acceso_nota_credito->infoTributaria->estab;
          $ptoEmi_nota_credito    = $acceso_nota_credito->infoTributaria->ptoEmi;
          $secuencial_nota_credito    = $acceso_nota_credito->infoTributaria->secuencial;

          $dirMatriz_nota_credito    = $acceso_nota_credito->infoTributaria->dirMatriz;

          $motivo    = $acceso_nota_credito->infoNotaCredito->motivo;

          $valorModificacion    = $acceso_nota_credito->infoNotaCredito->valorModificacion;
          $fechaEmision_nota_credito    = $acceso_nota_credito->infoNotaCredito->fechaEmision;


           ?>


          <tbody>
            <tr>
              <td class="td_bld">Emisor:</td>
              <td class="celda_confogurar"><?php echo $razonSocial_nota_credito ?></td>
            </tr>
            <style>
              .celda_confogurar {
                max-width: 240px; /* Ajusta esto según el ancho de tu contenedor */
                word-wrap: break-word;
                word-break: break-all;
                font-size: 100%; /* Empieza con el tamaño de fuente por defecto */
              }
            </style>
            <tr>
              <td class="td_bld">Matriz:</td>
              <td> <?php echo $dirMatriz_nota_credito ?></td>
            </tr>
            <tr>
              <td class="td_bld">Ruc:</td>
              <td><?php echo $ruc_nota_credito ?></td>
            </tr>
            <tr>
              <td class="td_bld">Correo:</td>
              <td class="celda_confogurar" ><?php echo $email_empresa_emisor ?></td>
            </tr>
            <tr>
               <td class="td_bld">Regimen:</td>
               <td><?php echo $regimen ?></td>
            </tr>
          </tbody>
        </table>

      </div>
      <div class="bloque_superior_row informacion_factura">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="td_bld">Nota de Crédito No :<?php echo $estab_nota_credito ?>-<?php echo $ptoEmi_nota_credito ?>-<?php echo $secuencial_nota_credito ?></td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion"><?php echo $claveAcceso_nota_credito ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Autorización</td>
              </tr>
              <tr>
                <td><?php echo $fechaEmision_nota_credito ?></td>
              </tr>
              <tr>
                <td class="td_bld">OBLIGADO A LLEVAR CONTABILIDAD:<?php echo $contabilidad  ?></td>

              </tr>
              <tr>
                <td class="td_bld">Ambiente:Producción</td>
              </tr>
              <tr>
                <td class="td_bld">EMISIÓN :NORMAL</td>
              </tr>
              <tr>
                <td class="td_bld">CLAVE ACCESO</td>
              </tr>
              <tr>
                <td> <img src="barra.gif" width="340px;" height="75px;" alt=""> </td>
              </tr>
              <tr>
                <td class="clave_ed_acces"><?php echo $claveAcceso_nota_credito ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <style media="screen">
    .parte_inermedia{

      padding: 2px;
    }
      .parte_inermedia table{
        padding: 0;
        margin: 0;
        width: 100%;
        font-size: 11px;
        border: 1px solid black; /* Establece el borde de la tabla y sus celdas */

      }

    </style>
    <div class="parte_inermedia">
      <table>
        <tbody>
          <tr>
            <td> <span class="td_bld" >Razon Social Comprador:</span><?php echo $razon_social_comprador ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >RUC/CI Comprador:</span> <?php echo $identificacion_comprador ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección:</span> <?php echo $direccion_comprador ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Celular:</span> <?php echo $celular_comprador ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Teléfono:</span> <?php echo $telefono_empresa_emisor ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Comprobante que se Módifica:</span> <?php echo $clave_acceso_factura ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Emisión Comprobante Módifica:</span> <?php echo $fechaEmision ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Razón Modificación:</span> <?php echo $motivo ?></td>
          </tr>
        </tbody>
      </table>
    </div>
        <br>

        <style media="screen">
          .parte_productos{
            padding: 1px;
            font-size: 11px;
          }

          .parte_productos .parte_productos_solo_productos{
            width: 100%;
            background: #e8e8e8 ;
            padding: 0px;
            margin: 0px;
            text-align: center;
          }

          .parte_productos .parte_productos_solo_productos {
            border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
        }

          .parte_productos .th_productos{
              border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
          }
          .parte_productos .td_productos{
              border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
          }
        </style>

        <div class="parte_productos">
          <div class="">
            <table class="parte_productos_solo_productos">
              <thead>
                <tr>
                  <th class="th_productos">Codigo</th>
                  <th class="th_productos">Cantidad</th>
                  <th class="th_productos">Descripcion</th>
                  <th class="th_productos">P/U</th>
                  <th class="th_productos">DSCT</th>
                  <th class="th_productos">IVA</th>
                  <th class="th_productos">Sub Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $facto =  110;												//CABECERA KARDEX TOTALES

               $contador_detalles = $acceso_factura->detalles->detalle;
               $base_tdll = 0;
               $base_array_detalle = 1;
               foreach($contador_detalles as $Item){
                 $descripcion_producto= $acceso_factura->detalles->detalle[$base_tdll]->descripcion;
                 $codigoPrincipal= $acceso_factura->detalles->detalle[$base_tdll]->codigoPrincipal;
                 $cantidad= $acceso_factura->detalles->detalle[$base_tdll]->cantidad;
                 $precioUnitario= $acceso_factura->detalles->detalle[$base_tdll]->precioUnitario;
                 $descuento= $acceso_factura->detalles->detalle[$base_tdll]->descuento;
                 $precioTotalSinImpuesto= $acceso_factura->detalles->detalle[$base_tdll]->precioTotalSinImpuesto;
                  $impuestos= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto;
                 //CODIGO PARA DETALLES
                 $codigo= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto->codigo;
                 $codigoPorcentaje= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto->codigoPorcentaje;
                 $tarifa= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto->tarifa;
                 $baseImponible= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto->baseImponible;
                 $valor= $acceso_factura->detalles->detalle[$base_tdll]->impuestos->impuesto->valor;
                  $base_tdll =$base_tdll +1;
                 ?>
                <tr>
                  <td class="td_productos"><?php echo ($codigoPrincipal); ?></td>
                  <td class="td_productos"><?php echo $cantidad; ?></td>
                  <td class="td_productos"><?php echo $descripcion_producto; ?></td>
                  <td class="td_productos"><?php echo ($precioUnitario); ?></td>
                  <td class="td_productos">$<?php echo ($descuento); ?></td>
                  <td class="td_productos">$<?php echo ($valor); ?></td>
                  <td class="td_productos">$<?php echo ($baseImponible); ?></td>
                </tr>

                <?php
                }
            ?>
              </tbody>
            </table>
          </div>
        </div>


<br><br>

    <style media="screen">

    .conte_gene_inferior_sc{
      width: 65%;
      font-size: 11px;
      display: flex;
      align-items: center; /* Alinea los elementos verticalmente en el centro */
      justify-content: space-between; /* Separa los elementos equitativamente */

    }

    .parte_inferior_informacion .informacion_ghd{
      width: 65%;
      font-size: 11px;


    }
    .parte_inferior_informacion .informacion_ghd table{
      width: 450px;
      margin: 3px;
      padding: 10px;
      border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
    }
    .parte_inferior_informacion{
      width: 99%;
      margin: 0 auto;
    }
    .informacion_financiero_bancario{
      width: 200px;
      border-radius: 5px;
      padding: 5px;
      margin: 5px;


    }
    .informacion_financiero_bancario table{
      width: 190px;
      text-align: center;
      margin: 3 auto;
      background:  #e8e8e8 ;
      padding: 5px;

    }
    .tabla_ghdd{
          border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
    }




    </style>

    <div class="parte_inferior_informacion">
      <div class="conte_gene_inferior_sc">
        <div class="informacion_ghd">
          <table class="tabla_ghdd">
            <tbody>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd" > <span class="td_bld" >Email Empresa: </span> <span><?php echo $email_empresa_emisor ?></span> </td>
                <td class="td_info_gjd"></td>
              </tr>
              <tr class="tr_info_gjd" >
                <td> <span class="td_bld" >Email Cliente:</span> <span><?php echo $email_comprador ?></td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld " >Teléfono Empresa:</span>  <span><?php echo $telefono_empresa_emisor ?></span> </td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld" >Direccion Cliente: </span> <span><?php echo $direccion_comprador ?></span> </td>
              </tr>

            </tbody>
          </table>

          <style media="screen">
          .formas_pago_table  {
            border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
            text-align: center;
            background: #fff;
        }

          .formas_pago_table .th_productos{
              border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
          }
          .formas_pago_table .td_productos{
              border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
          }
          </style>


        </div>
      </div>




       <style media="screen">
         .tabla_resumen_fernando{
                     border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
         }
       </style>


      <div class="informacion_financiero_bancario">
        <div class="">
          <table class="tabla_resumen_fernando">
            <tbody>
              <tr class="tr_resumen">
                <td class="td_bld td_resumen" >Total Modificación:</td>
                <td class="td_resumen">$<?php echo $valorModificacion ?></td>
              </tr>
            </tbody>
          </table>

        </div>

      </div>

    </div>

    <?php

    //codigo para saber como va el emisor
    if (empty($nombre_empresa) || $nombre_empresa == '0') {
      $nombre_salida = $razon_social;
    }else {
      $nombre_salida = $nombre_empresa;
    }

    if (!empty($facebook)) {
      $facebook = '<a style="text-align: center; margin:3px; padding4px  " href="'.$facebook.'"> <img src="https://guibis.com/home/img/reacciones/facebook.png" alt="" width="30px;"></a>';
    }else {
      $facebook = '';
    }

    if (!empty($instagram)) {
      $instagram = '<a style="text-align: center; margin:3px; padding4px " href="'.$instagram.'"> <img src="https://guibis.com/home/img/reacciones/instagram.png" alt="" width="30px;"></a>';
    }else {
      $instagram = '';
    }

    if (!empty($whatsapp)) {
      $whatsapp = '<a style="text-align: center; margin:3px; padding4px" href="https://api.whatsapp.com/send?phone='.$whatsapp.'&amp;text=Hola!&nbsp;Vengo&nbsp;De&nbsp;'.$nombre_salida.'&nbsp;"> <img src="https://guibis.com/home/img/reacciones/whatsapp.png" alt="" width="30px;"></a>';
    }else {
      $whatsapp = '';
    }

    if (!empty($pagina_web)) {
      $pagina_web = '<a style="text-align: center; margin:3px; padding4px" href="'.$pagina_web.'"> <img src="https://guibis.com/home/img/reacciones/web.png" alt="" width="30px;"></a>';
    }else {
      $pagina_web = '';
    }


     ?>
     <style media="screen">
       .informacion_adicional_factura{
         font-size: 11px;
         text-align: center;
         margin: 0 auto;
         padding: 10px;

       }
       .contenedores_redes{
         text-align: center;
         margin: 0 auto;
         width: 100%;
       }
       .fb_gy{
         width: 25%;
         display: inline-block;
       }
       .titulo_informacion_extra{
         padding: 5px;
         margin: 5px;
       }
     </style>
     <br><br><br><br><br>



    <div class="informacion_adicional_factura">
      <div class="titulo_informacion_extra">
        <h3>Nuestras Redes Sociales</h3>

      </div>
      <div class="contenedores_redes">
        <div class="fb_gy">
          <?php echo $facebook ?>

        </div>
        <div class="fb_gy">
              <?php echo $instagram ?>

        </div>
        <div class="fb_gy">
              <?php echo $whatsapp ?>

        </div>
        <div class="fb_gy">
              <?php echo $pagina_web ?>

        </div>
      </div>



    </div>



  </body>
</html>
