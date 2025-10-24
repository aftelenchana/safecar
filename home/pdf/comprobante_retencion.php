<?php

include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$iduser = $_GET['id'];
$clave_acc_guardar = $_GET['clave_acc_guardar'];
$codigo_factura = $_GET['codigo_factura'];
$email_retener = $_GET['email_retener'];

new barCodeGenrator(''.$clave_acc_guardar.'', 1, 'barra.gif', 455, 60, false);


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
$razon_social =  $result_documentos['razon_social'];

$contabilidad            = $result_documentos['contabilidad'];
$img_facturacion         = $result_documentos['img_facturacion'];

$facebook                = $result_documentos['facebook'];
$pagina_web                = $result_documentos['pagina_web'];
$instagram           = $result_documentos['instagram'];
$whatsapp             = $result_documentos['whatsapp'];
$url_img_upload             = $result_documentos['url_img_upload'];
$fecha_actual = date("d-m-Y");
$fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));

//CODIGO PARA SACAR LA INFORMACION DE LA RETENCION

$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;


  $ruta_factura = ''.$url2.'/home/facturacion/facturacionphp/comprobantes/retencion/no_firmados/'.$clave_acc_guardar.'.xml';
  $acceso_retencion = simplexml_load_file($ruta_factura);
  $codDocModificado                = $acceso_retencion->infoTributaria->codDoc;

 //para crear el numero dl documento necesito de 4 partes
  $estab                       = $acceso_retencion->infoTributaria->estab;
  $ptoEmi                      = $acceso_retencion->infoTributaria->ptoEmi;
  $secuencial                  = $acceso_retencion->infoTributaria->secuencial;
  $numDocModificado                = ''.$estab.'-'.$ptoEmi.'-'.$secuencial.'';

  $razonSocial                  = $acceso_retencion->infoTributaria->razonSocial;
  $dirMatriz                  = $acceso_retencion->infoTributaria->dirMatriz;
  $ruc                  = $acceso_retencion->infoTributaria->ruc;


  $ruc                  = $acceso_retencion->infoTributaria->ruc;

//informacion del sujeto retenido
$razonSocialSujetoRetenido                  = $acceso_retencion->infoCompRetencion->razonSocialSujetoRetenido;
$identificacionSujetoRetenido                  = $acceso_retencion->infoCompRetencion->identificacionSujetoRetenido;
$periodoFiscal                  = $acceso_retencion->infoCompRetencion->periodoFiscal;
$fechaEmision                  = $acceso_retencion->infoCompRetencion->fechaEmision;
//informacion del documento retenido

  $numDocSustento             = $acceso_retencion->docsSustento->docSustento->numDocSustento;


  // Acceder a los campos adicionales en infoAdicional
$infoAdicional = $acceso_retencion->infoAdicional->campoAdicional;

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
$direccion_comprador = $camposAdicionales['DIRECCION'];
$celular_comprador   = $camposAdicionales['CELULAR'];
$facturador_comprador= $camposAdicionales['FACTURADOR'];
$email_comprador      = $camposAdicionales['EMAIL'];

//CODIGO PARA SACAR LA INFORMACION DE LA FACTURA QUE SE ESTA REALIZANDO


 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Comprobante de Retención</title>
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
          <tbody>
            <tr>
              <td class="td_bld">Emisor:</td>
              <td class="celda_confogurar"><?php echo $razonSocial ?></td>
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
              <td> <?php echo $dirMatriz ?></td>
            </tr>
            <tr>
              <td class="td_bld">Ruc:</td>
              <td><?php echo $ruc ?></td>
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
                <td class="td_bld">Retención No :<?php echo $numDocModificado ?></td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion"><?php echo $clave_acc_guardar ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Autorización</td>
              </tr>
              <tr>
                <td><?php echo $fecha ?></td>
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
            <td> <span class="td_bld" >Razon Social Sujeto Retenido:</span><?php echo $razonSocialSujetoRetenido ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >RUC/CI Sujeto Retenido:</span> <?php echo $identificacionSujetoRetenido ?></td>
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
              <th class="th_productos">Comprobante</th>
              <th class="th_productos">Número</th>
              <th class="th_productos">Fecha Emisión</th>
              <th class="th_productos">Ej. Fiscal</th>
              <th class="th_productos">Base Imponible</th>
              <th class="th_productos">Impuesto</th>
              <th class="th_productos">Cod. Retención</th>
              <th class="th_productos">Porc. Retención</th>
              <th class="th_productos">Valor</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $facto =  110;												//CABECERA KARDEX TOTALES

           $contador_retenciones = $acceso_retencion->docsSustento->docSustento->retenciones->retencion;
           $base_tdll = 0;
           $base_array_detalle = 1;
           $totalValorRetenido = 0; // Inicializa la variable para sumar los valores retenidos

           foreach($contador_retenciones as $Item){
             $codigo           = $acceso_retencion->docsSustento->docSustento->retenciones->retencion[$base_tdll]->codigo;
             $codigoRetencion  = $acceso_retencion->docsSustento->docSustento->retenciones->retencion[$base_tdll]->codigoRetencion;
             $baseImponible    = $acceso_retencion->docsSustento->docSustento->retenciones->retencion[$base_tdll]->baseImponible;
             $porcentajeRetener= $acceso_retencion->docsSustento->docSustento->retenciones->retencion[$base_tdll]->porcentajeRetener;
             $valorRetenido    = $acceso_retencion->docsSustento->docSustento->retenciones->retencion[$base_tdll]->valorRetenido;

             $totalValorRetenido += $valorRetenido;
             $base_tdll =$base_tdll +1;
             if ($codigo == 1) {
               $codigo = 'RENTA';
               // code...
             }
             if ($codigo == 2) {
               $codigo = 'IVA';
               // code...
             }
             ?>
            <tr>
              <td class="td_productos">Factura</td>
              <td class="td_productos"><?php echo ($numDocSustento); ?></td>
              <td class="td_productos"><?php echo $fechaEmision; ?></td>
              <td class="td_productos"><?php echo ($periodoFiscal); ?></td>
              <td class="td_productos"><?php echo ($baseImponible); ?></td>
              <td class="td_productos"><?php echo ($codigo); ?></td>
              <td class="td_productos"><?php echo ($codigoRetencion); ?></td>
              <td class="td_productos"><?php echo ($porcentajeRetener); ?>%</td>
              <td class="td_productos"><?php echo ($valorRetenido); ?></td>
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
                <td> <span class="td_bld" >Email Sujeto a Retener:</span> <span><?php echo $email_retener ?></td>
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
                <td class="td_bld td_resumen" >Total Retención:</td>
                <td class="td_resumen">$<?php echo $totalValorRetenido ?></td>
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
