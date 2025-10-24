<?php

include "../../coneccion.php";
      mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$paciente = $_GET['paciente'];



 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Paciente <?php echo $paciente ?> </title>
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
     $query_paciente = mysqli_query($conection,"SELECT veterina_pacientes.id,veterina_pacientes.nombre_paciente,veterina_pacientes.imagen,
       DATE_FORMAT(veterina_pacientes.fecha, '%W  %d de %b %Y %H:%i:%s') as 'fecha_f',veterina_pacientes.nombre_representannte,
       veterina_razas.nombre_raza,
       veterina_pacientes.identificacion_paciente,veterina_pacientes.edad_meses,veterina_pacientes.nombre_representannte,
       veterina_pacientes.direccion_representante,veterina_pacientes.email_representante,veterina_pacientes.celular_representante,
       veterina_pacientes.ingreso,veterina_pacientes.qr,veterina_pacientes.descripcion_paciente,veterina_pacientes.identificacion_representante
       FROM `veterina_pacientes`
   INNER JOIN veterina_razas ON veterina_razas.id = veterina_pacientes.raza
   WHERE  veterina_pacientes.estatus = '1' AND veterina_pacientes.id = '$paciente'");
   $data_paciente=mysqli_fetch_array($query_paciente);
   ?>
   <div class="cabezera">
     <br>
     <h2>PERFIL DEL PACIENTE <?php echo $data_paciente['nombre_paciente']; ?> </h2>
   </div>

             <div class="card">
               <div class="row">
                 <div class="col">
                   <div class="img_alumno">
                     <img src="../img/uploads/<?php echo $data_paciente['imagen'] ?>" width="200px;" alt="<?php echo $data_paciente['imagen'] ?>">
                   </div>

                 </div>
               </div>
               <div class="row contenedor_informacion_paciente">
                 <div class="col">
                   <table>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Código Interno: </td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['id']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Paciente:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['nombre_paciente']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Raza : </td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['nombre_raza']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Identidicación Externa: </td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['identificacion_paciente']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Edad (Meses):</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['edad_meses']; ?> Meses</td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Nombre del Representante:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['nombre_representannte']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Identificación del Representante:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['identificacion_representante']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila">Dirección :</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['direccion_representante']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Email:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['email_representante']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Celular:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['celular_representante']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Fecha de Registro:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['fecha_f']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Ingreso:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['ingreso']; ?></td>
                     </tr>
                     <tr>
                       <td class="negrita_fila primer_bloque_fila" >Descripción de Ingreso:</td>
                       <td class="segundo_bloque_fila"><?php echo $data_paciente['descripcion_paciente']; ?></td>
                     </tr>
                     <tr>
                       <?php
                       $fecha_actual = date("d-m-Y H:i:s");

                        ?>
                       <td class="negrita_fila primer_bloque_fila" >Fecha de Generación del Documento:</td>
                       <td class="segundo_bloque_fila"><?php echo $fecha_actual ?>  </td>
                     </tr>


                     <tr>
                       <td class="negrita_fila primer_bloque_fila">Codigo QR:</td>
                       <td class="segundo_bloque_fila"> <img src="../img/qr/<?php echo $data_paciente['qr']; ?>" width="100px;" alt=""> </td>
                     </tr>
                   </table>



                 </div>

               </div>
             </div>



  </body>
</html>
