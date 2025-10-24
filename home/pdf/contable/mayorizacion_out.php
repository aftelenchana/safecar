<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_final = $_GET['fecha_final'];
$codigo_plan_cuentas = $_GET['codigo_plan_cuentas'];
$empresa = $_GET['empresa'];
$iduser  = $_GET['iduser'];

   //INFORMACION DEL USUARIO

   //CODIGO PARA SACAR INFORMACION DEL USUARIO
   $query_empresa_cookie = mysqli_query($conection, "SELECT * FROM empresas_registradas
      WHERE   empresas_registradas.id = '$empresa' ");
   $data_empresa_cookie = mysqli_fetch_array($query_empresa_cookie);



   //INFORMACION DE LA MAYORIZACION
   mysqli_query($conection,"SET lc_time_names = 'es_ES'");
    $query_consulta_datos = mysqli_query($conection, "SELECT contabilidad_datos_asiento_contable.id, contabilidad_plan_cuentas.nombre_cuenta,
        contabilidad_plan_cuentas.nivel_1, contabilidad_plan_cuentas.nivel_2, contabilidad_plan_cuentas.nivel_3, contabilidad_plan_cuentas.nivel_4,
        contabilidad_plan_cuentas.nivel_5, contabilidad_plan_cuentas.nivel_6, contabilidad_plan_cuentas.codigo, contabilidad_datos_asiento_contable.concepto,
        contabilidad_datos_asiento_contable.debito, contabilidad_datos_asiento_contable.credito, contabilidad_plan_cuentas.id as 'identificador_plan_cuentas',
        DATE_FORMAT(contabilidad_datos_asiento_contable.fecha, '%W  %d de %b %Y %h:%m:%s') as 'fecha'
        FROM contabilidad_datos_asiento_contable
        INNER JOIN contabilidad_plan_cuentas ON contabilidad_plan_cuentas.id = contabilidad_datos_asiento_contable.codigo
        WHERE contabilidad_datos_asiento_contable.iduser ='$iduser'
        AND contabilidad_datos_asiento_contable.estatus = '1'
        AND contabilidad_plan_cuentas.id = '$codigo_plan_cuentas'
        ORDER BY contabilidad_datos_asiento_contable.fecha DESC");

        $data_lista=mysqli_fetch_array($query_consulta_datos);


        $query_consulta = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
           WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
           AND contabilidad_plan_cuentas.id = '$codigo_plan_cuentas'
           ORDER BY nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6 ");
                  $data = array();
               while ($row = mysqli_fetch_assoc($query_consulta)) {
                   $data[] = $row;
               }
        foreach ($data as $row) {
              $jerarquia = '';
              for ($i = 1; $i <= 6; $i++) {
                  if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                      $jerarquia .= $row["nivel_{$i}"];
                      if ($i < 6 && $row["nivel_" . ($i + 1)] && $row["nivel_" . ($i + 1)] !== '0') {
                          $jerarquia .= '.';
                      }
                  }
              }
              // Asumo que 'id' es el identificador único para usar como valor del option.
              // Ajusta esta parte si deseas usar otro campo como el valor del option.
              $opcionValor = $row['id'];
              $nombre_cuenta = $row['nombre_cuenta'];
              // Usamos la jerarquía construida como el texto del option.
              $opcionTexto = $jerarquia;

              $dtlle_plan_cuentas =  "$opcionTexto - $nombre_cuenta";
          }


   $fecha_actual = date("d-m-Y H:i:s");



 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Mayorización  <?php echo $dtlle_plan_cuentas ?> de <?php echo $data_empresa_cookie['nombre_empresa'] ?></title>
  </head>
  <body>
    <style media="screen">

    .cabezera{
      width: 1125PX;
      height: 100px;
      margin-top: -50PX;
    }
  .cabezera h4{
    margin: auto;
    padding: 15px;

  }

  .cabezera img {
    width: 100px;
  }

.bloque_gene{
  display: inline-block;
  width: 500px;
}
.informacion_titulos_cabezera{
  margin-top: 10px;
}

.informacion_titulos_cabezera h5{
  padding: 0px;
  margin: 0px;
  color: #000;
  font-weight: bold;
}
    </style>


    <div class="cabezera">
      <br>
      <div class="bloque_gene">
        <br>
          <img src="<?php echo $data_empresa_cookie['url'] ?>/home/img/uploads/<?php echo $data_empresa_cookie['img_empresa'] ?>" alt="">
      </div>
      <div class="bloque_gene informacion_titulos_cabezera">
        <h5><?php echo $data_empresa_cookie['nombre_empresa']?> </h5>
        <h5><?php echo $fecha_actual ?></h5>

      </div>
    </div>

    <hr>
    <style media="screen">
      .contenedor_titu_asiento{
        text-align: center;
      }
    </style>
    <div class="contenedor_titu_asiento">
      <p>Mayorización de la cuenta <?php echo $dtlle_plan_cuentas ?>  </p>
      <p>Fecha Inicio: <?php echo $fecha_inicio ?> - <?php echo $fecha_final ?></p>
    </div>

   <style media="screen">
   .informacion_comprobante{
     font-size: 13px;
   }

   .negrita{
     font-weight: bold;
   }

   .bloque_gene_comproban{
     width: 50%;
     display: inline-block;
     vertical-align: top; /* Añadir alineación vertical */
   }

   /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
   .bloque_gene_comproban table {
     width: 100%;
   }

   </style>



    <style media="screen">
    .subtitulo{
      font-size: 13px;
      padding: 0px;
      margin: 0px;
    }

    </style>

    <style media="screen">
    .table-responsive tr,td,th  {
    border: solid 1px #c1c1c1;
    text-align: center;

    }

    .table-responsive .columna_grande_tabla{
    width: 190px;
    font-size: 11px;
    }

    .table-responsive .columna_chica_tabla{
    width: 90px;
    font-size: 11px;
    }


    .table-responsive table{
       border-collapse: collapse;
       margin: 0 auto;
           font-size: 11px;
    }
    </style>




<?php

     $result_consulta_datos= mysqli_num_rows($query_consulta_datos);
     if ($result_consulta_datos>0) {


                               $datos_estraidos = '';

                               $datos_credito = '' ;
                               $datos_debito  = '' ;
                               $suma_credito_grupo = 0;
                               $suma_debito_grupo = 0;

                               while ($data_lista=mysqli_fetch_array($query_consulta_datos)) {

                                $suma_credito_grupo += $data_lista['credito'];
                                $suma_debito_grupo += $data_lista['debito'];


                              $identificador_plan_cuentas = $data_lista['identificador_plan_cuentas'];

                                 $query_consulta = mysqli_query($conection, "SELECT * FROM contabilidad_plan_cuentas
                                    WHERE contabilidad_plan_cuentas.iduser ='$iduser' AND contabilidad_plan_cuentas.estatus = '1'
                                    AND contabilidad_plan_cuentas.id = '$identificador_plan_cuentas'
                                    ORDER BY nivel_1, nivel_2, nivel_3, nivel_4, nivel_5, nivel_6 ");
                                           $data = array();
                                        while ($row = mysqli_fetch_assoc($query_consulta)) {
                                            $data[] = $row;
                                        }
                                 foreach ($data as $row) {
                                       $jerarquia = '';
                                       for ($i = 1; $i <= 6; $i++) {
                                           if ($row["nivel_{$i}"] && $row["nivel_{$i}"] !== '0') {
                                               $jerarquia .= $row["nivel_{$i}"];
                                               if ($i < 6 && $row["nivel_" . ($i + 1)] && $row["nivel_" . ($i + 1)] !== '0') {
                                                   $jerarquia .= '.';
                                               }
                                           }
                                       }
                                       // Asumo que 'id' es el identificador único para usar como valor del option.
                                       // Ajusta esta parte si deseas usar otro campo como el valor del option.
                                       $opcionValor = $row['id'];
                                       $nombre_cuenta = $row['nombre_cuenta'];
                                       // Usamos la jerarquía construida como el texto del option.
                                       $opcionTexto = $jerarquia;

                                       $dtlle_plan_cuentas =  "$opcionTexto - $nombre_cuenta";
                                   }

                               $datos_estraidos =  '

                                                         <tr>
                                                           <td> '.$data_lista['concepto'].'</td>
                                                           <td> '.mb_strtoupper($data_lista['fecha']).'</td>
                                                           <td> $'.number_format($data_lista['debito'],2).'</td>
                                                           <td> $'.number_format($data_lista['credito'],2).'</td>
                                                         </tr>
                               '.$datos_estraidos;
                               }

                               $cabezera =  '        <div class="row">
                                           <div class="col-12">
                                              <h5> Mayorización de '.$dtlle_plan_cuentas.' </h5>
                                               <div class="table-responsive">
                                                   <table class="table table-bordered">
                                                       <thead>
                                                           <tr>
                                                               <th class="columna_grande_tabla">Concepto</th>
                                                               <th class="columna_grande_tabla">Fecha</th>
                                                               <th class="columna_chica_tabla">Débito</th>
                                                               <th class="columna_chica_tabla">Crédito</th>
                                                           </tr>
                                                       </thead>
                                                       <tbody>';

                               $pie =  '
                               </tbody>
                             </table>
                            </div>
                          </div>
                        </div>

                        ';

                        $datos_generales =$cabezera.$datos_estraidos.$pie;


                        //INFORMACION PARA LA TABLA DE RESUMEN
                        $cabezera_resumen =  '        <div class="row">
                                    <div class="col-12">
                                       <h5>Tabla de Resúmen de '.$dtlle_plan_cuentas.' </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="columna_grande_tabla">Suma Débitos</th>
                                                        <th class="columna_grande_tabla">Suma Créditos</th>
                                                        <th class="columna_grande_tabla">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $cuerpo_resumen =  '

                                                                          <tr>
                                                                            <td> $'.number_format($suma_credito_grupo,2).'</td>
                                                                            <td> $'.number_format($suma_debito_grupo,2).'</td>
                                                                            <td> $'.number_format(($suma_debito_grupo-$suma_credito_grupo),2).'</td>
                                                                          </tr>';
                                                                          $pie_resumen =  '
                                                                          </tbody>
                                                                        </table>
                                                                       </div>
                                                                     </div>
                                                                   </div>

                                                                   ';
                      $datos_resumen =  $cabezera_resumen.$cuerpo_resumen.$pie_resumen;


                      echo "$datos_generales";

                      echo "$datos_resumen";

       // code...
     }

 ?>









  </body>
</html>
