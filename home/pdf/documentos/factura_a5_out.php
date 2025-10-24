<?php
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

$iduser = $_GET['iduser'];
$factura = $_GET['factura'];


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


$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url2 = $protocol . $domain;
$ruta_factura = ''.$url2.'/home/facturacion/facturacionphp/comprobantes/no_firmados/'.$factura.'.xml';

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
    <meta charset="utf-8">
    <title>Factura</title>
  </head>
  <body>
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
      height: 115px;
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

    <div class="parte_superior">
      <div class="bloque_superior_row informacion_emisor">
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
                <td class="numero_autorzaxion"><?php echo $factura ?></td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Emisión</td>
              </tr>
              <tr>
                <td><?php echo $fechaEmision ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>


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


    <style media="screen">
    .informacion_firmado{
      font-size: 13px;
      text-align: center;
    }

    .negrita{
      font-weight: bold;
    }

    .bloque_gene_firmado{
      width: 50%;
      display: inline-block;
      vertical-align: top; /* Añadir alineación vertical */
    }

    /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
    .bloque_gene_firmado table {
      text-align: center;
      width: 100%;
    }

    </style>


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
    text-align: center;
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
       .contendeor_precio_final_entrega{
         text-align: center;
       }
     </style>
     <div class="contendeor_precio_final_entrega">
       <p>$<?php echo number_format($totalPagar, 2); ?></p>
     </div>



  </div>


  <style media="screen">
  .informacion_firmado{
    font-size: 13px;
    text-align: center;
  }

  .negrita{
    font-weight: bold;
  }

  .bloque_gene_firmado{
    width: 50%;
    display: inline-block;
    vertical-align: top; /* Añadir alineación vertical */
  }

  /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
  .bloque_gene_firmado table {
    text-align: center;
    width: 100%;
  }

  </style>

    <div class="informacion_firmado">

      <div class="bloque_gene_firmado">
        <table>
          <tr>
            <td class="negrita">_____________________</td>
          </tr>
          <tr>
            <td class="negrita">Entregado</td>
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
