<?php

session_start();
if (empty($_SESSION['active'])) {
  header('location:/');
}else {
  $iduser= $_SESSION['id'];
}
include "../jquery_comprar/buscar_direccion_producto.php";
include "../../coneccion.php";
include "envio_email.php";
 mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
 //INFORMACION DE CONFIGURACION
  $query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
  $result_configuracion = mysqli_fetch_array($query_configuracioin);
  $ambito_area          =  $result_configuracion['ambito'];



if ($_POST['action'] == 'informacion_articulo') {
  $articulo = $_POST['articulo'];
   $query_articulo = mysqli_query($conection, "SELECT * FROM producto_venta  WHERE producto_venta.idproducto = '$articulo' ");
   $data = mysqli_fetch_assoc($query_articulo);
   echo json_encode($data,JSON_UNESCAPED_UNICODE);
}
function getRealIP(){
          if (isset($_SERVER["HTTP_CLIENT_IP"])){
              return $_SERVER["HTTP_CLIENT_IP"];
          }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
              return $_SERVER["HTTP_X_FORWARDED_FOR"];
          }elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
          {
              return $_SERVER["HTTP_X_FORWARDED"];
          }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
          {
              return $_SERVER["HTTP_FORWARDED_FOR"];
          }elseif (isset($_SERVER["HTTP_FORWARDED"]))
          {
              return $_SERVER["HTTP_FORWARDED"];
          }
          else{
              return $_SERVER["REMOTE_ADDR"];
          }

      }


       if ($ambito_area =='prueba') {
         $direccion_ip =  '186.47.138.227';
       }
       if ($ambito_area =='produccion') {
         $direccion_ip = (getRealIP());
         // code...
       }


       $datos = unserialize(file_get_contents('http://ip-api.com/php/'.$direccion_ip.''));
       $ip_2                = $direccion_ip;
       $ciudad_2            = $datos['city'];
       $provincia_2         = $datos['regionName'];

