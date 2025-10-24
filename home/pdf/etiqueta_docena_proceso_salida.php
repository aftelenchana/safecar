<?php
include "../../coneccion.php";

     $proceso= $_GET['idproceso'];
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Etiqueta Proceso</title>
  </head>
  <?php

  $query_lista_gropup = mysqli_query($conection,"SELECT  proceso_fabricacion_zapatos_interno.contenido_qr,SUM(proceso_fabricacion_zapatos_interno.cantidad) as
   'cantidad_pares',proceso_fabricacion_zapatos_interno.serie,producto_venta.idproducto,producto_venta.foto,producto_venta.nombre,producto_venta.foto,
   proceso_fabricacion_zapatos_interno.talla,proceso_fabricacion_zapatos_interno.fecha,proceso_fabricacion_zapatos_interno.estado_cortado,
  proceso_fabricacion_zapatos_interno.resp_cortado  FROM proceso_fabricacion_zapatos_interno
INNER JOIN producto_venta ON producto_venta.idproducto = proceso_fabricacion_zapatos_interno.id_producto
WHERE contenido_qr='$proceso'
GROUP BY proceso_fabricacion_zapatos_interno.contenido_qr");

      $data_lista_group=mysqli_fetch_array($query_lista_gropup);
    $nombre_producto=  $data_lista_group['nombre'];
    $codigo=  $data_lista_group['contenido_qr'];
    $foto=  $data_lista_group['foto'];
    $idproducto=  $data_lista_group['idproducto'];
    $cantidad_producto=  $data_lista_group['cantidad_pares'];
    $serie=  $data_lista_group['serie'];


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
    					<td>Serie</td>
              <td><?php echo $serie ?></td>
    				</tr>
    				<tr>
    					<td>Cantidad</td>
    					<td> <?php echo $cantidad_producto ?> Pares</td>
    				</tr>
    				<tr>
    					<td>Codigo Venta:</td>
    					<td><?php echo $proceso ?></td>
    				</tr>
            <tr>
              <td>Estado:</td>
              <td>Almacen</td>
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
          <?php
          //PAGINADOR

            $query_lista = mysqli_query($conection,"SELECT * FROM proceso_fabricacion_zapatos_interno WHERE contenido_qr='$proceso'");
            $result_lista= mysqli_num_rows($query_lista);
            if ($result_lista > 0) {
                  while ($data_lista=mysqli_fetch_array($query_lista)) {
                  $talla =  $data_lista['talla'];
                  $cantidad =  $data_lista['cantidad']
              ?>
              <tr style="border: solid 1px #c1c1c1;">
                <td style="border: solid 1px #c1c1c1;width: 140px;"><?php echo $talla ?></td>
                <td style="border: solid 1px #c1c1c1;width: 140px;"><?php echo $cantidad ?></td>
              </tr>
              <?php
              }
            }
          ?>

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
