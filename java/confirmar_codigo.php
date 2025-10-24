<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
  include "../coneccion.php";
$iduser = $_POST['iduser'];


$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;


     $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
     $result_documentos = mysqli_fetch_array($query_doccumentos);
     $user_in     = $result_documentos['id'];


$query=mysqli_query($conection,"SELECT *FROM  usuarios WHERE id='$iduser'");
$result = mysqli_fetch_array($query);

$email = $result['email'];


$codigo_recuperacion = md5($_POST['codigo_recuperacion']);
$new_password = md5($_POST['new_password']);
$query=mysqli_query($conection,"SELECT *FROM  usuarios WHERE email='$email'");
$result = mysqli_fetch_array($query);
$codigp_password = $result['codigp_password'];
if ($result > 0) {
  if ($codigo_recuperacion == $codigp_password) {


    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;



      $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$user_in'");
      $result_documentos = mysqli_fetch_array($query_doccumentos);
      $regimen = $result_documentos['regimen'];
      $contabilidad             = $result_documentos['contabilidad'];
      $email_empresa_emisor     = $result_documentos['email'];
      $celular_empresa_emisor   = $result_documentos['celular'];
      $telefono_empresa_emisor  = $result_documentos['telefono'];
      $direccion_emisor          = $result_documentos['direccion'];
      $whatsapp                 = $result_documentos['whatsapp'];
      $nombres_user                  = $result_documentos['nombres'];
      $apellidos_user                = $result_documentos['apellidos'];
      $numero_identificacion_emisor  = $result_documentos['numero_identidad'];
      $contribuyente_especial   = $result_documentos['contribuyente_especial'];
      $estableciminento_f      = $result_documentos['estableciminento_f'];
      $contabilidad            = $result_documentos['contabilidad'];
      $punto_emision_f         = $result_documentos['punto_emision_f'];
      $img_facturacion         = $result_documentos['img_facturacion'];
      $contabilidad         = $result_documentos['contabilidad'];
      $regimen         = $result_documentos['regimen'];
      $url_img_upload                     = $result_documentos['url_img_upload'];

      $nombre_empresa               = $result_documentos['nombre_empresa'];

      $host_envio               = $result_documentos['host_envio'];
      $puerto_email_envio       = $result_documentos['puerto_email_envio'];
      $email_user_name_envio    = $result_documentos['email_user_name_envio'];
      $password_envio_email     = $result_documentos['password_envio_email'];
      $descripcion_envio_email  = $result_documentos['descripcion_envio_email'];
      if (empty($host_envio)) {
        $host_envio = 'mail.guibis.com';
      }
      if (empty($puerto_email_envio)) {
        $puerto_email_envio = '465';
      }
      if (empty($password_envio_email)) {
        $password_envio_email = 'NK6)&HY}V9B^Q)So;k2a';
      }
      if (empty($email_user_name_envio)) {
        $email_user_name_envio = 'guibis@guibis.com';
      }

        $iduser = $result['id'];
        $nombres = $result['nombres'];
        $apellidos = $result['apellidos'];
        $query_insert=mysqli_query($conection,"UPDATE usuarios SET  password='$new_password' WHERE id='$iduser' ");
        if ($query_insert) {


                  try {
                       // Configuración del servidor
                       $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
                       $mail -> isSMTP ();                                          // Enviar usando SMTP
                       $mail -> Host        = "$host_envio" ;                  // Configure el servidor SMTP para enviar a través de
                       $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
                       $mail->Username = "$email_user_name_envio";
                       $mail->Password = "$password_envio_email";                              // Contraseña SMTP
                       $mail -> SMTPSecure = 'ssl';         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
                         $mail->Port = "$puerto_email_envio";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

                         // Destinatarios cambia por tu info esa es la falla? sia ok xD
                         $mail -> setFrom ( $email_user_name_envio , 'Sistema de Recuperacion de Contraseña' );
                         $mail -> addAddress ( $email);     // Agrega un destinatario
                         $mail -> addAddress ($email_user_name_envio );
                         $mail -> addAddress ('guibis@guibis.com');


                      // Contenido
                      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
                      $mail->CharSet = 'UTF-8';
                      $mail -> Subject = 'Cambio de contraseña Exitoso' ;
                      $mail->Body = "
                              <body style='background: #f5f5f5;padding: 6px;margin: 25px;'>
                                  <div class='contenedor' style='background: #fff;padding: 20px;margin: 10px;'>
                                      <div class='logo-empresa' style='text-align: center;'>
                                          <img src='{$url}/img/guibis.png' alt='Logo de la Empresa' style='width: 200px;'>
                                      </div>
                                      <div class='contenedor-informacion' style='text-align: justify;'>
                                          <p>¡Hola <span>{$nombres}</span>!</p>
                                          <p>Queremos informarte que tu contraseña en {$nombre_empresa} ha sido cambiada exitosamente.</p>
                                          <p>Si has realizado este cambio, no necesitas hacer nada más.</p>
                                          <p>Si no has solicitado un cambio de contraseña, por favor, ponte en contacto con nosotros inmediatamente respondiendo a este correo para que podamos ayudarte a recuperar el acceso a tu cuenta.</p>
                                          <p>Recuerda que es una buena práctica cambiar tus contraseñas regularmente y asegurarte de que sean únicas y seguras.</p>
                                          <p>Si tienes alguna pregunta o encuentras algún problema, estamos aquí para ayudarte.</p>
                                          <p>Saludos cordiales,<br>El Equipo de {$nombre_empresa}</p>
                                      </div>
                                  </div>
                              </body>
                          ";

                      $mail -> send ();
                  } catch ( Exception  $e ) {
                  }


           $arrayName = array('resp_password' =>'positiva' );
          echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);



        }else {
          $arrayName = array('resp_password' =>'error_servidor' );
         echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

        }

  }else {
    $arrayName = array('resp_password' =>'ingrese_codigo_valido' );
   echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
  }




}else {
  $arrayName = array('resp_password' =>'cuenta_inexistente' );
 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

  }



 ?>
