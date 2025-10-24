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
    producto_venta.foto,producto_venta.idproducto,producto_venta.talla_calzado,ventas.codigo_venta,ventas.qr_venta  FROM `proceso_fabricacion_zapatos`
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
              <td><?php echo $nombre_producto ?></td>
    				</tr>
    				<tr>
    					<td>Cantidad</td>
    					<td> <?php echo $cantidad_producto ?> Pares</td>
    				</tr>
    				<tr>
    					<td>Codigo Venta:</td>
    					<td><?php echo $codigo_venta ?></td>
    				</tr>
            <tr>
              <td>Estado:</td>
              <td>Importante</td>
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


  </body>
</html>
