<?php
// Reportar todos los errores de PHP (ver el manual de PHP para más niveles de errores)
error_reporting(E_ALL);

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



require("../../mail/PHPMailer-master/src/PHPMailer.php");
require("../../mail/PHPMailer-master/src/Exception.php");
require("../../mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones

session_start();



 require '../../QR/phpqrcode/qrlib.php';
 include "../../../coneccion.php";
  mysqli_set_charset($conection, 'utf8'); //linea a colocar


    if ($_SESSION['rol'] == 'cuenta_empresa') {
    include "../../sessiones/session_cuenta_empresa.php";

    }

    if ($_SESSION['rol'] == 'Recursos Humano') {
            include "../../sessiones/session_cuenta_recursos_humanos.php";
    }



 if ($_POST['action'] == 'consultar_datos') {

   $query_consulta = mysqli_query($conection, "SELECT *
    FROM `tarifas_parqueo`
WHERE tarifas_parqueo.iduser  = '$iduser'  AND tarifas_parqueo.estatus='1'
ORDER BY `tarifas_parqueo`.`fecha` DESC");

   $data = array();
while ($row = mysqli_fetch_assoc($query_consulta)) {
    $data[] = $row;
}

echo json_encode(array("data" => $data));
   // code...
 }



 if ($_POST['action'] == 'info_categoria') {
      $categoria       = $_POST['categoria'];

   $query_consulta = mysqli_query($conection, "SELECT * FROM rst_categorias
      WHERE rst_categorias.iduser ='$iduser'  AND rst_categorias.estatus = '1' AND rst_categorias.id = '$categoria' ");
   $data_producto = mysqli_fetch_array($query_consulta);
   echo json_encode($data_producto,JSON_UNESCAPED_UNICODE);
 }



 if ($_POST['action'] == 'editar_caregoria') {

    $id_categoria       = $_POST['id_categoria'];
   $nombre_categoria       = $_POST['nombre_categoria'];

   if (!empty($_FILES['foto']['name'])) {
     $foto           =    $_FILES['foto'];
     $nombre_foto    =    $foto['name'];
     $type 					 =    $foto['type'];
     $url_temp       =    $foto['tmp_name'];
     $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
     $destino = '../../img/uploads/';
     $img_nombre = 'mesas_guibis'.md5(date('d-m-Y H:m:s').$iduser);
     $img_categoria = $img_nombre.'.'.$extension;
     $src = $destino.$img_categoria;
     move_uploaded_file($url_temp,$src);
     $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://'; $domain = $_SERVER['HTTP_HOST']; $url = $protocol . $domain;
   }else {

     $query_consulta = mysqli_query($conection, "SELECT * FROM rst_categorias
        WHERE rst_categorias.iduser ='$iduser'  AND rst_categorias.estatus = '1' AND rst_categorias.id = '$id_categoria' ");
     $data_producto = mysqli_fetch_array($query_consulta);
     $img_categoria = $data_producto['img_categoria'];
     $url = $data_producto['url'];
     // code...
   }

  $query_update =mysqli_query($conection,"UPDATE rst_categorias SET img_categoria= '$img_categoria',nombre_categoria= '$nombre_categoria' ,url= '$url'   WHERE id='$id_categoria' ");

   if ($query_update) {
     $arrayName = array('noticia' =>'insert_correct');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);



     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }





 if ($_POST['action'] == 'agregar_categoria') {

   $intervalo_tiempo_minutos    = $_POST['intervalo_tiempo_minutos'];
   $nombre_tarifa    = $_POST['nombre_tarifa'];
   $valor_recargo    = $_POST['valor_recargo'];
   $valor_servicio   = $_POST['valor_servicio'];
   $timpo_espera     = $_POST['timpo_espera'];


   $query_insert=mysqli_query($conection,"INSERT INTO  tarifas_parqueo (iduser,nombre_servicio,minutos_servicio,precio_sobrecargo,valor_servicio,timpo_espera)
                                 VALUES('$iduser','$nombre_tarifa','$intervalo_tiempo_minutos','$valor_recargo','$valor_servicio','$timpo_espera') ");

               if ($query_insert) {

                 $arrayName = array('noticia' =>'insert_correct');
                     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
               }else {
                 $arrayName = array('noticia' =>'error_insertar');
                     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
               }


 }



 if ($_POST['action'] == 'eliminar_categoria') {
   $categoria             = $_POST['categoria'];

   $query_delete=mysqli_query($conection,"UPDATE rst_categorias SET estatus= 0  WHERE id='$categoria' ");

   if ($query_delete) {
       $arrayName = array('noticia'=>'insert_correct','categoria'=> $categoria);
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }







 ?>
