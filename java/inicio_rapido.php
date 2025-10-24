<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones
$mail = new  PHPMailer ( true );
require "../coneccion.php" ;
  mysqli_set_charset($conection, 'utf8'); //linea a colocar

    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;



    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;

       $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
       $result_documentos = mysqli_fetch_array($query_doccumentos);
       $user_in     = $result_documentos['id'];


      if ($_POST['action'] == 'iniciar_sesion_rapida') {
          $rol = $_POST['rol'];
          $id_user = $_POST['codigo'];



          if ($rol == 'Cuenta Empresa') {
            $query = mysqli_query($conection,"SELECT * FROM usuarios WHERE id='$id_user'");
            $data_clave= mysqli_fetch_array($query);
                session_start();
                $_SESSION['active']=true;
                $_SESSION['id']=$id_user;
                $_SESSION['user_in']=$user_in;
                $_SESSION['rol']='cuenta_empresa';
                $arrayName = array('respuesta' => 'password_exitoso');
                echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);



          }



          if ($rol == 'Usuario Venta') {
            $query = mysqli_query($conection,"SELECT * FROM usuarios_punto_venta WHERE id='$id_user'");
            $data_clave= mysqli_fetch_array($query);

            $password_db  = $data_clave['password'];
            $mail_db      = $data_clave['mail'];

                session_start();
                $_SESSION['active']=true;
                $_SESSION['id']=$id_user;
                $_SESSION['user_in']=$user_in;
                $_SESSION['rol']='cuenta_usuario_venta';

                $arrayName = array('respuesta' => 'password_exitoso');
                echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);



          }


          if ($rol == 'Recursos Humanos') {

            $rol_interno = $_POST['rol_interno'];

            $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
            $result_documentos = mysqli_fetch_array($query_doccumentos);
            $regimen = $result_documentos['regimen'];
            $user_in  = $result_documentos['id'];



            $query = mysqli_query($conection,"SELECT * FROM recursos_humanos WHERE id='$id_user'");
            $data_clave= mysqli_fetch_array($query);

            $password_db  = $data_clave['password'];
            $mail_db      = $data_clave['mail'];


                session_start();
                $_SESSION['active']=true;
                $_SESSION['id']=$id_user;
                $_SESSION['user_in']=$user_in;
                $_SESSION['rol']='Recursos Humanos';
                $_SESSION['rol_interno']=$rol_interno;

                $arrayName = array('respuesta' => 'password_exitoso');
                echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);



          }




      // code...
    }

 ?>
