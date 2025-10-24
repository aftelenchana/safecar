<?php

require("../mail/PHPMailer-master/src/PHPMailer.php");
require("../mail/PHPMailer-master/src/Exception.php");
require("../mail/PHPMailer-master/src/SMTP.php");
require '../QR/phpqrcode/qrlib.php';

use  PHPMailer \ PHPMailer \ PHPMailer ;
use  PHPMailer \ PHPMailer \ Exception ;
$mail = new  PHPMailer ( true );
    include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
         session_start();
         $iduser= $_SESSION['id'];

           $query_existencia_cantidad = mysqli_query($conection, "SELECT * FROM `saldo_total_leben` WHERE idusuario = $iduser");
           $result_existencia_cantidad = mysqli_num_rows($query_existencia_cantidad);

           if ($result_existencia_cantidad > 0) {

           }else {
                     $query_insert_tabla_cantidad=mysqli_query($conection,"INSERT INTO saldo_total_leben(idusuario,cantidad)
                                                   VALUES('$iduser','0') ");
           }

           $query = mysqli_query($conection, "SELECT * FROM `saldo_total_leben`
            INNER JOIN usuarios ON saldo_total_leben.idusuario = usuarios.id
            WHERE usuarios.id = $iduser ");
            $result = mysqli_fetch_array($query);
            $qr_bancario =  $result['qr_bancario'];
            $password =  $result['password'];
            $fecha_creacion =  $result['fecha_creacion'];
            if ($qr_bancario == '') {
              $codigo = $iduser.md5(date('d-m-Y H:m:s').$fecha_creacion.$password.$iduser);
              $img_nombre = $iduser.md5(date('d-m-Y H:m:s').$fecha_creacion.$iduser);
              $imgProducto2 = $img_nombre.'.png';
              $dirboletos = '../img/qr_bancario/';
              $filename = $dirboletos.$imgProducto2;
              $tamanio = 7;
              $level = 'H';
              $frameSize = 5;
              $contenido = $codigo;
              QRcode::png ($contenido,$filename,$level,$tamanio,$frameSize);
              $query_insert=mysqli_query($conection,"UPDATE usuarios SET qr_bancario='$imgProducto2',codigo_qr_unico='$codigo'  WHERE id='$iduser' ");
            }
            $query = mysqli_query($conection, "SELECT * FROM `saldo_total_leben`
             INNER JOIN usuarios ON saldo_total_leben.idusuario = usuarios.id
             WHERE usuarios.id = $iduser ");
           mysqli_close($conection);
           $result = mysqli_num_rows($query);
           if ($result > 0) {
             $data = mysqli_fetch_assoc($query);
             echo json_encode($data,JSON_UNESCAPED_UNICODE);

           }



 ?>
