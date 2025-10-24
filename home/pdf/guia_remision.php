<?php
include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$iduser = $_GET['id'];
$clave_acceso_guia_remision = $_GET['clave_acc_guardar'];
$fechaAutorizacion = $_GET['fechaAutorizacion'];


//CODIGO PARA SACAR INFORMACION DEL USUARIO
$query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
$result_documentos = mysqli_fetch_array($query_doccumentos);
$email_empresa_emisor  = $result_documentos['email'];
$celular_empresa_emisor  = $result_documentos['celular'];
$telefono_empresa_emisor   = $result_documentos['telefono'];
$regimen   = $result_documentos['regimen'];
$contabilidad   = $result_documentos['contabilidad'];
$url_img_upload   = $result_documentos['url_img_upload'];
$img_facturacion   = $result_documentos['img_facturacion'];
$id_e   = $result_documentos['id_e'];

$facebook   = $result_documentos['facebook'];
$instagram   = $result_documentos['instagram'];
$whatsapp   = $result_documentos['whatsapp'];
$pagina_web   = $result_documentos['pagina_web'];


//codigo para sacar info que no se puede en la factura


//CODIGO PARA SACAR INFORMACION DE LA INFORMACION DE LA GUIA DE REMISION RECIEN CREADA
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;
$ruta_factura = ''.$url2.'/home/facturacion/facturacionphp/comprobantes/guia-remision/no_firmados/'.$clave_acceso_guia_remision.'.xml';

$acceso_guia_remision = simplexml_load_file($ruta_factura);

$codDocModificado                = $acceso_guia_remision->infoTributaria->codDoc;

 //para crear el numero dl documento necesito de 4 partes
 $razonSocial                  = $acceso_guia_remision->infoTributaria->razonSocial;
 $nombreComercial              = $acceso_guia_remision->infoTributaria->nombreComercial;
 $ruc                          = $acceso_guia_remision->infoTributaria->ruc;
 $dirMatriz                    = $acceso_guia_remision->infoTributaria->dirMatriz;
  $estab                       = $acceso_guia_remision->infoTributaria->estab;
  $ptoEmi                      = $acceso_guia_remision->infoTributaria->ptoEmi;
  $secuencial                  = $acceso_guia_remision->infoTributaria->secuencial;
  $numDocModificado_guia_remision            = ''.$estab.'-'.$ptoEmi.'-'.$secuencial.'';

//informacion de la guia de Remisión

$obligadoContabilidad               = $acceso_guia_remision->infoFactura->obligadoContabilidad;
$tipo_identificacion_comprador      = $acceso_guia_remision->infoFactura->tipoIdentificacionComprador;
$identificacion_comprador           = $acceso_guia_remision->infoGuiaRemision->identificacionComprador;
$direccionComprador                 = $acceso_guia_remision->infoGuiaRemision->direccionComprador;

$dirEstablecimiento                 = $acceso_guia_remision->infoGuiaRemision->dirEstablecimiento;
$dirPartida                         = $acceso_guia_remision->infoGuiaRemision->dirPartida;
$razonSocialTransportista           = $acceso_guia_remision->infoGuiaRemision->razonSocialTransportista;
$tipoIdentificacionTransportista    = $acceso_guia_remision->infoGuiaRemision->tipoIdentificacionTransportista;
$rucTransportista                   = $acceso_guia_remision->infoGuiaRemision->rucTransportista;
$fechaIniTransporte                 = $acceso_guia_remision->infoGuiaRemision->fechaIniTransporte;
$fechaFinTransporte                 = $acceso_guia_remision->infoGuiaRemision->fechaFinTransporte;
$placa                              = $acceso_guia_remision->infoGuiaRemision->placa;

$numAutDocSustento             = $acceso_guia_remision->destinatarios->destinatario->numAutDocSustento;
$razonSocialDestinatario             = $acceso_guia_remision->destinatarios->destinatario->razonSocialDestinatario;

