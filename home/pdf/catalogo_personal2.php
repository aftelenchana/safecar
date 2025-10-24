<?php
include "../../coneccion.php";
  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
     $iduser= $_GET['id'];
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Catalogo</title>
  </head>
  <body>
    <div class="info_empresa" style="text-align: center;display: block;">
      <?php
      $query=mysqli_query($conection,"SELECT * FROM usuarios WHERE id = $iduser");
      $result         = mysqli_fetch_array($query);
      $foto_perfil    = $result['img_perfil'];
      $logo           = $result['img_logo'];
      $nombres        = $result['nombres'];
      $apellidos      = $result['apellidos'];
      $whatsapp       = $result['whatsapp'];
      $nombre_empresa = $result['nombre_empresa'];
      $facebook       = $result['facebook'];
      $instagram      = $result['instagram'];
      $ruc            = $result['ruc'];
      $direccion      = $result['direccion'];
      $celular        = $result['celular'];
      $telefono       = $result['telefono'];
      $fecha_acceso   = $result['fecha_creacion'];
      $cuenta_paypal  = $result['cuenta_paypal'];
      $qr             = $result['img_qr'];
      $eslogan        = $result['eslogan'];
      if ($nombre_empresa == '') {
        $nombre_salida=$nombres;
        // code...
      }else {
        $nombre_salida=$nombre_empresa;
      }



       ?>
      <table style="margin: 0 auto;">
        <tr>
          <td >Usuario</td>
          <td style="font-weight: bold;"><?php echo $nombre_salida ?></td>

        </tr>
        <tr>
          <td>Celular</td>
          <td style="font-weight: bold;"><?php echo $whatsapp ?></td>
        </tr>
      </table>
    </div>
    <br>





<div class="total_bloque">

     <?php
     $query_lista = mysqli_query($conection,"SELECT producto_venta.idproducto, producto_venta.nombre as 'nombre_producto', producto_venta.precio,producto_venta.estado,provincia.nombre as 'provincia',ciudad.nombre as 'ciudad',producto_venta.foto,usuarios.facebook,
        usuarios.instagram,usuarios.whatsapp,usuarios.empresa, producto_venta.fecha_producto,categorias.nombre as 'categorias',producto_venta.qr,subcategorias.nombre as 'subcategorias',categorias.imagen as 'imagen_cat',producto_venta.descripcion  FROM producto_venta
     INNER JOIN categorias ON producto_venta.categorias = categorias.id
     INNER JOIN subcategorias ON producto_venta.subcategorias = subcategorias.id
     INNER JOIN provincia ON producto_venta.provincia = provincia.id
     INNER JOIN ciudad ON producto_venta.ciudad = ciudad.id
     INNER JOIN usuarios ON producto_venta.id_usuario = usuarios.id WHERE usuarios.id =$iduser and  producto_venta.estatus = 1
      ORDER BY fecha_producto DESC ");
     $result_lista= mysqli_num_rows($query_lista);
     if ($result_lista > 0) {
           while ($data_lista=mysqli_fetch_array($query_lista)) {
               $foto = '../img/uploads/'.$data_lista['foto'];
               $idp = $data_lista['idproducto'];
                $qr = '../img/qr/'.$data_lista['qr'];
               $nombre_producto = $data_lista['nombre_producto'];
               $precio = $data_lista['precio'];
               $descripcion = $data_lista['descripcion'];
               $idproducto = $data_lista['idproducto'];
               $subcategorias = $data_lista['subcategorias'];
               $categorias = $data_lista['categorias'];
     ?>
     <div class="conte_general" style="background: #F7F7F7;border-radius: 25px;">
       <br>
       <div class="contenedor_catalogo" style="align-items: center;padding: 20px;">
         <div class="img_producto" style="display: inline-block;width: 45%;text-align: center;">
           <img src="<?php echo $foto ?>" alt="" style="width: 50%;">
            <img  src="<?php echo $qr ?>" alt="" style="width: 25%;">
         </div>
         <div class="conte_info" style="display: inline-block;width: 45%;">
           <table>
             <tr>
               <td style="font-weight: bold;">Nombre:</td>
               <td><?php echo $nombre_producto; ?></td>
             </tr>
             <tr>
               <td style="font-weight: bold;">Categorias:</td>
               <td><?php echo $subcategorias; ?></td>
             </tr>
             <tr>
               <td style="font-weight: bold;">Subcategorias:</td>
               <td><?php echo $categorias; ?></td>
             </tr>
             <tr>
               <td style="font-weight: bold;">Precio:</td>
               <td style="color: #FFC300;font-weight: bold;font-size: 25px;">$<?php echo $precio ?></td>
             </tr>
             <tr>
               <td style="font-weight: bold;">ID</td>
               <td>#<?php echo $idproducto ?></td>
             </tr>

           </table>
         </div>

       </div>

     </div>






     <?php
     }
   }
 ?>




</div>

  </body>
</html>
