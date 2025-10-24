<?php
include "../../coneccion.php";

     $idp= $_GET['idp'];
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Contrato </title>
  </head>
  <?php
  date_default_timezone_set("America/Lima");
  $fecha_actual = date('d-m-Y H:m:s', time());
  $query_information = mysqli_query($conection, "SELECT usuarios.nombres as 'nombres_usuario',usuarios.apellidos as
    'apellidos_usuarios',usuarios.id as 'id_usuario',usuarios.numero_identidad,usuarios.fecha_creacion,usuarios.mi_leben,producto_venta.nombre
    as 'nombre_producto',producto_venta.precio,subcategorias.nombre as 'subcategorias',categorias.nombre as 'categorias',ciudad.nombre as
    'ciudad',provincia.nombre as 'provincia',producto_venta.fecha_producto,producto_venta.idproducto,producto_venta.porcentaje_colaboracion FROM `producto_venta`
INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
INNER JOIN categorias ON categorias.id=producto_venta.categorias
INNER JOIN subcategorias ON subcategorias.id=producto_venta.subcategorias
INNER JOIN ciudad ON ciudad.id=producto_venta.ciudad
INNER JOIN provincia ON provincia.id=producto_venta.provincia
WHERE producto_venta.idproducto = $idp ");
      $data_lista_information=mysqli_fetch_array($query_information);
    $nombres=  $data_lista_information['nombres_usuario'];
    $apellidos_usuarios=  $data_lista_information['apellidos_usuarios'];
    $numero_identidad=  $data_lista_information['numero_identidad'];
    $idproducto=  $data_lista_information['idproducto'];
    $precio=  $data_lista_information['precio'];
    $porcentaje_colaboracion=  $data_lista_information['porcentaje_colaboracion'];
   ?>
  <body>
    <div class="contrato_general">
    	<div class="img_general" style="width: 25%;margin: 0 auto;text-align: center;">
    		<img src="../img/reacciones/guibis.png" alt="" width="180px;">

    	</div>
    	<div class="fecha_text" style="margin: 15px;padding: 5px;font-weight: bold;">
      <p> Ambato <?php echo "$fecha_actual"; ?></p>
    	</div>
    	<div class="cuerpo_txt" style="text-align: justify;padding: 10px;margin: 10px;color: #fff;background: #232F3E;">
    		Yo, <span style="color: #FFC300;font-weight: bold;font-style: italic;"><?php echo "$nombres"; ?> <?php echo "$apellidos_usuarios";?></span>, con cedula de ciudadania
    		<span style="color: #FFC300;font-weight: bold;font-style: italic;"><?php echo "$numero_identidad"; ?></span>, he subido el producto con Id <span style="color: #FFC300;font-weight: bold;font-style: italic;">#<?php echo "$idproducto"; ?></span>, a la plataforma digital Guibis.com
    		a un precio <span style="color: #FFC300;font-weight: bold;font-style: italic;"> $<?php echo "$precio"; ?></span>, y en mutuo acuerdo con la plataforma autoirizo la fabricacion y comerciazlizacion de este producto en sus canales de los mismos con
    		un <span style="color: #FFC300;font-weight: bold;font-style: italic;"><?php echo "$porcentaje_colaboracion"; ?>%</span> de ganancia de cada producto, las ganancias se reflejaran en la cuenta digital de la plataforma con razon de producto colaborativo, este contrato tiene
    		validez hasta que el usuario se acerque a las oficinas a cancelarlo.
    	</div>
      <br><br>
    	<div class="firmas_txt" style="text-align: left;margin: 59px;">
    			<div class="conte_img_bg" style="position: absolute;text-align: center;opacity: 0.2;">
    						<img src="../img/reacciones/guibis.png" alt="" style="width: 250px;">
    						<img src="../img/reacciones/guibis.png" alt="" style="width: 250px;">
    						<img src="../img/reacciones/guibis.png" alt="" style="width: 250px;">
    		        <img src="../img/reacciones/guibis.png" alt="" style="width: 250px;">
    		        <img src="../img/reacciones/guibis.png" alt="" style="width: 250px;">
    			</div>
    			<div class="firma_gerente" style="display: inline-block;padding: 65px;">
    				<div class="img_firma_gerente">
    					<p style="font-weight: bold;">Firma Gerente</p>
    					<p style="font-style: italic;">1804843900</p>
    				</div>
    			</div>
    			<div class="firma_usuario" style="display: inline-block;padding: 65px;">
    				<div class="img_firma_usuario">

    					<p style="font-weight: bold;">Firma Usuario</p>
    					<p> <span style="font-style: italic;"><?php echo "$numero_identidad"; ?></span> </p>
    				</div>
    			</div>

    		</div>


    </div>


  </body>
</html>
