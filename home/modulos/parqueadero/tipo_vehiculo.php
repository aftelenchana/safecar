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
    FROM `tipo_vehiculo_guibis`
WHERE tipo_vehiculo_guibis.iduser  = '$iduser'  AND tipo_vehiculo_guibis.estatus='1'
ORDER BY `tipo_vehiculo_guibis`.`fecha` DESC");

   $data = array();
while ($row = mysqli_fetch_assoc($query_consulta)) {
    $data[] = $row;
}

echo json_encode(array("data" => $data));
   // code...
 }



 if ($_POST['action'] == 'info_categoria') {
      $categoria       = $_POST['categoria'];

   $query_consulta = mysqli_query($conection, "SELECT * FROM tipo_vehiculo_guibis
      WHERE tipo_vehiculo_guibis.iduser ='$iduser'  AND tipo_vehiculo_guibis.estatus = '1' AND tipo_vehiculo_guibis.id = '$categoria' ");
   $data_producto = mysqli_fetch_array($query_consulta);
   echo json_encode($data_producto,JSON_UNESCAPED_UNICODE);
 }



 if ($_POST['action'] == 'editar_caregoria') {

    $id_categoria       = $_POST['id_categoria'];
    $nombre    = $_POST['nombre'];

  $query_update =mysqli_query($conection,"UPDATE tipo_vehiculo_guibis SET nombre= '$nombre'  WHERE id='$id_categoria' ");

   if ($query_update) {
     $arrayName = array('noticia' =>'insert_correct');
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);



     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }





 if ($_POST['action'] == 'agregar_categoria') {

   $nombre    = $_POST['nombre'];



   $query_insert=mysqli_query($conection,"INSERT INTO  tipo_vehiculo_guibis (iduser,nombre)
                                 VALUES('$iduser','$nombre') ");

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

   $query_delete=mysqli_query($conection,"UPDATE tipo_vehiculo_guibis SET estatus= 0  WHERE id='$categoria' ");

   if ($query_delete) {
       $arrayName = array('noticia'=>'insert_correct','categoria'=> $categoria);
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }else {
       $arrayName = array('noticia' =>'error_insertar');
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
     }

 }







 ?>
