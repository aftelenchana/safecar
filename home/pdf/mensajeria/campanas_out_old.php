<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$codigo = $_GET['codigo'];
$iduser = $_GET['iduser'];


$query_lista = mysqli_query($conection,"SELECT mensajes_masivos_wsp.nombre_campana,
  mensajes_masivos_wsp.modo_tiempo,
  mensajes_masivos_wsp.fecha_hora_envio,
  mensajes_masivos_wsp.fecha,
  mensajes_masivos_wsp.intervalo_tiempo
   FROM mensajes_masivos_wsp
  INNER JOIN numeros_extras ON numeros_extras.id = mensajes_masivos_wsp.code_numero
   WHERE mensajes_masivos_wsp.estatus = '1' AND mensajes_masivos_wsp.id = '$codigo'");

   $data_consulta = mysqli_fetch_array($query_lista);

   $nombre_camp = $data_consulta['nombre_campana'];

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo $nombre_camp ?></title>
  </head>
  <body>
        <?php

        // Empezar la segunda tabla con los resúmenes
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Parametro</th>";
        echo "<th>Contenido</th>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Codigo</td>";
        echo "<td>" . htmlspecialchars($codigo) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Nombre</td>";
        echo "<td>" . htmlspecialchars($data_consulta['nombre_campana']) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Modo Tiempo</td>";
        echo "<td>" . htmlspecialchars($data_consulta['modo_tiempo']) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Fecha Envio</td>";
        echo "<td>" . htmlspecialchars($data_consulta['fecha_hora_envio']) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Fecha Registro</td>";
        echo "<td>" . htmlspecialchars($data_consulta['fecha']) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Intervalo Tiempo</td>";
        echo "<td>" . htmlspecialchars($data_consulta['intervalo_tiempo']) . " segundos</td>";
        echo "</tr>";

        echo "</table>";


        $query_lista = mysqli_query($conection," SELECT *
           FROM datos_mensajes_masivos
          WHERE datos_mensajes_masivos.id_mensajes_masivos = '$codigo'
    ORDER BY `datos_mensajes_masivos`.`fecha` DESC");


    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Numero</th>";
    echo "<th>Variables</th>";
    echo "<th>Tipo</th>";
    echo "<th>Estado</th>";
    echo "<th>Estado Envio</th>";

    echo "</tr>";

    while ($fila = mysqli_fetch_assoc($query_lista)) {

    $numero = $fila['numero'];
    $partes = explode('-', $numero);

    // El primer elemento siempre será el número de teléfono
    $numero = $partes[0];


    echo "<tr>";

    echo "<td>" . htmlspecialchars($numero) . "</td>";
    echo "<td>" . htmlspecialchars($fila['numero']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['tipo']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['estado']) . "</td>";
    echo "<td>" . htmlspecialchars($fila['estado_envio']) . "</td>";
    echo "</tr>";
    }
    echo "</table><br>";


     ?>

  </body>
</html>
