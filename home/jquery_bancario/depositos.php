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



$queryu = mysqli_query($conection, "SELECT * FROM usuarios  WHERE id = '$iduser'");
$resultu = mysqli_fetch_array($queryu);
$email = $resultu['email'];
$nombres = $resultu['nombres'];
 $apellidos = $resultu['apellidos'];
 $mi_leben = $resultu['mi_leben'];
  $celular = $resultu['celular'];


  $query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
  $result_configuracion = mysqli_fetch_array($query_configuracioin);
  $ambito_area          =  $result_configuracion['ambito'];
  $envio_wsp          =  $result_configuracion['envio_wsp'];




      if ($_POST['action'] == 'deposito_comprobante') {
        $cantidad      = $_POST['cantidad'];
        $tipo_banco    = $_POST['tipo_banco'];
        $numero_unico  = $_POST['numero_unico'];;
        if (empty($_POST['tipo_banco'])) {
          $arrayName = array('noticia' =>'entidad_bancaria_vacia');
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
          exit;
        }
        //if ($mi_leben != 'Activa') {
        //  $arrayName = array('noticia' =>'cuenta_bancaria_inactiva');
        //  echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
        //  exit;
        //}

        $query = mysqli_query($conection, "SELECT * FROM usuarios WHERE usuarios.id = $iduser");
        $result = mysqli_fetch_array($query);
        $password_bd =  $result['password'];
        /*consulta del numero nunico , iya existe en la base de datos no se hace del deposito*/

        $query_comprobante = mysqli_query($conection, "SELECT * FROM saldo_leben_1804843900 WHERE numero_unico = $numero_unico AND tipo_banco='$tipo_banco'");
        $result_comporbante = mysqli_fetch_array($query_comprobante);
        if ($result_comporbante) {
          $numero_unico_bd =  $result_comporbante['numero_unico'];
          if ($numero_unico_bd == $numero_unico ) {
            $arrayName = array('noticia' =>'comprobante_igual');
            echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
            exit;
            // code...
          }
          /**/
        }

        $foto           =    $_FILES['foto'];
        $nombre_foto    =    $foto['name'];
        $type 					 =    $foto['type'];
        $url_temp       =    $foto['tmp_name'];

        $imgProducto   =   'img_producto.jpg' || 'img_producto.png';
        if ($nombre_foto != '') {
         $destino = '../img/uploads/';
         $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
         $imgProducto = $img_nombre.'.jpg';
         $imgProducto2 = $img_nombre.'.png';
         $src = $destino.$imgProducto;
               move_uploaded_file($url_temp,$src);
        }
        $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url_dominio = $protocol . $domain;

        $query_insert=mysqli_query($conection,"INSERT INTO saldo_leben_1804843900(id_usuario,img_deposito,cantidad,numero_unico,metodo,tipo_banco,url_deposito,url_img_upload)
                                      VALUES('$iduser','$imgProducto','$cantidad','$numero_unico','Deposito','$tipo_banco','$url_dominio','$url_dominio') ");
                                      if ($query_insert) {
                                        $query_correo_facturacion = mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'alternativo'");
                                        $data_correo_facturacion= mysqli_fetch_array($query_correo_facturacion);

                                        $Host_facturacion        = $data_correo_facturacion['Host'];
                                        $Username_facturacion    = $data_correo_facturacion['Username'];
                                        $Password_facturacion    = $data_correo_facturacion['Password'];
                                        $Port_facturacion        = $data_correo_facturacion['Port'];
                                        $SMTPSecure_facturacion  = $data_correo_facturacion['SMTPSecure'];

                                        $imagen_deposito = $url_dominio.'/home/img/uploads/'.$imgProducto;

                                        try {
                                          $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
                                          $mail -> isSMTP ();                                          // Enviar usando SMTP
                                          $mail -> Host       = $Host_facturacion ;                  // Configure el servidor SMTP para enviar a través de
                                          $mail -> SMTPAuth   = true ;                                   // Habilita la autenticación SMTP
                                          $mail ->Username    =   $Username_facturacion ;                 // Nombrede usuario SMTP
                                          $mail ->Password    =   $Password_facturacion;                               // Contraseña SMTP
                                          $mail -> SMTPSecure = $SMTPSecure_facturacion;         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
                                          $mail -> Port       = $Port_facturacion ;                                    // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

                                          // Destinatarios
                                          $mail -> setFrom ( $Username_facturacion , 'Equipo de Finanzas de Guibis.com' );
                                          $mail -> addAddress ('alejiss401997@gmail.com');
                                          $mail -> addAddress ( $email);

                                            // Contenido
                                            $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
                                            $mail->CharSet = 'UTF-8';
                                            $mail->Subject = 'Notificación de Depósito Recibido';
                                                $mail->Body = '
                                                <body style="background: #f5f5f5; padding: 20px; margin: 0 auto; font-family: Arial, sans-serif;">
                                                    <div style="max-width: 600px; margin: 0 auto; background: #FFFFFF; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                                        <div style="text-align: center; margin-bottom: 25px;">
                                                            <img src="https://guibis.com/img/guibis.png" alt="Logo Guibis" style="width: 100px;">
                                                        </div>
                                                        <div style="background: #4CAF50; color: #ffffff; padding: 20px; text-align: center; border-radius: 8px;">
                                                            <h2 style="color: #ffffff; margin: 0;">Notificación de Depósito Pendiente de Validación</h2>
                                                        </div>
                                                        <div style="padding: 20px; background-color: #f5f5f5; color: #333333; border-radius: 8px; margin-top: 20px;">
                                                            <p>Estimado usuario,</p>
                                                            <p>Estimado/a '.$nombres.' '.$apellidos.' hemos recibido la información de tu depósito. Está pendiente de validación por nuestro equipo financiero. A continuación, se detallan los datos del depósito que has realizado:</p>

                                                            <ul>
                                                                <li>Cantidad depositada: $' . $cantidad . '</li>
                                                                <li>Entidad bancaria: ' . $tipo_banco . '</li>
                                                                <li>Número único de transacción: ' . $numero_unico . '</li>
                                                            </ul>

                                                            <p>Además, hemos recibido la imagen del comprobante de depósito que adjuntaste:</p>
                                                            <img src="'.$imagen_deposito.'" alt="Comprobante de Depósito" style="max-width:100%; height:auto; display: block; margin: 0 auto; border: 1px solid #ddd; padding: 10px; max-height: 500px;">

                                                            <p>Revisaremos el depósito y nos pondremos en contacto contigo una vez que se haya completado la validación. Si es necesario, podríamos requerir información adicional.</p>

                                                            <p>Si tienes alguna pregunta o requieres asistencia adicional, por favor no dudes en contactar a nuestro equipo de soporte.</p>

                                                            <p>Gracias por tu confianza en nosotros.</p>

                                                            <p>Atentamente,<br>El Equipo de Guibis.com</p>
                                                        </div>
                                                    </div>
                                                </body>
                                                ';


                                                $mensajeWhatsApp = "🏦 *Depósito Pendiente de Validación*\n\n";

                                                $mensajeWhatsApp .= "Hola *".$nombres." ".$apellidos."*, hemos recibido la información de tu depósito.\n\n";

                                                $mensajeWhatsApp .= "*Detalles del Depósito:*\n";
                                                $mensajeWhatsApp .= "Cantidad: *$".$cantidad."*\n";
                                                $mensajeWhatsApp .= "Banco: *".$tipo_banco."*\n";
                                                $mensajeWhatsApp .= "Número de Transacción: *".$numero_unico."*\n\n";

                                                $mensajeWhatsApp .= "Está pendiente de validación por nuestro equipo financiero. Revisaremos la información y te notificaremos una vez que el proceso esté completo.\n\n";

                                                $mensajeWhatsApp .= "Puedes ver la imagen del comprobante en el siguiente enlace:\n";

                                                $mensajeWhatsApp .= "Gracias por tu paciencia.\n\n";

                                                $mensajeWhatsApp .= "*Equipo de Guibis.com*";

                                                $mensajeWhatsApp .= $imagen_deposito;



                                                   if ($envio_wsp == 'SI') {
                                                        include '../mensajes/mensajes.php';
                                                        $respuestaDeposito    = enviarMensajeWhatsApp_guibis($celular, $mensajeWhatsApp);
                                                   }


                                                  if (!$mail->send()) {
                                                      // Manejo del caso de fallo en el envío
                                                      $response = array('noticia_email' => 'error_envio','noticia' =>'pago_agregado');
                                                      echo json_encode($response, JSON_UNESCAPED_UNICODE);
                                                  } else {
                                                      // Manejo del caso de éxito en el envío
                                                      $response = array('noticia_email' => 'envio_exitoso','noticia' =>'pago_agregado');
                                                      echo json_encode($response, JSON_UNESCAPED_UNICODE);
                                                  }
                                              } catch (Exception $e) {
                                                  // Manejo de una excepción durante la configuración o el envío
                                                  $response = array('noticia_email' => 'error_excepcion','noticia' =>'pago_agregado','detalle' => 'Ocurrió un error al intentar enviar el correo','error' => $e->getMessage());
                                                  echo json_encode($response, JSON_UNESCAPED_UNICODE);
                                              }

                                        }else {
                                          $arrayName = array('noticia' =>'error');
                                         echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
                                        }



      }


 ?>
