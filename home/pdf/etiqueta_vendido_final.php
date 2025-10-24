<?php
include "../../coneccion.php";

     $idv= $_GET['idv'];
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Etiqueta Proceso</title>
  </head>
  <?php

  $query_information = mysqli_query($conection, "SELECT producto_venta.nombre AS 'nombre_producto',ventas.cantidad_producto,ventas.id as 'codigo_venta',
    producto_venta.foto,producto_venta.idproducto,producto_venta.talla_calzado,ventas.codigo_venta,ventas.qr_venta,usuarios.nombres as 'nombres_comprador',
    usuarios.apellidos as'apellidos_comprador',usuarios.whatsapp,usuarios.telefono,usuarios.numero_identidad as 'cedula_comprador' FROM `proceso_fabricacion_zapatos`
INNER JOIN ventas ON ventas.id = proceso_fabricacion_zapatos.id_venta
INNER JOIN producto_venta ON producto_venta.idproducto = ventas.idp
INNER JOIN usuarios on ventas.id_comprador = usuarios.id
WHERE id_venta = '$idv'");
      $data_lista_information=mysqli_fetch_array($query_information);
    $nombre_producto=  $data_lista_information['nombre_producto'];
    $codigo_venta=  $data_lista_information['codigo_venta'];
    $foto=  $data_lista_information['foto'];
    $idproducto=  $data_lista_information['idproducto'];
    $cantidad_producto=  $data_lista_information['cantidad_producto'];
      $talla_calzado=  $data_lista_information['talla_calzado'];
      $qr_venta=  $data_lista_information['qr_venta'];
      $nombres_comprador=  $data_lista_information['nombres_comprador'];
      $apellidos_comprador=  $data_lista_information['apellidos_comprador'];
      $whatsapp=  $data_lista_information['whatsapp'];
      $telefono=  $data_lista_information['telefono'];
        $cedula_comprador=  $data_lista_information['cedula_comprador'];

      date_default_timezone_set("America/Lima");
      $fecha_actual = date('d-m-Y H:m:s', time());
      $query_information_2 = mysqli_query($conection, "SELECT usuarios.nombres as 'nombre_colaborador', usuarios.apellidos as'apellidos_colaborador',usuarios.numero_identidad as 'cedula_colaborador',
        usuarios.nombre_empresa FROM `usuarios`
INNER JOIN producto_venta ON producto_venta.id_usuario = usuarios.id
INNER JOIN ventas  ON ventas.idp = producto_venta.idproducto
WHERE ventas.id = '$idv'");
$data_lista_information_2=mysqli_fetch_array($query_information_2);
$nombre_empresa=  $data_lista_information_2['nombre_empresa'];

   ?>
  <body>

    	<div style="border: solid 1px black;" class="etiqueta_general">
    		<div style="text-align: center;" class="img_perfio">
    			<img src="../img/reacciones/guibis.png" width="150px" alt="">
    		</div>
    		<div class="nombre_etiquetal">
    			<table style="margin: 0 auto;">
    				<tr>
    					<td>Nombre del Producto</td>
              <td><?php echo $nombre_producto; ?></td>
    				</tr>
            <tr>
    					<td>Codigo Producto:</td>
              <td>#<?php echo $idproducto; ?></td>
    				</tr>
            <tr>
    					<td>Fecha Salida</td>
              <td><?php echo $fecha_actual; ?></td>
    				</tr>
    				<tr>
    					<td>Cantidad</td>
    					<td> <?php echo $cantidad_producto ?> Pares</td>
    				</tr>
            <tr>
    					<td>Nombres Comprador</td>
              <td><?php echo $nombres_comprador; echo " "; echo $apellidos_comprador; ?></td>
    				</tr>
            <tr>
    					<td>Identidicacion Comprador:</td>
    					<td><?php echo $cedula_comprador; ?></td>
    				</tr>
            <tr>
    					<td>Celular Comprador:</td>
    					<td><?php echo $whatsapp; ?></td>
    				</tr>
            <tr>
    					<td>Telefono Comprador:</td>
    					<td><?php echo $telefono; ?></td>
    				</tr>
    				<tr>
    					<td>Codigo Venta:</td>
    					<td>#<?php echo $idv; ?></td>
    				</tr>
            <tr>
              <td>Vendedor:</td>
              <td><?php echo $nombre_empresa; ?></td>
            </tr>
    			</table>

    		</div>

    	</div>
    <div style="display: flex;border: solid 1px black;" class="conte_general">
    	<div style="display: inline-block;margin: 0 auto;width: %;padding: 0px;border: solid 1px black;padding: 3px;text-align: center;" class="pares_contenido">
    		<div class="tabla_contenido"><table>
    			<tr style="border: solid 1px #c1c1c1;">
    				<td style="border: solid 1px #c1c1c1;width: 140px;">Talla</td>
    				<td style="border: solid 1px #c1c1c1;width: 140px;">Cantidad</td>
    			</tr>
    			<tr style="border: solid 1px #c1c1c1;">
    				<td style="border: solid 1px #c1c1c1;width: 140px;"><?php echo $talla_calzado ?></td>
    				<td style="border: solid 1px #c1c1c1;width: 140px;"><?php echo $cantidad_producto ?></td>
    			</tr>
    		</table>

    		</div>

    	</div>
    	<div  style="display: inline-block;margin: 0 auto;"class="imagen_producto">
    		<div class="">
    			<img src="../img/uploads/<?php echo $foto ?>"  width="150px;" alt="">
    		</div>

    	</div>
    </div>
    <br><br><br><br><br><br>
    <div style="text-align: center;" class="img_perfio">
      <img src="../img/qr_ventas/<?php echo $qr_venta; ?>" width="150px" alt="">
    </div>


  </body>
</html>