$identificacionDestinatario             = $acceso_guia_remision->destinatarios->destinatario->identificacionDestinatario;
$razonSocialDestinatario                = $acceso_guia_remision->destinatarios->destinatario->razonSocialDestinatario;
$dirDestinatario                        = $acceso_guia_remision->destinatarios->destinatario->dirDestinatario;
$motivoTraslado                         = $acceso_guia_remision->destinatarios->destinatario->motivoTraslado;
$ruta                                   = $acceso_guia_remision->destinatarios->destinatario->ruta;
$codDocSustento                         = $acceso_guia_remision->destinatarios->destinatario->codDocSustento;
$numDocSustento                         = $acceso_guia_remision->destinatarios->destinatario->numDocSustento;
$numAutDocSustento                      = $acceso_guia_remision->destinatarios->destinatario->numAutDocSustento;
$fechaEmisionDocSustento                = $acceso_guia_remision->destinatarios->destinatario->fechaEmisionDocSustento;




  // Acceder a los campos adicionales en infoAdicional
$infoAdicional = $acceso_guia_remision->infoAdicional->campoAdicional;

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
$email_reeptor      = $camposAdicionales['EMAIL'];



$email_empresa_emisor = str_replace('@', '&#64;', $email_empresa_emisor);

$email_receptor = str_replace('@', '&#64;', $email_reeptor);

 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura </title>
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
          <a href="https://guibis.com/code_user?code=<?php echo $id_e ?>"><img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt=""></a>


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
              <td class="td_bld">Teléfono</td>
              <td><?php echo $celular_empresa_emisor ?></td>
            </tr>
            <tr>
                    <td class="td_bld">Regimen:</td>
                    <td><?php echo $regimen ?></td>
            </tr>
          </tbody>
        </table>

      </div>

      <?php
      //CODIGO PARA SACAR LA INFORMACION DEL COMPRADOR A LA CUAL SE LE VA A ENTREGAR LOS PRODUCTOS ES DECIR A QUIEN FUE HECHA LA FACTURA Y A QUIEN VA DIRIGIDO LA GUIA DE REMISIÓN

      $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM comprobante_factura_final WHERE comprobante_factura_final.clave_acceso ='$numAutDocSustento' ");
      $data_existencia = mysqli_fetch_array($query_comprobacion_existencia);
      $ininterno = $data_existencia['id'];
      $url_file_upload = $data_existencia['url_file_upload'];

      $ruta_factura = ''.$url_file_upload.'/home/facturacion/facturacionphp/comprobantes/no_firmados/'.$numAutDocSustento.'.xml';

      $acceso_factura = simplexml_load_file($ruta_factura);

      $codDocModificado                = $acceso_factura->infoTributaria->codDoc;

       //para crear el numero dl documento necesito de 4 partes
       $razonSocial                       = $acceso_factura->infoTributaria->razonSocial;
       $nombreComercial                      = $acceso_factura->infoTributaria->nombreComercial;
       $ruc                       = $acceso_factura->infoTributaria->ruc;
       $dirMatriz                      = $acceso_factura->infoTributaria->dirMatriz;
       $estab                       = $acceso_factura->infoTributaria->estab;
       $ptoEmi                      = $acceso_factura->infoTributaria->ptoEmi;
       $secuencial                  = $acceso_factura->infoTributaria->secuencial;
       $numDocModificado              = ''.$estab.'-'.$ptoEmi.'-'.$secuencial.'';
       $identificacion_comprador             = $acceso_factura->infoFactura->identificacionComprador;



       ?>



      <div class="bloque_superior_row informacion_factura">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="td_bld">Guia de Remisión No : <?php echo $numDocModificado_guia_remision ?></td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion"><?php echo $clave_acceso_guia_remision ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Autorización</td>
              </tr>
              <tr>
                <td><?php echo $fechaAutorizacion ?></td>
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
                <td class="clave_ed_acces"><?php echo $clave_acceso_guia_remision ?></td>
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
    <?php

    $identificacionDestinatario             = $acceso_guia_remision->destinatarios->destinatario->identificacionDestinatario;
    $razonSocialDestinatario                = $acceso_guia_remision->destinatarios->destinatario->razonSocialDestinatario;
    $dirDestinatario                        = $acceso_guia_remision->destinatarios->destinatario->dirDestinatario;
    $motivoTraslado                         = $acceso_guia_remision->destinatarios->destinatario->motivoTraslado;
    $ruta                                   = $acceso_guia_remision->destinatarios->destinatario->ruta;
    $codDocSustento                         = $acceso_guia_remision->destinatarios->destinatario->codDocSustento;
    $numDocSustento                         = $acceso_guia_remision->destinatarios->destinatario->numDocSustento;
    $numAutDocSustento                      = $acceso_guia_remision->destinatarios->destinatario->numAutDocSustento;
    $fechaEmisionDocSustento                = $acceso_guia_remision->destinatarios->destinatario->fechaEmisionDocSustento;

     ?>

    <div class="parte_inermedia">
      <table>
        <tbody>
          <tr>
            <td> <span class="td_bld" >Razon Social Destinatario:</span><?php echo $razonSocialDestinatario ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >RUC/CI Destinatario:</span> <?php echo $identificacionDestinatario ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección Destinatario:</span> <?php echo $dirDestinatario ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Motivo Traslado:</span> <?php echo $motivoTraslado ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Ruta:</span> <?php echo $ruta ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Documento Sustento:</span> <?php echo $numDocSustento ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Emisión Documento Sustento:</span> <?php echo $fechaEmisionDocSustento ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Celular:</span> <?php echo $celular_comprador ?> </td>
          </tr>

          <tr>
            <td> <span class="td_bld" >Dirección Establecimiento:</span> <?php echo $dirEstablecimiento ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección Partida:</span> <?php echo $dirPartida ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Razón Social Transportista:</span> <?php echo $razonSocialTransportista ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Tipo Identificación Transportista:</span> <?php echo $tipoIdentificacionTransportista ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Inicio Transporte:</span> <?php echo $fechaIniTransporte ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Final Transporte:</span> <?php echo $fechaFinTransporte ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Placa:</span> <?php echo $placa ?> </td>
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
                  <th class="th_productos">Cod.</th>
                  <th class="th_productos">Cant.</th>
                  <th class="th_productos">Descrip.</th>
                  <th class="th_productos">Nota Extra 1</th>
                  <th class="th_productos">Nota Extra 2</th>
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

                  // Dividir la cadena en partes usando '-' como delimitador
                    $partes = explode('/', $descripcion_producto);

                    // Asignar cada parte a una variable
                    $nombreProducto = isset($partes[0]) ? $partes[0] : '';
                    $descripcionProducto = isset($partes[1]) ? $partes[1] : '';
                    $detalleExtra = isset($partes[2]) ? $partes[2] : '';
                    $detalleExtra2 = isset($partes[3]) ? $partes[3] : '';
                 ?>
                <tr>
                  <td class="td_productos">
                    <?php if ($codigoPrincipal != 0): ?>
                      <a href="https://guibis.com/producto?codigo=<?php echo $codigoPrincipal ?>"><?php echo ($codigoPrincipal); ?></a>
                    <?php endif; ?>
                    <?php if ($codigoPrincipal == 0): ?>
                      <?php echo ($codigoPrincipal); ?>
                    <?php endif; ?>

                    </td>
                  <td class="td_productos"><?php echo $cantidad; ?></td>

                  <td class="td_productos">
                    <?php if ($codigoPrincipal != 0): ?>
                      <a href="https://guibis.com/producto?codigo=<?php echo $codigoPrincipal ?>"><?php echo ($nombreProducto); ?> <?php echo ($descripcionProducto); ?></a>
                    <?php endif; ?>
                    <?php if ($codigoPrincipal == 0): ?>
                    <?php echo ($nombreProducto); ?> <?php echo ($descripcionProducto); ?>
                    <?php endif; ?>

                    </td>
                  <td class="td_productos"><?php echo $detalleExtra; ?></td>
                  <td class="td_productos"><?php echo $detalleExtra; ?></td>
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
                <td> <span class="td_bld" >Email Cliente:</span> <span><?php echo $email_reeptor ?>


                </span> </td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld " >Teléfono Empresa:</span>  <span><?php echo $telefono_empresa_emisor ?></span> </td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld" >Direccion Cliente: </span> <span><?php echo $direccion_comprador ?></span> </td>
              </tr>

               <?php if (!empty($nota_extra)): ?>
                 <tr>
                   <td class="td_info_gjd"> <span class="td_bld" >Nota Extras: </span> <span><?php echo $nota_extra ?></span> </td>
                 </tr>
               <?php endif; ?>
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



    </div>

    <?php

    //codigo para saber como va el emisor

      $nombre_salida = $razonSocial;


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
