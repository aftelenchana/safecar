<?php

// Reportar todos los errores de PHP (ver el manual de PHP para más niveles de errores)
error_reporting(E_ALL);

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$iduser = $_GET['id'];
$clave_acc_guardar = $_GET['clave_acc_guardar'];
$codigo_factura    = $_GET['codigo_factura'];
$fechaAutorizacion = $_GET['fechaAutorizacion'];
$empresa           = $_GET['empresa'];




$query_empresa_cookie = mysqli_query($conection, "SELECT * FROM empresas_registradas
   WHERE   empresas_registradas.id = '$empresa' ");
$data_empresa_cookie = mysqli_fetch_array($query_empresa_cookie);


//CODIGO PARA SACAR INFORMACION DEL USUARIO
$query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
$result_documentos = mysqli_fetch_array($query_doccumentos);
$email_empresa_emisor  = $result_documentos['email'];
$id_e   = $result_documentos['id_e'];

$facebook   = $result_documentos['facebook'];
$instagram   = $result_documentos['instagram'];
$whatsapp   = $result_documentos['whatsapp'];
$pagina_web   = $result_documentos['pagina_web'];




//codigo para sacar info que no se puede en la factura

			$query_resultados_emmisor = mysqli_query($conection,"SELECT * FROM comprobantes
			WHERE id_emisor= '$iduser' AND secuencial = '$codigo_factura' ORDER BY comprobantes.fecha DESC");
			$data__emmisor=mysqli_fetch_array($query_resultados_emmisor);

			$idcliente       					 	 = $data__emmisor['id_receptor'];


//CODIGO PARA SACAR INFORMACION DE L FACTURA NO FIRMADA
//de qui empezamos a sacar la informacion
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;
$ruta_factura = ''.$url2.'/home/facturacion/facturacionphp/comprobantes/no_firmados/'.$iduser.''.$empresa.'veifyguibis.xml';


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

//informacion del comprador

$obligadoContabilidad             = $acceso_factura->infoFactura->obligadoContabilidad;
$tipo_identificacion_comprador         = $acceso_factura->infoFactura->tipoIdentificacionComprador;



  $identificacion_comprador             = $acceso_factura->infoFactura->identificacionComprador;
  $direccionComprador             = $acceso_factura->infoFactura->direccionComprador;



  $tipo_identificacion_comprador         = $acceso_factura->infoFactura->tipoIdentificacionComprador;
  $razon_social_comprador                = $acceso_factura->infoFactura->razonSocialComprador;
  $obligadoContabilidad                = $acceso_factura->infoFactura->obligadoContabilidad;
  $fechaEmision                = $acceso_factura->infoFactura->fechaEmision;
  $totalSinImpuestos                = $acceso_factura->infoFactura->totalSinImpuestos;
  $totalDescuento                = $acceso_factura->infoFactura->totalDescuento;

  $importeTotal                = $acceso_factura->infoFactura->importeTotal;


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
          <a href="https://guibis.com/code_user?code=<?php echo $id_e ?>"><img src="<?php echo $data_empresa_cookie['url'] ?>/home/img/uploads/<?php echo $data_empresa_cookie['img_empresa'] ?>" alt=""></a>


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
              <td> <?php echo $dirMatriz ?></td>
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
                <td class="td_bld">Factura No :<?php echo $estab ?>-<?php echo $ptoEmi ?>-<?php echo $secuencial ?></td>
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
                <td><?php echo $fechaAutorizacion ?></td>
              </tr>
              <tr>
                <td class="td_bld">OBLIGADO A LLEVAR CONTABILIDAD:<?php echo $data_empresa_cookie['contabilidad']  ?></td>

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
            <td> <span class="td_bld" >RUC/CI:</span> <?php echo $identificacion_comprador ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección:</span> <?php echo $direccion_comprador ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Celular:</span> <?php echo $celular_comprador ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Fecha Emisión:</span> <?php echo $fechaEmision ?> </td>
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
                  <th class="th_productos">P/U</th>
                  <th class="th_productos">DSCT</th>
                  <th class="th_productos">IVA</th>
                  <th class="th_productos">Sub Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $facto =  110;

                $totalCantidad = 0; // Inicializar la variable acumuladora												//CABECERA KARDEX TOTALES

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

                  $totalCantidad += $cantidad;

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
                  <td class="td_productos"><?php echo ($precioUnitario); ?></td>
                  <td class="td_productos">$<?php echo ($descuento); ?></td>
                  <td class="td_productos">$<?php echo ($valor); ?></td>
                  <td class="td_productos">$<?php echo ($baseImponible); ?></td>
                </tr>

                <?php
                }
            ?>

            <tr>
              <td class="td_productos"></td>
              <td class="td_productos"><?php echo $totalCantidad; ?></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
              <td class="td_productos"></td>
            </tr>
              </tbody>



            </table>
          </div>
        </div>


<br><br><br><br>

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
                  <?php
                  $query__correos_email = mysqli_query($conection, "SELECT *  FROM lista_correos_envios_email_cliente
                     WHERE lista_correos_envios_email_cliente.iduser ='$iduser'  AND lista_correos_envios_email_cliente.estatus = '1' AND lista_correos_envios_email_cliente.cliente = '$idcliente'
                  ORDER BY `lista_correos_envios_email_cliente`.`fecha` DESC ");
                   $result__email= mysqli_num_rows($query__correos_email);
                  if ($result__email > 0) {
                        while ($data_email=mysqli_fetch_array($query__correos_email)) {
                          $email_cliente = $data_email['correo'];
                          echo "$email_cliente"."<br>";
                        }
                      }
                   ?>

                </span> </td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld " >Celular Empresa:</span>  <span><?php echo $data_empresa_cookie['celular'] ?></span> </td>
              </tr>
              <tr class="tr_info_gjd" >
                <td class="td_info_gjd"> <span class="td_bld" >Direccion Cliente: </span> <span><?php echo $direccion_comprador ?></span> </td>
              </tr>
              <?php

              $query_nota = mysqli_query($conection, "SELECT * FROM notas_extras_facturacion   WHERE iduser = '$iduser'
              AND codigo_factura = '$codigo_factura' AND codigo_factura = '$codigo_factura'");
              $data_nota = mysqli_fetch_array($query_nota);

              if ($data_nota) {
                $nota_extra = $data_nota['texto'];
              }else {
                $nota_extra = '';
              }
               ?>
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

          <?php

          $nombre_formas_pago_map = [
              '01' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO',
              '15' => 'COMPESACION DE DE DEUDAS',
              '16' => 'TARJETA DE DEBITO',
              '17' => 'DINERO ELECTRONICO',
              '18' => 'TARJETA PREPAGO',
              '19' => 'TARJETA DE CREDITO',
              '20' => 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO',
              '21' => 'ENDOSO DE TITULOS',
              // ...otros mapeos necesarios...
            ];

           ?>




            <table class="formas_pago_table">

                <thead>
                  <tr>
                    <th class="th_productos">Forma de Pago</th>
                    <th class="th_productos">Cantidad</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Itera sobre cada método de pago en el XML
                  foreach ($acceso_factura->infoFactura->pagos->pago as $metodo) {
                    $formaPago = (string)$metodo->formaPago;
                    $total = (string)$metodo->total;
                    $nombre_formas_pago = isset($nombre_formas_pago_map[$formaPago]) ? $nombre_formas_pago_map[$formaPago] : 'DESCONOCIDO';
                  ?>
                    <tr>
                      <td class="td_productos"><?php echo htmlspecialchars($nombre_formas_pago); ?></td>
                      <td class="td_productos">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>

            </table>




        </div>
      </div>


      <?php
      // Inicializa los totales para cada tipo de impuesto
      $totalesPorTipo = [
          'base0' => 0,
          'base12' => 0,
          'iva12' => 0,
          'base14' => 0,
          'iva14' => 0,
          'base15' => 0,
          'iva15' => 0,
          'base5' => 0,
          'iva5' => 0,
          'basedif' => 0,
          'ivadif' => 0,
          'base13' => 0,
          'iva13' => 0,
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
                 case '3': // IVA 12%
                     $totalesPorTipo['base14'] += $baseImponible;
                     $totalesPorTipo['iva14'] += $valor;
                     break;

                 case '4': // IVA 12%
                     $totalesPorTipo['base15'] += $baseImponible;
                     $totalesPorTipo['iva15'] += $valor;
                     break;
                 case '5': // IVA 12%
                     $totalesPorTipo['base5'] += $baseImponible;
                     $totalesPorTipo['iva5'] += $valor;
                     break;

                  case '6': // No objeto de IVA
                      $totalesPorTipo['noObjeto'] += $baseImponible;
                      break;
                  case '7': // Exento de IVA
                      $totalesPorTipo['exentoIVA'] += $baseImponible;
                      break;
                  case '8': // IVA 12%
                  $totalesPorTipo['basedif'] += $baseImponible;
                  break;

                  case '10': // IVA 12%
                      $totalesPorTipo['base13'] += $baseImponible;
                      $totalesPorTipo['iva13'] += $valor;
                      break;
                  // Si hay más códigos, agregar casos adicionales
              }
          }
      }

      // Calcula el subtotal y el total a pagar
      $subtotal = $totalesPorTipo['base0'] + $totalesPorTipo['base12'] + $totalesPorTipo['noObjeto'] + $totalesPorTipo['exentoIVA']
      + $totalesPorTipo['base14']+ $totalesPorTipo['base15']+ $totalesPorTipo['base5']+ $totalesPorTipo['basedif']+ $totalesPorTipo['base13'];
      $totalPagar = $subtotal + $totalesPorTipo['iva12']+ $totalesPorTipo['iva14']+ $totalesPorTipo['iva15']+ $totalesPorTipo['iva5']
      + $totalesPorTipo['iva13'];
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
                      <td class="td_bld td_resumen">Bas.Imp. 0%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base0'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. 12%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base12'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. 14%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base14'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. 15%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base15'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. 5%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base5'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. 13%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['base13'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Bas.Imp. Diferenciado</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['basedif'], 2); ?></td>
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
                      <td class="td_bld td_resumen">IVA 12%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva12'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">IVA 14%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva14'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">IVA 15%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva15'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">IVA 5%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva5'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">IVA 13%</td>
                      <td class="td_resumen">$<?php echo number_format($totalesPorTipo['iva13'], 2); ?></td>
                  </tr>
                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Descuento</td>
                      <?php
                      $totalDescuento = floatval($totalDescuento); ?>
                      <td class="td_resumen">$<?php echo number_format($totalDescuento, 2); ?></td>
                  </tr>


                  <tr class="tr_resumen">
                      <td class="td_bld td_resumen">Subtotal</td>
                      <td class="td_resumen">$<?php echo number_format($subtotal, 2); ?></td>
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
