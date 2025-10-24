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

     $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
     $domain = $_SERVER['HTTP_HOST'];
     $domain = preg_replace('/^www\./i', '', $domain);
     $url = $protocol . $domain;



 if ($_POST['action'] == 'agregar_empresas') {


   if (!empty($_FILES['foto']['name'])) {
     $foto           =    $_FILES['foto'];
     $nombre_foto    =    $foto['name'];
     $type 					 =    $foto['type'];
     $url_temp       =    $foto['tmp_name'];
     $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
     $destino = '../img/uploads/';
     $img_nombre = 'guibis_empresa'.md5(date('d-m-Y H:m:s').$iduser);
     $img_empresa = $img_nombre.'.'.$extension;
     $src = $destino.$img_empresa;
     move_uploaded_file($url_temp,$src);

     }else {
        $img_empresa = 'usuario.png';
     }

     $sincronizacion = '0';



     $identificacion   =  mysqli_real_escape_string($conection,$_POST['identificacion']);
     $celular          =  mysqli_real_escape_string($conection,$_POST['celular']);
     $telefono         =  mysqli_real_escape_string($conection,$_POST['telefono']);
     $email            =  mysqli_real_escape_string($conection,$_POST['email']);
     $direccion        =  mysqli_real_escape_string($conection,$_POST['direccion']);

     $observaciones        =  mysqli_real_escape_string($conection,$_POST['observaciones']);
     $actividadEconomica        =  mysqli_real_escape_string($conection,$_POST['actividadEconomica']);

     //VARIABLES CUANDO LA SINCONICACION NO ESTA ACTIVA
     $razon_social      = (isset($_REQUEST['razon_social'])) ? $_REQUEST['razon_social'] : '';
     $nombre_empresa    = (isset($_REQUEST['nombre_empresa'])) ? $_REQUEST['nombre_empresa'] : '';
     $clave_firma       = (isset($_REQUEST['clave_firma'])) ? $_REQUEST['clave_firma'] : '';
     $registro_mercantil       = (isset($_REQUEST['registro_mercantil'])) ? $_REQUEST['registro_mercantil'] : '';

     //CODIGO PARA LA FIRMA ELECTRONICA

        if (!empty($_FILES['firma_electronocia']['name'])) {
          $firma_electronocia           =    $_FILES['firma_electronocia'];
          $nombre_firma_electronica     =    $firma_electronocia['name'];
          $type 					              =    $firma_electronocia['type'];
          $url_temp                     =    $firma_electronocia['tmp_name'];

          $firma_electronica   =   'firma_electronica.p12';
          if ($nombre_firma_electronica != '') {
           $destino = '../facturacion/facturacionphp/controladores/firmas_electronicas/';
           $img_nombre = 'firma_guibis'.md5(date('d-m-Y H:m:s').$iduser);
           $firma_electronica = $img_nombre.'.p12';
           $src_firma = $destino.$firma_electronica;
           move_uploaded_file($url_temp,$src_firma);
          }

          require_once '../facturacion/facturacionphp/lib/nusoap.php';
          $almacen_cert = file_get_contents($src_firma);

         if (openssl_pkcs12_read($almacen_cert, $info_cert, $clave_firma)) {

           // Asumiendo que $info_cert es tu array que contiene la información del certificado
           $certificado = openssl_x509_read($info_cert["cert"]);
           $detalles = openssl_x509_parse($certificado);
           // Fecha de caducidad del certificado
           $fechaCaducidad = $detalles['validTo_time_t'];
           // Convertir la fecha de caducidad a un formato legible
           $fechaCaducidadLegible = date('Y-m-d H:i:s', $fechaCaducidad);
             // Obtener la fecha y hora actual
             $fechaActual = date('Y-m-d H:i:s');
             if (strtotime($fechaCaducidadLegible) > strtotime($fechaActual)) {



               $query_insert=mysqli_query($conection,"INSERT INTO empresas_registradas (iduser,razon_social, nombre_empresa ,identificacion,img_empresa,celular,telefono,email,direccion,url,sincronizacion,fecha_caducidad,clave_firma,firma,registro_mercantil,observaciones,actividadEconomica)
                                             VALUES('$iduser','$razon_social','$nombre_empresa','$identificacion','$img_empresa','$celular','$telefono','$email','$direccion','$url','$sincronizacion','$fechaCaducidadLegible','$clave_firma','$firma_electronica','$registro_mercantil','$observaciones','actividadEconomica') ");

             if ($query_insert) {
                 $arrayName = array('noticia'=>'insert_correct');
                 echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

               }else {

                $arrayName = array('noticia' =>'error_insertar','contenido_error' => mysqli_error($conection));
                           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
               }
               exit;

             }
            // code...
          }
       // code...
       }


     $query_insert=mysqli_query($conection,"INSERT INTO empresas_registradas (iduser,razon_social, nombre_empresa ,identificacion,img_empresa,celular,telefono,email,direccion,url,sincronizacion,registro_mercantil,observaciones,actividadEconomica)
                                   VALUES('$iduser','$razon_social','$nombre_empresa','$identificacion','$img_empresa','$celular','$telefono','$email','$direccion','$url','$sincronizacion','$registro_mercantil','$observaciones','actividadEconomica') ");


           if ($query_insert) {
               $arrayName = array('noticia'=>'insert_correct');
               echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

             }else {

              $arrayName = array('noticia' =>'error_insertar','contenido_error' => mysqli_error($conection));
               echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
             }
             exit;

  // code...
}



if ($_POST['action'] == 'informacion_datos_empresa') {

     $empresa       = $_POST['empresa'];
     $query_consulta = mysqli_query($conection, "SELECT * FROM empresas_registradas
        WHERE   empresas_registradas.id = '$empresa' ");
  $data_producto = mysqli_fetch_array($query_consulta);
  echo json_encode($data_producto,JSON_UNESCAPED_UNICODE);
}


if ($_POST['action'] == 'editar_empresa') {



  $empresa         = mysqli_real_escape_string($conection,$_POST['empresa']);


  $razon_social      = (isset($_REQUEST['razon_social'])) ? $_REQUEST['razon_social'] : '';
  $nombre_empresa      = (isset($_REQUEST['nombre_empresa'])) ? $_REQUEST['nombre_empresa'] : '';
  $clave_firma      = (isset($_REQUEST['clave_firma'])) ? $_REQUEST['clave_firma'] : '';
  $regimen          = (isset($_REQUEST['regimen'])) ? $_REQUEST['regimen'] : '';
  $contabilidad      = (isset($_REQUEST['contabilidad'])) ? $_REQUEST['contabilidad'] : '';
  $contribuyente_especial      = (isset($_REQUEST['contribuyente_especial'])) ? $_REQUEST['contribuyente_especial'] : '';
  $agente_retencion      = (isset($_REQUEST['agente_retencion'])) ? $_REQUEST['agente_retencion'] : '';
  $identificacion      = (isset($_REQUEST['identificacion'])) ? $_REQUEST['identificacion'] : '';
  $celular      = (isset($_REQUEST['celular'])) ? $_REQUEST['celular'] : '';
  $telefono      = (isset($_REQUEST['telefono'])) ? $_REQUEST['telefono'] : '';
  $email      = (isset($_REQUEST['email'])) ? $_REQUEST['email'] : '';
  $direccion      = (isset($_REQUEST['direccion'])) ? $_REQUEST['direccion'] : '';
  $password      = (isset($_REQUEST['password'])) ? $_REQUEST['password'] : '';
  $registro_mercantil       = (isset($_REQUEST['registro_mercantil'])) ? $_REQUEST['registro_mercantil'] : '';


  if (!empty($_FILES['firma_electronocia']['name'])) {

    $firma_electronica   =    $_FILES['firma_electronocia'];
    $nombre_firma        =    $firma_electronica['name'];
    $type 					      =    $firma_electronica['type'];
    $url_temp            =    $firma_electronica['tmp_name'];
    $extension = pathinfo($nombre_firma, PATHINFO_EXTENSION);
    $destino = '../facturacion/facturacionphp/controladores/firmas_electronicas/';
    $file_firma = 'firma_guiibis'.md5(date('d-m-Y H:m:s').$iduser);
    $firma_electronica = $file_firma.'.'.$extension;
    $src = $destino.$firma_electronica;
      move_uploaded_file($url_temp,$src);

      require_once '../facturacion/facturacionphp/lib/nusoap.php';

      $almacen_cert = file_get_contents($src);

      if (openssl_pkcs12_read($almacen_cert, $info_cert, $clave_firma)) {
        // Asumiendo que $info_cert es tu array que contiene la información del certificado
        $certificado = openssl_x509_read($info_cert["cert"]);
        $detalles = openssl_x509_parse($certificado);
        // Fecha de caducidad del certificado
        $fechaCaducidad = $detalles['validTo_time_t'];
        // Convertir la fecha de caducidad a un formato legible
        $fechaCaducidadLegible = date('Y-m-d H:i:s', $fechaCaducidad);

          // Obtener la fecha y hora actual
          $fechaActual = date('Y-m-d H:i:s');

           if (strtotime($fechaCaducidadLegible) > strtotime($fechaActual)) {

             if (!empty($_FILES['foto']['name'])) {
               $foto           =    $_FILES['foto'];
               $nombre_foto    =    $foto['name'];
               $type 					 =    $foto['type'];
               $url_temp       =    $foto['tmp_name'];
               $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
               $destino = '../img/uploads/';
               $img_nombre = 'guibis_imagen'.md5(date('d-m-Y H:m:s').$iduser);
               $img_empresa = $img_nombre.'.'.$extension;
               $src = $destino.$img_empresa;
               move_uploaded_file($url_temp,$src);

               $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
               $domain = $_SERVER['HTTP_HOST'];
               $domain = preg_replace('/^www\./i', '', $domain);
               $url_img_upload = $protocol . $domain;
               }else {
                 $query_consulta = mysqli_query($conection, "SELECT * FROM empresas_registradas
                    WHERE   empresas_registradas.id = '$empresa' ");
                    $data_consulta  = mysqli_fetch_array($query_consulta);
                 $img_empresa = $data_consulta['img_empresa'];
                 $url_img_upload = $data_consulta['url'];
               }



             $query_update =mysqli_query($conection,"UPDATE empresas_registradas SET firma= '$firma_electronica' ,clave_firma= '$clave_firma'
               ,razon_social= '$razon_social',nombre_empresa= '$nombre_empresa',identificacion= '$identificacion'
               ,celular= '$celular',telefono= '$telefono',firma = '$firma_electronica',clave_firma = '$clave_firma',img_empresa='$img_empresa'
               ,email='$email',direccion='$direccion',fecha_caducidad='$fechaCaducidadLegible'
               ,url='$url_img_upload'
               ,fecha_expiracion='$fecha_expiracion'
               ,cantidad_documentos='$cantidad_documentos'
               ,registro_mercantil='$registro_mercantil'
               WHERE id='$empresa' ");
               if ($query_update) {
                 $arrayName = array('noticia' =>'insert_correct');
                echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

                 }else {
                   $arrayName = array('noticia' =>'error_insertar','contenido_error' => mysqli_error($conection));
                  echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
                 }
            // code...
          }else {
            $arrayName = array('noticia' =>'firma_caducada');
           echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
            // code...
          }



      }else {
        $arrayName = array('noticia' =>'error_credenciales_firma');
       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
      }



  }else {
    $query_consulta = mysqli_query($conection, "SELECT * FROM empresas_registradas
       WHERE   empresas_registradas.id = '$empresa' ");
       $data_consulta  = mysqli_fetch_array($query_consulta);

       $firma_electronica = $data_consulta['firma'];
       $clave_firma       = $data_consulta['clave_firma'];

       //CODIGO PARA LA IMAGEN

       if (!empty($_FILES['foto']['name'])) {
         $foto           =    $_FILES['foto'];
         $nombre_foto    =    $foto['name'];
         $type 					 =    $foto['type'];
         $url_temp       =    $foto['tmp_name'];
         $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
         $destino = '../img/uploads/';
         $img_nombre = 'guibis_imagen'.md5(date('d-m-Y H:m:s').$iduser);
         $img_empresa = $img_nombre.'.'.$extension;
         $src = $destino.$img_empresa;
         move_uploaded_file($url_temp,$src);
         $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
         $domain = $_SERVER['HTTP_HOST'];
         $domain = preg_replace('/^www\./i', '', $domain);
         $url_img_upload = $protocol . $domain;
         }else {
           $img_empresa = $data_consulta['img_empresa'];
         }

         $url_img_upload = mysqli_real_escape_string($conection, $url_img_upload);

       $query_update =mysqli_query($conection,"UPDATE empresas_registradas SET firma= '$firma_electronica' ,clave_firma= '$clave_firma'
         ,razon_social= '$razon_social',nombre_empresa= '$nombre_empresa',regimen= '$regimen',contabilidad= '$contabilidad'
         ,contribuyente_especial= '$contribuyente_especial',agente_retencion= '$agente_retencion',identificacion= '$identificacion'
         ,celular= '$celular',telefono= '$telefono',img_empresa='$img_empresa',email='$email',direccion='$direccion'
         ,url='$url_img_upload',registro_mercantil='$registro_mercantil'
         WHERE id='$empresa' ");

        if ($query_update) {
          $arrayName = array('noticia' =>'insert_correct');
         echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);

          }else {
            $arrayName = array('noticia' =>'error_insertar','contenido_error' => mysqli_error($conection));
                       echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
          }

  }

}



if ($_POST['action'] == 'consultar_datos_empresas') {

  //VERIFICAMOS SI SON TODOS O CIERTAS FUENTES
  mysqli_query($conection,"SET lc_time_names = 'es_ES'");
     $sql = "
         SELECT *
         FROM empresas_registradas
         WHERE empresas_registradas.iduser = '$iduser'
             AND empresas_registradas.estatus = '1'
     ";


          $sql .= " ORDER BY empresas_registradas.fecha DESC  ";

   // Ejecutar la consulta SQL
   $query_consulta = mysqli_query($conection, $sql);

  $data = array();
  while ($row = mysqli_fetch_assoc($query_consulta)) {

      $data[] = $row;

  }
  echo json_encode(array("data" => $data));

}

 ?>
