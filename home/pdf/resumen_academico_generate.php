<?php

include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
      $libro = $_GET['libro'];
      $iduser = $_GET['iduser'];
      $query_libro = mysqli_query($conection,"SELECT * FROM yoestudiante_libros
      WHERE yoestudiante_libros.estatus = 1 AND yoestudiante_libros.iduser= '$iduser' AND yoestudiante_libros.id = '$libro' ");
      $data_libro=mysqli_fetch_array($query_libro);
       ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Resumen Acádemico de <?php echo $data_libro['nombre'] ?> </title>
  </head>
  <style media="screen">
  .img_alumno{
    text-align: center;
  }
   .contenedor_informacion_alumno{
     padding: 10px;
     margin: 10px;
   }
  .contenedor_accion_descarga{
    text-align: center;
    padding: 15px;
    margin: 10px;
  }

  .negrita_fila{
    font-weight: bold;
  }
  .primer_bloque_fila{
    background:  #ebf2bd ;
  }

  .segundo_bloque_fila{
    background:  #c1fa9f ;
  }
  .contenedor_informacion_paciente table{
    margin: 0 auto;
  }
  .contenedor_informacion_paciente td{
    width: 310px;
    padding: 10px;
  }
  .cabezera{
    background: #212529;
    color: #fff;
    width: 800PX;
    height: 100px;
    margin-top: -60PX;
    text-align: center;
    margin-left: -45px;


  }
.cabezera h4{
  margin: auto;
  padding: 15px;

}
.cabezera {
background-repeat: repeat;
background-size: auto;
opacity: 0.7;
}
  </style>
  <body>
   <div class="cabezera">
     <br>
     <h2>Resúmen  <?php echo $data_libro['nombre'] ?>  </h2>
   </div>
   <style media="screen">
     .imagen_empresa{
       text-align: center;
     }
     .imagen_empresa img{
       width: 200px;
     }
   </style>


    <style media="screen">
      .informacion_usuario{
        text-align: center;
        font-size: 18px;
        font-weight: bold;
      }
      .bd_jhg{
        font-size: 20px;
        color: #263238;

      }
    </style>

   <div class="informacion_usuario">
     <h5>Rseúmen del Libro  <?php echo $data_libro['nombre'] ?> </h5>
   </div>
<style media="screen">
.table-responsive tr,td,th  {
border: solid 1px #c1c1c1;
text-align: center;

}

.table-responsive th{
width: 138px;
}
.table-responsive td{
width: 138px;
}

.table-responsive table{
   border-collapse: collapse;
   margin: 0 auto;
}
</style>

   <main role="main" class="container">
       <div class="row">
           <div class="col-12">
               <div class="table-responsive">
                   <table class="table table-bordered">
                       <thead>
                           <tr>
                               <th>Código</th>
                               <th>Páginas</th>
                               <th>Descripción</th>
                               <th>Palabras Clave</th>
                               <th>Imagen Referencial</th>
                           </tr>
                       </thead>
                       <tbody>
                         <?php
                         mysqli_query($conection,"SET lc_time_names = 'es_ES'");
                          $query_lista = mysqli_query($conection,"SELECT DATE_FORMAT(yoestudiante_libros_descripcion.fecha, '%W  %d de %b %Y %H:%i:%s') as 'fecha_f',yoestudiante_libros_descripcion.id,
                          yoestudiante_libros_descripcion.paginas,yoestudiante_libros_descripcion.descripcion,yoestudiante_libros_descripcion.palabras_claves,yoestudiante_libros_descripcion.imagen_referencial
                      FROM `yoestudiante_libros_descripcion`
                      WHERE yoestudiante_libros_descripcion.iduser = '$iduser' AND yoestudiante_libros_descripcion.estatus = '1' AND yoestudiante_libros_descripcion.libro = '$libro'
                      ORDER BY `yoestudiante_libros_descripcion`.`fecha` DESC");
                              $result_lista= mysqli_num_rows($query_lista);
                            if ($result_lista > 0) {
                                  while ($data_lista=mysqli_fetch_array($query_lista)) {
                                    $imagen_referencial = $data_lista['imagen_referencial'];
                          ?>
                          <tr id="fila_<?php echo $data_lista['id'];?>" >
                            <td data-titulo="Código"><?php echo $data_lista['id'];?></td>
                            <td data-titulo="Páginas"><?php echo $data_lista['paginas'];?> </td>
                            <td data-titulo="Descripción"><?php echo $data_lista['descripcion'];?> </td>
                            <td data-titulo="Palabras Clave"><?php echo $data_lista['palabras_claves'];?> </td>
                             <td data-titulo="Imagen Referencial ">
                               <?php if ($imagen_referencial!= ''): ?>
                                 <a target="_blank" href="https://yoestudiante.guibis.com/home/img/uploads/<?php echo $imagen_referencial ?>"><img class="abrir_ventana_imagen" imagen = "<?php echo $data_lista['imagen_referencial']?>" src="../img/uploads/<?php echo $imagen_referencial ?>" width="100px;" alt=""></a>

                               <?php endif; ?>

                              </td>
                          </tr>
                           <?php
                           }
                           }
                       ?>
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </main>

  </body>
</html>
