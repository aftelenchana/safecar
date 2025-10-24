<?php
include "../../coneccion.php";
 mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

 require("../mail/PHPMailer-master/src/PHPMailer.php");
 require("../mail/PHPMailer-master/src/Exception.php");
 require("../mail/PHPMailer-master/src/SMTP.php");
 require '../QR/phpqrcode/qrlib.php';

 use  PHPMailer \ PHPMailer \ PHPMailer ;
 use  PHPMailer \ PHPMailer \ Exception ;
 // La instanciación y el paso de `true` habilita excepciones
 $mail = new  PHPMailer ( true );





    session_start();
    $iduser= $_SESSION['id'];


 if ($_POST['action'] == 'buscar_productos_guibis_pay') {
   $busqueda_productos_guibis_pay = $_POST['busqueda_productos_guibis_pay'];
   $query_producto = mysqli_query($conection,"SELECT * FROM producto_venta WHERE producto_venta.idproducto ='$busqueda_productos_guibis_pay'");

   $result_lista= mysqli_num_rows($query_producto);
   if ($result_lista >0) {
      $query_resultados_productos = mysqli_query($conection,"SELECT * FROM contenido_guibipay    WHERE idp= '$busqueda_productos_guibis_pay' AND iduser = '$iduser'");

       $result_existente_producto = mysqli_num_rows($query_resultados_productos);
       if ($result_existente_producto > 0) {
         $resultados_producto = mysqli_fetch_array($query_resultados_productos);

         //SACAMOS INFORMACION DEL PRODUCTO
          $data_producto = mysqli_fetch_array($query_producto);
         $precio_producto = $data_producto['precio'];



         $query_insert=mysqli_query($conection,"INSERT INTO contenido_guibipay(iduser,idp,cantidad,precio_unidad,precio_total)
                                                          VALUES('$iduser','$busqueda_productos_guibis_pay','1','$precio_producto','$precio_producto') ");
            if ($query_insert) {


              $query_resultados_productos = mysqli_query($conection,"SELECT * FROM contenido_guibipay
                 WHERE iduser = '$iduser'");
                 $resumen_tabla_producto='';

              while ($resultados_producto = mysqli_fetch_array($query_resultados_productos)) {
                $idproductogenerado  = $resultados_producto['idp'];
              $queryproducto_generado = mysqli_query($conection,"SELECT * FROM producto_venta WHERE producto_venta.idproducto ='$idproductogenerado'");
              $data_producto_generado = mysqli_fetch_array($queryproducto_generado);
                $resumen_tabla_producto = '
                <tr>
                    <td>'.$data_producto_generado['nombre'].'</td>
                    <td>'.$resultados_producto['cantidad'].'</td>
                    <td>'.$data_producto_generado['descripcion'].'</td>
                    <td>'.$resultados_producto['precio_unidad'].'</td>
                    <td>'.$resultados_producto['precio_total'].'</td>
                </tr>'.$resumen_tabla_producto.'
                ';
              }

              $query_lista_t = mysqli_query($conection,"SELECT SUM(((contenido_guibipay.cantidad)*(contenido_guibipay.precio_unidad))) as
            'compra_total'
            FROM contenido_guibipay
            WHERE contenido_guibipay.iduser = '$iduser'");
            $data_lista_t=mysqli_fetch_array($query_lista_t);

            $resumen_pago= '
                   <table id="example2" class="table table-bordered table-hover table-responsive">
                   <tr class="tabala_ch">
                   <th>Total</th>
                   </tr>
                   <tr>
                   <td class="out_number">$ '.number_format($data_lista_t['compra_total'],2).'</td>
                   </tr>
                   </table>';


                   $arrayName = array('respuesta' =>'producto_existente','resumen_tabla_producto'=>$resumen_tabla_producto,'resumen_pago'=>$resumen_pago);
                   echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
            }
       }else {
           $resultados_producto = mysqli_fetch_array($query_resultados_productos);

           //SACAMOS INFORMACION DEL PRODUCTO
            $data_producto = mysqli_fetch_array($query_producto);
           $precio_producto = $data_producto['precio'];

           $query_insert=mysqli_query($conection,"INSERT INTO contenido_guibipay(iduser,idp,cantidad,precio_unidad,precio_total)
                                                            VALUES('$iduser','$busqueda_productos_guibis_pay','1','$precio_producto','$precio_producto') ");
              if ($query_insert) {

$resumen_tabla_producto='';
                $query_resultados_productos = mysqli_query($conection,"SELECT * FROM contenido_guibipay
                   WHERE  iduser = '$iduser'");
                while ($resultados_producto = mysqli_fetch_array($query_resultados_productos)) {
                  $idproductogenerado  = $resultados_producto['idp'];
                $queryproducto_generado = mysqli_query($conection,"SELECT * FROM producto_venta WHERE producto_venta.idproducto ='$idproductogenerado'");
                $data_producto_generado = mysqli_fetch_array($queryproducto_generado);



                  $resumen_tabla_producto = '
                  <tr>
                      <td>'.$data_producto_generado['nombre'].'</td>
                      <td>'.$resultados_producto['cantidad'].'</td>
                      <td>'.$data_producto_generado['descripcion'].'</td>
                      <td>'.$resultados_producto['precio_unidad'].'</td>
                      <td>'.$resultados_producto['precio_total'].'</td>
                  </tr>'.$resumen_tabla_producto.'
                  ';
                }

                $query_lista_t = mysqli_query($conection,"SELECT SUM(((contenido_guibipay.cantidad)*(contenido_guibipay.precio_unidad))) as
              'compra_total'
              FROM contenido_guibipay
              WHERE contenido_guibipay.iduser = '$iduser'");
              $data_lista_t=mysqli_fetch_array($query_lista_t);

              $resumen_pago= '
                     <table id="example2" class="table table-bordered table-hover table-responsive">
                     <tr class="tabala_ch">
                     <th>Total</th>
                     </tr>
                     <tr>
                     <td class="out_number">$ '.number_format($data_lista_t['compra_total'],2).'</td>
                     </tr>
                     </table>';


                     $arrayName = array('respuesta' =>'agregado_primera_vez','resumen_tabla_producto'=>$resumen_tabla_producto,'resumen_pago'=>$resumen_pago);
                     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);




              }

       }

   }else {
     $arrayName = array('respuesta' =>'no_existe_producto');
     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
   }

 }

 if ($_POST['action'] == 'limpiar_consola') {
   $query_delete=mysqli_query($conection,"DELETE contenido_guibipay FROM contenido_guibipay WHERE contenido_guibipay.iduser = '$iduser' ");
if ($query_delete) {

  $resumen_tabla_producto='';

  $query_resultados_productos = mysqli_query($conection,"SELECT * FROM contenido_guibipay
     WHERE iduser = '$iduser'");
  $resultados_producto = mysqli_fetch_array($query_resultados_productos);
  while ($resultados_producto = mysqli_fetch_array($query_resultados_productos)) {
    $idproductogenerado  = $resultados_producto['idp'];
  $queryproducto_generado = mysqli_query($conection,"SELECT * FROM producto_venta WHERE producto_venta.idproducto ='$idproductogenerado'");
  $data_producto_generado = mysqli_fetch_array($queryproducto_generado);



    $resumen_tabla_producto = '
    <tr>
        <td>'.$data_producto_generado['nombre'].'</td>
        <td>'.$resultados_producto['cantidad_guibis'].'</td>
        <td>'.$data_producto_generado['descripcion'].'</td>
        <td>'.$resultados_producto['precio_unidad'].'</td>
        <td>'.$resultados_producto['precio_total'].'</td>
    </tr>'.$resumen_tabla_producto.'
    ';
  }

  $query_lista_t = mysqli_query($conection,"SELECT SUM(((contenido_guibipay.cantidad)*(contenido_guibipay.precio_unidad))) as
'compra_total'
FROM contenido_guibipay
WHERE contenido_guibipay.iduser = '$iduser'");
$data_lista_t=mysqli_fetch_array($query_lista_t);

$resumen_pago= '
       <table id="example2" class="table table-bordered table-hover table-responsive">
       <tr class="tabala_ch">
       <th>Total</th>
       </tr>
       <tr>
       <td class="out_number">$ '.number_format($data_lista_t['compra_total'],2).'</td>
       </tr>
       </table>';


       $arrayName = array('respuesta' =>'consola_limpia','resumen_tabla_producto'=>$resumen_tabla_producto,'resumen_pago'=>$resumen_pago);
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

  // code...
}else {
  $arrayName = array('noticia' =>'error_limpiar_consola');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
}
   // code...
 }

 if ($_POST['action'] == 'infomracion_venta') {

   $resumen_tabla_producto='';
                   $query_resultados_productos = mysqli_query($conection,"SELECT * FROM contenido_guibipay
                      WHERE  iduser = '$iduser'");
                   while ($resultados_producto = mysqli_fetch_array($query_resultados_productos)) {
                     $idproductogenerado  = $resultados_producto['idp'];
                   $queryproducto_generado = mysqli_query($conection,"SELECT * FROM producto_venta WHERE producto_venta.idproducto ='$idproductogenerado'");
                   $data_producto_generado = mysqli_fetch_array($queryproducto_generado);



                     $resumen_tabla_producto = '
                     <tr>
                         <td>'.$data_producto_generado['nombre'].'</td>
                         <td>'.$resultados_producto['cantidad'].'</td>
                         <td>'.$resultados_producto['precio_unidad'].'</td>
                         <td>'.$resultados_producto['precio_total'].'</td>
                     </tr>'.$resumen_tabla_producto.'
                     ';
                   }

                   $query_lista_t = mysqli_query($conection,"SELECT SUM(((contenido_guibipay.cantidad)*(contenido_guibipay.precio_unidad))) as
                 'compra_total'
                 FROM contenido_guibipay
                 WHERE contenido_guibipay.iduser = '$iduser'");
                 $data_lista_t=mysqli_fetch_array($query_lista_t);

                 $resumen_pago= '
                        <table id="example2" class="table table-bordered table-hover table-responsive">
                        <tr class="tabala_ch">
                        <th>Total</th>
                        </tr>
                        <tr>
                        <td class="out_number">$ '.number_format($data_lista_t['compra_total'],2).'</td>
                        </tr>
                        </table>';

                   $arrayName = array('respuesta' =>'existencia_correcta','resumen_tabla_producto'=>$resumen_tabla_producto,'resumen_pago'=>$resumen_pago);
                   echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

   // code...
 }

 if ($_POST['action'] == 'verificar_email_comprador') {


   $email_comprador = $_POST['email_comprador'];

   $query_email_verificador = mysqli_query($conection,"SELECT * FROM usuarios
      WHERE  email = '$email_comprador'");
         $result_usuario = mysqli_num_rows($query_email_verificador);

         if ($result_usuario > 0) {
           $resultados_usuario = mysqli_fetch_array($query_email_verificador);

           $email_usuario = $resultados_usuario['email'];
           $nombres = $resultados_usuario['nombres'];
           $apellidos = $resultados_usuario['apellidos'];
           $id_comprador = $resultados_usuario['id'];

           date_default_timezone_set("America/Lima");
           $mifecha = new DateTime();
           $mifecha->modify('+7 minute');
          $y =  $mifecha->format('Y-m-d H:i:s');
           $numero_aleatorio = rand(100000, 999999);

           $query_insert=mysqli_query($conection,"INSERT INTO  codigo_seguridad_email(id_tienda,id_user,codigo,fecha_maxima)
                                                            VALUES('$iduser','$id_comprador','$numero_aleatorio','$y') ");

            if ($query_insert) {
              $query_insert=mysqli_query($conection,"INSERT INTO  codigo_seguridad_email(id_tienda,id_user,codigo,fecha_maxima)
                                                               VALUES('$iduser','$id_comprador','$numero_aleatorio','$y') ");
              try {
                // Configuración del servidor
                $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
                $mail -> isSMTP ();                                          // Enviar usando SMTP
                $mail -> Host        = 'mail.guibis.com' ;                  // Configure el servidor SMTP para enviar a través de
                $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
                $mail ->Username = 'pay@guibis.com' ;                       // Nombrede usuario SMTP
                $mail ->Password = 'MACAra666_' ;                               // Contraseña SMTP
                $mail -> SMTPSecure = 'ssl';         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
                $mail -> Port        = 465 ;                                     // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

                // Destinatarios
                $mail -> setFrom ( 'pay@guibis.com' , 'guibispay' );  // Agrega un destinatario
                $mail -> addAddress ($email_usuario);     // Agrega un destinatario
                $mail -> addAddress ('pay@guibis.com');
                // Contenido
                $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
                $mail->CharSet = 'UTF-8';
                $mail -> Subject = 'VERIFICACIÓN DE EMAIL';
                $mail -> Body     = '

                <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
                <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
                <div class="logo-empresa" style="text-align: center;">
                <img src="https://guibis.com/home/img/reacciones/guibis.png" alt="" style="width: 200px;">
                </div>
                <div class="contenedor-informacion" style="text-align: justify;">
                <div class="">
                <div class="">
                <p style="padding: 5px;">Estimado(a) '.$nombres.' '.$apellidos.', </p>

                <p style="padding: 5px;">Este es un mensaje de verificación a continuación tienes el código de seguridad. </p>
                <p style="padding: 5px;">Proporciona este código al usuario que te esta vendiendo, no entregues a ninguna persona adicional más que al vendedor/p>
                <p style="padding: 5px;">Posterior a que entregues el código dirigite a <a target="_blank"  href="https://guibis.com/home/active-token">Activar Token</a>, que escanee y ya esta hecha tu compra. </p>
                <p style="padding: 5px;">¡Gracias por elegir nuestra empresa de comercio electrónico para sus compras en línea!</p>
                <p style="padding: 5px;">Si necesitas más información o tienes alguna pregunta, no dudes en comunicarte con nosotros. </p>
                <p style="padding: 5px;">Agradecemos tu cooperación en este proceso y valoramos tu compromiso con nuestros procesos de venta. </p>

                <div class="codigo_seguridad" style="width: 40%;margin: 0 auto;">
                <p style="background: #263238;color: #fff;text-align: center;padding: 15px;font-size: 25px;">'.$numero_aleatorio.'</p>
                </div>


                <p style="padding: 5px;"> Atentamente: </p>
                <p style="padding: 5px;"> GUIBIS.COM </p>

                </div>
                </div>

                <div class="redes-sociales">
                  <div class="redes_email" style="text-align: center;">
                    <a style="text-align: center; margin:3px; padding:4px;" href="https://www.facebook.com/guibisEcuador"><img src="https://guibis.com/home/img/reacciones/facebook.png" alt="" width="50px;"> </a>
                    <a style="text-align: center; margin:3px; padding:4px;" href="https://www.youtube.com/channel/UCUv90_DETO87rRse6GKCJvg"><img src="https://guibis.com/home/img/reacciones/youtube.png" alt="" width="50px;"> </a>
                    <a style="text-align: center; margin:3px; padding:4px;" href="https://api.whatsapp.com/send?phone=593960055956&amp;text=Hola!&nbsp;Vengo&nbsp;De&nbsp;Guibis&nbsp;"><img src="https://guibis.com/home/img/reacciones/whatsapp.png" alt="" width="50px;"> </a>
                  </div>
                </div>
                <div class="footer" style="font-size: 10px;">
                  <p style="color: #c1c1c1;">Los usuarios de ventas tienen la opción de presentar un contrareporte en respuesta a un reporte de no entrega presentado por un cliente.
                    Para llevar a cabo el proceso de resolución de conflictos, se requiere la evaluación del Departamento Legal de la empresa.
                    Si la decisión final favorece al usuario de ventas, no habrá costos adicionales asociados con el proceso de resolución de conflictos.
                    Si la decisión final favorece al cliente, el usuario de ventas deberá pagar una comisión del 2% del valor total de la venta, además de una comisión del 3% sobre el precio de compra.
                    La decisión final se tomará después de un período de 24 horas laborables desde la presentación del contrareporte.
                    Si el usuario de ventas no está de acuerdo con la decisión final, se pueden tomar medidas adicionales, que pueden incluir costos adicionales.
                    La empresa se reserva el derecho de cancelar o modificar el proceso de resolución de conflictos en cualquier momento y sin previo aviso.
                    La empresa no se hace responsable por cualquier daño o pérdida resultante del proceso de resolución de conflictos.
                    El uso del proceso de contrareporte y resolución de reportes de no entrega implica la aceptación de estos términos y condiciones.
                    Al presentar un contrareporte, el usuario de ventas reconoce haber leído y comprendido estos términos y condiciones y acepta cumplir con ellos en su totalidad.</p>

                  </div>

                </div>
              </div>

            </body>

            ' ;
            $mail -> send ();
          } catch ( Exception  $e ) {
          }

          $arrayName = array('respuesta' =>'envio_exitoso');
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);


        }else {
          $arrayName = array('respuesta' =>'error_servidor');
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
        }

       // code..
     }else {
       $arrayName = array('respuesta' =>'no_existe_usuario');
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }



 if ($_POST['action'] == 'verificar_codigo_seguridad') {

   $codigo_seguridad = $_POST['codigo_seguridad'];
   $query_verificador = mysqli_query($conection,"SELECT * FROM  codigo_seguridad_email
      WHERE  codigo = '$codigo_seguridad' AND id_tienda = '$iduser'");

      $result_codigo = mysqli_num_rows($query_verificador);
      if ($result_codigo >0) {
        $arrayName = array('respuesta' =>'existe_dato');
              echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
        // code...
      }else {
        $arrayName = array('respuesta' =>'no_existe_dato');
        echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
      }



   // code...
 }



 ?>
