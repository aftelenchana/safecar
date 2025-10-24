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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $nombre_camp ?></title>
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
    background: #263238;
    color: #fff;
    width: 800PX;
    height: 70px;
    margin-top: -60PX;
    text-align: center;
    margin-left: -48px;


  }
.cabezera h4{
  margin: auto;
  padding: 15px;

}
  </style>
  <body>
    <?php
$query = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
$result=mysqli_fetch_array($query);
$nombres           = $result['nombres'];
$direccion         = $result['direccion'];
$apellidos         = $result['apellidos'];
$img_facturacion          = $result['img_facturacion'];
$url_img_upload  = $result['url_img_upload'];


     ?>
   <div class="cabezera">
     <br>
     <h4>Campaña <?php echo $nombre_camp ?></h4>
   </div>
   <style media="screen">
     .imagen_empresa{
       text-align: center;
     }
     .imagen_empresa img{
       width: 200px;
     }
   </style>

   <div class="imagen_empresa">
     <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
   </div>


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
     <h5>Campaña</h5>
   </div>
<style media="screen">
.table-responsive tr,td,th  {
border: solid 1px #c1c1c1;
text-align: center;

}

.table-responsive th{
width: 95px;
  font-size: 11px;
}
.table-responsive td{
width: 95px;
  font-size: 11px;
}

.table-responsive table{
   border-collapse: collapse;
   margin: 0 auto;
     font-size: 11px;
}
</style>
   <main role="main" class="container">
       <div class="row">
           <div class="col-12">
               <div class="table-responsive">
                   <table class="table table-bordered">
                       <thead>
                           <tr>
                               <th>Numero</th>
                               <th>Variables</th>
                               <th>Tipo</th>
                               <th>Estado</th>
                               <th>Estado Envio</th>
                           </tr>
                       </thead>
                       <tbody>
                         <?php
                         mysqli_query($conection,"SET lc_time_names = 'es_ES'");
                         $query_lista = mysqli_query($conection," SELECT *
                            FROM datos_mensajes_masivos
                           WHERE datos_mensajes_masivos.id_mensajes_masivos = '$codigo'
                     ORDER BY `datos_mensajes_masivos`.`fecha` DESC");
                     $result_lista= mysqli_num_rows($query_lista);
                   if ($result_lista > 0) {
                         while ($data_lista=mysqli_fetch_array($query_lista)) {

                           $numero = $data_lista['numero'];
                           $partes = explode('-', $numero);

                           // El primer elemento siempre será el número de teléfono
                           $numero = $partes[0];


                          ?>
                           <tr>
                                <td><?php echo $numero; ?></td>
                               <td><?php echo $data_lista['numero']; ?> </td>
                               <td><?php echo $data_lista['tipo']; ?></td>
                               <td><?php echo $data_lista['estado']; ?></td>
                               <td><?php echo $data_lista['estado_envio']; ?></td>
                           </tr>
                           <?php
                           }
                           }
                       ?>
                       </tbody>
                   </table>
                   <style media="screen">
                   .tabla_resumen_tr{
                     width: 100px;
                     height: 20px
                     padding: 5px;
                   }
                   .resumen_ganancias_dia{
                     padding: 5px;
                     margin: 3px;
                   }

                   </style>
                   <div class="resumen_ganancias_dia">
                     <?php

                     $query_lista = mysqli_query($conection,"SELECT mensajes_masivos_wsp.nombre_campana,
                       mensajes_masivos_wsp.modo_tiempo,
                       mensajes_masivos_wsp.fecha_hora_envio,
                       mensajes_masivos_wsp.fecha,
                       mensajes_masivos_wsp.intervalo_tiempo,
                       numeros_extras.numero
                        FROM mensajes_masivos_wsp
                       INNER JOIN numeros_extras ON numeros_extras.id = mensajes_masivos_wsp.code_numero
                        WHERE mensajes_masivos_wsp.estatus = '1' AND mensajes_masivos_wsp.id = '$codigo'");

                        $data_consulta = mysqli_fetch_array($query_lista);

                        $nombre_camp = $data_consulta['nombre_campana'];

                      ?>

                     <table>
                       <tr class="tabla_resumen_tr">
                         <th class="tabla_resumen_tr">Parametro</th>
                         <th class="tabla_resumen_tr">Contenido</th>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Codigo</td>
                         <td class="tabla_resumen_tr"><?php echo $codigo?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Nombre</td>
                         <td class="tabla_resumen_tr"><?php echo $data_consulta['nombre_campana']?></td>
                       </tr>

                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Modo Tiempo</td>
                         <td class="tabla_resumen_tr"><?php echo $data_consulta['modo_tiempo']?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Fecha Envio</td>
                         <td class="tabla_resumen_tr"><?php echo $data_consulta['fecha_hora_envio']?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Fecha</td>
                         <td class="tabla_resumen_tr"><?php echo $data_consulta['fecha']?></td>
                       </tr>
                       <tr class="tabla_resumen_tr">
                         <td class="tabla_resumen_tr">Número</td>
                         <td class="tabla_resumen_tr"><?php echo $data_consulta['numero']?></td>
                       </tr>

                     </table>

                   </div>
               </div>
           </div>
       </div>
   </main>


      <style type="text/css">


          .page-break {
              page-break-before: always;
              margin-top: 0;
              padding-top: 0;
          }

          .titulo_consiguiente {
              margin-top: 0;
              padding-top: 0;
          }

      </style>





  </body>
</html>
