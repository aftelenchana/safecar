<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
  include "../coneccion.php";


  if ($_POST['action'] == 'primero_paso_recuperar_password') {

    $email = strtolower($_POST['email']);
    $query=mysqli_query($conection,"SELECT *FROM  usuarios WHERE email='$email'");
    $result = mysqli_fetch_array($query);
    if ($result > 0) {
      $iduser = $result['id'];
      $nombres = $result['nombres'];

     $password = rand(100000, 999999);
     $password_guardar = md5($password);

         $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;





      $query_insert=mysqli_query($conection,"UPDATE usuarios SET  codigp_password='$password_guardar' WHERE id='$iduser' ");

      if ($query_insert) {

                try {


                  $query_correo_registro= mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'registro'");
                  $data_correo_registro = mysqli_fetch_array($query_correo_registro);

                  $Host_registro        = $data_correo_registro['Host'];
                  $Username_registro    = $data_correo_registro['Username'];
                  $Password_registro    = $data_correo_registro['Password'];
                  $Port_registro        = $data_correo_registro['Port'];
                  $SMTPSecure_registro  = $data_correo_registro['SMTPSecure'];

                  $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
                  $mail -> isSMTP ();                                          // Enviar usando SMTP
                  $mail -> Host        = "$Host_registro" ;                  // Configure el servidor SMTP para enviar a través de
                  $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
                  $mail->Username = "$Username_registro";
                  $mail->Password = "$Password_registro";                              // Contraseña SMTP
                  $mail -> SMTPSecure = "$SMTPSecure_registro";         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
                  $mail->Port = "$Port_registro";                            // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

                    // Destinatarios cambia por tu info esa es la falla? sia ok xD
                    $mail -> setFrom ( $Username_registro , 'Sistema de Recuperacion de Contraseña' );
                    $mail -> addAddress ($email);     // Agrega un destinatario


                    // Contenido
                    $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
                    $mail->CharSet = 'UTF-8';
                    $mail -> Subject = 'Recuperacion de Contraseña' ;

                      $mail->Body = "
                          <body style='background: #f5f5f5;padding: 6px;margin: 25px;'>
                              <div class='contenedor' style='background: #fff;padding: 20px;margin: 10px;'>
                                  <div class='logo-empresa' style='text-align: center;'>
                                   <img src='{$url}/img/guibis.png' alt='Logo de la Empresa' style='width: 200px;'>
                                  </div>
                                  <div class='contenedor-informacion' style='text-align: justify;'>
                                      <p>¡Hola <span>{$nombres}</span>!</p>
                                      <p>Hemos recibido una solicitud para restablecer tu contraseña en  Ecuacompus.</p>
                                      <p>Si no has realizado esta solicitud, por favor ignora este correo. Si deseas restablecer tu contraseña, utiliza el siguiente PIN:</p>
                                      <p style='text-align: center;'>
                                          <strong>{$password}</strong>
                                      </p>
                                      <p>Por favor, introduce este PIN en la página de restablecimiento de contraseña para continuar con el proceso. Este PIN es válido por 30 minutos.</p>
                                      <p>Si tienes alguna pregunta o encuentras algún problema, no dudes en responder a este correo. Estamos aquí para ayudarte.</p>
                                      <p>Saludos cordiales,<br>El Equipo de Ecuacompus</p>
                                  </div>
                              </div>
                          </body>
                      ";


                      if (!$mail->send()) {
                          // Manejo del caso de fallo en el envío
                          $response = array('noticia_email' => 'error_envio','resp_password' =>'positiva','iduser' =>$iduser);
                          echo json_encode($response, JSON_UNESCAPED_UNICODE);
                      } else {
                          // Manejo del caso de éxito en el envío
                          $response = array('noticia_email' => 'envio_exitoso','resp_password' =>'positiva','iduser' =>$iduser);
                          echo json_encode($response, JSON_UNESCAPED_UNICODE);
                      }
                  } catch (Exception $e) {
                      // Manejo de una excepción durante la configuración o el envío
                      $response = array('noticia_email' => 'error_excepcion','resp_password' =>'positiva','detalle' => 'Ocurrió un error al intentar enviar el correo','error' => $e->getMessage());
                      echo json_encode($response, JSON_UNESCAPED_UNICODE);
                  }


      }else {
        $arrayName = array('resp_password' =>'error_enviar_codigo');
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);


      }






    }else {
      $arrayName = array('resp_password' =>'no_existe_email');
     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);


      }


    // code...
  }


if ($_POST['action'] == 'verificar_codigo') {

  $iduser = $_POST['idsuer'];


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





          $iduser = $result['id'];
          $nombres = $result['nombres'];



          $query_insert=mysqli_query($conection,"UPDATE usuarios SET  password='$new_password' WHERE id='$iduser' ");
          $nombre_empresa = 'Ecuacompus';


          if ($query_insert) {

            $query_correo_registro= mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'registro'");
            $data_correo_registro = mysqli_fetch_array($query_correo_registro);

            $Host_registro        = $data_correo_registro['Host'];
            $Username_registro    = $data_correo_registro['Username'];
            $Password_registro    = $data_correo_registro['Password'];
            $Port_registro        = $data_correo_registro['Port'];
            $SMTPSecure_registro  = $data_correo_registro['SMTPSecure'];


                    try {
                         // Configuración del servidor
                         $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
                         $mail -> isSMTP ();                                          // Enviar usando SMTP
                         $mail -> Host        = "$Host_registro" ;                  // Configure el servidor SMTP para enviar a través de
                         $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
                         $mail->Username = "$Username_registro";
                         $mail->Password = "$Password_registro";                              // Contraseña SMTP
                         $mail -> SMTPSecure = "$SMTPSecure_registro";         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
                         $mail->Port = "$Port_registro";                           // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba

                           // Destinatarios cambia por tu info esa es la falla? sia ok xD
                           $mail -> setFrom ( $Username_registro , 'Sistema de Recuperacion de Contraseña' );
                           $mail -> addAddress ( $email);     // Agrega un destinatario


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


             $arrayName = array('resp_password' =>'contrasena_cambiada' );
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

  // code...
}




 ?>
