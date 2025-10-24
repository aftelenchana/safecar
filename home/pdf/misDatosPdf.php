<?php
include "../../coneccion.php";
     $iduser= $_GET['id'];
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Mi Informacion </title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Sacramento&display=swap" rel="stylesheet">
    <style media="screen">
    *{
          font-family: 'Signika Negative', sans-serif;

    }
    body{
      border-color: #4C5484;
    border-width: 1px;
    border-style: solid;
    border-radius: 9px;
    padding: 10px;
    margin: 10px;
    }

    .titulo {
      text-align: center;
      margin: 12px;
    }
    .titulo h3{
      font-family: 'Sacramento', cursive;
      font-size: 80px;
      margin: 0;
      padding: 0;
    }
    .titulo  p{
      font-size: 35px;
      padding: 0;
      margin: 0;
    font-family: 'Dancing Script', cursive;
    }
    .titulo .img_logo{
      margin: 0 auto;
      width: 180px;
      height: 180px;
    }
    .titulo .img_logo img{
      max-width: 180px;
      max-height: 180px;

    }
    .informacion_personal{
      width: 320px;
      padding: 10px;

      display: inline-block;
      border-color: #4C5484;
    border-width: 1px;
    border-style: solid;
    border-radius: 9px;
    }
    .contenido{
      text-align: center;
    }
    .informacion_personal .bold{
      font-weight: bold;
    }
    .img_qr{
      display: inline-block;
      padding: 10px;
    }
    .img_qr img{
      width: 200px;
    }
    .pie_pagina{
        text-align: center;
    }
    .pie_pagina .log_leben img {
      width: 150px;

    }
    </style>
  </head>
  <?php
  $query=mysqli_query($conection,"SELECT * FROM usuarios WHERE id = $iduser");
  $result         = mysqli_fetch_array($query);
  $foto_perfil    = $result['img_perfil'];
  $logo           = $result['img_logo'];
  $nombres        = $result['nombres'];
  $apellidos      = $result['apellidos'];
  $whatsapp       = $result['whatsapp'];
  $nombre_empresa = $result['nombre_empresa'];
  $facebook       = $result['facebook'];
  $instagram      = $result['instagram'];
  $ruc            = $result['ruc'];
  $direccion      = $result['direccion'];
  $celular        = $result['celular'];
  $telefono       = $result['telefono'];
  $fecha_acceso   = $result['fecha_creacion'];
  $cuenta_paypal  = $result['cuenta_paypal'];
  $qr             = $result['img_qr'];
  $eslogan        = $result['eslogan'];



   ?>
  <body>
    <div class="titulo">
      <?php if ($nombre_empresa == ''){?>
      <h3><?php echo "$nombres" ;  ?> <?php echo "$apellidos"; ?></h3>
      <?php } ?>
      <?php if ($nombre_empresa != ''){?>
      <h3><?php echo "$nombre_empresa" ;  ?> </h3>
      <?php } ?>
      <?php if ($eslogan != ''){?>
        <p><?php echo "$eslogan" ;  ?></p>
      <?php } ?>
      <?php if ($eslogan == ''){?>
      <p></p>
      <?php } ?>
      <div class="img_logo">
        <?php if ($logo == ''){?>
        <img src="../img/uploads/<?php echo "$foto_perfil"; ?>" alt="">
        <?php } ?>

        <?php if ($logo != ''){?>
        <img src="../img/uploads/<?php echo "$logo"; ?>" alt="">
        <?php } ?>
      </div>
    </div>
    <div class="contenido">

    <div class="informacion_personal">
      <table>
        <?php if ($nombre_empresa != ''){?>
          <tr>
            <td class="bold">Empresa:</td>
            <td><?php echo "$nombre_empresa"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($nombres != ''){?>
          <tr>
            <td class="bold">Nombres:</td>
            <td><?php echo "$nombres"; ?></td>
          </tr>
        <?php } ?>

        <?php if ($apellidos != ''){?>
          <tr>
            <td class="bold">Apellidos:</td>
            <td><?php echo "$apellidos"; ?></td>
          </tr>
        <?php } ?>

        <?php if ($direccion != ''){?>
          <tr>
            <td class="bold">Direccion:</td>
            <td><?php echo "$direccion"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($telefono != ''){?>
          <tr>
            <td class="bold">Telefono:</td>
            <td><?php echo "$telefono"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($whatsapp != ''){?>
          <tr>
            <td class="bold">Whatsapp:</td>
            <td><?php echo "$whatsapp"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($facebook != ''){?>
          <tr>
            <td class="bold">Facebook:</td>
            <td><?php echo "$facebook"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($instagram != ''){?>
          <tr>
            <td class="bold">Instagram:</td>
            <td><?php echo "$instagram"; ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td class="bold">Fecha Acceso:</td>
          <td><?php echo "$fecha_acceso"; ?></td>
        </tr>
        <?php if ($ruc != ''){?>
          <tr>
            <td class="bold">Ruc:</td>
            <td><?php echo "$ruc"; ?></td>
          </tr>
        <?php } ?>
        <?php if ($cuenta_paypal != ''){?>
          <tr>
            <td class="bold">Cuenta Pay Pal:</td>
            <td><?php echo "$cuenta_paypal"; ?></td>
          </tr>
        <?php } ?>
      </table>

    </div>
    <div class="img_qr">
      <img src="../img/qr/<?php echo "$qr"; ?>" alt="">

    </div>
        </div>

    <div class="pie_pagina">
      <div class="log_leben">
        <img src="../img/reacciones/guibis.png" alt="">

      </div>

    </div>




  </body>
</html>
