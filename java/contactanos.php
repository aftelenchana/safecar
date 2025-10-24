<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
  include "../coneccion.php";

  $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;


       $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
       $result_documentos = mysqli_fetch_array($query_doccumentos);
       $email_user_in     = $result_documentos['email'];
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
       $iduser               = $result_documentos['id'];


  if ($_POST['action'] == 'contactanos') {
  $nombres = $_POST['nombres'];
  $celular = $_POST['celular'];
  $email = $_POST['email'];
  $mensaje = $_POST['mensaje'];

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
      $mail -> setFrom ( $Username_registro , 'Área de Emergencias' );
      $mail -> addAddress ($email_user_in);     // Agrega un destinatario


      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = ' '.$nombres.' tiene una emergencia ' ;

        $mail->Body = "
            <body style='background: #f5f5f5;padding: 6px;margin: 25px;'>
                <div class='contenedor' style='background: #fff;padding: 20px;margin: 10px;'>
                    <div class='logo-empresa' style='text-align: center;'>
                     <img src='{$url}/img/guibis.png' alt='Logo de la Empresa' style='width: 200px;'>
                    </div>
                    <div class='contenedor-informacion' style='text-align: justify;'>
                        <p>{$nombres} - {$celular} - {$email} - {$mensaje} .   </p>




                        <p>Saludos cordiales,<br>El Equipo de {$nombre_empresa}</p>
                    </div>
                </div>
            </body>
        ";


        if (!$mail->send()) {
            // Manejo del caso de fallo en el envío
            $response = array('noticia_email' => 'error_envio','resp_password' =>'positiva');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            // Manejo del caso de éxito en el envío
            $response = array('noticia_email' => 'envio_exitoso','resp_password' =>'positiva');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        // Manejo de una excepción durante la configuración o el envío
        $response = array('noticia_email' => 'error_excepcion','resp_password' =>'positiva','detalle' => 'Ocurrió un error al intentar enviar el correo','error' => $e->getMessage());
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }


  }

  if ($_POST['action'] == 'ingreso_emegergencia') {


  $nombres   = $_POST['nombres'];
  $celular   = $_POST['celular'];
  $email     = $_POST['email'];
  $mensaje   = $_POST['mensaje'];
  $latitud   = $_POST['latitud'];
  $longitud  = $_POST['longitud'];

  $identificacion  = $_POST['identificacion'];

  //buscamos al paciente con la identificacion

  $query_existencia_paciente = mysqli_query($conection, "SELECT * FROM clientes
     WHERE clientes.iduser ='$iduser'  AND clientes.estatus = '1' AND clientes.identificacion = '$identificacion' ");


 $result_existencia= mysqli_num_rows($query_existencia_paciente);

    if ($result_existencia > 0) {
      $data_paciente = mysqli_fetch_array($query_existencia_paciente);

      $historial_clinica = $data_paciente['secuencial'];
      $nombres_paciente  = $data_paciente['nombres'];
      $id_paciente  = $data_paciente['id'];

      $cadena_paciente = "Se Trata de una paciente de nuestro equipo con Historia Clinica {$historial_clinica}  a nombre de {$nombres_paciente} y código  {$id_paciente}";

    }else {
      $cadena_paciente = "Este paciente no se encuentra registrado en nuestra base de datos ";
    }






   $query_insert=mysqli_query($conection,"INSERT INTO  emergencias  (iduser,nombres,celular,email ,mensaje,latitud,longitud,estado)
                                                                VALUES('$iduser','$nombres','$celular','$email','$mensaje','$latitud','$longitud','Iniciado') ");


      $query_max_id_asiento = mysqli_query($conection, "SELECT MAX(id) as max_id FROM emergencias
            WHERE iduser = '$iduser' AND estatus = '1' ");
        $result_max_id_asiento = mysqli_fetch_assoc($query_max_id_asiento);
        $id_codigo_emergencia = $result_max_id_asiento['max_id'];

  if ($query_insert) {

    if (!empty($latitud)) {

      $mapa_emergencia = "<a href='{$url}/home/mapa_trayectoria_emergencia?codigo={$id_codigo_emergencia}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Visualizar mapa</a>
                  </div>";
    }else {
      $mapa_emergencia = '';
    }
    // code...



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
      $mail -> setFrom ( $Username_registro , 'Ingreso de Emergencia' );
      $mail -> addAddress ($email_user_in);     // Agrega un destinatario
      $mail -> addAddress ('alejiss401997@gmail.com');     // Agrega un destinatario


      // Contenido
      $mail -> isHTML ( true );                                  // Establecer el formato de correo electrónico en HTML
      $mail->CharSet = 'UTF-8';
      $mail -> Subject = ' '.$nombres.' tiene una emergencia ' ;

      $mail->Body = "
          <body style='background-color: #f5f5f5; padding: 20px; font-family: Arial, sans-serif;'>
              <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                  <div style='text-align: center; margin-bottom: 20px;'>
                      <img src='{$url}/img/guibis.png' alt='Logo de la Empresa' style='width: 150px;'>
                  </div>
                  <div style='margin-bottom: 20px; text-align: center;'>
                      <h2 style='color: #333333; margin: 0 0 10px;'>Emergencia Reportada</h2>
                      <p style='color: #555555; margin: 0;'>Detalles de la emergencia recibida</p>
                  </div>
                  <div style='background-color: #f9f9f9; padding: 15px; border-radius: 8px;'>
                      <p style='color: #333333;'><strong>Nombre:</strong> {$nombres}</p>
                      <p style='color: #333333;'><strong>Celular:</strong> {$celular}</p>
                      <p style='color: #333333;'><strong>Email:</strong> {$email}</p>
                      <p style='color: #333333;'><strong>Mensaje:</strong> {$mensaje}</p>
                      <p style='color: #333333;'><strong>Información Sistema:</strong> {$cadena_paciente}</p>
                  </div>
                 {$mapa_emergencia}
                  <div style='margin-top: 20px; text-align: center;'>
                      <p style='color: #777777;'>Saludos cordiales,</p>
                      <p style='color: #333333; font-weight: bold;'>{$nombre_empresa}</p>
                  </div>
              </div>
          </body>
      ";


        if (!$mail->send()) {
            // Manejo del caso de fallo en el envío
            $response = array('noticia_email' => 'error_envio','resp_emergencia' =>'positiva');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            // Manejo del caso de éxito en el envío
            $response = array('noticia_email' => 'envio_exitoso','resp_emergencia' =>'positiva');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        // Manejo de una excepción durante la configuración o el envío
        $response = array('noticia_email' => 'error_excepcion','resp_emergencia' =>'positiva','detalle' => 'Ocurrió un error al intentar enviar el correo','error' => $e->getMessage());
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

  }else {
    $response = array('resp_emergencia' => 'error_insertar');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    // code...
  }


  }
 ?>
