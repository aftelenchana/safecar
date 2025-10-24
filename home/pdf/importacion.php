<?php
include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
$iduser = $_GET['id'];
$codigoFactura     = $_GET['codigoFactura'];
$rol_user          = $_GET['rol_user'];
$id_generacion     = $_GET['id_generacion'];
$empresa           = $_GET['empresa'];
$clave_acc_guardar = $_GET['clave_acc_guardar'];
		new barCodeGenrator(''.$clave_acc_guardar.'', 1, 'barra.gif', 455, 60, false);
$query_resultados_emmisor = mysqli_query($conection,"SELECT * FROM comprobantes
WHERE id_emisor= '$iduser' AND secuencial = $codigoFactura");
$data__emmisor=mysqli_fetch_array($query_resultados_emmisor);
  $celular_receptor_uwu        = $data__emmisor['celular_receptor'];
  $direccion_receptor_uwu      = $data__emmisor['direccion_reeptor'];
  $email_receptor              = $data__emmisor['email_reeptor'];
  $codigo_formas_pago          = $data__emmisor['formas_pago'];
	$fecha_vencimiento_proforma  = $data__emmisor['fecha_vencimiento_proforma'];
	$idcliente                   = $data__emmisor['id_receptor'];



	$query_empresa_cookie = mysqli_query($conection, "SELECT * FROM empresas_registradas
		 WHERE   empresas_registradas.id = '$empresa' ");
	$data_empresa_cookie = mysqli_fetch_array($query_empresa_cookie);

$query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
$result_documentos = mysqli_fetch_array($query_doccumentos);
$regimen = $result_documentos['regimen'];
$contabilidad             = $result_documentos['contabilidad'];
$email_empresa_emisor     = $result_documentos['email'];
$celular_empresa_emisor   = $result_documentos['celular'];
$telefono_empresa_emisor  = $result_documentos['telefono'];
$direccion_emisor          = $result_documentos['direccion'];
$whatsapp                 = $result_documentos['whatsapp'];
$nombres                  = $result_documentos['nombres'];
$apellidos                = $result_documentos['apellidos'];
$numero_identificacion_emisor  = $result_documentos['numero_identidad'];
$contribuyente_especial   = $result_documentos['contribuyente_especial'];
$estableciminento_f      = $result_documentos['estableciminento_f'];
$contabilidad            = $result_documentos['contabilidad'];
$punto_emision_f         = $result_documentos['punto_emision_f'];
$img_facturacion         = $result_documentos['img_facturacion'];
$numero_identidad_emisor        = $result_documentos['numero_identidad'];
  $url_img_upload          = $result_documentos['url_img_upload'];

	$razon_social          = $result_documentos['razon_social'];

$estableciminento_f   = str_pad($estableciminento_f, 3, "0", STR_PAD_LEFT);
$punto_emision_f      = str_pad($punto_emision_f, 3, "0", STR_PAD_LEFT);

$nombre_empresa      = $result_documentos['nombre_empresa'];
if ($nombre_empresa == '' || $nombre_empresa== '0') {
  $nombre_empresa = ''.$nombres.' '.$apellidos.'';
  // code...
}else {
  $nombre_empresa      = $result_documentos['nombre_empresa'];
}

//fechas
$fecha_actual = date("d-m-Y");
$fecha =  str_replace("-","/",date("d-m-Y",strtotime($fecha_actual." - 0 hours")));



//INFORMACION COMERCIAL DE LOS PUNTOS DE EMISON
$query = mysqli_query($conection, "SELECT * FROM  importaciones   WHERE  importaciones.id_emisor  = '$iduser'
	AND importaciones.empresa = '$empresa'  ORDER BY fecha DESC");
$result = mysqli_fetch_array($query);
if ($result) {
  $secuencial_proforma = $result['secuencial'];
  $secuencial_proforma = $secuencial_proforma+1;
  // code...
}else {
  $secuencial_proforma =1;
}
     $numeroConCeros = str_pad($secuencial_proforma, 9, "0", STR_PAD_LEFT);


     $fecha_actual = date("d-m-Y");
     $fechasf =  str_replace("-","",date("d-m-Y",strtotime($fecha_actual." -0 hours")));

     $clave_acc_guardar= ''.$fechasf.'55'.$numero_identidad_emisor.'2'.$estableciminento_f.''.$punto_emision_f .''.$numeroConCeros.'1234567810';


//$clave_acc_g

 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PROFORMA </title>
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
					<a href="https://guibis.com/code_user?code=<?php echo $id_e ?>"><img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $data_empresa_cookie['img_empresa'] ?>" alt=""></a>


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
							<td> <?php echo $direccion_emisor ?></td>
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
								<td class="td_bld">Importación No :<?php echo $estableciminento_f ?>-<?php echo $punto_emision_f ?>-<?php echo $numeroConCeros ?></td>
							</tr>
							<tr>
								<td class="td_bld">Número de Autorización</td>
							</tr>
							<tr>
								<td class="numero_autorzaxion"><?php echo $clave_acc_guardar ?></td>
							</tr>
							<tr>
								<td class="td_bld">Fecha Vencimiento</td>
							</tr>
							<tr>
								<td><?php echo $fecha_vencimiento_proforma ?></td>
							</tr>
							<tr>
								<td class="td_bld">OBLIGADO A LLEVAR CONTABILIDAD:<?php echo $contabilidad  ?></td>

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
            <td> <span class="td_bld" >Razon Social del Proveedor:</span><?php echo $nombre_empresa ?> </td>
          </tr>
          <tr>
            <td> <span class="td_bld" >RUC/CI:</span> <?php echo $numero_identificacion_emisor ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección:</span> <?php echo $direccion_emisor ?></td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Teléfono:</span> <?php echo $celular_empresa_emisor ?></td>
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
							<th class="th_productos">Cant.</th>
							<th class="th_productos">Cod.</th>
							<th class="th_productos">Descrip.</th>
							<th class="th_productos">Talla</th>
							<th class="th_productos">Nombre</th>
							<th class="th_productos">P/U</th>
							<th class="th_productos">Sub Total</th>
						</tr>
					</thead>
					<tbody>
						<?php
						 $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;
						$query_resultados = mysqli_query($conection,"SELECT * FROM comprobantes
							WHERE id_emisor= '$iduser' AND secuencial = $codigoFactura");
						while ($resultados = mysqli_fetch_array($query_resultados)) {
									 $producto = $resultados['id_producto'];

									 $query_producto = mysqli_query($conection, "SELECT * FROM producto_venta WHERE idproducto = $producto");
									 $data_producto  = mysqli_fetch_array($query_producto);
									 $img_producto   =  $data_producto['foto'];
									 $url_upload_img =  $data_producto['url_upload_img'];
									 $descripcion_producto = $data_producto['descripcion'];
									 $codigo_extra_product = $data_producto['codigo_extra'];
									 $marca_producto       = $data_producto['marca'];

									 //EMPESAMOS A SACAR LA INFORMACION DEL COMPROBANTE PARA PASAR AL PROFORMAS
									 $id_emisor = $resultados['id_emisor'];
									 $nombre_producto = $resultados['nombre_producto'];
									 $descripcion_producto = $resultados['descripcion_producto'];
									 $valor_unidad = $resultados['valor_unidad'];
									 $cantidad_producto = $resultados['cantidad_producto'];
									 $tipo_ambiente = $resultados['tipo_ambiente'];
									 $codigos_impuestos = $resultados['codigos_impuestos'];
									 $detalle_extra = $resultados['detalle_extra'];
									 $precio_neto = $resultados['precio_neto'];
									 $iva_producto = $resultados['iva_producto'];
									 $precio_p_incluido_iva = $resultados['precio_p_incluido_iva'];
									 $id_producto = $resultados['id_producto'];
									 $descuento = $resultados['descuento'];
									 $iva_frontend = $resultados['iva_frontend'];
									 $subtotal_frontend = $resultados['subtotal_frontend'];



									 $nombres_receptor = $resultados['nombres_receptor'];
									 $numero_identidad_receptor = $resultados['numero_identidad_receptor'];
									 $email_reeptor = $resultados['email_reeptor'];
									 $direccion_reeptor = $resultados['direccion_reeptor'];
									 $id_receptor = $resultados['id_receptor'];
									 $tipo_identificacion = $resultados['tipo_identificacion'];
									 $celular_receptor = $resultados['celular_receptor'];
									 $id_receptor = $resultados['id_receptor'];
									 $formas_pago = $resultados['formas_pago'];
									 $efectivo = $resultados['efectivo'];
									 $vuelto = $resultados['vuelto'];
									 $estado_financiero = $resultados['estado_financiero'];
									 $limpiar_consola = $resultados['limpiar_consola'];
									 $fecha_vencimiento_proforma = $resultados['fecha_vencimiento_proforma'];
									 $modulo = $resultados['modulo'];


									 $query_insert=mysqli_query($conection,"INSERT INTO importaciones (id_emisor,nombre_producto, descripcion_producto, valor_unidad,cantidad_producto,tipo_ambiente,codigos_impuestos,detalle_extra,precio_neto,iva_producto,precio_p_incluido_iva,id_producto,descuento,iva_frontend,subtotal_frontend,
										nombres_receptor, numero_identidad_receptor,email_reeptor,direccion_reeptor,id_receptor,tipo_identificacion,celular_receptor,formas_pago,efectivo,vuelto,estado_financiero,limpiar_consola,secuencial,clave_acceso,modulo,url,IDROLPUNTOVENTA,rol,empresa)
									 VALUES('$iduser','$nombre_producto', '$descripcion_producto', '$valor_unidad','$cantidad_producto','$tipo_ambiente','$codigos_impuestos','$detalle_extra','$precio_neto','$iva_producto','$precio_p_incluido_iva','$id_producto','$descuento','$iva_frontend','$subtotal_frontend',
									 '$nombres_receptor', '$numero_identidad_receptor','$email_reeptor','$direccion_reeptor','$id_receptor','$tipo_identificacion','$celular_receptor','$formas_pago','$efectivo','$vuelto','$estado_financiero','$limpiar_consola','$secuencial_proforma','$clave_acc_guardar','$modulo','$url','$id_generacion','$rol_user','$empresa')");


									 if ($producto!= '0') {
										 $id_producto     = $resultados['id_producto'];
										 $cantidad_vende  = $resultados['cantidad_producto'];
										 $nombre_producto = $resultados['nombre_producto'];
										 //INFORMACION DEL PRECIO DEL PRODUCTO PARA QUE SE PUEDA GENERAR LA INFORMACION PARA EL ASIENTO CONTABLE
										 $precio_p_incluido_iva = $resultados['precio_p_incluido_iva'];

										 $precio_total_g = round($precio_p_incluido_iva*$cantidad_vende,2);
										 $query_resultados_productos = mysqli_query($conection,"SELECT * FROM producto_venta   WHERE idproducto= '$id_producto'");
										 $resultados_producto = mysqli_fetch_array($query_resultados_productos);
										 $cantidad_existente = $resultados_producto['cantidad'];
										 if ($cantidad_existente == '' || $cantidad_existente == '0') {
											 $cantidad_existente=$cantidad_vende;
											// code...
										 }

										 $url_upload = $url.'/home/facturacion/facturacionphp/comprobantes/importacion/pdf/'.$clave_acc_guardar.'.pdf';
										 $cantidad_new = $cantidad_existente+$cantidad_vende;
										 $query_edit_cantidad = mysqli_query($conection,"UPDATE producto_venta SET cantidad='$cantidad_new' WHERE idproducto = '$id_producto'");
										 $query_insert_cantidad=mysqli_query($conection,"INSERT INTO inventario(cantidad,motivo,detalles_extras,idproducto,iduser,cantidad_new,accion,url_upload,codigo_ingreso)
																																								 VALUES('$cantidad_vende','Entrada Emportación','Importación','$id_producto','$iduser','$cantidad_new','AUMENTAR','$url_upload','$clave_acc_guardar') ");
									 }


							$id_producto = $resultados['id_producto'];
							$cantidad_producto = $resultados['cantidad_producto'];
							$descripcion_producto = $resultados['descripcion_producto'];
							$valor_unidad = $resultados['valor_unidad'];
							$descuento = $resultados['descuento'];
							$iva_producto = $resultados['iva_producto'];
							$subtotal_frontend = $resultados['subtotal_frontend'];
						 ?>
						 <tr>
							 <td class="td_productos"><?php echo $cantidad_producto; ?></td>
							 <td class="td_productos"><?php echo ($codigo_extra_product); ?></td>
							 <td class="td_productos"><?php echo $descripcion_producto; ?></td>
							 <td class="td_productos"><?php echo $marca_producto; ?></td>
							 <td class="td_productos"><?php echo $nombre_producto; ?></td>
							 <td class="td_productos"><?php echo number_format($valor_unidad,2); ?></td>
							 <td class="td_productos">$<?php echo number_format($subtotal_frontend,2); ?></td>
						 </tr>
						<?php
						}
				?>
					</tbody>



				</table>
			</div>
		</div>



    <br><br>
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
								<td class="td_info_gjd"> <span class="td_bld " >Teléfono Empresa:</span>  <span><?php echo $telefono_empresa_emisor ?></span> </td>
							</tr>
							<tr class="tr_info_gjd" >
								<td class="td_info_gjd"> <span class="td_bld" >Direccion Cliente: </span> <span><?php echo $direccion_receptor_uwu ?></span> </td>
							</tr>
							<?php
							$query_nota = mysqli_query($conection, "SELECT * FROM notas_extras_facturacion   WHERE iduser = '$iduser'
							AND codigo_factura = '$codigoFactura'");
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





						<table class="formas_pago_table">

								<thead>
									<tr>
										<th class="th_productos">Forma de Pago</th>
										<th class="th_productos">Cantidad</th>
									</tr>
								</thead>
								<tbody>
									<?php
										mysqli_query($conection,"SET lc_time_names = 'es_ES'");
										 $query_lista = mysqli_query($conection,"SELECT * FROM formas_pago_facturacion WHERE formas_pago_facturacion.iduser ='$iduser'
											  AND formas_pago_facturacion.codigo_factura = '$codigoFactura'
										 ORDER BY `formas_pago_facturacion`.`fecha` DESC ");
												 $result_lista= mysqli_num_rows($query_lista);
											 if ($result_lista > 0) {
														 while ($data_lista=mysqli_fetch_array($query_lista)) {

																$id_pago = $data_lista['id'];

															 $codigo_formas_pago = $data_lista['formaPago'];
																$cantidad_metodo_pago = $data_lista['cantidad_metodo_pago'];

															 if ($codigo_formas_pago == 01) {
																	$nombre_formas_pago = 'SIN UTILIZACION DEL SISTEMA FINANCIERO';
																}

																if ($codigo_formas_pago == 15) {
																	$nombre_formas_pago = 'COMPESACION DE DE DEUDAS';
																}

																if ($codigo_formas_pago == 16) {
																	$nombre_formas_pago = 'TARJETA DE DEBITO';
																}

																if ($codigo_formas_pago == 17) {
																	$nombre_formas_pago = 'DINERO ELECTRONICO';
																}

																if ($codigo_formas_pago == 18) {
																	$nombre_formas_pago = 'TARJETA PREPAGO';
																}

																if ($codigo_formas_pago == 19) {
																	$nombre_formas_pago = 'TARJETA DE CREDITO';
																}

																if ($codigo_formas_pago == 20) {
																	$nombre_formas_pago = 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO';
																}

																if ($codigo_formas_pago == 21) {
																	$nombre_formas_pago = 'ENDOSO DE TITULOS';
																}
											?>
										<tr>
											<td class="td_productos"><?php echo ($nombre_formas_pago); ?></td>
											<td class="td_productos">$<?php echo number_format($cantidad_metodo_pago, 2); ?></td>
										</tr>
									<?php
									}
									}
									?>
								</tbody>

						</table>

				</div>
			</div>

			<?php
			$tarifasIva = ['0', '2', '3', '4', '5', '6', '7', '8', '10']; // Agregar todos los códigos necesarios

      // Inicializar un arreglo para almacenar los totales por tipo de tarifa de IVA
      $totalesPorTarifa = [];

      // Total general
      $compra_general = 0;

      $descuento_total = 0;

      foreach ($tarifasIva as $tarifa) {
          // Realizar la consulta para cada tarifa de IVA
          $query = mysqli_query($conection, "SELECT SUM((comprobantes.cantidad_producto * comprobantes.valor_unidad)) as 'compra_total',
                                              SUM(comprobantes.iva_frontend) AS 'iva_general',
                                              SUM((comprobantes.precio_neto + comprobantes.iva_producto)) AS 'precioncluido_iva',
                                              SUM(comprobantes.descuento) AS 'descuento_total'
                                              FROM `comprobantes`
                                              WHERE comprobantes.id_emisor = '$iduser'
                                              AND comprobantes.secuencial = '$codigoFactura'
                                              AND comprobantes.tipo_ambiente = '$tarifa'");
          $data = mysqli_fetch_array($query);

          // Calcular el total para esta tarifa después del descuento
          $totalesPorTarifa[$tarifa] = $data['compra_total'] - $data['descuento_total'];

          // Agregar al total general
          $compra_general += $totalesPorTarifa[$tarifa];

          // Acumular el total de descuentos
         $descuento_total += $data['descuento_total'];

          }

					$query_lista_t = mysqli_query($conection,"SELECT SUM(((comprobantes.cantidad_producto)*(comprobantes.valor_unidad))) as
					'compra_total', SUM(((comprobantes.iva_producto))) AS 'iva_general',
					SUM(((comprobantes.precio_neto)+(comprobantes.iva_producto))) AS 'precioncluido_iva',SUM(comprobantes.descuento) AS 'descuento_total'
					FROM `comprobantes`
					WHERE comprobantes.id_emisor = '$iduser'  AND comprobantes.secuencial = '$codigoFactura'");
					$data_lista_t=mysqli_fetch_array($query_lista_t);


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
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['0'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Bas.Imp. 12%</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['2'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Bas.Imp. 14%</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['3'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Bas.Imp. 15%</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['4'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Bas.Imp. 5%</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['5'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Bas.Imp. 13%</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['10'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">No Objeto</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['6'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Excento de Iva</td>
										<td class="td_resumen">$<?php echo number_format($totalesPorTarifa['7'], 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">IVA</td>
										<td class="td_resumen">$<?php echo number_format($data_lista_t['iva_general'], 2); ?></td>
								</tr>

								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Descuento</td>
										<td class="td_resumen">$<?php echo number_format($descuento_total, 2); ?></td>
								</tr>
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Subtotal</td>
										<td class="td_resumen">$<?php echo number_format($compra_general, 2); ?></td>
								</tr>
								<!-- Agrega aquí otras filas para impuestos como ICE o IRBPNR si son necesarios -->
								<tr class="tr_resumen">
										<td class="td_bld td_resumen">Total:</td>
										<td class="td_resumen">$<?php echo number_format(($compra_general + $data_lista_t['iva_general']), 2); ?></td>
								</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
  </body>
</html>
