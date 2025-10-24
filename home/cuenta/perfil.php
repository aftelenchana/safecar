<?php

include "../../coneccion.php";
 mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

 session_start();

     if ($_SESSION['rol'] == 'cuenta_empresa') {
     include "../sessiones/session_cuenta_empresa.php";



     }

     if ($_SESSION['rol'] == 'cuenta_usuario_venta') {
     include "../sessiones/session_cuenta_usuario_venta.php";

     }



 if ($_POST['action'] == 'editar_perfil') {

     $nombres          =  mysqli_real_escape_string($conection,$_POST['nombres']);
     $email            =  mysqli_real_escape_string($conection,$_POST['email']);
     $nombre_empresa   =  mysqli_real_escape_string($conection,$_POST['nombre_empresa']);
     $celular          =  mysqli_real_escape_string($conection,$_POST['celular']);



   if (!empty($_FILES['foto']['name'])) {
     $foto           =    $_FILES['foto'];
     $nombre_foto    =    $foto['name'];
     $type 					 =    $foto['type'];
     $url_temp       =    $foto['tmp_name'];
     $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
     $destino = '../img/uploads/';
     $img_nombre = 'guibis_img_perfil'.md5(date('d-m-Y H:m:s').$iduser);
     $img_facturacion = $img_nombre.'.'.$extension;
     $src = $destino.$img_facturacion;
     move_uploaded_file($url_temp,$src);
     $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url_img_upload = $protocol . $domain;
     }else {
       $query_consulta = mysqli_query($conection, "SELECT * FROM usuarios
          WHERE usuarios.id = '$iduser'");
       $data_consulta = mysqli_fetch_array($query_consulta);
       $img_facturacion = $data_consulta['img_facturacion'];
       $url_img_upload = $data_consulta['url_img_upload'];
     }


     if (!empty($_FILES['json_google_drive']['name'])) {
       $json_google_drive    =    $_FILES['json_google_drive'];
       $nombre_json          =    $json_google_drive['name'];
       $type 					       =    $json_google_drive['type'];
       $url_temp             =    $json_google_drive['tmp_name'];
       $extension = pathinfo($nombre_json, PATHINFO_EXTENSION);
       $destino = '../files/json/';
       $img_nombre = 'json_guibis'.md5(date('d-m-Y H:m:s').$iduser);
       $json_google_drive = $img_nombre.'.'.$extension;
       $src = $destino.$json_google_drive;
       move_uploaded_file($url_temp,$src);
       }else {
         $query_consulta = mysqli_query($conection, "SELECT * FROM usuarios
            WHERE usuarios.id = '$iduser'");
         $data_consulta = mysqli_fetch_array($query_consulta);
         $json_google_drive = $data_consulta['json_google_drive'];
       }

     $query_update = "
         UPDATE usuarios
         SET
             img_facturacion = '$img_facturacion',
             url_img_upload = '$url_img_upload',
             nombres = '$nombres',
             email = '$email',
             nombre_empresa = '$nombre_empresa',
             celular = '$celular',
             json_google_drive = '$json_google_drive'
         WHERE id = '$iduser'
     ";

     $query_insert = mysqli_query($conection, $query_update);

   if ($query_insert) {
       $arrayName = array('noticia'=>'insert_correct','iduser'=>$iduser);
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

     }else {
       $arrayName = array('noticia' =>'error','contenido_error' => mysqli_error($conection));
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }
  // code...
}


 ?>