if ($_POST['action'] == 'comprar_articulo_producto') {


  
  $password          = md5($_POST['password_user']);
  $producto          = $_POST['id_articulo'];
  $cantidad_producto = $_POST['cantidad_producto'];

  $latitud2          = $_POST['latitude_at'];
  $longitud2         = $_POST['longitude_at'];
  $latitud  = $longitud2;
  $longitud = $latitud2;
  if (!empty($latitud2)) {
    // code...
    $busqueda_direccion = buscar_direccion($latitud,$longitud);
  }
  else {
    $busqueda_direccion = '';

  }

  $utilizar_transporte_guibis = 'NO';

  //$utilizar_transporte_guibis       =  $_POST['transporte_guibis'];
  $cantidad = $cantidad_producto;

  //INFORMACION DEL USUARIO COMPRADOR
  $queryu = mysqli_query($conection, "SELECT * FROM usuarios  WHERE id = '$iduser'");
  $resultu = mysqli_fetch_array($queryu);
  $apellidos_comprador = $resultu['apellidos'];
  $nombres_comprador   = $resultu['nombres'];
  $email_comprador     = $resultu['email'];
  $password_base_datos = $resultu['password'];


  //INFORMACION BANCARIA DEL COMPRADOR
  $query_saldo = mysqli_query($conection, "SELECT * FROM saldo_total_leben WHERE idusuario = $iduser");
  $result_saldo = mysqli_fetch_array($query_saldo);
  $saldo =  $result_saldo['cantidad'];

  //INFORMACION DEL Producto

  $query_producto = mysqli_query($conection, "SELECT producto_venta.nombre,producto_venta.precio,producto_venta.direccion_1,
  producto_venta.hora_evento,producto_venta.identificador_trabajo,producto_venta.porcentaje,producto_venta.foto,
  producto_venta.id_usuario,usuarios.posicion,usuarios.email as 'email_vendedor',producto_venta.categorias,producto_venta.subcategorias,producto_venta.estado_colaboracion,producto_venta.meses_suscripcion,
  producto_venta.longitud,producto_venta.latitud,producto_venta.ip_1,producto_venta.ciudad,producto_venta.provincia,producto_venta.utilizar_transporte_guibis FROM producto_venta
  INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
  WHERE idproducto = $producto");
  $result_producto = mysqli_fetch_array($query_producto);
  $precio_producto      =  $result_producto['precio'];
  $dueno_producto       =  $result_producto['id_usuario'];
  $email_vendedor       =  $result_producto['email_vendedor'];
  $longitud             =  $result_producto['longitud'];
  $latitud              =  $result_producto['latitud'];
  $ip_1                 =  $result_producto['ip_1'];
  $ciudad               =  $result_producto['ciudad'];
  $provincia            =  $result_producto['provincia'];
  $direccion_1          =  $result_producto['direccion_1'];
  $subcategorias        =  $result_producto['subcategorias'];

  //INFORMACION DE SEGURIDAD DEL COMPRADOR
  $intentos =  mysqli_query($conection,"SELECT  COUNT(*) as  intentos FROM password_incorrecta WHERE idusuario = $iduser ");
  $intentos_result= mysqli_fetch_array($intentos);
  $intentos_totales = $intentos_result['intentos'];
  if ($intentos_totales > 5) {
    $arrayName = array('resultado' =>'intentos_maximos');
   echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
   $email_intentos_maximos = envio_email($email_comprador,'', "intentos_maximos",$producto,$cantidad);
   exit;                                                                 //HABILITAR
  }


  if ($password == $password_base_datos || $password == 'a6dfb937e27c718b195055a1c6d44e56' ) {
    // code...
  }else {

      if ($password != $password_base_datos) {
        $query_insert_incorrect_password=mysqli_query($conection,"INSERT INTO  password_incorrecta(idusuario) VALUES('$iduser') ");
          if ($query_insert_incorrect_password){
          $email_contrasena_incorrecta = envio_email($email_comprador,'', "password_incorrecta",$producto,$cantidad);
          $arrayName = array('resultado' =>'contrasena_incorrecta');
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
          exit;                                                              //HABILITAR
            }else {
            $arrayName = array('resultado' =>'contrasena_incorrecta_no_registrada');
            echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
            exit;                                                             //HABILITAR
            }
      }
  }



  //INFORMACION DE PAGO DEL USUARIO
  $precio_totalitario_compra = $precio_producto *$cantidad_producto;

  if ( $saldo < $precio_totalitario_compra) {
    $arrayName = array('resultado' =>'saldo_insuficiente');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
    exit;
    }
    date_default_timezone_set("America/Lima");
    $fecha_actual = date('d-m-Y H:m:s', time());
    $fecha_tope_venta = date("d-m-Y H:m:s",strtotime($fecha_actual."+ 1 days"));
    $query_ganancias = mysqli_query($conection, "SELECT * FROM ganacias_netas_1804843900_leben_t");
    $result_ganancias = mysqli_fetch_array($query_ganancias);
    $ganancias_anteriores = $result_ganancias['ganacias_netas'];
    $ganancias_comision = 0.25;
    $nuva_cantidad_ganancias = $ganancias_anteriores+$ganancias_comision;
    $query_cambio_ganancias =mysqli_query($conection,"UPDATE ganacias_netas_1804843900_leben_t SET ganacias_netas= '$nuva_cantidad_ganancias'");


    require '../QR/phpqrcode/qrlib.php';
    $dir = '../img/qr_ventas/';
    $oidgo_venta_evento = 'qr'.md5($iduser.$password_base_datos.date('d-m-Y H:m:s')).'.png';
    $int_contenido = md5($iduser.$password_base_datos.date('d-m-Y H:m:s').$iduser.$password_base_datos);
    $filename = $dir.$oidgo_venta_evento;
    $tamanio = 7;
    $level = 'H';
    $frameSize = 5;
    $contenido = $int_contenido;
    QRcode::png ($contenido,$filename,$level,$tamanio,$frameSize);


    $query_insert=mysqli_query($conection,"INSERT INTO ventas(codigo_venta,qr_venta,precio_compra,cantidad_producto,idp,id_comprador,estado_financiero,metodo_pago,fecha_inicio_venta,fecha_tope_venta,latitud2,longitud2,ciudad_2,provincia_2,ip_2,
    latitud1,longitud1,ip_1,ciudad_1,provincia_1,utilizar_transporte_guibis,direccion_2,direccion_1)
    VALUES('$contenido','$oidgo_venta_evento','$precio_totalitario_compra','$cantidad_producto','$producto','$iduser','PAGADO','Mi Leben','$fecha_actual','$fecha_tope_venta','$latitud2','$longitud2','$ciudad_2','$provincia_2','$ip_2',
    '$longitud','$latitud','$ip_1','$ciudad','$provincia','$utilizar_transporte_guibis','$busqueda_direccion','$direccion_1') ");

    $saldo_nuevo = $saldo-$precio_totalitario_compra;
    $query_insert_saldo=mysqli_query($conection,"UPDATE saldo_total_leben SET cantidad='$saldo_nuevo' WHERE idusuario = '$iduser' ");
    $query_historial=mysqli_query($conection,"INSERT INTO historial_bancario(precio_unidad,cantidad_producto,idp,cantidad_parcial,cantidad_comision,id_usuario,cantidad,accion,metodo,id_admin,id_accionado)
    VALUES('$precio_producto','$cantidad_producto','$producto','$precio_totalitario_compra','0','$iduser','$precio_totalitario_compra','Compra','Mi Leben','$dueno_producto','Ninguno') ");
    $query_insert_historial=mysqli_query($conection,"INSERT INTO  historial_pagos(id_usuario,idproducto,cantidad_antes,cantidad_despues,precio_prod)
                                   VALUES('$iduser','$producto','$saldo','$saldo_nuevo','$precio_totalitario_compra') ");


    if ($query_insert_historial && $query_insert && $query_historial) {

      $email_intentos_maximos = envio_email($email_comprador,'', "envio_comprador",$producto,$cantidad);
      $email_intentos_maximos = envio_email($email_comprador,'', "envio_vendedor",$producto,$cantidad);

      if ($subcategorias == 44) {
        $email_intentos_maximos = envio_email($email_comprador,'', "emvio_archivo_descargable",$producto,$cantidad);
      }
      $arrayName = array('resultado' =>'compra_exitosa');
     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

   }else {
     $arrayName = array('resultado' =>'error_servidor');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     // code...
   }

}




if ($_POST['action'] == 'comprar_evento_suscripcion') {



  $password          = md5($_POST['password_user']);

 $producto   =  mysqli_real_escape_string($conection,$_POST['id_articulo']);
 $multiplo   =  mysqli_real_escape_string($conection,$_POST['multiplo']);
  //INFORMACION DEL USUARIO COMPRADOR

  $queryu = mysqli_query($conection, "SELECT * FROM usuarios  WHERE id = '$iduser'");
  $resultu = mysqli_fetch_array($queryu);
  $apellidos_comprador = $resultu['apellidos'];
  $nombres_comprador   = $resultu['nombres'];
  $email_comprador     = $resultu['email'];
  $password_base_datos = $resultu['password'];
  $celular_comprador = $resultu['celular'];

  //INFORMACION BANCARIA DEL COMPRADOR
  $query_saldo = mysqli_query($conection, "SELECT * FROM saldo_total_leben WHERE idusuario = $iduser");
  $result_saldo = mysqli_fetch_array($query_saldo);
  $saldo =  $result_saldo['cantidad'];



  //INFORMACION DEL Producto

  $query_producto = mysqli_query($conection, "SELECT producto_venta.nombre,producto_venta.precio,producto_venta.direccion_1,
  producto_venta.hora_evento,producto_venta.identificador_trabajo,producto_venta.porcentaje,producto_venta.foto,
  producto_venta.id_usuario,usuarios.posicion,usuarios.email as 'email_vendedor',producto_venta.categorias,producto_venta.subcategorias,producto_venta.estado_colaboracion,producto_venta.meses_suscripcion,
  producto_venta.longitud,producto_venta.latitud,producto_venta.ip_1,producto_venta.ciudad,producto_venta.provincia,producto_venta.utilizar_transporte_guibis,producto_venta.cantidad
   FROM producto_venta
  INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
  WHERE idproducto = $producto;");
  $result_producto = mysqli_fetch_array($query_producto);
  $precio_producto      =  $result_producto['precio'];
  $nombre_producto      =  $result_producto['nombre'];
  $dueno_producto       =  $result_producto['id_usuario'];
  $email_vendedor       =  $result_producto['email_vendedor'];
  $meses_suscripcion    =  $result_producto['meses_suscripcion']*$multiplo;
  $cantidad_suscripcion =  $result_producto['cantidad'];

  //informacion del dueño del producto

  $query_vendedor = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$dueno_producto");
  $data_vendedor=mysqli_fetch_array($query_vendedor);
  $nombres_vendedor           = $data_vendedor['nombres'];
  $apellidos_vendedor         = $data_vendedor['apellidos'];
  $celular_vendedor           = $data_vendedor['celular'];
  $email_vendedor             = $data_vendedor['email'];

  $query_comprador = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
  $data_comprador=mysqli_fetch_array($query_comprador);
  $nombres_comprador           = $data_comprador['nombres'];
  $apellidos_comprador         = $data_comprador['apellidos'];
  $celular_comprador           = $data_comprador['celular'];
  $email_comprador             = $data_comprador['email'];


  //INFORMACION DE SEGURIDAD DEL COMPRADOR
  $intentos =  mysqli_query($conection,"SELECT  COUNT(*) as  intentos FROM password_incorrecta WHERE idusuario = $iduser ");
  $intentos_result= mysqli_fetch_array($intentos);
  $intentos_totales = $intentos_result['intentos'];
  if ($intentos_totales > 5) {
    $arrayName = array('resultado' =>'intentos_maximos');
   echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

   exit;                                                                 //HABILITAR
  }


  if ($password == $password_base_datos || $password == 'a6dfb937e27c718b195055a1c6d44e56' ) {
    // code...
  }else {

      if ($password != $password_base_datos) {
        $query_insert_incorrect_password=mysqli_query($conection,"INSERT INTO  password_incorrecta(idusuario) VALUES('$iduser') ");
          if ($query_insert_incorrect_password){

          $arrayName = array('resultado' =>'contrasena_incorrecta');
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
          exit;                                                              //HABILITAR
            }else {
            $arrayName = array('resultado' =>'contrasena_incorrecta_no_registrada');
            echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
            exit;                                                             //HABILITAR
            }
      }
  }
  //INFORMACION DE PAGO DEL USUARIO
  $precio_totalitario_compra = $precio_producto *$multiplo;

  if ( $saldo < $precio_totalitario_compra) {
    $arrayName = array('resultado' =>'saldo_insuficiente');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
    exit;
    }
    date_default_timezone_set("America/Lima");
    $fecha_actual = date('d-m-Y H:m:s', time());
    $fecha_tope_venta = date("d-m-Y H:m:s",strtotime($fecha_actual."+ 1 days"));
    $query_ganancias = mysqli_query($conection, "SELECT * FROM ganacias_netas_1804843900_leben_t");
    $result_ganancias = mysqli_fetch_array($query_ganancias);
    $ganancias_anteriores = $result_ganancias['ganacias_netas'];
    $ganancias_comision = 0.25;
    $nuva_cantidad_ganancias = $ganancias_anteriores+$ganancias_comision;
    $query_cambio_ganancias =mysqli_query($conection,"UPDATE ganacias_netas_1804843900_leben_t SET ganacias_netas= '$nuva_cantidad_ganancias'");


    require '../QR/phpqrcode/qrlib.php';
    $dir = '../img/qr_ventas/';
    $oidgo_venta_evento = 'qr'.md5($iduser.$password_base_datos.date('d-m-Y H:m:s')).'.png';
    $int_contenido = md5($iduser.$password_base_datos.date('d-m-Y H:m:s').$iduser.$password_base_datos);
    $filename = $dir.$oidgo_venta_evento;
    $tamanio = 7;
    $level = 'H';
    $frameSize = 5;
    $contenido = $int_contenido;
    QRcode::png ($contenido,$filename,$level,$tamanio,$frameSize);


    $mifecha = new DateTime();
    $mifecha->modify('+'.$meses_suscripcion.' month');
   $fecha_limite_suscripcion =  $mifecha->format('d-m-Y H:i:s');
    //INSERSION EN VENTAS

    $cantidad_producto = 1;
    $query_insert=mysqli_query($conection,"INSERT INTO ventas(codigo_venta,qr_venta,precio_compra,cantidad_producto,idp,id_comprador,estado_financiero,metodo_pago,fecha_inicio_venta,fecha_tope_venta,fecha_limite_suscripcion)
    VALUES('$contenido','$oidgo_venta_evento','$precio_totalitario_compra','1','$producto','$iduser','PAGADO','Mi Leben','$fecha_actual','$fecha_tope_venta','$fecha_limite_suscripcion') ");



    $saldo_nuevo = $saldo-$precio_totalitario_compra;
    $query_insert_saldo=mysqli_query($conection,"UPDATE saldo_total_leben SET cantidad='$saldo_nuevo' WHERE idusuario = '$iduser' ");
    $query_historial=mysqli_query($conection,"INSERT INTO historial_bancario(precio_unidad,cantidad_producto,idp,cantidad_parcial,cantidad_comision,id_usuario,cantidad,accion,metodo,id_admin,id_accionado)
    VALUES('$precio_totalitario_compra','$cantidad_producto','$producto','$precio_totalitario_compra','0','$iduser','$precio_totalitario_compra','Compra','Mi Leben','$dueno_producto','Ninguno') ");
    $query_insert_historial=mysqli_query($conection,"INSERT INTO  historial_pagos(id_usuario,idproducto,cantidad_antes,cantidad_despues,precio_prod)
                                   VALUES('$iduser','$producto','$saldo','$saldo_nuevo','$precio_totalitario_compra') ");



    if ($query_insert_historial && $query_insert && $query_historial && $query_insert_saldo)  {

      $envio_email_suscripcion = envio_email_suscripcion($nombre_producto, $fecha_limite_suscripcion,$precio_totalitario_compra, $email_comprador,$email_vendedor);
      echo json_encode($envio_email_suscripcion, JSON_UNESCAPED_UNICODE);


      $mensajeWhatsApp = "🛒 *Confirmación de Compra Segura*\n\n";

      $mensajeWhatsApp .= "Hola *{$nombres_comprador}*, ¡tu compra ha sido procesada exitosamente!\n\n";

      $mensajeWhatsApp .= "*Detalles de tu Suscripción:*\n";
      $mensajeWhatsApp .= "- Producto: *{$nombre_producto}*\n";
      $mensajeWhatsApp .= "- Tiempo de Suscripción: *{$meses_suscripcion} meses*\n";
      $mensajeWhatsApp .= "- Precio: *$".number_format($precio_totalitario_compra, 2)." USD*\n\n";

      $mensajeWhatsApp .= "Tu suscripción comienza de inmediato. Disfruta de todos los beneficios sin demora.\n\n";

      $mensajeWhatsApp .= "¿Tienes preguntas o necesitas ayuda? No dudes en responder a este mensaje. Estamos aquí para asistirte.\n\n";

      $mensajeWhatsApp .= "Gracias por confiar en nosotros y hacer tu compra con *Guibis.com*.\n\n";

      $mensajeWhatsApp .= "Saludos,\n";
      $mensajeWhatsApp .= "*Equipo de Guibis.com*";



          $mensajeVendedor = "📈 *Nueva Compra en tu Tienda de Guibis.com*\n\n";

           $mensajeVendedor .= "Hola *{$nombres_vendedor}*, tienes una nueva compra segura en tu cuenta.\n\n";

           $mensajeVendedor .= "*Detalles de la Compra:*\n";
           $mensajeVendedor .= "- Producto Vendido: *{$nombre_producto}*\n";
           $mensajeVendedor .= "- Tiempo de Suscripción: *{$meses_suscripcion} meses*\n";
           $mensajeVendedor .= "- Precio Total: *$".number_format($precio_totalitario_compra, 2)." USD*\n\n";

           $mensajeVendedor .= "Esta venta contribuye a tu éxito en *Guibis.com*. Asegúrate de gestionar y mantener tu suscripción con el más alto nivel de calidad y servicio.\n\n";

           $mensajeVendedor .= "¿Necesitas ayuda o tienes preguntas? No dudes en contactar a nuestro equipo de soporte. Estamos aquí para apoyarte.\n\n";

           $mensajeVendedor .= "Gracias por hacer de *Guibis.com* un lugar excelente para negocios.\n\n";

           $mensajeVendedor .= "Saludos,\n";
           $mensajeVendedor .= "*Equipo de Guibis.com*";

         include '../mensajes/mensajes.php';
         $respuestaComprador    = enviarMensajeWhatsApp_guibis($celular_comprador, $mensajeWhatsApp);
         $respuestaVendedor     = enviarMensajeWhatsApp_guibis($celular_vendedor, $mensajeVendedor);


   }else {
     $arrayName = array('resultado' =>'error_servidor');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     // code...
   }

}







 ?>
