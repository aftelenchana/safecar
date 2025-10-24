<?php



ob_start();

include "../coneccion.php";

  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

      session_start();

      if (empty($_SESSION['active'])) {

        header('location:/');

      }else {

      }

       $iduser= $_SESSION['id'];

       $query_config = mysqli_query($conection, "SELECT * FROM configuraciones");

        $result_config = mysqli_fetch_array($query_config);

        if (!empty($result_config['foto_representativa'])) {

          $foto_representativa = $result_config['foto_representativa'];

        }else {

          $foto_representativa ='subir.png';

        }



        $id_user= $_SESSION['id'];

        $query = mysqli_query($conection, "SELECT * FROM usuarios WHERE usuarios.id = $id_user");

        $result = mysqli_fetch_array($query);

        $mi_leben = $result['mi_leben'];

        $nombres_user = $result['nombres'];


           include "scripts/cuenta_pago.php";
  ?>

<!DOCTYPE html>

<html lang="es">

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Generar Factura</title>

  <?php   if (!empty($result_config['foto_representativa'])) {

     $foto_representativa = $result_config['foto_representativa'];

   }else {

     $foto_representativa ='subir.png';

   } ?>

  <link rel="icon" href="https://guibis.com/home/img/reacciones/guibis.png">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <link rel="stylesheet" href="../ricrey/theme_responsive/plugins/fontawesome-free/css/all.min.css">

  <link rel="stylesheet" href="../ricrey/theme_responsive/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

  <link rel="stylesheet" href="../ricrey/theme_responsive/dist/css/adminlte.min.css">

  <link rel="stylesheet" href="../ricrey/theme_responsive/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">

  <link rel="stylesheet" href="../ricrey/theme_responsive/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <link rel="stylesheet" href="../ricrey/theme_responsive/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <link rel="stylesheet" href="estiloshome/load.css">

  <link rel="stylesheet" type="text/css" href="/code_garantia/resources/css/framework/semantic/semantic.min.css">

  <link rel="stylesheet" type="text/css" href="/code_garantia/resources/css/hoja_de_estilos_generales.css">

  <link rel="stylesheet" type="text/css" href="/code_garantia/resources/css/hoja_de_estilos_lector.css">

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <script  src="/code_garantia/resources/js/jsQR.js"></script>



</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">

<div class="wrapper">

<div class="notificacion_factura_2" id="notificacion_factura_2">



</div>





 <?php include "scripts/menu-superior.php"; ?>



  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <div class="content-header">

      <div class="container-fluid">

        <div class="row mb-2">

          <div class="col-sm-6">

            <h1 class="m-0">Generar Facturas</h1>

          </div><!-- /.col -->

          <div class="col-sm-6">

            <ol class="breadcrumb float-sm-right">

              <li class="breadcrumb-item"><a href="#">Inicio</a></li>

              <li class="breadcrumb-item active">Generar Facturas</li>



            </ol>

          </div><!-- /.col -->

        </div><!-- /.row -->

      </div><!-- /.container-fluid -->

    </div>

    <!-- /.content-header -->

    <?php

  $fecha=date('d/m/Y');

  $query = mysqli_query($conection, "SELECT * FROM  comprobante_factura_final  WHERE  comprobante_factura_final.id_emisor  = '$iduser' ORDER BY fecha DESC");

  $query_cantidad = mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");

  $result_cantidad = mysqli_fetch_array($query_cantidad);

  $documentos_electronicos = $result_cantidad['documentos_electronicos'];

  $estableciminento_f = $result_cantidad['estableciminento_f'];

  $punto_emision_f = $result_cantidad['punto_emision_f'];

    $result = mysqli_fetch_array($query);

    if ($result) {

      $secuencial = $result['codigo_factura'];

      $secuencial = $secuencial +1;

      // code...

    }else {

      $secuencial =1;

    }

    $secuencial = str_pad($secuencial, 9, "0", STR_PAD_LEFT);

    $estableciminento_f = str_pad($estableciminento_f, 3, "0", STR_PAD_LEFT);

    $punto_emision_f = str_pad($punto_emision_f, 3, "0", STR_PAD_LEFT);









   ?>



   <?php

