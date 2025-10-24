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
    $empresa = $_COOKIE['empresa_id'];

    }

    if ($_SESSION['rol'] == 'Recursos Humano') {
            include "../../sessiones/session_cuenta_recursos_humanos.php";
    }



 if ($_POST['action'] == 'consultar_datos') {

   $query_consulta = mysqli_query($conection, "SELECT *
    FROM `espacio_parqueo_guibis`
WHERE espacio_parqueo_guibis.iduser  = '$iduser'  AND espacio_parqueo_guibis.estatus='1'
AND espacio_parqueo_guibis.empresa = '$empresa'
ORDER BY `espacio_parqueo_guibis`.`fecha` DESC");

   $data = array();
while ($row = mysqli_fetch_assoc($query_consulta)) {
    $data[] = $row;
}

echo json_encode(array("data" => $data));
   // code...
 }



 if ($_POST['action'] == 'info_categoria') {
      $categoria       = $_POST['categoria'];

   $query_consulta = mysqli_query($conection, "SELECT * FROM espacio_parqueo_guibis
      WHERE espacio_parqueo_guibis.iduser ='$iduser'  AND espacio_parqueo_guibis.estatus = '1' AND espacio_parqueo_guibis.id = '$categoria' ");
   $data_producto = mysqli_fetch_array($query_consulta);
   echo json_encode($data_producto,JSON_UNESCAPED_UNICODE);
 }



 if ($_POST['action'] == 'editar_caregoria') {

    $id_categoria       = $_POST['id_categoria'];
    $cantidad    = $_POST['cantidad'];
    $descripcion    = $_POST['descripcion'];

  $query_update =mysqli_query($conection,"UPDATE espacio_parqueo_guibis SET cantidad= '$cantidad',descripcion= '$descripcion'  WHERE id='$id_categoria' ");

   if ($query_update) {
     $arrayName = array('noticia' =>'insert_correct');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);



     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }





 if ($_POST['action'] == 'agregar_categoria') {

   $cantidad    = $_POST['cantidad'];
   $descripcion    = $_POST['descripcion'];



   $query_insert=mysqli_query($conection,"INSERT INTO  espacio_parqueo_guibis (iduser,cantidad,descripcion,empresa)
                                 VALUES('$iduser','$cantidad','$descripcion','$empresa') ");

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

   $query_delete=mysqli_query($conection,"UPDATE espacio_parqueo_guibis SET estatus= 0  WHERE id='$categoria' ");

   if ($query_delete) {
       $arrayName = array('noticia'=>'insert_correct','categoria'=> $categoria);
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }







 ?>
