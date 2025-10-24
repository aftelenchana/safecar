<?php
require("../home/mail/PHPMailer-master/src/PHPMailer.php");
require("../home/mail/PHPMailer-master/src/Exception.php");
require("../home/mail/PHPMailer-master/src/SMTP.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
require "../coneccion.php";
mysqli_set_charset($conection, 'utf8');

$usuarios_totales = []; // Mover esta línea fuera del bucle while

if (isset($_COOKIE['session_token'])) {
    $session_token = $_COOKIE['session_token'];
    $session_token = mysqli_real_escape_string($conection, $session_token); // Protección contra inyección SQL

    $query = mysqli_query($conection, "SELECT * FROM sessions WHERE session_token = '$session_token'");

    while ($row = mysqli_fetch_array($query)) {
        $user_id = $row['user_id'];
        $rol = $row['rol'];

        if ($rol == 'Cuenta Empresa') {
            $query_usuario_central = mysqli_query($conection, "SELECT usuarios.nombres,usuarios.id,usuarios.email,usuarios.img_facturacion,usuarios.url_img_upload FROM usuarios WHERE id ='$user_id'");
            while ($data_usuario_central = mysqli_fetch_assoc($query_usuario_central)) {
                $data_usuario_central['rol'] = 'Cuenta Empresa';
                $usuarios_totales[] = $data_usuario_central;
            }
        }

        if ($rol == 'Usuario Venta') {
            $query_usuario_punto_venta = mysqli_query($conection, "SELECT usuarios_punto_venta.nombres,usuarios_punto_venta.id,usuarios_punto_venta.mail, usuarios_punto_venta.foto,usuarios_punto_venta.url_img_upload
              FROM usuarios_punto_venta WHERE id = '$user_id'
            AND usuarios_punto_venta.estatus = '1' ");
            while ($data_usuario_punto_venta = mysqli_fetch_assoc($query_usuario_punto_venta)) {
                $data_usuario_punto_venta['rol'] = 'Usuario Venta';
                $usuarios_totales[] = $data_usuario_punto_venta;
            }
        }


        if ($rol == 'Recursos Humanos') {
          $query_usuario_recursos_humanos = mysqli_query($conection, "SELECT recursos_humanos.id,recursos_humanos.nombres,recursos_humanos.url_img_upload,
           recursos_humanos.foto,categoria_recursos_humanos.nombre  FROM recursos_humanos
            INNER JOIN categoria_recursos_humanos ON categoria_recursos_humanos.id = recursos_humanos.categoria_recursos_humanos
            WHERE recursos_humanos.estatus = '1' AND recursos_humanos.id = '$user_id'  ");
          while ($data_usuario_recursos_humanos = mysqli_fetch_assoc($query_usuario_recursos_humanos)) {
                  $data_usuario_recursos_humanos['rol'] = 'Recursos Humanos'; // Agregar el rol
                  $usuarios_totales[] = $data_usuario_recursos_humanos; // Añadir al array total
           }
        }
    }

    // Ahora, fuera del bucle while, hacemos echo del JSON una sola vez
    if (count($usuarios_totales) > 0) {
        echo json_encode($usuarios_totales, JSON_UNESCAPED_UNICODE);
    } else {
        $arrayName = array('respuesta' => 'no_existe_resultados');
        echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
    }
} else {
    $arrayName = array('respuesta' => 'no_existe_resultados');
    echo json_encode($arrayName, JSON_UNESCAPED_UNICODE);
}
?>
