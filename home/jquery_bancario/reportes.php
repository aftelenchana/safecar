<?php


require("../mail/PHPMailer-master/src/PHPMailer.php");
require("../mail/PHPMailer-master/src/Exception.php");
require("../mail/PHPMailer-master/src/SMTP.php");

use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
include "../../coneccion.php";
  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
     session_start();
     $iduser= $_SESSION['id'];

     $queryu = mysqli_query($conection, "SELECT * FROM usuarios  WHERE id = '$iduser'");
     $resultu = mysqli_fetch_array($queryu);
     $email = $resultu['email'];
     $nombres = $resultu['nombres'];
      $apellidos = $resultu['apellidos'];
      $mi_leben = $resultu['mi_leben'];


     if ($_POST['action'] == 'info_reportes') {
       $movimiento = $_POST['movimiento'];

       $query_movimiento = mysqli_query($conection, "SELECT * FROM `reporte_movimientos` WHERE iduser = $iduser AND idmovimiento = '$movimiento'");
       $result_movimientos = mysqli_num_rows($query_movimiento);
       if ($result_movimientos>0) {
           $data_movimiento = mysqli_fetch_array($query_movimiento);
           $reporte = $data_movimiento['reporte'];
            $fecha = $data_movimiento['fecha'];
             $estado = $data_movimiento['estado'];
              $resolucion = $data_movimiento['resolucion'];

           $arrayName = array('respuesta' =>'existente','movimiento'=>$movimiento,'reporte'=>$reporte,'fecha'=>$fecha,'estado'=>$estado,'resolucion'=>$resolucion);
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

       }else {
         $arrayName = array('respuesta' =>'no_existente','movimiento'=>$movimiento);
         echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

       }

     }

     if ($_POST['action'] == 'agregar_reporte_movimientos') {
       $movimiento = $_POST['movimiento'];
       $descripcion_reporte = $_POST['descripcion_reporte'];


       $query_movimiento = mysqli_query($conection, "SELECT * FROM `reporte_movimientos` WHERE iduser = $iduser AND idmovimiento = '$movimiento'");
       $result_movimientos = mysqli_num_rows($query_movimiento);
       if ($result_movimientos>0) {
           $data_movimiento = mysqli_fetch_array($query_movimiento);

           $arrayName = array('respuesta' =>'existente');
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

       }else {
         $query_historial_principal=mysqli_query($conection,"INSERT INTO reporte_movimientos(iduser,idmovimiento,reporte)
         VALUES('$iduser','$movimiento','$descripcion_reporte') ");
         if ($query_historial_principal) {

           try {
                // Configuración del servidor
               $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
               $mail -> isSMTP ();                                          // Enviar usando SMTP
               $mail -> Host        = 'cwpjava.hostingsupremo.org' ;                  // Configure el servidor SMTP para enviar a través de
               $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
               $mail ->Username = 'soporte@guibis.com' ;                     // Nombrede usuario SMTP
               $mail ->Password = 'MACAra666_' ;                               // Contraseña SMTP
               $mail -> SMTPSecure = 'ssl';         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
               $mail -> Port        = 465 ;                                     // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

               // Destinatarios
               $mail -> setFrom ( 'soporte@guibis.com' , 'SOPORTE' );
               $mail -> addAddress ( $email);     // Agrega un destinatari
               $mail -> addAddress ('departamento-legal@guibis.com');    // Agrega un destinatario
               $mail -> addAddress ('soporte@guibis.com');    // Agrega un destinatario
               $mail -> addAddress ('financiero@guibis.com');    // Agrega un destinatario
               // Contenido
               $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
                   $mail->CharSet = 'UTF-8';
               $mail -> Subject = 'SOPORTE MOVIMIENTO '.$movimiento.'';
               $mail -> Body     = '
               <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
                 <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
                   <div class="logo-empresa" style="text-align: center;">
                     <img src="https://guibis.com/home/img/reacciones/guibis.png" alt="" style="width: 200px;">
                   </div>
                   <div class="contenedor-informacion" style="text-align: justify;">
                     <div class="">
                       <div class="">
                         <p style="padding: 5px;">Estimado(a) '.$nombres.' </p>

                         <p style="padding: 5px;">Estamos evaluando cuidadosamente el reporte, nuestro equipo te dara una pronta solución. </p>
                         <p style="padding: 5px;">Resumen:Código de reporte '.$movimiento.' si tienes alguna duda o enviar documentos se lo puede realizar a departamento-legal@guibis.com </p>
                         <p style="padding: 5px;">El reporte a este movimiento con resumen: '.$descripcion_reporte.' se ha enviado con éxito </p>
                         <p style="padding: 5px;">¡Gracias por elegir nuestra empresa de comercio electrónico para sus compras en línea!</p>
                         <p style="padding: 5px;">Si necesitas más información o tienes alguna pregunta, no dudes en comunicarte con nosotros. </p>
                         <p style="padding: 5px;">Agradecemos tu cooperación en este proceso y valoramos tu compromiso con nuestros procesos de venta. </p>
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








           $arrayName = array('respuesta' =>'exitoso');
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
         }else {
           $arrayName = array('respuesta' =>'error');
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
         }
       }









     }

 ?>
