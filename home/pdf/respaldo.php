<?php
include "../../coneccion.php";
$proceso = $_GET['venta'];

      ?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Etiqueta General</title>
</head>
<body>
  <?php
  $query_lista_gropup = mysqli_query($conection,"SELECT usuarios.nombres as 'nombres_vendedor',usuarios.apellidos as
    'apellidos_vendedor',usuarios.nombre_empresa as 'empresa_vendedor',usuarios.celular as 'celular_vendedor',usuarios.id
    AS 'usuario_vendedor',usuarios.telefono as 'telefono_vendedor',usuarios.id as 'id_usuario_vendedor',usuarios.img_qr
    as 'qr_vendedor',usuario_comprador.nombres as 'nombres_comprador',usuario_comprador.apellidos as 'apellidos_comprador',
    usuario_comprador.nombre_empresa as 'empresa_comprador',usuario_comprador.celular as 'celular_comprador',usuario_comprador.telefono as
    'telefono_comprador',usuarios.id as 'id_usuario_comprador',usuario_comprador.img_qr as 'qr_comprador',producto_venta.nombre as
    'nombre_producto',producto_venta.idproducto,ventas.id as 'id_venta',ventas.cantidad_producto,ventas.precio_compra,
    ventas.fecha as 'fecha_venta',ventas.qr_venta,producto_venta.foto,usuario_comprador.numero_identidad as 'cedula_comprador'
    ,categorias.nombre AS 'categorias',subcategorias.nombre as'subcategorias',provincia.nombre as 'provincia',
    ciudad.nombre as 'ciudad'
     FROM `ventas`
 INNER JOIN producto_venta ON producto_venta.idproducto = ventas.idp
 INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
 INNER JOIN usuarios as usuario_comprador ON ventas.id_comprador=usuarios.id
 INNER JOIN categorias ON producto_venta.categorias=categorias.id
 INNER JOIN subcategorias ON subcategorias.id=producto_venta.subcategorias
 INNER JOIN provincia ON provincia.id = producto_venta.provincia
 INNER JOIN ciudad ON ciudad.id = producto_venta.ciudad
 WHERE ventas.id ='$proceso'");

      $data_lista_group=mysqli_fetch_array($query_lista_gropup);
    $nombres_vendedor=  $data_lista_group['nombres_vendedor'];
    $apellidos_vendedor=  $data_lista_group['apellidos_vendedor'];
     $empresa_vendedor=  $data_lista_group['empresa_vendedor'];
    $celular_vendedor=  $data_lista_group['celular_vendedor'];
    $id_usuario_vendedor=  $data_lista_group['id_usuario_vendedor'];
    $telefono_vendedor=  $data_lista_group['telefono_vendedor'];
    $qr_vendedor=  $data_lista_group['qr_vendedor'];
    $nombres_comprador=  $data_lista_group['nombres_comprador'];
    $apellidos_comprador=  $data_lista_group['apellidos_comprador'];
    $empresa_comprador=  $data_lista_group['empresa_comprador'];
    $celular_comprador=  $data_lista_group['celular_comprador'];
    $telefono_comprador=  $data_lista_group['telefono_comprador'];
    $id_usuario_comprador=  $data_lista_group['id_usuario_comprador'];
    $qr_comprador=  $data_lista_group['qr_comprador'];
    $nombre_producto=  $data_lista_group['nombre_producto'];
    $idproducto=  $data_lista_group['idproducto'];
    $id_venta=  $data_lista_group['id_venta'];
    $cantidad_producto=  $data_lista_group['cantidad_producto'];
    $precio_compra=  $data_lista_group['precio_compra'];
    $fecha_venta=  $data_lista_group['fecha_venta'];
    $qr_venta=  $data_lista_group['qr_venta'];
     $foto=  $data_lista_group['foto'];
       $categorias=  $data_lista_group['categorias'];
         $subcategorias=  $data_lista_group['subcategorias'];
           $provincia=  $data_lista_group['provincia'];
             $ciudad=  $data_lista_group['ciudad'];
     if ($empresa_vendedor == '') {
       $nombre_vendedor_salida = $nombres_vendedor.' '.$apellidos_vendedor;
       // code...
     }else {
       $nombre_vendedor_salida=$empresa_vendedor;
     }

     if ($empresa_comprador == '') {
       $nombre_comprador_salida = $nombres_comprador.' '.$apellidos_comprador;
       // code...
     }else {
       $nombre_comprador_salida=$empresa_comprador;
     }



   ?>
  <div style="border: solid 1px black;text-align: center;" class="etiqueta_general">
    <div style="text-align: center;" class="img_perfio">
      <img src="../img/reacciones/guibis.png" width="175px" alt="">
    </div>
    <div style="display: inline-block;border: solid 1px;text-align: center;" class="nombre_etiquetal">
      <table style="margin: 0 auto;">
        <tr>
          <td>Vendedor</td>
          <td><?php echo $nombre_vendedor_salida; ?></td>
        </tr>
        <tr>
          <td>ID Vendedor</td>
          <td><?php echo $id_usuario_vendedor; ?></td>
        </tr>
        <tr>
          <td>ID Producto</td>
          <td><?php echo $idproducto; ?></td>
        </tr>
        <tr>
          <td>Celular</td>
          <td><?php echo $celular_vendedor ?></td>
        </tr>
        <tr>
          <td>Codigo Vendedor</td>
          <td> <img src="../img/qr/<?php echo $qr_vendedor ?>" width="120px;" alt=""> </td>
        </tr>
      </table>

    </div>

    <div style="display: inline-block;border: solid 1px;text-align: center;" class="nombre_etiquetal">
      <table style="margin: 0 auto;">
        <tr>
          <td>Comprador</td>
          <td><?php echo $nombre_comprador_salida ?></td>
        </tr>
        <tr>
          <td>ID Comprador</td>
          <td><?php echo $id_usuario_comprador ?></td>
        </tr>
        <tr>
          <td>Cedula Comprador</td>
          <td><?php echo $data_lista_group['cedula_comprador'] ?></td>
        </tr>
        <tr>
          <td>Celular</td>
          <td><?php echo $celular_comprador ?></td>
        </tr>
        <tr>
          <td>Codigo Comprador</td>
          <td> <img src="../img/qr/<?php echo $qr_comprador ?>" width="120px;" alt=""> </td>
        </tr>

      </table>

    </div><br>
    <div style="display: inline-block;border: solid 1px;text-align: center;" class="nombre_etiquetal">
      <table style="margin: 0 auto;">
        <tr>
          <td>Imagen</td>
          <td><img src="../img/uploads/<?php echo $foto ?>" width="100px;" alt=""></td>
        </tr>
        <tr>
          <td>Nombre del Producto</td>
          <td><?php echo $nombre_producto ?></td>
        </tr>
        <tr>
            <td>Categoria:</td>
            <td><?php echo $categorias ?></td>
          </tr>
          <tr>
            <td>Subcategorias</td>
            <td><?php echo $subcategorias ?></td>
          </tr>
          <tr>
            <td>Provincia</td>
            <td><?php echo $provincia ?></td>
          </tr>
          <tr>
            <td>Ciudad</td>
            <td><?php echo $ciudad ?></td>
          </tr>
          <td>ID venta</td>
          <td><?php echo $proceso ?></td>
        <tr>
          <td>Codigo Venta</td>
          <td> <img src="../img/qr_ventas/<?php echo $qr_venta ?>" width="150px;" alt=""> </td>
        </tr>
      </table>

    </div>

  </div>




</body>
</html>
