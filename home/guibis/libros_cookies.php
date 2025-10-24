<?php
$libros = [];

// Extracción de la información de las cookies
foreach ($_COOKIE as $key => $value) {
  if (preg_match('/^currentPage_(.+)$/', $key, $matches)) {
      $encodedBookName = $matches[1];
      $bookName = urldecode($encodedBookName);
      $currentPage = $value;
      $currentZoom = $_COOKIE['currentZoom_' . $encodedBookName] ?? 'No especificado';
      $creationDate = $_COOKIE['creationDate_' . $encodedBookName] ?? 'Fecha no especificada'; // Extraer la fecha de creación

      // Convertir la fecha ISO a un timestamp
      $timestamp = strtotime($creationDate);
      // Formatear la fecha en español
      $fechaFormateada = strftime('%d de %B del %Y a las %H:%M', $timestamp);

      // Suponemos que la imagen del libro se puede generar o localizar por el nombre del libro
      $libros[] = [
          'nombre' => $bookName,
          'pagina_actual' => $currentPage,
          'zoom' => $currentZoom,
          'fecha_creacion' => $fechaFormateada, // Añadir la fecha de creación formateada
          'timestamp' => $timestamp  // Añadir timestamp para ordenación
      ];
  }
}

// Función de comparación para ordenar por fecha de creación
function compararFechas($a, $b) {
  return $b['timestamp'] - $a['timestamp'];  // Orden descendente
}

// Ordenar libros por fecha de creación
usort($libros, 'compararFechas');

?>

   <div class="mt-3 ">
         <div class="guibis_tarjeta-scroll ">
             <?php foreach ($libros as $libro): ?>
               <?php
               //CODIGO PARA SACAR LA INFORMACION DE CADA LIBRO

               $codigo_total = $libro['nombre'];
               $partes = explode('-', $codigo_total); // Dividir la cadena en partes basadas en el guion
               $codigo_libro = $partes[0]; // El primer elemento del array resultante

               $query_libro = mysqli_query($conection,"SELECT * FROM yoestudiante_libros
               WHERE yoestudiante_libros.estatus = 1 AND  yoestudiante_libros.id = '$codigo_libro' ");
               $data_libro=mysqli_fetch_array($query_libro);


                ?>
                <a style="padding: 0px;margin: 0px;" href="vie-libro?code=<?php echo $codigo_libro ?>" class="col-auto mb-3">
                        <div  class="card guibis_tarjeta-card">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="/home/img/uploads/<?php echo $data_libro['imagen'] ?>" class="img-fluid guibis_tarjeta-img" alt="<?php echo $data_libro['imagen'] ?>">
                                </div>
                                <div class="col-8">
                                    <div class="card-body guibis_tarjeta-body">
                                        <h6 class="card-title"><?php echo $data_libro['nombre'] ?></h6>
                                        <p style="padding: 0;margin: 0;" class="card-text">Página: <?= htmlspecialchars($libro['pagina_actual']) ?></p>
                                        <p style="padding: 0;margin: 0;"><?= htmlspecialchars($libro['fecha_creacion']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

             <?php endforeach; ?>
         </div>
     </div>
