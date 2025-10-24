<?php

include "../../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$ingreso = $_GET['ingreso'];



 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ingreso <?php echo $ingreso ?> </title>
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

    <?php

    mysqli_query($conection,"SET lc_time_names = 'es_ES'");
     $query_ingreso = mysqli_query($conection,"SELECT DATE_FORMAT(gym_descripcion_ingreso.fecha, '%W  %d de %b %Y %H:%i:%s') as 'fecha_f',
     gym_descripcion_ingreso.id,gym_descripcion_ingreso.fecha_inicio_plan,gym_descripcion_ingreso.usuario,gym_descripcion_ingreso.plan,
     gym_descripcion_ingreso.recurrencia_plan,gym_descripcion_ingreso.estado_financiero,
     clientes.nombres as 'nombre_cliente',clientes.foto as'imagen_cliente',
     gym_planes.id as 'codigo_plan',gym_planes.nombre_plan as 'nombre_plan',gym_planes.descripcion_plan,gym_planes.meses_plan,gym_planes.precio

   FROM `gym_descripcion_ingreso`
   INNER JOIN clientes ON clientes.id = gym_descripcion_ingreso.usuario
   INNER JOIN gym_planes  ON gym_planes.id = gym_descripcion_ingreso.plan
   WHERE  gym_descripcion_ingreso.estatus = '1'
   AND gym_descripcion_ingreso.id = '$ingreso' ");
   $data_ingreso=mysqli_fetch_array($query_ingreso);
   $imagen_cliente = $data_ingreso['imagen_cliente'];
   ?>
   <div class="cabezera">
     <br>
     <h2>PERFIL DEL INGRESO DE  <?php echo $data_ingreso['nombre_cliente']; ?> </h2>
   </div>

             <div class="card">
               <div class="row">
                 <div class="col">
                   <div class="img_alumno">
                     <img src="../../img/uploads/<?php echo $data_ingreso['imagen_cliente'] ?>" width="200px;" alt="<?php echo $data_ingreso['imagen'] ?>">
                   </div>

                 </div>
               </div>
               <div class="row contenedor_informacion_paciente">
                 <div class="col">
                   <table>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Código Ingreso: </td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['id']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Plan Contratado:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['nombre_plan']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Descripción Plan : </td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['descripcion_plan']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Tiempo de Servicio: </td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['meses_plan']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Recurrencia:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['recurrencia_plan']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Precio del Servicio:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['precio']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila">Estado Financiero: :</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['estado_financiero']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Fecha de Registro:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['fecha_f']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Nombres del Usuario:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_ingreso['nombre_cliente']; ?></td>
                     </tr>

                     <tr>
                       <?php
                       $fecha_actual = date("d-m-Y H:i:s");

                        ?>
                       <td class="negrita_fila primer_bloque_fila" >Fecha de Generación del Documento:</td>
                       <td class="segundo_bloque_fila"><?php echo $fecha_actual ?>  </td>
                     </tr>
                   </table>



                 </div>

               </div>
             </div>



  </body>
</html>
