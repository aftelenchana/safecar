<?php
include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar


  $codigo_factura          = $_GET['codigo_factura'];
  $iduser                  = $_GET['iduser'];


  //SACAMOS INFORMACION CON EL CODIGO DE LA VENTA DE LA TABLA VENTA FACTURACION

  $query_comprobacion_existencia = mysqli_query($conection,"SELECT * FROM ventas_documentos WHERE ventas_documentos.id ='$codigo_factura' ");
  $data_comprobante = mysqli_fetch_array($query_comprobacion_existencia);

  $empresa              = $data_comprobante['empresa'];
  $sucursal_facturacion = $data_comprobante['sucursal'];

  $clave_acceso_factura  = $data_comprobante['clave_acceso'];
  $n_documento           = $data_comprobante['n_documento'];
  $fecha_emision_factura = $data_comprobante['fecha_emision_factura'];



  $query_factura = mysqli_query($conection, "SELECT * FROM comprobante_factura_final WHERE  comprobante_factura_final.clave_acceso = '$clave_acceso_factura'");
  $data_factura =mysqli_fetch_array($query_factura);

  $id_comprador            = $data_factura['id_receptor'];
  $razon_social_comprador  = $data_factura['nombres_receptor'];
  $email_comprador         = $data_factura['email_receptor'];
  $identificacion_comprador= $data_factura['cedula_receptor'];

  $direccion_comprador        = $data_factura['direccion_receptor'];
  $celular_comprador          = $data_factura['celular_receptor'];

  $tipo_identificacion_comprador  = $data_factura['tipo_identificacion'];
  $email_reeptor                  = $data_factura['email_receptor'];


  $fecha_emision_barra = date("Y/m/d");

  $razon_modficiacion  = 'Procesando....';


  $query_empresas = mysqli_query($conection, "SELECT * FROM empresas_registradas
    WHERE   empresas_registradas.estatus = 1
    AND empresas_registradas.id = '$empresa' ");
    $data_empresa = mysqli_fetch_array($query_empresas);

    $numero_digitos = $data_empresa['numero_digitos'];



 $query_verificador_existencia_sucursal = mysqli_query($conection, "SELECT * FROM sucursales WHERE  sucursales.id = '$sucursal_facturacion'");
  $data_sucursal =mysqli_fetch_array($query_verificador_existencia_sucursal);

  $direccion_sucursal        = $data_sucursal['direccion_sucursal'];

  $estableciminento_f  = str_pad($data_sucursal['establecimiento'], 3, "0", STR_PAD_LEFT);
  $punto_emision_f  = str_pad($data_sucursal['punto_emision'], 3, "0", STR_PAD_LEFT);

  $fecha_actual = date("d-m-Y");
  $fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));


  //codigo para sacar la secuencia del usuario

  $establecimiento_sinceros        = $data_sucursal['establecimiento'];
  $punto_emision_sinceros        = $data_sucursal['punto_emision'];

  $query_secuencia = mysqli_query($conection, "SELECT * FROM  comprobante_nota_credito  WHERE  comprobante_nota_credito.id_emisor  = '$iduser' AND comprobante_nota_credito.punto_emision ='$punto_emision_sinceros'
    AND comprobante_nota_credito.establecimiento ='$establecimiento_sinceros' AND  comprobante_nota_credito.empresa = '$empresa'   ORDER BY id DESC");
   $result_secuencia = mysqli_fetch_array($query_secuencia);
   if ($result_secuencia) {
     $secuencial = $result_secuencia['secuencia'];
     $secuencial = $secuencial +1;
     // code...
   }else {
     $secuencial =1;
   }
   $secuencial_actual_segundo = str_pad($secuencial, 9, "0", STR_PAD_LEFT);



   $query_empresa_cookie = mysqli_query($conection, "SELECT * FROM empresas_registradas
      WHERE   empresas_registradas.id = '$empresa' ");
   $data_empresa_cookie = mysqli_fetch_array($query_empresa_cookie);


  $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
  $result_documentos = mysqli_fetch_array($query_doccumentos);
  $regimen = $result_documentos['regimen'];
  $contabilidad             = $result_documentos['contabilidad'];
  $email_empresa_emisor     = $result_documentos['email'];
  $celular_empresa_emisor   = $result_documentos['celular'];
  $telefono_empresa_emisor  = $result_documentos['telefono'];
  $direccion_emisor          = $result_documentos['direccion'];
  $nombres                  = $result_documentos['nombres'];
  $apellidos                = $result_documentos['apellidos'];
  $numero_identificacion_emisor  = $result_documentos['numero_identidad'];
  $contribuyente_especial   = $result_documentos['contribuyente_especial'];

  $contabilidad            = $result_documentos['contabilidad'];
  $img_facturacion         = $result_documentos['img_facturacion'];
  $contabilidad            = $result_documentos['contabilidad'];
  $regimen                 = $result_documentos['regimen'];
  $url_img_upload          = $result_documentos['url_img_upload'];
  $razon_social          = $result_documentos['razon_social'];

    $facebook                = $result_documentos['facebook'];
    $pagina_web                = $result_documentos['pagina_web'];
    $instagram           = $result_documentos['instagram'];
    $whatsapp             = $result_documentos['whatsapp'];



  $fecha_actual = date("d-m-Y");
  $fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));

  $nombre_empresa      = $result_documentos['nombre_empresa'];


  $clave_acc_guardar= $fecha.'01'.$numero_identificacion_emisor.'1'.$estableciminento_f.$punto_emision_f.$secuencial_actual_segundo.'123456781';
  new barCodeGenrator(''.$clave_acc_guardar.'', 1, 'barra.gif', 455, 60, false);

  //fechas
  $fecha_actual = date("d-m-Y");
  $fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));




  $email_empresa_emisor = str_replace('@', '&#64;', $email_empresa_emisor);



  $query_lista_t = mysqli_query($conection,"SELECT SUM(((comprobantes.cantidad_producto)*(comprobantes.valor_unidad))) as
  'compra_total', SUM(((comprobantes.iva_producto))) AS 'iva_general',
  SUM(((comprobantes.precio_neto)+(comprobantes.iva_producto))) AS 'precioncluido_iva',SUM(comprobantes.descuento) AS 'descuento_total',
    SUM(comprobantes.cantidad_producto) AS 'cantidad_productos'
  FROM `comprobantes`
  WHERE comprobantes.id_emisor = '$iduser'  AND comprobantes.secuencial = '$codigo_factura'");
  $data_lista_t=mysqli_fetch_array($query_lista_t);

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nota de Crédito Previsualizada <?php echo $n_documento ?></title>
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
          <img src="<?php echo $data_empresa_cookie['url'] ?>/home/img/uploads/<?php echo $data_empresa_cookie['img_empresa'] ?>" alt="">
        </div>
        <table>
          <tbody>
            <tr>
              <td class="td_bld">Emisor:</td>
              <td class="celda_confogurar"><?php echo $data_empresa_cookie['razon_social'] ?></td>
            </tr>
            <?php if (!empty($data_empresa_cookie['nombre_empresa'])): ?>
              <tr>
                <td class="td_bld">Nombre Comercial:</td>
                <td class="celda_confogurar"><?php echo $data_empresa_cookie['nombre_empresa'] ?></td>
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
              <td><?php echo $direccion_sucursal ?></td>
            </tr>
            <tr>
              <td class="td_bld">Ruc:</td>
              <td><?php echo $data_empresa_cookie['identificacion'] ?></td>
            </tr>
            <tr>
              <td class="td_bld">Correo:</td>
              <td class="celda_confogurar" ><?php echo $data_empresa_cookie['email'] ?></td>
            </tr>
            <tr>
              <td class="td_bld">Teléfono</td>
              <td><?php echo $data_empresa_cookie['telefono'] ?></td>
            </tr>
            <tr>
                    <td class="td_bld">Regimen:</td>
                    <td><?php echo $data_empresa_cookie['regimen'] ?></td>
            </tr>
          </tbody>
        </table>

      </div>
      <div class="bloque_superior_row informacion_factura">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="td_bld">Previzualización Nota de Crédito No :<?php echo $estableciminento_f ?>-<?php echo $punto_emision_f ?>-<?php echo $secuencial_actual_segundo ?></td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización Previzualización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion"><?php echo $clave_acc_guardar ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Previzualización</td>
              </tr>
              <tr>
                <td><?php echo $fecha ?></td>
              </tr>
              <tr>
                <td class="td_bld">OBLIGADO A LLEVAR CONTABILIDAD:<?php echo $contabilidad  ?></td>

              </tr>
              <tr>
                <td class="td_bld">Ambiente:Pruebas</td>
              </tr>
              <tr>
                <td class="td_bld">EMISIÓN :Previzualización</td>
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
            <td> <span class="td_bld" >Fecha Emisión:</span><?php echo $fecha_emision_barra ?> </td>
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
            <td> <span class="td_bld" >Fecha Emisión Comprobante Módifica:</span> <?php echo $fecha_emision_factura ?></td>
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
                  <th class="th_productos">Referencia</th>
                  <th class="th_productos">Cantidad</th>
                  <th class="th_productos">Descripcion</th>
                  <th class="th_productos">P/U</th>
                  <th class="th_productos">DSCT</th>
                  <th class="th_productos">IVA</th>
                  <th class="th_productos">Sub Total</th>
                  <th class="th_productos">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query_resultados = mysqli_query($conection, "SELECT *
                    FROM productos_ventas_contabilidad
                    WHERE productos_ventas_contabilidad.compra = '$codigo_factura'
                    AND productos_ventas_contabilidad.estatus = '1'
                    AND productos_ventas_contabilidad.precionotacredito != ''
                    AND productos_ventas_contabilidad.precionotacredito != '0'
                    AND productos_ventas_contabilidad.cantidadnotacredito != ''
                    AND productos_ventas_contabilidad.cantidadnotacredito != '0'");
                while ($data_lista=mysqli_fetch_array($query_resultados)) {

                  $tarifasIVA = [
                      '0' => 0,
                      '2' => 12,
                      '3' => 14,
                      '4' => 15,
                      '5' => 5,
                      '6' => 0,
                      '7' => 0,
                      '8' => 0,
                      '10' => 13
                  ];

                  $tipoAmbiente = $data_lista['codigoPorcentajenotacredito'];
                  $porcentajeIVA = array_key_exists($tipoAmbiente, $tarifasIVA) ? $tarifasIVA[$tipoAmbiente] : 0.00;

                  $tarifa = $porcentajeIVA;
                  $precio_total = $data_lista['precionotacredito'];
                  $cantidad_productos = $data_lista['cantidadnotacredito'];

                  $base_imponible = $precio_total / (1 + ($porcentajeIVA / 100));
                  $valor_impuesto = $base_imponible * ($porcentajeIVA / 100);
                  $precio_unitario = $base_imponible / $cantidad_productos;

                  $cod_principal = $data_lista['cod_principal'];


                  $query_productos = mysqli_query($conection, "SELECT * FROM producto_venta
                     WHERE producto_venta.idproducto ='$cod_principal' ");

                     $existencia_producto  = mysqli_num_rows($query_productos);

                     if ($existencia_producto > 0) {
                       $data_productos = mysqli_fetch_assoc($query_productos);
                       $codigo_secundadio = $data_productos['codigo_extra'];
                     }else {
                       $codigo_secundadio = '';
                     }


                 ?>
                 <tr>
                   <td class="td_productos"><?php echo ($data_lista['cod_principal']); ?></td>
                   <td class="td_productos"><?php echo ($codigo_secundadio); ?></td>
                   <td class="td_productos"><?php echo $data_lista['cantidadnotacredito']; ?></td>
                   <td class="td_productos"><?php echo $data_lista['nombre_producto']; ?></td>
                   <td class="td_productos"><?php echo number_format($precio_unitario,2); ?></td>
                   <td class="td_productos">$0</td>
                   <td class="td_productos">$<?php echo number_format($valor_impuesto,2); ?></td>
                   <td class="td_productos">$<?php echo number_format($base_imponible,2); ?></td>
                   <td class="td_productos">$<?php echo number_format($precio_total,2); ?></td>
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
            </tbody>
          </table>
        </div>
      </div>



      <?php
      $tarifasIva = ['0', '2', '3', '4', '5', '6', '7', '8', '10']; // Agregar todos los códigos necesarios

      // Inicializar un arreglo para almacenar los totales por tipo de tarifa de IVA
      $totalesPorTarifa = [];

      // Total general
      $compra_general = 0;

      $descuento_total = 0;

      foreach ($tarifasIva as $tarifa) {
          // Realizar la consulta para cada tarifa de IVA
          $query = mysqli_query($conection, "SELECT SUM((comprobantes.cantidad_producto * comprobantes.valor_unidad)) as 'compra_total',
                                              SUM(comprobantes.iva_frontend) AS 'iva_general',
                                              SUM((comprobantes.precio_neto + comprobantes.iva_producto)) AS 'precioncluido_iva',
                                              SUM(comprobantes.descuento) AS 'descuento_total'
                                              FROM `comprobantes`
                                              WHERE comprobantes.id_emisor = '$iduser'
                                              AND comprobantes.secuencial = '$codigo_factura'
                                              AND comprobantes.tipo_ambiente = '$tarifa'");
          $data = mysqli_fetch_array($query);

          // Calcular el total para esta tarifa después del descuento
          $totalesPorTarifa[$tarifa] = $data['compra_total'] - $data['descuento_total'];

          // Agregar al total general
          $compra_general += $totalesPorTarifa[$tarifa];

          // Acumular el total de descuentos
         $descuento_total += $data['descuento_total'];

          }





       ?>
       <style media="screen">
         .tabla_resumen_fernando{
                     border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
         }
       </style>

       <?php

       $query_sumas = mysqli_query($conection, "SELECT SUM((productos_ventas_contabilidad.precio_compra) ) as 'precio_compra',
       SUM((productos_ventas_contabilidad.valor) ) as 'valor',
       SUM((productos_ventas_contabilidad.descuento*productos_ventas_contabilidad.cantidad) ) as 'descuento'
      FROM `productos_ventas_contabilidad`
      WHERE productos_ventas_contabilidad.compra = '$codigo_factura'");
       $data_sumas = mysqli_fetch_array($query_sumas);



       $query_sumas_notas_credito = mysqli_query($conection, "SELECT SUM((productos_ventas_contabilidad.precionotacredito) ) as 'preciototalcredito',
       SUM((productos_ventas_contabilidad.valor_notacredito) ) as 'valorcredito'
      FROM `productos_ventas_contabilidad`
      WHERE productos_ventas_contabilidad.compra = '$codigo_factura'");
       $data_sumas_nora_credito = mysqli_fetch_array($query_sumas_notas_credito);


      $valorModificacion_credito = $data_sumas_nora_credito['preciototalcredito'];


        ?>


        <div class="informacion_financiero_bancario">
          <div class="">
            <table class="tabla_resumen_fernando">
              <tbody>
                <tr class="tr_resumen">
                  <td class="td_bld td_resumen" >Total Nóta de Crédito:</td>
                  <td class="td_resumen">$<?php echo number_format($valorModificacion_credito,2) ?></td>
                </tr>
              </tbody>
            </table>

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
