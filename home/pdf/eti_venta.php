<?php
include "../../coneccion.php";
  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$proceso = $_GET['venta'];

      ?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Etiqueta General</title>
</head>
<body>
  <?php
  $query_lista_gropup = mysqli_query($conection,"SELECT usuarios.nombres as 'nombres_vendedor',usuarios.apellidos as
    'apellidos_vendedor',usuarios.nombre_empresa as 'empresa_vendedor',usuarios.celular as 'celular_vendedor',usuarios.id
    AS 'usuario_vendedor',usuarios.telefono as 'telefono_vendedor',usuarios.id as 'id_usuario_vendedor',usuarios.img_qr
    as 'qr_vendedor',producto_venta.nombre as
    'nombre_producto',producto_venta.idproducto,ventas.id as 'id_venta',ventas.cantidad_producto,ventas.precio_compra,
    ventas.fecha as 'fecha_venta',ventas.qr_venta,producto_venta.foto
    ,categorias.nombre AS 'categorias',subcategorias.nombre as'subcategorias',provincia.nombre as 'provincia',
    ciudad.nombre as 'ciudad',ventas.id_comprador
     FROM `ventas`
 INNER JOIN producto_venta ON producto_venta.idproducto = ventas.idp
 INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
 INNER JOIN categorias ON producto_venta.categorias=categorias.id
 INNER JOIN subcategorias ON subcategorias.id=producto_venta.subcategorias
 INNER JOIN provincia ON provincia.id = producto_venta.provincia
 INNER JOIN ciudad ON ciudad.id = producto_venta.ciudad
 WHERE ventas.id = '$proceso'");

      $data_lista_group=mysqli_fetch_array($query_lista_gropup);
    $nombres_vendedor=  $data_lista_group['nombres_vendedor'];
    $apellidos_vendedor=  $data_lista_group['apellidos_vendedor'];
     $empresa_vendedor=  $data_lista_group['empresa_vendedor'];
    $celular_vendedor=  $data_lista_group['celular_vendedor'];
    $id_usuario_vendedor=  $data_lista_group['id_usuario_vendedor'];
    $telefono_vendedor=  $data_lista_group['telefono_vendedor'];
    $qr_vendedor=  $data_lista_group['qr_vendedor'];
      $id_usuario_comprador=  $data_lista_group['id_comprador'];
    $query_lista_comprador = mysqli_query($conection,"SELECT * FROM usuarios where id= '$id_usuario_comprador'");
     $data_lista_comprador=mysqli_fetch_array($query_lista_comprador);
    $nombres_comprador=  $data_lista_comprador['nombres'];
    $apellidos_comprador=  $data_lista_comprador['apellidos'];
    $empresa_comprador=  $data_lista_comprador['nombre_empresa'];
    $celular_comprador=  $data_lista_comprador['celular'];
    $telefono_comprador=  $data_lista_comprador['telefono'];

    $qr_comprador=  $data_lista_comprador['img_qr'];
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
    <div style="text-align: center;width: 50%;height:20%;margin: 0 auto;"class="img_perfio">
      <img src="../img/reacciones/guibis.png" width="150px" alt="">
    </div>
    <div style="width: 50%;margin: 0 auto;" class="sss">
      <div style="display: inline-block;border: solid 1px;text-align: center;width: 220px;" class="nombre_etiquetal">
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
            <td> <img src="../img/qr/<?php echo $qr_vendedor ?>" width="100px;" alt=""> </td>
          </tr>
        </table>

      </div>

      <div style="display: inline-block;border: solid 1px;text-align: center;width: 220px;" class="nombre_etiquetal">
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
            <td><?php echo $data_lista_comprador['numero_identidad'] ?></td>
          </tr>
          <tr>
            <td>Celular</td>
            <td><?php echo $celular_comprador ?></td>
          </tr>
          <tr>
            <td>Codigo Comprador</td>
            <td> <img src="../img/qr/<?php echo $qr_comprador ?>" width="100px;" alt=""> </td>
          </tr>

        </table>

      </div>
    </div>

    <div style="display:block;border: solid 1px;text-align: center;width:300px;margin: 0 auto;" class="nombre_etiquetal">
      <table>
        <tr>
          <td>Imagen</td>
          <td><img src="../img/uploads/<?php echo $foto ?>" width="150px;" alt=""></td>
        </tr>
        <tr>
          <td>Nombre del Producto</td>
          <td><?php echo $nombre_producto ?></td>
        </tr>
        <tr>
          <td>Cantidad</td>
          <td><?php echo $cantidad_producto ?> Unid.</td>
        </tr>
        <tr>
          <td>Precio</td>
          <td>$<?php echo $precio_compra ?></td>
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
          <tr>
            <td>ID venta</td>
            <td><?php echo $proceso ?></td>
          </tr>
          <tr>
            <td>Codigo Venta</td>
            <td> <img src="../img/qr_ventas/<?php echo $qr_venta ?>" width="150px;" alt=""> </td>
          </tr>

      </table>

    </div>
    <br>

  </div>




</body>
</html>
