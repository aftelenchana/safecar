<?php

include "../coneccion.php";
  mysqli_set_charset($conection, 'utf8'); //linea a colocar

      $numero_ciruc = $_POST['numero_ciruc'];
      $datos = json_decode(file_get_contents('https://www.guibis.com/dev/ruc?key=F48ECE4E445FD02B738F96B8561E32B9&callback=all&CIRUC='.$numero_ciruc.''),true);

      if (!empty($datos['noticia'])){
        $arrayName = array('noticia'=>'consulta_no_existente');
        echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);
        exit;
      }


      $arrayName = array('noticia'=>'consulta_exitosa','NUMERO_RUC' =>$datos['NUMERO_RUC'],'ACTIVIDAD_ECONOMICA'=>
    $datos['ACTIVIDAD_ECONOMICA'],'DESCRIPCION_PROVINCIA' =>$datos['DESCRIPCION_PROVINCIA'],'DESCRIPCION_CANTON' =>$datos['DESCRIPCION_CANTON'],'RAZON_SOCIAL' =>$datos['RAZON_SOCIAL'],
  'NOMBRE_COMERCIAL' =>$datos['NOMBRE_COMERCIAL'],'ESTADO_CONTRIBUYENTE' =>$datos['ESTADO_CONTRIBUYENTE']);
      echo json_encode($arrayName,JSON_UNESCAPED_UNICODE);




 ?>
