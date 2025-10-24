<?php
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$codigo = $_GET['codigo'];
$query_consulta = mysqli_query($conection, "SELECT pago_salarios_recurso_humano.fecha_corte,categoria_recursos_humanos.nombre as 'cargo',
  categoria_recursos_humanos.salario,pago_salarios_recurso_humano.estado,recursos_humanos.foto,recursos_humanos.nombres as 'nombres_recursos_humanos',recursos_humanos.mail,
  recursos_humanos.tipo_identificacion,recursos_humanos.direccion,recursos_humanos.identificacion,recursos_humanos.celular,recursos_humanos.tipo_cliente,
  recursos_humanos.actividad_economica,recursos_humanos.parroquia,recursos_humanos.ciudad,recursos_humanos.provincia,recursos_humanos.telefono,
  recursos_humanos.categoria_recursos_humanos,recursos_humanos.cargas_familiares,pago_salarios_recurso_humano.id,usuarios.id as 'iduser',
  usuarios.nombre_empresa,categoria_recursos_humanos.salario,recursos_humanos.id as 'id_recurso_humano',usuarios.apellidos as 'apellidos_user',
  usuarios.nombres as 'nombres_user',usuarios.img_facturacion,usuarios.url_img_upload,pago_salarios_recurso_humano.debito_iees,pago_salarios_recurso_humano.adelantos,
  pago_salarios_recurso_humano.multas,pago_salarios_recurso_humano.total_pago
   FROM pago_salarios_recurso_humano
   INNER JOIN recursos_humanos ON recursos_humanos.id = pago_salarios_recurso_humano.id_usuario_recurso_humano
   INNER JOIN categoria_recursos_humanos ON categoria_recursos_humanos.id = recursos_humanos.categoria_recursos_humanos
   INNER JOIN usuarios ON usuarios.id = recursos_humanos.iduser
   WHERE  pago_salarios_recurso_humano.estatus = '1'
   AND pago_salarios_recurso_humano.id = '$codigo'
ORDER BY `pago_salarios_recurso_humano`.`fecha` DESC ");

$data_usuaio  = mysqli_fetch_array($query_consulta);
$salario_general = $data_usuaio['salario'];
$iduser      = $data_usuaio['iduser'];
$id_recurso_humano      = $data_usuaio['id_recurso_humano'];

//INFORMACION DEL USUARIO EMPRESA
$apellidos_user         = $data_usuaio['apellidos_user'];
$nombres_user      = $data_usuaio['nombres_user'];

$img_facturacion = $data_usuaio['img_facturacion'];
$url_img_upload  = $data_usuaio['url_img_upload'];
$fecha_corte     = $data_usuaio['fecha_corte'];

$total_pago     = $data_usuaio['total_pago'];

 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Rol de Pagos</title>
  </head>

<body>
  <style media="screen">

  body {
        background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>');
        background-size: cover;
        background-position: center;
    }

    .contenedor_titulo{
      text-align: center;
      font-weight: bold;
    }
    .contenedor_titulo h2,h4{
      padding: 1px;
      margin: 1px;
    }
    .contenedor_informacion_usuario_recursos_humanos{
      padding: 5px;
      margin: 5px;

    }

    .contenedor_informacion_usuario_recursos_humanos table{
      padding: 0;
      margin: 0;
      width: 100%;
      font-size: 12px;
      border: 1px solid black; /* Establece el borde de la tabla y sus celdas */

    }

    .contenedor_tabla_resumen table{
      padding: 0;
      margin: 0;
      width: 100%;
      font-size: 12px;
      border: 1px solid black; /* Establece el borde de la tabla y sus celdas */

    }

    .contenedor_tabla_resumen .tabla_ingresos table{
      text-align: center;
    }

    .contenedor_tabla_resumen .tabla_egresos table{
      text-align: center;
    }

    .contenedor_tabla_resumen table {
      border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
  }

    .contenedor_tabla_resumen th{
        border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
    }
    .contenedor_tabla_resumen td{
        border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
    }







    .contendeor_logo_empresa img{
    width: 150px;
    }
    .contendeor_logo_empresa{
      text-align: center;
    }
  </style>

  <div class="contendeor_logo_empresa">
    <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">

  </div>
   <div class="contenedor_titulo">
     <h2 style="padding: 0px;margin: 0px;"><?php echo $data_usuaio['nombre_empresa'] ?></h2>
     <h4 style="padding: 0px;margin: 0px;">ROL DE PAGOS</h4>
   </div>
   <div class="contenedor_informacion_usuario_recursos_humanos">
     <table>
       <tr>
         <td class="negrita">Nombres Y Apellidos</td>
         <td><?php echo $data_usuaio['nombres_recursos_humanos'] ?></td>
       </tr>
       <tr>
         <td class="negrita">Fecha Corte - Mes </td>
         <td>
           <?php

           setlocale(LC_TIME, 'es_ES.UTF-8'); // Asegúrate de que la localidad 'es_ES' esté instalada en tu servidor

           // Crear un objeto DateTime
           $fechaDateTime = new DateTime($fecha_corte);

           // Utilizar strftime() para formatear la fecha en español
           $mesNombre = strftime('%B', $fechaDateTime->getTimestamp()); // '%B' retorna el nombre completo del mes

           // Mostrar el nombre del mes en español
           echo "$fecha_corte - $mesNombre;"

            ?>
        </td>
       </tr>
       <tr>
         <td class="negrita">Cargo</td>
         <td><?php echo $data_usuaio['cargo'] ?></td>
       </tr>
     </table>
   </div>

   <div class="contenedor_tabla_resumen">
     <div class="tabla_ingresos">
       <h5 style="padding: 0px;margin: 0px;">Tabla de Ingresos</h5>
       <table>
         <tr>
           <td>Sueldo</td>
            <td>$<?php echo $data_usuaio['salario'] ?></td>
         </tr>
       </table>

     </div>

     <style media="screen">
       .tabla_egresos td{
         width: 33%;

       }
     </style>

     <div class="tabla_egresos">
       <h5 style="padding: 0px;margin: 0px;">Tabla de Avances</h5>
       <table>
         <thead>
           <tr>
             <th class="th_productos">Desripción</th>
             <th class="th_productos">Monto</th>
             <th class="th_productos">Fecha</th>

           </tr>
         </thead>
         <tbody>

           <?php

           // Convertir la fecha de corte a formato de fecha de PHP
           $fechaCorteDateTime = new DateTime($fecha_corte);

           // Clonar y modificar para obtener la fecha de inicio (un mes antes)
           $fechaInicioDateTime = clone $fechaCorteDateTime;
           $fechaInicioDateTime->modify('-1 month');

           // Convertir las fechas de DateTime a formato de cadena Y-m-d para usar en SQL
           $fecha_inicio = $fechaInicioDateTime->format('Y-m-d');
           $fecha_corte = $fechaCorteDateTime->format('Y-m-d');

           mysqli_query($conection,"SET lc_time_names = 'es_ES'");
           $query_lista = mysqli_query($conection, "SELECT DATE_FORMAT(adelantos_recursos_humanos.fecha, '%W  %d de %b %Y %h:%m:%s') as 'fecha',
           adelantos_recursos_humanos.descripcion,adelantos_recursos_humanos.cantidad
           FROM adelantos_recursos_humanos
           WHERE adelantos_recursos_humanos.iduser ='$iduser'  AND adelantos_recursos_humanos.estatus = '1' AND adelantos_recursos_humanos.codigo_usuario = '$id_recurso_humano'
           AND adelantos_recursos_humanos.fecha BETWEEN '$fecha_inicio' AND '$fecha_corte'
           ORDER BY `adelantos_recursos_humanos`.`fecha` DESC ");

           $result_lista= mysqli_num_rows($query_lista);
           if ($result_lista > 0) {
             while ($data_lista=mysqli_fetch_array($query_lista)) {
               ?>
               <tr>
                 <td><?php echo $data_lista['descripcion'] ?></td>
                 <td>$<?php echo number_format($data_lista['cantidad'],2) ?></td>
                 <td><?php echo mb_strtoupper($data_lista['fecha']) ?></td>
               </tr>
               <?php
             }
           }else {
             echo '
             <tr>
               <td>-</td>
               <td>$0.00</td>
               <td>-</td>
             </tr>
             ';
           }
           ?>
         </tbody>
       </table>
     </div>




     <div class="tabla_egresos">
       <h5 style="padding: 0px;margin: 0px;">Tabla de Multas</h5>
       <table>
         <thead>
           <tr>
             <th class="th_productos">Desripción</th>
             <th class="th_productos">Monto</th>
             <th class="th_productos">Fecha</th>

           </tr>
         </thead>
         <tbody>
           <?php

           // Convertir la fecha de corte a formato de fecha de PHP
             $fechaCorteDateTime = new DateTime($fecha_corte);

             // Clonar y modificar para obtener la fecha de inicio (un mes antes)
             $fechaInicioDateTime = clone $fechaCorteDateTime;
             $fechaInicioDateTime->modify('-1 month');

             // Convertir las fechas de DateTime a formato de cadena Y-m-d para usar en SQL
             $fecha_inicio = $fechaInicioDateTime->format('Y-m-d');
             $fecha_corte = $fechaCorteDateTime->format('Y-m-d');

             mysqli_query($conection,"SET lc_time_names = 'es_ES'");
             $query_multas = mysqli_query($conection, "SELECT DATE_FORMAT(multas_recursos_humanos.fecha, '%W  %d de %b %Y %h:%m:%s') as 'fecha',
              multas_recursos_humanos.descripcion,multas_recursos_humanos.cantidad  FROM multas_recursos_humanos
              WHERE multas_recursos_humanos.iduser ='$iduser'  AND multas_recursos_humanos.estatus = '1' AND multas_recursos_humanos.codigo_usuario = '$id_recurso_humano'
              AND multas_recursos_humanos.fecha BETWEEN '$fecha_inicio' AND '$fecha_corte'
              ORDER BY `multas_recursos_humanos`.`fecha` DESC ");

               $result_multas= mysqli_num_rows($query_multas);
             if ($result_multas > 0) {
                   while ($data_multas=mysqli_fetch_array($query_multas)) {
            ?>
           <tr>
             <td><?php echo $data_multas['descripcion'] ?></td>
             <td>$<?php echo number_format($data_multas['cantidad'],2) ?></td>
             <td><?php echo mb_strtoupper($data_multas['fecha']) ?></td>
           </tr>
           <?php
           }
          }else {
            echo '
            <tr>
              <td>-</td>
              <td>$0.00</td>
              <td>-</td>
            </tr>
            ';
          }
       ?>


         </tbody>

       </table>

     </div>

     <div class="tabla_egresos">
       <h5 style="padding: 0px;margin: 0px;">RESUMEN DE EGRESOS</h5>
       <table>
         <thead>
           <tr>
             <th class="th_productos">Descripción</th>
             <th class="th_productos">Monto</th>
           </tr>
         </thead>
         <tbody>
               <tr>
                 <td>PAGO DEL IEES </td>
                 <td>$<?php echo number_format($data_usuaio['debito_iees'],2) ?></td>
               </tr>
               <tr>
                 <td>ADELANTOS </td>
                 <td>$<?php echo number_format($data_usuaio['adelantos'],2) ?></td>
               </tr>
               <tr>
                 <td>MULTAS </td>
                 <td>$<?php echo number_format($data_usuaio['multas'],2) ?></td>
               </tr>

         </tbody>
       </table>
     </div>
   </div>



   <style media="screen">
     .parte_productos{
       padding: 1px;
       font-size: 11px;
     }

     .parte_productos .parte_productos_solo_productos{
       width: 100%;
       background: #e8e8e8 ;
       padding: 0px;
       margin: 0px;
       text-align: center;
     }

     .parte_productos .parte_productos_solo_productos {
       border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
   }

     .parte_productos .th_productos{
         border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
     }
     .parte_productos .td_productos{
         border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
     }
   </style>
   <br>

   <div class="parte_productos">
     <div class="">
       <table class="parte_productos_solo_productos">
         <thead>
           <tr>
             <th class="th_productos">Fecha Registro</th>
             <th class="th_productos">Acción</th>
             <th class="th_productos">Horas Trabajadas</th>

           </tr>
         </thead>
         <tbody>
           <?php

           $query_lista = mysqli_query($conection, "SELECT DATE_FORMAT(registro_acceso_recursos_humanos.fecha_registro, '%W  %d de %b %Y %h:%m:%s') as 'fecha_registro',
            registro_acceso_recursos_humanos.accion,registro_acceso_recursos_humanos.horas_trababajadas FROM registro_acceso_recursos_humanos
              WHERE registro_acceso_recursos_humanos.estatus = '1' AND registro_acceso_recursos_humanos.usuario_recursos_humanos = '$id_recurso_humano'
              AND registro_acceso_recursos_humanos.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_corte'
           ORDER BY `registro_acceso_recursos_humanos`.`id` DESC ");

           $result_lista= mysqli_num_rows($query_lista);
         if ($result_lista > 0) {
               while ($data_lista=mysqli_fetch_array($query_lista)) {

            ?>
           <tr>
             <td class="td_productos"><?php echo mb_strtoupper($data_lista['fecha_registro']) ?></td>
             <td class="td_productos"><?php echo $data_lista['accion'] ?></td>
             <td class="td_productos"><?php echo $data_lista['horas_trababajadas'] ?></td>
           </tr>

           <?php
           }
          }else {
            echo '
            <tr>
              <td class="td_productos">-</td>
              <td class="td_productos">-</td>
              <td class="td_productos">-</td>
            </tr>
            ';
          }
       ?>
         </tbody>



       </table>
     </div>
   </div>






   <style media="screen">
     .salario_recibir{
       text-align: center;
       font-weight: bold;
       font-size: 30px;
     }
   </style>

   <div class="salario_recibir">
     <h5>$<?php echo number_format($total_pago,2) ?></h5>

   </div>


  <style media="screen">
  .contenedor_resposables{
    text-align: center;
    font-size: 12px;
    display: block;
    align-items: center;  /* Alineación vertical */
  }
    .responsable{
      display: inline-block;
      padding: 10px;
      width: 50%;
    }
    .negrita{
      font-weight: bold;
    }

  </style>
   <div class="contenedor_resposables">
     <div class="responsable">
       <br><br>
       <p>_____________________________</p>
       <p><?php echo $data_usuaio['nombres_user'] ?> <?php echo $data_usuaio['apellidos_user'] ?>  </p>
       <p class="negrita">Pagador</p>

     </div>
     <div class="responsable">
       <p>_____________________________</p>
       <p><?php echo $data_usuaio['nombres_recursos_humanos'] ?> </p>
       <p class="negrita">Recibi Conforme</p>

     </div>

   </div>



</body>
</html>