$query_resultados_ff = mysqli_query($conection,"SELECT * FROM comprobantes

WHERE id_emisor= '$iduser'");

$resultados_ff = mysqli_fetch_array($query_resultados_ff);





   if (empty($resultados_ff['nombres_receptor'])) {

       $nombres_receptor   = '';

   }else {

     $nombres_receptor   = $resultados_ff['nombres_receptor'];

   }

   if (empty($resultados_ff['celular_receptor'])) {

       $celular_receptor   = '';

   }else {

     $celular_receptor   = $resultados_ff['celular_receptor'];

   }



   if (empty($resultados_ff['numero_identidad_receptor'])) {

       $cedula_receptor    = '';

   }else {

       $cedula_receptor    = $resultados_ff['numero_identidad_receptor'];

   }



   if (empty($resultados_ff['email_reeptor'])) {

     $email_recepto      = '';

   }else {

     $email_recepto      = $resultados_ff['email_reeptor'];

   }

   if (empty($resultados_ff['direccion_reeptor'])) {

     $direccion_receptor = '';

   }else {

     $direccion_receptor = $resultados_ff['direccion_reeptor'];

   }

   if (empty($resultados_ff['id_receptor'])) {

     $id_receptor = '0';

   }else {

     $id_receptor = $resultados_ff['id_receptor'];

   }

   if (empty($resultados_ff['tipo_identificacion'])) {

     $tipo_identificacion = '0';

   }else {

     $tipo_identificacion = $resultados_ff['tipo_identificacion'];

   }

?>


<!-- Main content -->

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box">
          <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Factura N°:</span>
            <span class="info-box-number">
            <?php echo "$estableciminento_f-$punto_emision_f-$secuencial"?>
            </span>
          </div>
        </div>
      </div>

      <!-- /.col -->

      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Fecha: </span>
            <span class="info-box-number"><?php echo $fecha ?></span>
          </div>
        </div>


      </div>

      <!-- /.col -->



      <!-- fix for small devices only -->

      <div class="clearfix hidden-md-up"></div>
      <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Facturas:</span>
            <span class="info-box-number">Ilimitadas</span>
          </div>
        </div>
      </div>
</section>



    <div class="modal fade bd-example-modal-lg" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header header_guibis" style="background-color: #263238;">
            <h5 class="modal-title" id="exampleModalLongTitle" style="">AGREGAR METODO DE PAGO Y COMPRADOR </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <form class="" method="post" name="agregar_usuario_unico_bg" id="agregar_usuario_unico_bg" onsubmit="event.preventDefault(); sendData_agregar_usuaio_unico_bg();" >
                <div class="row">
                <div class="col" style="text-align: center;">
                    <h5>Agregar Usuario</h5>
                </div>
                </div>
               <div class="row">
                 <div class="col">
                   <div class="contenedor_select">
                     <input type="hidden" name="action" value="buscar_usuarios_id">
                   <select   class="form-control" name="datos" id="search_usuarios" >
                     <?php
                     $query_clientes = mysqli_query($conection,"SELECT * FROM clientes WHERE  clientes.iduser= '$iduser'   AND clientes.estatus = 1");
                     while ($clientes = mysqli_fetch_array($query_clientes)) {
                       echo '<option  value="'.$clientes['id'].'">'.$clientes['nombres'].'/ '.$clientes['identificacion'].'</option>';
                     }
                      ?>
                     </select>
                   </div>
                 </div>
                 <div class="col">
                   <button type="submit" class="btn btn-info">Agregar</button>
                   <div class="noti_ad_ususario_bg">
                   </div>
                 </div>
               </div>
              </form>
            <form class="" method="post" name="enviar_facturar" id="enviar_facturar" onsubmit="event.preventDefault(); sendData_enviar_facturar();" >
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6">
                    <h5>Agrega una Forma de Pago</h5>
                      <div class="">
                      <select   class="form-control" name="formas_pago" required>
                           <option value="01">SIN UTILIZACION DEL SISTEMA FINANCIERO</option>
                           <option value="15">COMPESACION DE DE DEUDAS</option>
                           <option value="16">TARJETA DE DEBITO</option>
                           <option value="17">DINERO ELECTRONICO</option>
                           <option value="18">TARJETA PREPAGO</option>
                           <option value="19">TARJETA DE CREDITO</option>
                           <option value="20">OTROS CON UTILIZACION DEL SISTEMA FINANCIERO</option>
                           <option value="21">ENDOSO DE TITULOS</option>
                        </select>
                      </div>

                 <br>

                   <h5>Seleccione Documento</h5>
                     <div class="">
                       <select class="form-control form-control-sm" id="tipo_documento_digital" name="tipo_documento_digital" required>
                           <option value="Factura">Factura</option>
                           <option value="Ticket">Nota de Venta</option>
                           <option value="Proforma">Proforma</option>
                       </select>
                     </div>
                     <div class="fecha_maxima_proforma" id="fecha_maxima_proforma">

                     </div>

                     <h5>Limpiar Consola</h5>
                       <div class="">
                         <select class="form-control form-control-sm" name="limpiar_consola" required>
                             <option value="SI">SI</option>
                             <option value="NO">NO</option>
                         </select>
                       </div>

                     <div class="row">
                       <div class="col">
                         <h5>Estado Financiero</h5>
                         <select   class="form-control" id="estado_financiero" name="estado_financiero" required>
                              <option value="CONTADO">CONTADO</option>
                              <option value="CREDITO">CREDITO</option>
                           </select>
                       </div>
                     </div>
                     <div class="contenedor_vueltos">
                       <h5>Efectivo</h5>
                         <div class="">
                           <input oninput="dar_vuelto();" type="text" class="form-control form-control-sm" id="efectivo" name="efectivo" value="" required placeholder="Ingrese El Efectivo">
                         </div>
                         <h5>Vuelto</h5>
                           <div class="">
                             <input type="text" class="form-control form-control-sm" id="vuelto_venta" readonly name="vuelto" value="" required placeholder="">
                           </div>
                     </div>
                </div>

                <div class="col-md-6">
                    <p class="col-8">Identificación:</p>
                    <select onchange="consumidor_final();" id="identificacion" class="form-control form-control-sm" name="tipo_identificacion" required>
                      <option value="04">RUC</option>
                      <option value="05">CEDULA</option>
                      <option value="06">PASAPORTE</option>
                      <option value="07">VENTA A CONSUMIDOR FINAL</option>
                      <option value="08">IDENTIFICACION DEL EXTERIOR</option>
                    </select>
                    <p class="col-1">Nombres:</p>
                    <input type="text" class="form-control form-control-sm" id="nombres_receptor"  name="nombres_receptor" value="<?php echo $nombres_receptor ?>" required placeholder="Nombre del usuario">
                  <input type="hidden" name="action" value="facturar_final">
                  <p class="col-8">Cédula o Ruc:</p>
                  <input type="text" class="clase_consumidor_final form-control form-control-sm" id="numero_identidad_receptor" name="numero_identidad_receptor" value="<?php echo $cedula_receptor ?>" required placeholder="Ingrese cedula de identidad">
                  <p class="col-8">Email:</p>
                  <input class="form-control form-control-sm" type="text" id="email_reeptor" name="email_reeptor" value="<?php echo $email_recepto ?>" required placeholder=" Ingrese email del usuario ">
                  <p class="col-8">Direccion:</p>
                  <input class="form-control form-control-sm" type="text" id="direccion_reeptor" name="direccion_reeptor" value="<?php echo $direccion_receptor ?>" required placeholder="Ingrese la direccion">
                  <p class="col-8">Teléfono Cliente:</p>
                  <input class="form-control form-control-sm" type="text" id="celular_receptor" name="celular_receptor" value="<?php echo $celular_receptor ?>" required placeholder="Ingrese el Celular">
                  <p class="col-8">ID (Opcional)</p>
                  <input class="form-control form-control-sm" type="text" id="id_usuario_receptor" name="id_usuario_receptor" value="<?php echo $id_receptor ?>" readonly  >
                </div>
              </div>
              <div class="row">
                <div class="notificacion_facturacion">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Facturar</button>
            </div>
           </form>
          </div>
        </div>
    </div>
  </div>



  <div class="modal fade" id="exampleModalLong2323" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-qrcode fa-lg"></i> Lector QR</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                  <div class="panel-lectura">
                    <div class="panel-camara" style="padding: 0;margin: 0 auto;">
                      <div id="preMensaje">No se encuentra una cámara, asegúrate de tener habilitada una.</div>
                        <canvas id="canvas" hidden></canvas>
                      <div id="datosSalida" hidden>
                        <div id="mensajeSalida">No se ha detectado ningún código QR</div>
                        <div hidden><b>Código: </b><span id="qrDetectado"></span></div>
                      </div>
                    </div>
                    <div class="panel-resultado" id="resultado" style="display: none;">
                    </div>
                  </div>
                  <div class="sonido">
                    <audio controls id="sonido_qr">
                      <source src="/code_garantia/resources/sonido/beep.mp3" type="audio/mpeg">
                    </audio>
                  </div>
                    </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>






    <!-- Modal -->

    <div class="col-md-12">

      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">

  <li class="nav-item">

    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Buscar Productos</a>

  </li>

  <li class="nav-item">

    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Productos Sin Registrar</a>

  </li>


</ul>

<div class="tab-content" id="pills-tabContent">

  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
    <div class="card card-secondary">
      <div class="card-header">
        <h3 class="card-title">Buscar Productos Registrados:</h3>
      </div>
      <div class="card-body">
        <div class="form-group general_buscador">
          <div class="bloque1 buscador_productos">
              <form class="" method="post" name="formu_usuario_barra" id="formu_usuario_barra" onsubmit="event.preventDefault(); sendData_unico_producto_bg();" >
              <div class="row">
              <div class="col-md-12">
                <input type="hidden" class="form-control" name="action" value="buscar_usuarios">
                <select id="busqueda_usuarios" class="form-control" name="datos">
                  <?php
                  $query_opciones = mysqli_query($conection,"SELECT * FROM producto_venta WHERE  producto_venta.id_usuario= '$iduser' AND producto_venta.sistema ='facturacion'    AND producto_venta.estatus = 1");
                  while ($producto = mysqli_fetch_array($query_opciones)) {
                    echo '<option producto="'.$producto['idproducto'].'" value="'.$producto['idproducto'].'">ID:'.$producto['idproducto'].'-Nombre:'.$producto['nombre'].'- Precio:$'.number_format($producto['precio'],2).'- Cantidad:'.$producto['cantidad'].' </option>';
                  }
                  ?>
                </select>
              </div></div>
              <div class="row" style="padding: 15px;">
                <button type="submit" class="btn btn-info"><i class="fas fa-box-open"></i> Agregar Producto</button>
              </div>
              </form>
              <br>
              <div class="">
                <form id="formUsuarios">
                  <div class="" id="alerta_conatidad_existente">
                  </div>
                  <div id="imagen_producto_hyy" class="imagen_producto_hyy">
                  </div>
                  <input type="hidden" id="foto_dl" name="foto_dl" value="">
                  <div class="row">
                  <p class="col-4">Nombre del Producto:</p>
                    <div class="col-8">
                      <input type="text" class="form-control form-control-sm" id="nombre_producto" name="nombre_producto" value="" placeholder="Ingrese nombre del producto" readonly >
                    </div>
                    <p class="col-4">Cantidad:</p>
                    <div class="col-8">
                      <input class="form-control form-control-sm" oninput="sensor_cantidad();"  type="number" id="cantidad_producto" name="cantidad_producto" value="" placeholder="Ingrese la cantidad de producto" required  >
                    </div>
                    <p class="col-4">Precio de Venta por Unidad</p>
                    <div class="col-8">
                      <input type="text" class="form-control form-control-sm" type="number" name="valor_unidad" id="valor_unidad" value="" step="0.0000001" placeholder="Ingrese el valor por unidad"  >
                      <input class="form-control" type="hidden" name="action" value="agregar_cantidad">
                    </div>
                    <p class="col-4">%Descuento</p>
                    <div class="col-8">
                      <input type="text" class="form-control form-control-sm" oninput="sensor_cantidad_descuento();" type="number" name="porcentaje_descuento" id="porcentaje_descuento" value="" step="0.0000001" placeholder="Ingrese el porcentaje de descuento" >
                    </div>
                    <p class="col-4">Cantidad de Descuento</p>
                    <div class="col-8">
                      <input type="text" class="form-control form-control-sm" type="number" name="cantidad_descuento" id="cantidad_descuento" value="" step="0.0000001" readonly >
                    </div>
                    <p class="col-4">Tarifa IVA</p>
                    <div class="col-8">
                    <select class="form-control form-control-sm" name="tipo_ambiente" id="tipo_ambiente" >
                    <option value="2">CON IVA</option>
                    <option value="0">SIN IVA</option>
                    <option value="6">Exento de IVA </option>
                    <option value="7">No Objeto de Impuesto </option>
                  </select>
                    </div>
                    <p class="col-4">Códigos de los impuestos</p>
                    <div class="col-8">
                    <select class="form-control form-control-sm" name="codigos_impuestos" id="codigos_impuestos" >
                    <option value="2">IVA</option>
                    <option value="3">ICE</option>
                    <option value="5">IRBPNR</option>
                  </select>
                    </div>
                    <p class="col-4">Descripción del Producto</p>
                    <div class="col-8">
                    <input type="text" class="form-control form-control-sm" id="descripcion_producto" name="descripcion_producto" rows="8" cols="50"  placeholder="Ingrese una Descripción como Desea en la factura" readonly>
                    </div>
                    <p class="col-4">Detalle Extra</p>
                    <div class="col-8">
                    <input class="form-control form-control-sm" type="text" id="detalle_extra_s" name="detalle_extra" value="" placeholder="Ingrese el detalle Adicional">
                    </div>
                    <p class="col-4">ID Producto</p>
                    <div class="col-8">
                    <input class="form-control form-control-sm" type="text" id="id_producto" name="id_producto" value="" placeholder="" readonly>
                    </div>
                    <div class="d-flex align-items-center justify-content-center sbn_a col-12">
                    <input type="submit" class="btn btn-primary" value="Agregar Producto"></div>
                    <br>
                    <div class="notificacion_add_producto">
                    </div>
                  </div>
                </form>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>






  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">Productos Sin Registrar</h3>
      </div>
      <div class="">
        <form id="formproductos2">
          <div class="row">
          <p class="col-4">Nombre del Producto:</p>
            <div class="col-8">
              <input type="text" class="form-control form-control-sm" id="nombre_producto_2" name="nombre_producto" value="" placeholder="Ingrese nombre del producto" required >
            </div>
            <p class="col-4">Cantidad:</p>

            <div class="col-8">
              <input class="form-control form-control-sm" type="number" id="cantidad_producto_2" name="cantidad_producto" value="" placeholder="Ingrese la cantidad de producto" required  >
            </div>
            <p class="col-4">Precio de Venta por Unidad</p>
            <div class="col-8">
              <input type="text" class="form-control form-control-sm" type="number" name="valor_unidad" id="valor_unidad_2" value="" step="0.0000001" placeholder="Ingrese el valor por unidad" required >
              <input class="form-control" type="hidden" name="action" value="agregar_cantidad">
            </div>
            <p class="col-4">%Descuento</p>
            <div class="col-8">
              <input type="text" class="form-control form-control-sm" oninput="sensor_cantidad_descuento2();" type="number" name="porcentaje_descuento" id="porcentaje_descuento2" value="" step="0.0000001" placeholder="Ingrese el porcentaje de descuento" >
            </div>
            <p class="col-4">Cantidad de Descuento</p>
            <div class="col-8">
              <input type="text" class="form-control form-control-sm" type="number" name="cantidad_descuento" id="cantidad_descuento2" value="" step="0.0000001" readonly >
            </div>
            <p class="col-4">Tarifa IVA</p>
            <div class="col-8">
            <select class="form-control form-control-sm" name="tipo_ambiente" id="tipo_ambiente_2" >
            <option value="2">CON IVA</option>
            <option value="0">SIN IVA</option>
            <option value="6">Exento de IVA </option>
            <option value="7">No Objeto de Impuesto </option>
          </select>
            </div>
            <p class="col-4">Códigos de los impuestos</p>
            <div class="col-8">
            <select class="form-control form-control-sm" name="codigos_impuestos" id="codigos_impuestos_2" >
            <option value="2">IVA</option>
            <option value="3">ICE</option>
            <option value="5">IRBPNR</option>
          </select>
            </div>
            <p class="col-4">Descripción del Producto</p>
            <div class="col-8">
            <input type="text" class="form-control form-control-sm" id="descripcion_producto_2" name="descripcion_producto" rows="8" cols="50"  placeholder="Ingrese una Descripción como Desea en la factura" required>
            </div>
            <p class="col-4">Detalle Extra</p>
            <div class="col-8">
            <input class="form-control form-control-sm" type="text" id="detalle_extra_s_2" name="detalle_extra" value="" placeholder="Ingrese el detalle Adicional">
            </div>
            <p class="col-4">ID Producto</p>
            <div class="col-8">
            <input class="form-control form-control-sm" type="text" id="id_producto_2" name="id_producto_2" value="0" placeholder="" readonly>
            </div>
            <div class="d-flex align-items-center justify-content-center sbn_a col-12">
            <input type="submit" class="btn btn-primary" value="Agregar Producto"></div>
            <br>
            <div class="notificacion_add_producto">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
    </div>

    <!-- PRODUCTOS EN FACTURAS -->

    <div class="card card-success">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-luggage-cart"></i> Productos:</h3>
      <div class="card-tools">
      <button type="button" class="btn btn-dark btn-sm" id="btn-ventana2323" name="button"><i class="fas fa-qrcode"></i> Lector QR</button>
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>


    <div class="card-body">
      <div class="row">
      <div class="table">
          <table id="tablaUsuarios" class="table table-striped table-bordered table-condensed" style="width:100%" >
              <thead class="text-center">
                  <tr>
                      <th>ID</th>
                      <th>Cod. ID</th>
                      <th>Nombre</th>
                      <th>Precio U.</th>
                      <th>Cant.</th>
                      <th>Descripción</th>
                      <th>Detalle Extra</th>
                      <th>Desc.</th>
                      <th>IVA</th>
                      <th>Subtotal</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
      </div>
      </div>
  </div>
  </div>
<!--Modal para CRUD-->

<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header header_guibis" style="background-color: #263238;">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form id="formUsuarios">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">ID:</label>
                    <input type="text" class="form-control" id="id">
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre_producto">
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Descripcion</label>
                    <input type="text" class="form-control" id="descripcion_producto">
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">valor_unidad</label>
                    <input type="text" class="form-control" id="valor_unidad">
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Iva</label>
                    <input type="text" class="form-control" id="iva">
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="row">
<div class="col-sm-6">
<div class="card card-warning">
  <div class="card-header">
    <h3 class="card-title">Detalles de Factura</h3>
  </div>
  <div class="" id="resumen_pago_tabla">
        <table id="example2" class="table table-bordered table-hover table-responsive">
         <tr class="tabala_ch">
            <th>Subtotal</th>
            <th>12%</th>
            <th>Descuento</th>
            <th>Valor Total</th>
          </tr>
          <tr>

          <?php
          $query_lista_t = mysqli_query($conection,"SELECT SUM(((comprobantes.cantidad_producto)*(comprobantes.valor_unidad))) as
          'compra_total', SUM(((comprobantes.iva_producto))) AS 'iva_general',
          SUM(((comprobantes.precio_neto)+(comprobantes.iva_producto))) AS 'precioncluido_iva',SUM(comprobantes.descuento) AS 'descuento_total'
          FROM `comprobantes`
          WHERE comprobantes.id_emisor = '$iduser'");
          $data_lista_t=mysqli_fetch_array($query_lista_t) ?>
          <td class="out_number">$ <?php echo number_format($data_lista_t['compra_total'],2); ?></td>
          <td class="out_number">$ <?php echo number_format($data_lista_t['iva_general'],2); ?></td>
          <td class="out_number">$ <?php echo number_format($data_lista_t['descuento_total'],2); ?></td>
          <td class="out_number">$<?php echo number_format($data_lista_t['compra_total']+$data_lista_t['iva_general']-$data_lista_t['descuento_total'],2) ?></td>
        </tr>
    </table>
  </div>
</div>
</div>

<div class="col-sm-6">
<div class="card card-danger">
  <div class="card-header">
    <h3 class="card-title">Finalizar Factura</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="contenedor_comprar titu_conte__comprar">
        <button type="button" class="btn btn-success" id="btn-ventana" name="button"><i class="fas fa-dollar-sign"></i><b> Generar Factura</b></button>
        <button type="button" class="btn btn-info limipiar_consola_h"><i class="fas fa-hand-sparkles"></i> Limpiar Consola</button>
        <div class="notificacion_consola_limpia">
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
  </div>




  <div class="modal fade" id="configurar_caja" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header header_guibis" style="background-color: #263238;">
         <h5 class="modal-title" id="exampleModalLongTitle">No tienes una caja Abierta Actualmente</h5>
         </button>
       </div>
       <div class="modal-body contendedor_respuestas_generales">

         <form class="form_add_producto" action="" method="post"   name="add_iniciar_caja" id="add_iniciar_caja" onsubmit="event.preventDefault(); sendDataedit_iniciar_caja();">
             <div class="form-group">
               <label for="exampleInputEmail1">Ingrese el Monto de Apertura</label>
               <input type="text" class="form-control" required id="inicio_caja" name="inicio_caja"  placeholder="Monto de Apertura">
             </div>

             <input type="hidden" name="action" value="abrir_caja">

             <div class="modal-footer footer_modal_abrir_caja">
                <button type="submit" class="btn btn-primary">Abrir Caja</button>
             </div>
             <div class="alerta_iniciar_caja2">

             </div>
           </form>
       </div>

     </div>
   </div>
 </div>

  <footer class="main-footer">

    <?php

        require "scripts/footer.php";

     ?>

  </footer>

  <aside class="control-sidebar control-sidebar-dark">

  </aside>

</div>

<div class="panel-lectura">

<script type="text/javascript" src="jquery/home.js"></script>
<script src="jquery_comprar/generar_comprobante.js"></script>
<script type="text/javascript" src="jquery/jquery.min.js"></script>
<script src="/code_garantia/resources/css/framework/semantic/semantic.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/popper.min.js" integrity="sha512-eHo1pysFqNmmGhQ8DnYZfBVDlgFSbv3rxS0b/5+Eyvgem/xk0068cceD8GTlJOZsUrtjANIrFhhlwmsL1K3PKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="/crud_2020_ajax/assets/datatables/datatables.min.js"></script>
<script type="text/javascript" src="/crud_2020_ajax/main.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="../ricrey/theme_responsive/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../ricrey/theme_responsive/dist/js/adminlte.js"></script>
<script src="../ricrey/theme_responsive/dist/js/demo.js"></script>
<script src="java/index_formularios_bg.js"></script>
<script src="java/index_extras.js" charset="utf-8"></script>

<script src="java/configuracion_caja.js" charset="utf-8"></script>



  <script type="text/javascript">

document.getElementById("formUsuarios").style.display = "none";

$(document).ready(function(){

  $("#busqueda_usuarios").change(function(){

    var datos = $("#busqueda_usuarios").val();

    var action = 'buscar_usuarios';

    $.ajax({

      type:"post",

      url:"busquea.php",

      data: {action:action,datos:datos},

      success:function(response){

        if (response =='error') {

          $('.alert_general').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')

        }else {

        var info = JSON.parse(response);

        document.getElementById("formUsuarios").style.display = "block";

        $("#cantidad").val('');

        $("#detalles_extra").val('');

        $("#nombre_producto").val(info.nombre);

        $("#valor_unidad").val(info.precio);

        $("#id_producto").val(info.idproducto);

        $("#descripcion_producto").val(info.descripcion);

        $("#cantidad_producto").val('');

        $("#detalle_extra_s").val('');

        $("#tipo_ambiente").val('2');

        $("#codigos_impuestos").val('2');

        $('#alerta_conatidad_existente').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">'+

              '<strong>Hola !</strong> existen <span id="">'+info.cantidad+'</span> Unidades de <span>'+info.nombre+'</span> en tu Inventario'+

              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+

              '  <span aria-hidden="true">&times;</span>'+

              '</button>'+

            '</div>');

        }

      }

    })



  });



});



</script>

<script type="text/javascript">

$("#busqueda_usuarios").select2();

</script>



<script type="text/javascript">

$(document).ready(function(){

  $("#search_usuarios").change(function(){

    var datos = $("#search_usuarios").val();

    var action = 'buscar_usuarios_id';

    $.ajax({

      type:"post",

      url:"busquea.php",

      data: {action:action,datos:datos},

      success:function(response){

        console.log(response);

        if (response =='error') {

          $('.alert_general').html('<p class="alerta_negativa">Error al Cargar</p>')

        }else {

        var info = JSON.parse(response);

        $("#nombres_receptor").val(info.nombres);

        $("#identificacion").val(info.tipo_identificacion);

        $("#numero_identidad_receptor").val(info.identificacion);

        $("#email_reeptor").val(info.mail);

        $("#direccion_reeptor").val(info.direccion);

        $("#celular_receptor").val(info.celular);

        $("#id_usuario_receptor").val(info.id);



        }

      }



    })



  });



});







</script>

<script type="text/javascript">

$('#search_usuarios').select2({

   dropdownParent: $('#exampleModalLong .modal-body')





    });

</script>





<script type="text/javascript">
(function(){
  $(function(){
    $('#btn-ventana').on('click',function(){
      $('#exampleModalLong').modal();
      $("#efectivo").val('');
      $("#vuelto_venta").val('');
      $(".notificacion_facturacion").html('');
    });
  });

}());

</script>

<script type="text/javascript">

(function(){

  $(function(){

    $('#btn-ventana2323').on('click',function(){

      $('#exampleModalLong2323').modal();

    });





  });



}());

$( function() {

   $( "#exampleModalLong2323" ).draggable();

 } );

</script>



  <script type="text/javascript">

  function consumidor_final() {

  var tipo_identificacion =  document.getElementById("identificacion").value;

  if (tipo_identificacion == '07') {

    $('.clase_consumidor_final').val('9999999999999');

    $("#nombres_receptor").val('CONSUMIDOR FINAL');

    $("#email_reeptor").val('guibis@gmail.com');

    $("#direccion_reeptor").val('N/N');

    $("#celular_receptor").val('00000000000');

    $("#id_usuario_receptor").val('0');



  }else {

    $('.clase_consumidor_final').val('');

    $("#nombres_receptor").val('');

    $("#email_reeptor").val('');

    $("#direccion_reeptor").val('');

    $("#celular_receptor").val('');

    $("#id_usuario_receptor").val('');

  }





  }

  </script>

  <script type="text/javascript">

  function dar_vuelto() {

  var efectivo =  document.getElementById("efectivo").value;

  var action = 'dar_vuelto';

  $.ajax({

    url: 'jquery_comprar/resumen_pago.php',

    type:'POST',

    async: true,

    data: {action:action,efectivo:efectivo},

    success: function(response){

      console.log(response);

      $('#vuelto_venta').val(response)

    },



     });







  }

  </script>



  <script type="text/javascript">

  function sensor_cantidad() {

  var cantidad_producto =  document.getElementById("cantidad_producto").value;

  var id_producto       =  document.getElementById("id_producto").value;

  var action = 'verificar_cantidad';

  $.ajax({

    url: 'jquery_comprar/resumen_pago.php',

    type:'POST',

    async: true,

    data: {action:action,cantidad_producto:cantidad_producto,id_producto:id_producto},

    success: function(response){

     var info = JSON.parse(response);

     if (info.noticia == 'negativo') {

       $('#alerta_conatidad_existente').html('<div class="alert alert-danger  alert-dismissible fade show" role="alert">'+

            '<strong>Hola '+info.nombres_user+'!</strong> Te faltan '+info.resultado+' unidades para completar esta venta.'+

            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+

              '<span aria-hidden="true">&times;</span>'+

            '</button>'+

          '</div>');



     }

     if (info.noticia == 'positivo') {

       $('#alerta_conatidad_existente').html('<div class="alert alert-success   alert-dismissible fade show" role="alert">'+

            '<strong>Hola '+info.nombres_user+'!</strong> en tu bodega tienes '+info.resultado+' unidades si realizas esta venta.'+

            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+

              '<span aria-hidden="true">&times;</span>'+

            '</button>'+

          '</div>');



     }







    },



     });







  }

  </script>





<script>

$(function () {

  bsCustomFileInput.init();

});

</script>



<script>

  $(function () {

    $("#example1").DataTable({

      "responsive": true, "lengthChange": false, "autoWidth": false,

      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

    });

    $('#example2').DataTable({

      "paging": true,

      "lengthChange": false,

      "searching": false,

      "ordering": true,

      "info": true,

      "autoWidth": false,

      "responsive": true,

    });

  });

</script>





</body>

</html>

<?php

ob_end_flush();

?>
