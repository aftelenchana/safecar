<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;


function envio_email($iduser, $accion,$celular_db,$pais,$provincia,$ciudad,$sistema_operativo,$buscador,$direccion_ip,$rol) {

  include "../coneccion.php";
    mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
  $query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
  $result_configuracion = mysqli_fetch_array($query_configuracioin);
  $ambito_area          =  $result_configuracion['ambito'];
  $envio_wsp          =  $result_configuracion['envio_wsp'];


  // Asumimos que la sesión está activa y tenemos la información del dominio
  $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
  $domain = $_SERVER['HTTP_HOST'];

  $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
  $result_documentos = mysqli_fetch_array($query_doccumentos);

  if ($result_documentos) {
      $url_img_upload = $result_documentos['url_img_upload'];
      $img_facturacion = $result_documentos['img_facturacion'];

      $nombre_empresa = $result_documentos['nombre_empresa'];

      // Asegúrate de que esta ruta sea correcta y corresponda con la estructura de tu sistema de archivos
      $img_sistema = $url_img_upload.'/home/img/uploads/'.$img_facturacion;
  } else {
      // Si no hay resultados, tal vez quieras definir una imagen por defecto
    $img_sistema = '/img/guibis.png';
  }

 // La instanciación y el paso de `true` habilita excepciones
  $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;



$fecha_actual =  date("Y-m-d H:i:s");
include "../coneccion.php";
  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

  if ($rol == 'Cliente') {
    $queryu = mysqli_query($conection, "SELECT * FROM clientes  WHERE id = '$iduser'");
    $resultu = mysqli_fetch_array($queryu);
    $nombres_usuario   = $resultu['nombres'];
    $email_usuario     = $resultu['mail'];
    // code...
  }

  if ($rol == 'Cuenta Empresa') {
    $queryu = mysqli_query($conection, "SELECT * FROM usuarios  WHERE id = '$iduser'");
    $resultu = mysqli_fetch_array($queryu);
    $nombres_usuario   = $resultu['nombres'].' '.$resultu['apellidos'];
    $email_usuario     = $resultu['email'];
    // code...
  }




  //CODIGO PARA VERIFICAR LA CUENTA

  if ($accion == 'verificar_cuenta') {

    $texto_noticia = "Se te ha enviado un enlace de verificación a tu email  $sistema_operativo - $buscador - $pais - $provincia - $ciudad - $direccion_ip";

    $query_insert_notificaciones = mysqli_query($conection,"INSERT INTO notificaciones_guibis  (iduser,texto,estado)
                                                                               VALUES('$iduser','$texto_noticia','Exitoso') ");

    $query_correo_registro= mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'registro'");
    $data_correo_registro = mysqli_fetch_array($query_correo_registro);

    $Host_registro        = $data_correo_registro['Host'];
    $Username_registro    = $data_correo_registro['Username'];
    $Password_registro    = $data_correo_registro['Password'];
    $Port_registro        = $data_correo_registro['Port'];
    $SMTPSecure_registro  = $data_correo_registro['SMTPSecure'];

    $codigo_registro = mb_strtoupper(md5($email_usuario.date('d-m-Y H:m:s')));

    $query_update =mysqli_query($conection,"UPDATE usuarios SET codigo_registro='$codigo_registro' WHERE id='$iduser' ");


    if ($query_update) {

      $mail = new  PHPMailer ( true );
     try {
       $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
       $mail -> isSMTP ();                                          // Enviar usando SMTP
       $mail -> Host        = "$Host_registro" ;                  // Configure el servidor SMTP para enviar a través de
       $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
       $mail->Username = "$Username_registro";
       $mail->Password = "$Password_registro";                              // Contraseña SMTP
       $mail -> SMTPSecure = "$SMTPSecure_registro";         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
      $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
       // Destinatarios
       $mail -> setFrom ( $Username_registro , 'guibis.com' );
       $mail -> addAddress($Username_registro);

       $mail -> addAddress ($email_usuario);     // Agrega un destinatario
       // Contenido
       $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
       $mail->CharSet = 'UTF-8';
       $mail -> Subject = 'Verificación Cuenta ' ;
       $mail->Body = '
           <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
           <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
           <div class="logo-empresa" style="text-align: center;">
           <img src="'.$url_img_upload.'/home/img/uploads/'.$img_facturacion.'"  style="width: 200px;">
           </div>
           <div class="contenedor-informacion" style="text-align: justify;">
           <div class="">
           <div class="">
               <p>¡<span>'.$nombres_usuario.' </span> tienes que verificar tu cuenta por tu seguridad!</p>
               <!-- Tabla de Información del Usuario -->
               <table style="width: 99%;border-collapse: collapse;margin-top: 20px; margin-left: auto; margin-right: auto;">
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dispositivo:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$sistema_operativo.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Navegador:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$buscador.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Fecha:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$fecha_actual.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">País:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$pais.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Provincia:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$provincia.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Ciudad:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$ciudad.'</td>
                   </tr>
                   <tr>
                       <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dirección IP:</th>
                       <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$direccion_ip.'</td>
                   </tr>
               </table>
               <p style="text-align: center;">
                   <a href="' . $url . '/verificar_cuenta?codigo_registro=' . $codigo_registro . '&user='.$iduser.'" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block;">Confirmar mi Cuenta</a>
               </p>
               <div class="" style="color: #c1c1c1;padding: 10px;margin: 10px;">
                 <p>Este mensaje se envio en automático, no respondas a este mensaje, para cualquier duda te presentamos nuestras lineas directas correo:
                    soporte@guibis.com Teléfono: +593960055956 </p>
               </div>
           </div>
           </div>
           </div>
           </div>
           </body>
       ';
       $mail -> send ();
     } catch ( Exception  $e ) {
     }


     $mensajeWhatsApp = "";
     $mensajeWhatsApp .= "¡Hola *{$nombres_usuario} *! 🚨\n\n";
     $mensajeWhatsApp .= "Tienes que verificar tu cuenta. Aquí tienes los detalles:\n\n";
     $mensajeWhatsApp .= "- *Dispositivo:* {$sistema_operativo}\n";
     $mensajeWhatsApp .= "- *Navegador:* {$buscador}\n";
     $mensajeWhatsApp .= "- *Fecha:* {$fecha_actual}\n";
     $mensajeWhatsApp .= "- *País:* {$pais}\n";
     $mensajeWhatsApp .= "- *Provincia:* {$provincia}\n";
     $mensajeWhatsApp .= "- *Ciudad:* {$ciudad}\n";
     $mensajeWhatsApp .= "- *Dirección IP:* {$direccion_ip}\n\n";
     $mensajeWhatsApp .= "- *Enlace :* {$url}/verificar_cuenta?codigo_registro={$codigo_registro}&user={$iduser}\n\n";
     $mensajeWhatsApp .= "Este mensaje se envió automáticamente. Por favor, no respondas a este mensaje. Si tienes alguna duda, contacta nuestras líneas directas:\n";
     $mensajeWhatsApp .= "Correo: _soporte@guibis.com_\n";
     $mensajeWhatsApp .= "Teléfono: *+593960055956*";

     include '../home/mensajes/mensajes.php';


     if ($envio_wsp == 'SI') {
       //$respuestaDeposito = enviarMensajeWhatsApp_guibis($celular_db, $mensajeWhatsApp);
     }
     return 'esto es un aviso de intentos máximos ';
      // code...
    }else {
      return 'Error en el servidor para editar el codigo registro ';
      // code...
    }
  }







  if ($accion == 'password_incorrecta') {


    $texto_noticia = "Contraseña incorrecta registrada  $sistema_operativo - $buscador - $pais - $provincia - $ciudad - $direccion_ip";


    $query_insert_notificaciones = mysqli_query($conection,"INSERT INTO notificaciones_guibis  (iduser,texto,estado)
                                                                               VALUES('$iduser','$texto_noticia','Exitoso') ");

    $query_correo_registro= mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'registro'");
    $data_correo_registro = mysqli_fetch_array($query_correo_registro);

    $Host_registro        = $data_correo_registro['Host'];
    $Username_registro    = $data_correo_registro['Username'];
    $Password_registro    = $data_correo_registro['Password'];
    $Port_registro        = $data_correo_registro['Port'];
    $SMTPSecure_registro  = $data_correo_registro['SMTPSecure'];



     $mail = new  PHPMailer ( true );
    try {
      $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
      $mail -> isSMTP ();                                          // Enviar usando SMTP
      $mail -> Host        = "$Host_registro" ;                  // Configure el servidor SMTP para enviar a través de
      $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
      $mail->Username = "$Username_registro";
      $mail->Password = "$Password_registro";                              // Contraseña SMTP
      $mail -> SMTPSecure = "$SMTPSecure_registro";         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);

      $mail -> addAddress ($email_usuario);     // Agrega un destinatario
      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'Intento de Acceso a tu cuenta ' ;
      $mail->Body = '
          <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
          <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
          <img src="'.$url_img_upload.'/home/img/uploads/'.$img_facturacion.'"  style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
          <div class="">
          <div class="">
              <p>¡<span>'.$nombres_usuario.' </span> se ha detectado un inicio de sesión no exitoso en nuestro sitio!</p>
              <!-- Tabla de Información del Usuario -->
              <table style="width: 99%;border-collapse: collapse;margin-top: 20px; margin-left: auto; margin-right: auto;">
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dispositivo:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$sistema_operativo.'</td>
              </tr>
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Navegador:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$buscador.'</td>
              </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Fecha:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$fecha_actual.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">País:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$pais.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Provincia:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$provincia.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Ciudad:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$ciudad.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dirección IP:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$direccion_ip.'</td>
                  </tr>
              </table>
              <div class="" style="color: #c1c1c1;padding: 10px;margin: 10px;">
                <p>Este mensaje se envio en automático, no respondas a este mensaje, para cualquier duda te presentamos nuestras lineas directas correo:
                   soporte@guibis.com Teléfono: +593960055956 </p>
              </div>
          </div>
          </div>
          </div>
          </div>
          </body>
      ';
      $mail -> send ();
    } catch ( Exception  $e ) {
    }


    $mensajeWhatsApp = "";
    $mensajeWhatsApp .= "¡Hola *{$nombres_usuario} *! 🚨\n\n";
    $mensajeWhatsApp .= "Se ha detectado un *inicio de sesión no exitoso* en nuestro sitio. Aquí tienes los detalles:\n\n";
    $mensajeWhatsApp .= "- *Dispositivo:* {$sistema_operativo}\n";
    $mensajeWhatsApp .= "- *Navegador:* {$buscador}\n";
    $mensajeWhatsApp .= "- *Fecha:* {$fecha_actual}\n";
    $mensajeWhatsApp .= "- *País:* {$pais}\n";
    $mensajeWhatsApp .= "- *Provincia:* {$provincia}\n";
    $mensajeWhatsApp .= "- *Ciudad:* {$ciudad}\n";
    $mensajeWhatsApp .= "- *Dirección IP:* {$direccion_ip}\n\n";
    $mensajeWhatsApp .= "Este mensaje se envió automáticamente. Por favor, no respondas a este mensaje. Si tienes alguna duda, contacta nuestras líneas directas:\n";
    $mensajeWhatsApp .= "Correo: _soporte@guibis.com_\n";
    $mensajeWhatsApp .= "Teléfono: *+593960055956*";

    include '../home/mensajes/mensajes.php';


    if ($envio_wsp == 'SI') {
      //$respuestaDeposito = enviarMensajeWhatsApp_guibis($celular_db, $mensajeWhatsApp);
    }
    return 'esto es un aviso de intentos máximos ';

    // code...
  }
  if ($accion == 'intentos_maximos') {
     $mail = new  PHPMailer ( true );

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
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);

      $mail -> addAddress ($email_usuario);     // Agrega un destinatario
      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'Protección de Cuentas Activado' ;
      $mail->Body = '
          <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
          <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
          <img src="'.$url_img_upload.'/home/img/uploads/'.$img_facturacion.'"  style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
          <div class="">
          <div class="">
              <p>¡<span>'.$nombres_usuario.' </span> se ha detectado un inicio de sesión no exitoso y limitado en nuestro sitio, desde este momento debes comunicarte con soporte paran poder recuperar tu cuenta!</p>
              <!-- Tabla de Información del Usuario -->
              <table style="width: 99%;border-collapse: collapse;margin-top: 20px; margin-left: auto; margin-right: auto;">
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dispositivo:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$sistema_operativo.'</td>
              </tr>
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Navegador:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$buscador.'</td>
              </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Fecha:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$fecha_actual.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">País:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$pais.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Provincia:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$provincia.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Ciudad:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$ciudad.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dirección IP:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$direccion_ip.'</td>
                  </tr>
              </table>
              <div class="" style="color: #c1c1c1;padding: 10px;margin: 10px;">
                <p>Este mensaje se envio en automático, no respondas a este mensaje, para cualquier duda te presentamos nuestras lineas directas correo:
                   soporte@guibis.com Teléfono: +593960055956 </p>
              </div>
          </div>
          </div>
          </div>
          </div>
          </body>
      ';

      $mail -> send ();
    } catch ( Exception  $e ) {
    }
          $mensajeWhatsApp = "";
          $mensajeWhatsApp .= "¡Hola *{$nombres_usuario} *! 🚨\n\n";
          $mensajeWhatsApp .= "Se ha detectado un *inicio de sesión no exitoso* en nuestro sitio. Aquí tienes los detalles:\n\n";
          $mensajeWhatsApp .= "- *Dispositivo:* {$sistema_operativo}\n";
          $mensajeWhatsApp .= "- *Navegador:* {$buscador}\n";
          $mensajeWhatsApp .= "- *Fecha:* {$fecha_actual}\n";
          $mensajeWhatsApp .= "- *País:* {$pais}\n";
          $mensajeWhatsApp .= "- *Provincia:* {$provincia}\n";
          $mensajeWhatsApp .= "- *Ciudad:* {$ciudad}\n";
          $mensajeWhatsApp .= "- *Dirección IP:* {$direccion_ip}\n\n";
          $mensajeWhatsApp .= "Este mensaje se envió automáticamente. Por favor, no respondas a este mensaje. Si tienes alguna duda, contacta nuestras líneas directas:\n";
          $mensajeWhatsApp .= "Correo: _soporte@guibis.com_\n";
          $mensajeWhatsApp .= "Teléfono: *+593960055956*";

          include '../home/mensajes/mensajes.php';

          if ($envio_wsp == 'SI') {
          //  $respuestaDeposito = enviarMensajeWhatsApp_guibis($celular_db, $mensajeWhatsApp);
      }

    return ('esto es un aviso por contraseña incorrecta');
    // code...
  }

  if ($accion == 'ingreso_correcto') {




    $texto_noticia = "Ingreso correcto a tu cuenta $sistema_operativo - $buscador - $pais - $provincia - $ciudad - $direccion_ip";

    $query_insert_notificaciones = mysqli_query($conection,"INSERT INTO notificaciones_guibis  (iduser,texto,estado)
                                                                               VALUES('$iduser','$texto_noticia','Exitoso') ");


     $mail = new  PHPMailer ( true );

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
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);

      $mail -> addAddress ($email_usuario);     // Agrega un destinatario
      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'Ingreso exitoso a tu cuenta' ;
      $mail->Body = '
          <body style="background: #f5f5f5;padding: 6px;margin: 25px;">
          <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
          <img src="'.$url_img_upload.'/home/img/uploads/'.$img_facturacion.'"  style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
          <div class="">
          <div class="">
              <p>¡<span>'.$nombres_usuario.' </span> se ha detectado un inicio de sesión  exitoso. !</p>
              <!-- Tabla de Información del Usuario -->
              <table style="width: 99%;border-collapse: collapse;margin-top: 20px; margin-left: auto; margin-right: auto;">
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dispositivo:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$sistema_operativo.'</td>
              </tr>
              <tr>
                  <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Navegador:</th>
                  <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$buscador.'</td>
              </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Fecha:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$fecha_actual.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">País:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$pais.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Provincia:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$provincia.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Ciudad:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$ciudad.'</td>
                  </tr>
                  <tr>
                      <th style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">Dirección IP:</th>
                      <td style="text-align: left;padding: 8px;border-bottom: 1px solid #ddd;">'.$direccion_ip.'</td>
                  </tr>
              </table>
              <div class="" style="color: #c1c1c1;padding: 10px;margin: 10px;">
                <p>Este mensaje se envio en automático, no respondas a este mensaje, para cualquier duda te presentamos nuestras lineas directas correo:
                   soporte@guibis.com Teléfono: +593960055956 </p>
              </div>
          </div>
          </div>
          </div>
          </div>
          </body>
      ';
      $mail -> send ();
    } catch ( Exception  $e ) {
    }



    $mensajeWhatsApp = "";
    $mensajeWhatsApp .= "¡Hola *{$nombres_usuario} *! 🚨\n\n";
    $mensajeWhatsApp .= "Se ha detectado un *inicio de sesión exitoso* en nuestro sitio. Aquí tienes los detalles:\n\n";
    $mensajeWhatsApp .= "- *Dispositivo:* {$sistema_operativo}\n";
    $mensajeWhatsApp .= "- *Navegador:* {$buscador}\n";
    $mensajeWhatsApp .= "- *Fecha:* {$fecha_actual}\n";
    $mensajeWhatsApp .= "- *País:* {$pais}\n";
    $mensajeWhatsApp .= "- *Provincia:* {$provincia}\n";
    $mensajeWhatsApp .= "- *Ciudad:* {$ciudad}\n";
    $mensajeWhatsApp .= "- *Dirección IP:* {$direccion_ip}\n\n";
    $mensajeWhatsApp .= "Este mensaje se envió automáticamente. Por favor, no respondas a este mensaje. Si tienes alguna duda, contacta nuestras líneas directas:\n";
    $mensajeWhatsApp .= "Correo: _soporte@guibis.com_\n";
    $mensajeWhatsApp .= "Teléfono: *+593960055956*";

    include '../home/mensajes/mensajes.php';


    if ($envio_wsp == 'SI') {
      //$respuestaDeposito = enviarMensajeWhatsApp_guibis($celular_db, $mensajeWhatsApp);
}

    return ('esto es un aviso por contraseña incorrecta');
    // code...
  }


  if ($accion == 'envio_comprador') {
    include "../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
    $producto = $producto;
    $query_producto = mysqli_query($conection, "SELECT producto_venta.nombre,producto_venta.precio,producto_venta.ciudad as 'ciudad', producto_venta.provincia as 'provincia',producto_venta.fecha_evento,
    producto_venta.hora_evento,producto_venta.identificador_trabajo,producto_venta.porcentaje,producto_venta.foto,
    producto_venta.id_usuario,producto_venta.peso,usuarios.posicion,usuarios.email as 'email_vendedor',usuarios.nombres as 'nombre_vendedor',producto_venta.categorias,producto_venta.subcategorias,producto_venta.estado_colaboracion,producto_venta.meses_suscripcion FROM producto_venta
    INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
    WHERE idproducto = $producto");
    $result_producto = mysqli_fetch_array($query_producto);
    $precio_producto      =  $result_producto['precio'];
    $nombre_vendedor      =  $result_producto['nombre_vendedor'];
    $nombre_producto      =  $result_producto['nombre'];
    $dueno_producto       =  $result_producto['id_usuario'];
    $email_vendedor       =  $result_producto['email_vendedor'];
    $precio_total = $cantidad*$precio_producto;
     $filep = '../archivos/accioncomprar/guia_compra.pdf';
     $mail = new  PHPMailer ( true );

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
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);

      $mail -> addAddress ($email_vendedor);     // Agrega un destinatario
      $mail->AddAttachment($filep, 'guia_compra.pdf');
      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'COMPRA EXITOSA DEL PRODUCTO '.$nombre_producto.' ' ;
      $mail -> Body     = '
      <body style="background: #f5f5f5;padding: 10px;margin: 25px;">
        <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
            <img src="https://guibis.com/home/img/reacciones/guibis.png" alt="" style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
            <div class="">
              <h5>'.$nombres_comprador.' COMPRA EXITOSA DEL PRODUCTO '.$nombre_producto.' .</h5>
              <div class="">
                <p>Hola '.$nombres_comprador.', haz hecho una compra del producto  ID #'.$producto.' de  cantidad '.$cantidad.' unidades, por el
                precio total de $'.$precio_total.' dólares Americanos, se ha generado un código QR para que puedas ver el estado de tu compra,
                esta compra tiene la condición de 24 horas laborables después de que te entreguen el producto, es decir entrega físicamente y
                también en nuestra plataforma en ventas, te adjuntamos una guía en formato pdf para más información.  </p>
              </div>
            </div>
            <div class="soporte" style="text-align: center;padding: 10px;margin: 5px;">
              <p>Si tienes alguna duda comunicate con nuestro equipo</p>
              <a style="display: block;text-decoration: none;padding: 10px;" href="tel:+593960055956">+593960055956</a> <br>
              <a style="display: block;text-decoration: none;padding: 10px;" href="mailto:soporte@guibis.com">soporte@guibis.com</a>
            </div>
            <div class="redes-sociales">
              <div class="redes_email" style="text-align: center;">
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.facebook.com/guibisEcuador"> <img src="https://guibis.com/home/img/reacciones/facebook.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.youtube.com/channel/UCUv90_DETO87rRse6GKCJvg"> <img src="https://guibis.com/home/img/reacciones/youtube.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://api.whatsapp.com/send?phone=593960055956&amp;text=Hola!&nbsp;Vengo&nbsp;De&nbsp;Guibis&nbsp;"> <img src="https://guibis.com/home/img/reacciones/whatsapp.png" alt="" width="50px;"> </a>
              </div>
          </div>
          <div class="footer" style="font-size: 10px;">
            <p>Este mensaje fue enviado en automático no responsas a este mensaje, te recordamos que una transacción no tiene costo ninguno y tampoco tarifa de cobro, el total cantidad a verificación es el total que una vez verificado el total que se va importar dentro de guibis.com , en caso de que no se verifique el deposito de forma correcta o hay sido con la intención de estafar se desactivara la cuenta y se procederá de manera inmediata apegada a la ley, se recuerda que para realizar transacciones se activa con la cedula de ciudadanía es decir se puede activar una sola vez, para reaizar retiros la cuenta vinculada a la plataforma tiene que ser del mimso titular caso contrario se declina la transferencia de manera inmediata, al ver una actividad sospechosa se suspendera la cuenta del titular.</p>

          </div>

        </div>
        </div>

      </body>


      ' ;
      $mail -> send ();
    } catch ( Exception  $e ) {
    }
    // code...
  }

  if ($accion == 'envio_vendedor') {
    include "../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
    $query_producto = mysqli_query($conection, "SELECT producto_venta.nombre,producto_venta.precio,producto_venta.ciudad as 'ciudad', producto_venta.provincia as 'provincia',producto_venta.fecha_evento,
    producto_venta.hora_evento,producto_venta.identificador_trabajo,producto_venta.porcentaje,producto_venta.foto,
    producto_venta.id_usuario,producto_venta.peso,usuarios.posicion,usuarios.email as 'email_vendedor',usuarios.nombres as 'nombre_vendedor',producto_venta.categorias,producto_venta.subcategorias,producto_venta.estado_colaboracion,producto_venta.meses_suscripcion FROM producto_venta
    INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
    WHERE idproducto = $producto");
    $result_producto = mysqli_fetch_array($query_producto);
    $precio_producto      =  $result_producto['precio'];
    $nombre_vendedor      =  $result_producto['nombre_vendedor'];
    $nombre_producto      =  $result_producto['nombre'];
    $dueno_producto       =  $result_producto['id_usuario'];
    $email_vendedor       =  $result_producto['email_vendedor'];
    $precio_total = $cantidad*$precio_producto;
    $filep = '../archivos/accioncomprar/guia_venta.pdf';
     $mail = new  PHPMailer ( true );

     $query_correo_registro= mysqli_query($conection, "SELECT * FROM credenciales_correos  WHERE area = 'registro'");
     $data_correo_registro = mysqli_fetch_array($query_correo_registro);

     $Host_registro        = $data_correo_registro['Host'];
     $Username_registro    = $data_correo_registro['Username'];
     $Password_registro    = $data_correo_registro['Password'];
     $Port_registro        = $data_correo_registro['Port'];
     $SMTPSecure_registro  = $data_correo_registro['SMTPSecure'];

    try {
      $mail -> SMTPDebug = 0;                                      // Habilita la salida de depuración detallada
      $mail -> isSMTP ();                                          // Enviar usando SMTP
      $mail -> Host        = "$Host_registro" ;                  // Configure el servidor SMTP para enviar a través de
      $mail -> SMTPAuth    = true ;                                   // Habilita la autenticación SMTP
      $mail->Username = "$Username_registro";
      $mail->Password = "$Password_registro";                              // Contraseña SMTP
      $mail -> SMTPSecure = "$SMTPSecure_registro";         // Habilite el cifrado TLS; Se recomienda `PHPMailer :: ENCRYPTION_SMTPS`
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);

      $mail -> addAddress ($email_usuario);     // Agrega un destinatario
      $mail->AddAttachment($filep, 'guia_venta.pdf');

      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'VENTA EXITOSA DEL PRODUCTO '.$nombre_producto.'' ;
      $mail -> Body     = '
      <body style="background: #f5f5f5;padding: 10px;margin: 25px;">
        <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
            <img src="https://guibis.com/home/img/reacciones/guibis.png" alt="" style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
            <div class="">
              <h5>'.$nombre_vendedor.' VENTA EXITOSA DEL PRODUCTO '.$nombre_producto.'.</h5>
              <div class="">
                <p>Hola '.$nombre_vendedor.', haz hecho una venta del producto  ID #'.$producto.' de  cantidad '.$cantidad.' unidades,
                por el precio total de $'.$precio_total.' dólares Americanos, se ha generado un código QR para que puedas ver el
                 estado de tu venta, esta venta tiene la condición de 24 horas laborables después de que entregues el
                 producto, es decir entrega físicamente
                y también en nuestra plataforma en ventas, te adjuntamos una guía en formato pdf para mas información.  </p>
              </div>
            </div>
            <div class="soporte" style="text-align: center;padding: 10px;margin: 5px;">
              <p>Si tienes alguna duda comunicate con nuestro equipo</p>
              <a style="display: block;text-decoration: none;padding: 10px;" href="tel:+593960055956">+593960055956</a> <br>
              <a style="display: block;text-decoration: none;padding: 10px;" href="mailto:soporte@guibis.com">soporte@guibis.com</a>
            </div>
            <div class="redes-sociales">
              <div class="redes_email" style="text-align: center;">
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.facebook.com/guibisEcuador"> <img src="https://guibis.com/home/img/reacciones/facebook.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.youtube.com/channel/UCUv90_DETO87rRse6GKCJvg"> <img src="https://guibis.com/home/img/reacciones/youtube.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://api.whatsapp.com/send?phone=593960055956&amp;text=Hola!&nbsp;Vengo&nbsp;De&nbsp;Guibis&nbsp;"> <img src="https://guibis.com/home/img/reacciones/whatsapp.png" alt="" width="50px;"> </a>
              </div>
          </div>
          <div class="footer" style="font-size: 10px;">
            <p>Este mensaje fue enviado en automático no responsas a este mensaje, te recordamos que una transacción no tiene costo ninguno y tampoco tarifa de cobro, el total cantidad a verificación es el total que una vez verificado el total que se va importar dentro de guibis.com , en caso de que no se verifique el deposito de forma correcta o hay sido con la intención de estafar se desactivara la cuenta y se procederá de manera inmediata apegada a la ley, se recuerda que para realizar transacciones se activa con la cedula de ciudadanía es decir se puede activar una sola vez, para reaizar retiros la cuenta vinculada a la plataforma tiene que ser del mimso titular caso contrario se declina la transferencia de manera inmediata, al ver una actividad sospechosa se suspendera la cuenta del titular.</p>

          </div>

        </div>
        </div>

      </body>


      ' ;
      $mail -> send ();
    } catch ( Exception  $e ) {
    }
  }



  if ($accion == 'emvio_archivo_descargable') {
    include "../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
    $query_producto = mysqli_query($conection, "SELECT producto_venta.nombre,producto_venta.precio,producto_venta.ciudad as 'ciudad', producto_venta.provincia as 'provincia',producto_venta.fecha_evento,
    producto_venta.hora_evento,producto_venta.identificador_trabajo,producto_venta.porcentaje,producto_venta.foto,producto_venta.cat1sub44_enlace_descarga,
    producto_venta.id_usuario,producto_venta.peso,usuarios.posicion,usuarios.email as 'email_vendedor',usuarios.nombres as 'nombre_vendedor',producto_venta.categorias,producto_venta.subcategorias,producto_venta.estado_colaboracion,producto_venta.meses_suscripcion FROM producto_venta
    INNER JOIN usuarios ON usuarios.id = producto_venta.id_usuario
    WHERE idproducto = $producto");
    $result_producto = mysqli_fetch_array($query_producto);
    $precio_producto      =  $result_producto['precio'];
    $nombre_vendedor      =  $result_producto['nombre_vendedor'];
    $nombre_producto      =  $result_producto['nombre'];
    $dueno_producto       =  $result_producto['id_usuario'];
    $email_vendedor       =  $result_producto['email_vendedor'];
    $archivo_descarga     =  $result_producto['cat1sub44_enlace_descarga'];
    $precio_total = $cantidad*$precio_producto;
    $filep = '../archivos/accioncomprar/guia_venta.pdf';
     $mail = new  PHPMailer ( true );

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
     $mail->Port = "$Port_registro";                             // Puerto TCP para conectarse, use 465 para `PHPMailer :: ENCRYPTION_SMTPS` arriba
      // Destinatarios
      $mail -> setFrom ( $Username_registro , ''.$nombre_empresa.' de guibis.com' );
      $mail -> addAddress($Username_registro);


      $mail -> addAddress ($email_usuario);     // Agrega un destinatario
      $mail->AddAttachment($filep, 'guia_venta.pdf');

      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = 'ARHIVO DE  '.$nombre_producto.'' ;
      $mail -> Body     = '
      <body style="background: #f5f5f5;padding: 10px;margin: 25px;">
        <div class="contenedor" style="background: #fff;padding: 20px;margin: 10px;">
          <div class="logo-empresa" style="text-align: center;">
            <img src="https://guibis.com/home/img/reacciones/guibis.png" alt="" style="width: 200px;">
          </div>
          <div class="contenedor-informacion" style="text-align: justify;">
            <div class="">
              <h5>'.$nombre_vendedor.' VENTA EXITOSA DEL PRODUCTO '.$nombre_producto.'.</h5>
              <div class="">
                <p>Hola '.$nombre_vendedor.', haz hecho una compra del producto  ID #'.$producto.' de  cantidad '.$cantidad.' unidades,
                por el precio total de $'.$precio_total.' dólares Americanos, a continuacion esta el enlace de descarga que viene por esta compra </p>
                      <a target="_blanck" style="display: block;text-decoration: none;padding: 10px;" href="'.$archivo_descarga.'">'.$archivo_descarga.'</a> <br>
              </div>
            </div>
            <div class="soporte" style="text-align: center;padding: 10px;margin: 5px;">
              <p>Si tienes alguna duda comunicate con nuestro equipo</p>
              <a style="display: block;text-decoration: none;padding: 10px;" href="tel:+593960055956">+593960055956</a> <br>
              <a style="display: block;text-decoration: none;padding: 10px;" href="mailto:soporte@guibis.com">soporte@guibis.com</a>
            </div>
            <div class="redes-sociales">
              <div class="redes_email" style="text-align: center;">
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.facebook.com/guibisEcuador"> <img src="https://guibis.com/home/img/reacciones/facebook.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://www.youtube.com/channel/UCUv90_DETO87rRse6GKCJvg"> <img src="https://guibis.com/home/img/reacciones/youtube.png" alt="" width="50px;"> </a>
                <a style="text-align: center; margin:3px; padding4px;" href="https://api.whatsapp.com/send?phone=593960055956&amp;text=Hola!&nbsp;Vengo&nbsp;De&nbsp;Guibis&nbsp;"> <img src="https://guibis.com/home/img/reacciones/whatsapp.png" alt="" width="50px;"> </a>
              </div>
          </div>
          <div class="footer" style="font-size: 10px;">
            <p>Este mensaje fue enviado en automático no responsas a este mensaje, te recordamos que una transacción a no se que sea una venta no tiene costo ninguno y tampoco tarifa de cobro, el total cantidad a verificación es el total que una vez verificado el total que se va importar dentro de guibis.com , en caso de que no se verifique el deposito de forma correcta o hay sido con la intención de estafar se desactivara la cuenta y se procederá de manera inmediata apegada a la ley, se recuerda que para realizar transacciones se activa con la cedula de ciudadanía es decir se puede activar una sola vez, para reaizar retiros la cuenta vinculada a la plataforma tiene que ser del mimso titular caso contrario se declina la transferencia de manera inmediata, al ver una actividad sospechosa se suspendera la cuenta del titular.</p>

          </div>

        </div>
        </div>

      </body>


      ' ;
      $mail -> send ();
    } catch ( Exception  $e ) {
    }
  }




}


 ?>
