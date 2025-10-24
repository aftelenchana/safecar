<?php

include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$iduser = $_GET['id'];
$clave_acc_guardar = $_GET['clave_acc_guardar'];
$nomnto_modificacion = $_GET['nomnto_modificacion'];
$razon_modficiacion = $_GET['razon_modficiacion'];
$clave_acceso_factura = $_GET['clave_acceso_factura'];
$sucursal_facturacion = $_GET['sucursal_facturacion'];
$user_in = $_GET['user_in'];
		new barCodeGenrator(''.$clave_acc_guardar.'', 1, 'barra.gif', 455, 60, false);


$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$ambito_area          =  $result_configuracion['ambito'];


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
$id_e_mpresa         = $result_documentos['id_e'];

$nombre_empresa         = $result_documentos['nombre_empresa'];
$razon_social_usuario          = $result_documentos['razon_social'];

$facebook                = $result_documentos['facebook'];
$pagina_web                = $result_documentos['pagina_web'];
$instagram           = $result_documentos['instagram'];
$whatsapp             = $result_documentos['whatsapp'];
$url_img_upload             = $result_documentos['url_img_upload'];
$fecha_actual = date("d-m-Y");
$fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));



//de qui empezamos a sacar la informacion
if ($ambito_area == 'prueba') {
  $ruta_factura = 'C:\\xampp\\htdocs\\home\\facturacion\\facturacionphp\\comprobantes\\no_firmados\\'.$clave_acceso_factura.'.xml';

}else {

  $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM ventas_externas_generadas WHERE ventas_externas_generadas.claveAcceso ='$clave_acceso_factura' ");
  $data_existencia = mysqli_fetch_array($query_comprobacion_existencia);
  $ininterno = $data_existencia['id'];
  $url_file_upload = $data_existencia['url'];
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


    // Acceder a los campos adicionales en infoAdicional
  $infoAdicional = $acceso_factura->infoAdicional->campoAdicional;

  // Crear un array para almacenar los campos adicionales
  $camposAdicionales = [];

  // Recorrer cada campo adicional
  foreach ($infoAdicional as $campo) {
      $nombreCampo = (string)$campo->attributes()->nombre; // Obtener el nombre del atributo
      $valorCampo = (string)$campo; // Obtener el valor del campo
      $camposAdicionales[$nombreCampo] = $valorCampo;
  }

  // Ahora $camposAdicionales contiene los datos de los campos adicionales
  // Ejemplo de uso:
  if (!empty($camposAdicionales['DIRECCION'])) {
      $direccion_comprador = $camposAdicionales['DIRECCION'];
    // code...
  }else {
    $direccion_comprador = '';
    // code...
  }
  if (!empty($camposAdicionales['CELULAR'])) {
      $celular_comprador = $camposAdicionales['CELULAR'];
    // code...
  }
  else {
    $celular_comprador = '';
    // code...
  }
  if (!empty($camposAdicionales['FACTURADOR'])) {
      $facturador_comprador = $camposAdicionales['FACTURADOR'];
    // code...
  }
  else {
    $facturador_comprador = '';
    // code...
  }
  if (!empty($camposAdicionales['EMAIL'])) {
      $email_reeptor = $camposAdicionales['EMAIL'];
    // code...
  }else {
    $email_reeptor = '';
    // code...
  }


 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nota de Crédito </title>
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


  <?php

  //codigo para sacar informacion del xml que se va enviar
  if ($ambito_area == 'prueba') {
    $ruta_nota_credito = 'C:\\xampp\\htdocs\\home\\facturacion\\facturacionphp\\comprobantes\\nota-credito\\no_firmados\\notacredito'.$iduser.'.xml';

  }else {
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;
     $ruta_nota_credito = ''.$url.'/home/facturacion/facturacionphp/comprobantes/nota-credito/no_firmados/notacredito'.$iduser.'.xml';
  }


  $acceso_nota_credito = simplexml_load_file($ruta_nota_credito);
  $codDoc_nota                = $acceso_nota_credito->infoTributaria->codDoc;
  $estab_nota                = $acceso_nota_credito->infoTributaria->estab;
  $ptoEmi_nota                = $acceso_nota_credito->infoTributaria->ptoEmi;
  $secuencial_nota                = $acceso_nota_credito->infoTributaria->secuencial;
  $dirMatriz_nota                = $acceso_nota_credito->infoTributaria->dirMatriz;

  $numero_nita_credito                = ''.$estab_nota.'-'.$ptoEmi_nota.'-'.$secuencial_nota.'';


  $numDocModificado_nota                = $acceso_nota_credito->infoNotaCredito->numDocModificado;
  $fechaEmision_nota                = $acceso_nota_credito->infoNotaCredito->fechaEmision;





   ?>
  <body>
    <div class="parte_superior">
      <div class="bloque_superior_row informacion_emisor">
        <div class="img_logo_empresa">
          <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
        </div>
        <table>
          <tbody>
            <tr>
              <td class="td_bld">Emisor:</td>
              <td class="celda_confogurar"><?php echo $razon_social_usuario ?></td>
            </tr>
            <?php if (!empty($nombre_empresa)): ?>
              <tr>
                <td class="td_bld">Nombre Comercial:</td>
                <td class="celda_confogurar"><?php echo $nombre_empresa ?></td>
              </tr>

            <?php endif; ?>
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
              <td> <?php echo $dirMatriz_nota ?></td>
            </tr>
            <tr>
              <td class="td_bld">Ruc:</td>
              <td><?php echo $numero_identificacion_emisor ?></td>
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
                <td class="td_bld">Nota de Crédito No :<?php echo $numero_nita_credito ?></td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion"><?php echo $clave_acc_guardar ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Emisión</td>
              </tr>
              <tr>
                <td><?php echo $fechaEmision_nota ?></td>
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
                <td class="clave_ed_acces"><?php echo $clave_acc_guardar ?></td>
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
            <td> <span class="td_bld" >Número Comprobante que se Módifica:</span> <?php echo $numDocModificado_nota ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Emisión Comprobante Módifica:</span> <?php echo $fechaEmision ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Razón Modificación:</span> <?php echo $razon_modficiacion ?></td>
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

      <?php
      // Inicializa los totales para cada tipo de impuesto
      $totalesPorTipo = [
          'base0' => 0,
          'base12' => 0,
          'iva12' => 0,
          'noObjeto' => 0,
          'exentoIVA' => 0,
      ];

      foreach ($acceso_factura->detalles->detalle as $detalle) {
          foreach ($detalle->impuestos->impuesto as $impuesto) {
              $codigoPorcentaje = (string)$impuesto->codigoPorcentaje;
              $baseImponible = (float)$impuesto->baseImponible;
              $valor = (float)$impuesto->valor;

              switch ($codigoPorcentaje) {
                  case '0': // IVA 0%
                      $totalesPorTipo['base0'] += $baseImponible;
                      break;
                  case '2': // IVA 12%
                      $totalesPorTipo['base12'] += $baseImponible;
                      $totalesPorTipo['iva12'] += $valor;
                      break;
                  case '6': // No objeto de IVA
                      $totalesPorTipo['noObjeto'] += $baseImponible;
                      break;
                  case '7': // Exento de IVA
                      $totalesPorTipo['exentoIVA'] += $baseImponible;
                      break;
                  // Si hay más códigos, agregar casos adicionales
              }
          }
      }

      // Calcula el subtotal y el total a pagar
      $subtotal = $totalesPorTipo['base0'] + $totalesPorTipo['base12'] + $totalesPorTipo['noObjeto'] + $totalesPorTipo['exentoIVA'];
      $totalPagar = $subtotal + $totalesPorTipo['iva12'];
      ?>




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
                      <td class="td_bld td_resumen">Sin Iva</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base0'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Con Iva</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base12'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">No Objeto</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['noObjeto'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Excento de Iva</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['exentoIVA'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Subtotal</td>
                      <td class="td_resumen">$<?php echo number_format($subtotal, 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">IVA 12%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva12'], 2); ?></td>
                  </tr>
                  <!-- Agrega aquí otras filas para impuestos como ICE o IRBPNR si son necesarios -->
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Total:</td>
                      <td class="td_resumen">$<?php echo number_format($totalPagar, 2); ?></td>
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
       .compartir_enlace__tienda{
         font-size: 10px;
       }
     </style>





     <br><br><br>
     <div class="compartir_enlace__tienda">
       <a href="<?php echo $url ?>/code_user?code=<?php echo $id_e_mpresa ?>">Visita mi espacio en el mundo de la red</a>
     </div>
     <br><br>



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
