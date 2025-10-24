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

    $fecha_actual = date("d-m-Y H:i:s");

   $query_consulta = mysqli_query($conection, "SELECT
     ingreso_vehiculo_parqueadero_guibis.id,
     ingreso_vehiculo_parqueadero_guibis.notas_extras,
     ingreso_vehiculo_parqueadero_guibis.placa,
     ingreso_vehiculo_parqueadero_guibis.fecha_inicio,
     ingreso_vehiculo_parqueadero_guibis.estado,
     ingreso_vehiculo_parqueadero_guibis.precio_cobrado,
     tarifas_parqueo.minutos_servicio,
     tarifas_parqueo.timpo_espera
    FROM `ingreso_vehiculo_parqueadero_guibis`
    INNER JOIN tarifas_parqueo on tarifas_parqueo.id = ingreso_vehiculo_parqueadero_guibis.tarifa
WHERE ingreso_vehiculo_parqueadero_guibis.iduser  = '$iduser'  AND ingreso_vehiculo_parqueadero_guibis.estatus='1'
ORDER BY `ingreso_vehiculo_parqueadero_guibis`.`fecha` DESC");

   $data = array();
while ($row = mysqli_fetch_assoc($query_consulta)) {

  $tiempo_total_tolerancia = $row['timpo_espera']; //tiempo que esta en minutos
  $minutos_servicio = $row['minutos_servicio']; //tiempo que esta en minutos
  $nueva_fecha = date('d-m-Y H:i:s', strtotime($row['fecha_inicio'] . ' + '.$tiempo_total_tolerancia.' minute'));

  $minutos_transcurridos = ceil((strtotime(date($fecha_actual))-strtotime(date($row['fecha_inicio'])))/(60));



  $minutos_tolerencia_totales = $tiempo_total_tolerancia;
  $minutos_recorridos = $minutos_transcurridos - $minutos_tolerencia_totales;

  $intervalos =  ceil($minutos_recorridos/$minutos_servicio);

  $row['nueva_fecha']               =     $nueva_fecha;
  $row['minutos_transcurridos']     =     $minutos_transcurridos;
  $row['intervalos']                =     $intervalos;



    $data[] = $row;
}

echo json_encode(array("data" => $data));
   // code...
 }



 if ($_POST['action'] == 'info_categoria') {
      $categoria       = $_POST['categoria'];

   $query_consulta = mysqli_query($conection, "SELECT * FROM rst_categorias
      WHERE rst_categorias.iduser ='$iduser'  AND rst_categorias.estatus = '1' AND rst_categorias.id = '$categoria' ");
   $tarifas_parqueo = mysqli_fetch_array($query_consulta);
   echo json_encode($tarifas_parqueo,JSON_UNESCAPED_UNICODE);
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
     $tarifas_parqueo = mysqli_fetch_array($query_consulta);
     $img_categoria = $tarifas_parqueo['img_categoria'];
     $url = $tarifas_parqueo['url'];
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

   $tarifas_parqueo    = $_POST['tarifas_parqueo'];
   $vehiculo           = $_POST['tipo_vehiculo'];
   $placa            = strtoupper($_POST['placa']);

   $notas_extras  = $_POST['nota_extra'];

   //QR DEL TRANSPORTISTA

   $fecha_actual = date("d-m-Y H:i:s");

   $img_nombre = 'guibis_qringreso_'.md5(date('d-m-Y H:m:s'));
   $qr_img = $img_nombre.'.png';
   $contenido = md5(date('d-m-Y H:m:s').$iduser);

   $direccion = '../../img/qr/';
   $filename = $direccion.$qr_img;
   $tamanio = 7;
   $level = 'H';
   $frameSize = 5;
   $contenido = $contenido;
   QRcode::png ($contenido,$filename,$level,$tamanio,$frameSize);

   $query_verificador_secuencial = mysqli_query($conection, "SELECT * FROM  ingreso_vehiculo_parqueadero_guibis  WHERE  ingreso_vehiculo_parqueadero_guibis.iduser  = $iduser ORDER BY id DESC LIMIT 1");
   $result_verificador_secuencial = mysqli_fetch_array($query_verificador_secuencial);

   if ($result_verificador_secuencial) {
     $secuencial = $result_verificador_secuencial['secuencial'];
     $secuencial = $secuencial +1;
   }else {
     $secuencial =1;
   }




   $query_insert=mysqli_query($conection,"INSERT INTO ingreso_vehiculo_parqueadero_guibis(iduser,tarifa,placa,qr_imagen,qr_contenido,fecha_inicio,secuencial,notas_extras,vehiculo)
                                 VALUES('$iduser','$tarifas_parqueo','$placa','$qr_img','$contenido','$fecha_actual','$secuencial','$notas_extras','$vehiculo') ");

               if ($query_insert) {


                 $query_parqueo = mysqli_query($conection,"SELECT * FROM ingreso_vehiculo_parqueadero_guibis WHERE ingreso_vehiculo_parqueadero_guibis.iduser ='$iduser' ORDER BY ingreso_vehiculo_parqueadero_guibis.id DESC");
                 $data_parqueo = mysqli_fetch_array($query_parqueo);
                 $id_creado = $data_parqueo['id'];

                 $arrayName = array('noticia' =>'insert_correct','id_creado'=>$id_creado);
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


 if ($_POST['action'] == 'buscar_informacion_parqueo') {
   $codigo_parqueo = $_POST['parqueo'];

   $query_consulta = mysqli_query($conection, "SELECT
     ingreso_vehiculo_parqueadero_guibis.id,
     ingreso_vehiculo_parqueadero_guibis.notas_extras,
     ingreso_vehiculo_parqueadero_guibis.placa,
     ingreso_vehiculo_parqueadero_guibis.fecha_inicio,
     ingreso_vehiculo_parqueadero_guibis.estado,
     ingreso_vehiculo_parqueadero_guibis.precio_cobrado,
     tarifas_parqueo.minutos_servicio,
     tarifas_parqueo.nombre_servicio,
     tarifas_parqueo.valor_servicio ,
     tarifas_parqueo.timpo_espera
    FROM `ingreso_vehiculo_parqueadero_guibis`
    INNER JOIN tarifas_parqueo on tarifas_parqueo.id = ingreso_vehiculo_parqueadero_guibis.tarifa
WHERE ingreso_vehiculo_parqueadero_guibis.iduser  = '$iduser'  AND ingreso_vehiculo_parqueadero_guibis.estatus='1'
AND ingreso_vehiculo_parqueadero_guibis.id = '$codigo_parqueo'
ORDER BY `ingreso_vehiculo_parqueadero_guibis`.`fecha` DESC");


     $data_consulta =mysqli_fetch_array($query_consulta);

     $placa = $data_consulta['placa'];
     $fecha_actual = date("d-m-Y H:i:s");

     //INFORMACION DEL TIPO DE PLAN
     $minutos_servicio = $data_consulta['minutos_servicio'];
     $nombre_servicio = $data_consulta['nombre_servicio'];
     $valor_servicio  =$data_consulta['valor_servicio'];
     $tiempo_espera_basico = $data_consulta['timpo_espera']; //tiempo que esta en minutos


      $minutos_transcurridos = ceil((strtotime(date($fecha_actual))-strtotime(date($data_consulta['fecha_inicio'])))/(60));
      $minutos_tolerencia_totales = $tiempo_espera_basico;


      if ($minutos_transcurridos <= $minutos_tolerencia_totales) {
       $precio_servicio = $valor_servicio;
      }else {


        $minutios_corregidos_esera_tarida = $minutos_transcurridos - $data_consulta['timpo_espera'];



        $intervalos_tarifa_no_basica =  (ceil($minutios_corregidos_esera_tarida/$minutos_servicio)*$valor_servicio);


        $ancho_minutos = ceil($minutios_corregidos_esera_tarida/$minutos_servicio);
        $precio_servicio = $intervalos_tarifa_no_basica;
        $precio_servicio_redondeado = ceil($precio_servicio / 0.05) * 0.05;
      }


     $arrayName = array('noticia' =>'existe_datos','tiempo_calculado'=>$minutos_transcurridos,'precio_servicio'=>$precio_servicio,'fecha_inicio'=>$data_consulta['fecha_inicio'],'placa'=>$placa,'id_parqueo'=>$codigo_parqueo,
   'estado'=>$data_consulta['estado'],'minutos_servicio'=>$minutos_servicio,'nombre_servicio'=>$nombre_servicio);
    echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);




   // code...
 }







     if ($_POST['action'] == 'cobrar_parqueo') {


       $idparqueo   = $_POST['id_categoria'];
       $metodos_pago= $_POST['metodos_pago'];

       $query_consulta = mysqli_query($conection, "SELECT
         ingreso_vehiculo_parqueadero_guibis.id,
         ingreso_vehiculo_parqueadero_guibis.notas_extras,
         ingreso_vehiculo_parqueadero_guibis.placa,
         ingreso_vehiculo_parqueadero_guibis.fecha_inicio,
         ingreso_vehiculo_parqueadero_guibis.estado,
         ingreso_vehiculo_parqueadero_guibis.precio_cobrado,
         tarifas_parqueo.minutos_servicio,
         tarifas_parqueo.nombre_servicio,
         tarifas_parqueo.valor_servicio ,
         tarifas_parqueo.timpo_espera
        FROM `ingreso_vehiculo_parqueadero_guibis`
        INNER JOIN tarifas_parqueo on tarifas_parqueo.id = ingreso_vehiculo_parqueadero_guibis.tarifa
    WHERE ingreso_vehiculo_parqueadero_guibis.iduser  = '$iduser'  AND ingreso_vehiculo_parqueadero_guibis.estatus='1'
    AND ingreso_vehiculo_parqueadero_guibis.id = '$idparqueo'
    ORDER BY `ingreso_vehiculo_parqueadero_guibis`.`fecha` DESC");



         $data_consulta =mysqli_fetch_array($query_consulta);

         $placa = $data_consulta['placa'];
         $fecha_actual = date("d-m-Y H:i:s");

         //INFORMACION DEL TIPO DE PLAN
         $minutos_servicio = $data_consulta['minutos_servicio'];
         $nombre_servicio = $data_consulta['nombre_servicio'];
         $valor_servicio  =$data_consulta['valor_servicio'];
         $tiempo_espera_basico = $data_consulta['timpo_espera']; //tiempo que esta en minutos


          $minutos_transcurridos = ceil((strtotime(date($fecha_actual))-strtotime(date($data_consulta['fecha_inicio'])))/(60));
          $minutos_tolerencia_totales = $tiempo_espera_basico;


          if ($minutos_transcurridos <= $minutos_tolerencia_totales) {
           $precio_servicio = $valor_servicio;
          }else {


            $minutios_corregidos_esera_tarida = $minutos_transcurridos - $data_consulta['timpo_espera'];



            $intervalos_tarifa_no_basica =  (ceil($minutios_corregidos_esera_tarida/$minutos_servicio)*$valor_servicio);


            $ancho_minutos = ceil($minutios_corregidos_esera_tarida/$minutos_servicio);
            $precio_servicio = $intervalos_tarifa_no_basica;
            $precio_servicio_redondeado = ceil($precio_servicio / 0.05) * 0.05;
          }


         $query_update=mysqli_query($conection,"UPDATE ingreso_vehiculo_parqueadero_guibis SET horas='$ancho_minutos',fecha_final ='$fecha_actual',precio_cobrado='$precio_servicio',estado ='FINALIZADO',metodos_pago='$metodos_pago'  WHERE id='$idparqueo' ");

         if ($query_update) {
             $arrayName = array('noticia'=>'insert_correct','idparqueo'=>$idparqueo);
             echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);



           }else {
             $arrayName = array('noticia' =>'error_insertar');
            echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
           }


     }


 ?>
