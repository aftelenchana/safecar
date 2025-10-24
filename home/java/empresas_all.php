<?php

require("../mail/PHPMailer-master/src/PHPMailer.php");
require("../mail/PHPMailer-master/src/Exception.php");
require("../mail/PHPMailer-master/src/SMTP.php");
use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
// La instanciación y el paso de `true` habilita excepciones

session_start();

 require '../QR/phpqrcode/qrlib.php';
 include "../../coneccion.php";
  mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar


    if ($_SESSION['rol'] == 'cuenta_empresa') {
    include "../sessiones/session_cuenta_empresa.php";
    $empresa = $_COOKIE['empresa_id'];

    }

    if ($_SESSION['rol'] == 'cuenta_usuario_venta') {
    include "../sessiones/session_cuenta_usuario_venta.php";

    }

    $query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
    $result_configuracion = mysqli_fetch_array($query_configuracioin);
    $ambito_area          =  $result_configuracion['ambito'];
    $envio_wsp          =  $result_configuracion['envio_wsp'];


    if ($_POST['action'] == 'consultar_datos') {

      $filtro       = $_POST['filtro'];
      $rol          = $_POST['categoria_recursos_humanos'];
      $tipo_cliente = $_POST['tipo_cliente'];
      $estado       = $_POST['estado'];

      // Configurar el formato de fechas a español
      mysqli_query($conection, "SET lc_time_names = 'es_ES'");

      // Consulta inicial
      $sql = "SELECT * FROM aplicaciones_api WHERE aplicaciones_api.iduser = '$iduser' ";

      // Bandera para determinar si es necesario usar WHERE o AND
      $whereAdded = true;

      // Filtros basados en rol
      if ($rol != 'Todos') {
          if ($rol == 'Distribuidor') {
              $sql .= " WHERE usuarios.id = usuarios.user_in";
              $whereAdded = true;
              $abrir_enlace = 'si';
          }
      }

      // Filtros basados en el término de búsqueda ($filtro)
      if ($filtro != '') {
          if ($whereAdded) {
              $sql .= " AND";
          } else {
              $sql .= " WHERE";
              $whereAdded = true;
          }
          $sql .= " (aplicaciones_api.iduser LIKE '%$filtro%' OR aplicaciones_api.nombre LIKE '%$filtro%' )";
      }

      // Orden final
      $sql .= " ORDER BY aplicaciones_api.id DESC";

      $sql .= " LIMIT 50";

      // Ejecutar la consulta SQL
      $query_consulta = mysqli_query($conection, $sql);

      // Almacenar los resultados en un array
      $data = array();
      while ($row = mysqli_fetch_assoc($query_consulta)) {
             $data[] = $row;
      }

      // Devolver los datos en formato JSON
      echo json_encode(array("data" => $data));
  }




 if ($_POST['action'] == 'informacion_aplicacion') {

      $aplicacion       = $_POST['aplicacion'];
      $query_consulta = mysqli_query($conection, "SELECT * FROM aplicaciones_api
         WHERE aplicaciones_api.id = '$aplicacion' ");
   $data_producto = mysqli_fetch_array($query_consulta);
   echo json_encode($data_producto,JSON_UNESCAPED_UNICODE);

 }




 if ($_POST['action'] == 'editar_aplicacion') {

     $aplicacion   =  mysqli_real_escape_string($conection,$_POST['aplicacion']);


     $nombre = mysqli_real_escape_string($conection, $_POST['nombre']);
     $estado = mysqli_real_escape_string($conection, $_POST['estado']);

     $query_insert = mysqli_query($conection,"UPDATE aplicaciones_api SET
       nombre='$nombre',
       estado='$estado'
       WHERE id = '$aplicacion'");
     if ($query_insert) {

         $arrayName = array('noticia'=>'insert_correct');
         echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

       }else {
         $arrayName = array('noticia' =>'error_insertar','contenido_error' => mysqli_error($conection));
        echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
       }

   }

   if ($_POST['action'] == 'agregar_categoria') {

       $nombre = mysqli_real_escape_string($conection, $_POST['nombre']);
       $estado = mysqli_real_escape_string($conection, $_POST['estado']);

       // ===== Generar API KEY =====
       // Fecha actual
       $stamp = md5(date('YmdHis')); // YYYYMMDDHHMMSS

       // Random extra
       $random = bin2hex(random_bytes(16)); // 32 chars

       // Base única con iduser + fecha + random
       $base = $iduser . '|' . $stamp . '|' . $random;

       // Hash fuerte en mayúsculas
       $hmac = strtoupper(hash('sha256', $base));

       // Formateo con guiones + fecha/hora al final
       $key_api = substr($hmac, 0, 8) . '-' .
                  substr($hmac, 8, 4) . '-' .
                  substr($hmac, 12, 4) . '-' .
                  substr($hmac, 16, 4) . '-' .
                  substr($hmac, 20, 12) . '-' .
                  $stamp;

       // ===== Insert =====
       $query_insert = mysqli_query(
           $conection,
           "INSERT INTO aplicaciones_api (iduser,nombre,estado,key_api)
            VALUES('$iduser','$nombre','$estado','$key_api')"
       );

       if ($query_insert) {
           $arrayName = array(
               'noticia' => 'insert_correct',
               'key_api' => $key_api
           );
           echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
       } else {
           $arrayName = array(
               'noticia' => 'error',
               'contenido_error' => mysqli_error($conection)
           );
           echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
       }
   }





 ?>
