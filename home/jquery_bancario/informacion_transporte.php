<?php
session_start();
if (empty($_SESSION['active'])) {
  header('location:/');
}else {
  $iduser= $_SESSION['id'];
}
include "../../coneccion.php";
include "envio_email.php";
 mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
 if ($_POST['action'] == 'informacion_producto_transporte') {
   $producto = $_POST['producto'];
   $queryu = mysqli_query($conection, "SELECT * FROM producto_venta  WHERE idproducto = '$producto'");
   $resultu = mysqli_fetch_array($queryu);
   $utilizar_transporte_guibis = $resultu['utilizar_transporte_guibis'];
   if ($utilizar_transporte_guibis == 'SI') {
    $latitud =  $resultu['latitud'];
    $longitud =  $resultu['longitud'];
    $arrayName = array('noticia' =>'con_transporte','latitud'=>$latitud,'longitud'=>$longitud);
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
   }
   else {
     $arrayName = array('noticia' =>'sin_transporte');
     echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
   }

 }


 ?>
