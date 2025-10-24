<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../../coneccion.php";

mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar

$iduser   = $_GET['iduser'];
$empresa  = $_GET['empresa'];

$codigo_rol_pagos = $_GET['codigo'];

//SACAMOS INFORMACION DEL ROL DE PAGOS

$query_rol_pagos = mysqli_query($conection, "SELECT * FROM rol_pagos
    WHERE rol_pagos.iduser = '$iduser' AND rol_pagos.estatus = '1' AND rol_pagos.id = '$codigo_rol_pagos'");


$data_rol_pagos = mysqli_fetch_array($query_rol_pagos);

$recurso_humano = $data_rol_pagos['recurso_humano'];
$anio           = $data_rol_pagos['anio'];
$mes            = $data_rol_pagos['mes'];
$quincena       = $data_rol_pagos['quincena'];

if ($quincena == 1) {
  $intervalo_dias = 15;
}else {
  $intervalo_dias = 30;
}



function formatearFechas($anio, $mes, $quincena) {
// Formatear el mes para que siempre tenga dos dígitos
$mes_formateado = str_pad($mes, 2, '0', STR_PAD_LEFT);

// Crear las fechas en base al valor de quincena
if ($quincena == 1) {
  $fecha_inicio = "01/$mes_formateado/$anio";
  $fecha_fin = "15/$mes_formateado/$anio";
} elseif ($quincena == 2) {
  $fecha_inicio = "01/$mes_formateado/$anio";

  // Determinar si el mes tiene 30 o 31 días
  $ultimo_dia_mes = date("t", strtotime("$anio-$mes_formateado-01"));
  $fecha_fin = "$ultimo_dia_mes/$mes_formateado/$anio";
} else {
  // Si la quincena no es válida
  return "Error: Quincena no válida";
}

// Retornar un array con ambas fechas
return [
  'fecha_inicio' => $fecha_inicio,
  'fecha_fin' => $fecha_fin
];
}

   //INFORMACION DEL USUARIO

   //CODIGO PARA SACAR INFORMACION DEL USUARIO
   $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
   $result_documentos = mysqli_fetch_array($query_doccumentos);
   $email_empresa_emisor  = $result_documentos['email'];
   $celular_empresa_emisor  = $result_documentos['celular'];
   $telefono_empresa_emisor   = $result_documentos['telefono'];
   $nombre_empresa   = $result_documentos['nombre_empresa'];
   $regimen   = $result_documentos['regimen'];
   $contabilidad   = $result_documentos['contabilidad'];
   $url_img_upload   = $result_documentos['url_img_upload'];
   $img_facturacion   = $result_documentos['img_facturacion'];
   $id_e   = $result_documentos['id_e'];

   $facebook    = $result_documentos['facebook'];
   $instagram   = $result_documentos['instagram'];
   $whatsapp    = $result_documentos['whatsapp'];
   $pagina_web  = $result_documentos['pagina_web'];


   $query_empresa_cookie = mysqli_query($conection, "SELECT * FROM empresas_registradas
      WHERE   empresas_registradas.id = '$empresa' ");
   $data_empresa_cookie = mysqli_fetch_array($query_empresa_cookie);



   function fecha_espanol($fecha) {
    // Array de meses en español
    $meses = [
        1 => "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    // Array de días de la semana en español
    $dias_semana = [
        "Sunday" => "Domingo",
        "Monday" => "Lunes",
        "Tuesday" => "Martes",
        "Wednesday" => "Miércoles",
        "Thursday" => "Jueves",
        "Friday" => "Viernes",
        "Saturday" => "Sábado"
    ];

    // Obtener día de la semana, día, mes, año, y hora
    $dia_semana = $dias_semana[date("l", strtotime($fecha))]; // Día de la semana
    $dia = date("d", strtotime($fecha));                      // Día del mes
    $mes = date("n", strtotime($fecha));                      // Número del mes (1 al 12)
    $anio = date("Y", strtotime($fecha));                     // Año
    $hora = date("H:i", strtotime($fecha));                   // Hora en formato 24 horas

    // Formatear la fecha en español
    return $dia_semana . ", " . $dia . " de " . $meses[$mes] . " del " . $anio . " " . $hora;
}

$fecha_actual = date("Y-m-d H:i:s");
$fecha_impresion =  fecha_espanol($fecha_actual);

//CODIGO PARA SEPARA EL CODIGO UNICO


function obtenerNombreMes($mes) {
    $meses = [
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre',
    ];

    return $meses[$mes] ?? 'Mes inválido';
}

function obtenerRangoQuincena($anio, $mes, $quincena) {
    $nombre_mes = obtenerNombreMes($mes);

    if ($quincena == 1) {
        $fecha_inicio = "01/$mes/$anio";
        $fecha_fin = "15/$mes/$anio";
    } elseif ($quincena == 2) {
        $fecha_inicio = "1/$mes/$anio";
        $ultimo_dia = date("t", strtotime("$anio-$mes-01"));  // Obtiene el último día del mes
        $fecha_fin = "$ultimo_dia/$mes/$anio";
    } else {
        return "Quincena inválida";
    }

    return "$fecha_inicio - $fecha_fin";
}




$rango_fechas = obtenerRangoQuincena($anio, $mes, $quincena);

$mes_trabajado  = obtenerNombreMes($mes);

// Mostrar resultados
//echo "Año: $anio\n";
//echo "Mes: $mes\n";
//echo "Quincena: $quincena_texto\n";
//echo "Código de usuario: $codigo_usuario\n";
//exit;

$query_recurso_humano = mysqli_query($conection,"SELECT recursos_humanos.id,DATE_FORMAT(recursos_humanos.fecha, '%W  %d de %b %Y %H:%i:%s') as 'fecha_f',recursos_humanos.foto,recursos_humanos.nombres,recursos_humanos.identificacion,
recursos_humanos.celular,recursos_humanos.categoria_recursos_humanos
FROM `recursos_humanos`
WHERE  recursos_humanos.estatus = '1' AND recursos_humanos.id = '$recurso_humano'");

$data_recursos_humanos =mysqli_fetch_array($query_recurso_humano);

$categoria_recursos_humanos = $data_recursos_humanos['categoria_recursos_humanos'];

//CODIGO PARA SACAR LA PROVINCIA Y LA CIUDAD

    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';$domain = $_SERVER['HTTP_HOST'];$url = $protocol . $domain;
    function getRealIP(){
              if (isset($_SERVER["HTTP_CLIENT_IP"])){
                  return $_SERVER["HTTP_CLIENT_IP"];
              }elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                  return $_SERVER["HTTP_X_FORWARDED_FOR"];
              }elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
              {
                  return $_SERVER["HTTP_X_FORWARDED"];
              }elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
              {
                  return $_SERVER["HTTP_FORWARDED_FOR"];
              }elseif (isset($_SERVER["HTTP_FORWARDED"]))
              {
                  return $_SERVER["HTTP_FORWARDED"];
              }
              else{
                  return $_SERVER["REMOTE_ADDR"];
              }

          }
          if ($url =='http://localhost') {
            $direccion_ip =  '186.42.10.32';
          }else {
            $direccion_ip = (getRealIP());
          }

          $datos = unserialize(file_get_contents('http://ip-api.com/php/'.$direccion_ip.''));

           $pais            = $datos['country'];
           $ciudad            = $datos['city'];
           $provincia         = $datos['regionName'];



           //CATEGORIA RECURSOS HUMANOS

           $query_categoria_recursos_humanos =  mysqli_query($conection, "SELECT * FROM  categoria_recursos_humanos   WHERE  id  = '$categoria_recursos_humanos'");

           $existencia_categoria_recurso_humano = mysqli_num_rows($query_categoria_recursos_humanos);

           if ($existencia_categoria_recurso_humano > 0) {
             $data_categoria_recursos_humanos = mysqli_fetch_array($query_categoria_recursos_humanos);
             $nombre_categoria_recurso_humano = $data_categoria_recursos_humanos['nombre'];
             // code...
           }else {
             $nombre_categoria_recurso_humano = '';
           }



 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Previzualisar Rol de Pagos <?php echo $codigo ?> </title>
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
          <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_facturacion ?>" alt="">
      </div>
      <div class="bloque_gene informacion_titulos_cabezera">
        <h5><?php echo $data_empresa_cookie['razon_social']?></h5>
        <h5><?php echo $fecha_impresion ?></h5>
        <h5>Usuario: <?php echo $data_empresa_cookie['razon_social'] ?></h5>

      </div>
    </div>


    <hr style="border: 0.5px solid black; width: 100%;">


    <style media="screen">
      .contenedor_titu_asiento{
        text-align: center;
        font-weight: bold;
        font-size: 20px;
      }
    </style>
    <div class="contenedor_titu_asiento">
      <p>Rol de Pagos </p>
    </div>

    <style media="screen">
      .contenedor_titu_asiento{
        text-align: center;
      }
      .informacion_comprobante{
        font-size: 11px;
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

    <div class="informacion_comprobante">
      <div class="bloque_gene_comproban">
        <table>
          <tr>
            <td class="negrita">Persona</td>
            <td><?php echo $data_recursos_humanos['nombres'] ?></td>
          </tr>
          <tr>
            <td class="negrita">Cédula</td>
            <td> <?php echo $data_recursos_humanos['identificacion'] ?></td>
          </tr>
          <tr>
            <td class="negrita">Cargo</td>
            <td> <?php echo $nombre_categoria_recurso_humano ?> </td>
          </tr>
          <tr>
            <td class="negrita">Provincia</td>
            <td> <?php echo $provincia ?> </td>
          </tr>
        </table>
      </div>

      <div class="bloque_gene_comproban">
        <table>
          <tr>
            <td class="negrita">Periodo</td>
            <td><?php echo $rango_fechas ?> </td>
          </tr>
          <tr>
              <td class="negrita">Mes</td>
              <td> <?php echo $mes_trabajado ?> </td>
          </tr>
          <tr>
              <td class="negrita">Dias Trabajados</td>
              <td> <?php echo $intervalo_dias ?> </td>
          </tr>

          <tr>
              <td class="negrita">Ciudad</td>
              <td><?php echo $ciudad ?> </td>
          </tr>
        </table>
      </div>
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

    .subrayado-cursiva {
        text-decoration: underline; /* Subraya el texto */
        font-style: italic; /* Aplica cursiva al texto */
      }

      .subtitulo_detalle_rubros{
        margin-top: -60px;
      }

    </style>


    <div class="subtitulo_detalle_rubros">
      <h4 class="subrayado-cursiva" >Detalle Rubros</h4>

    </div>

    <style media="screen">
      .parte_productos{
        padding: 1px;
        font-size: 11px;
        border: 1px solid black; /* Establece el borde de la tabla y sus celdas */
        border-radius: 8px;
        text-align: center;
      }

      .parte_productos .parte_productos_solo_productos{
        width: 100%;
        padding: 0px;
        margin: 0px;
      }

      .parte_productos .parte_productos_solo_productos {
        border-collapse: collapse; /* Opcional: para eliminar el espacio entre bordes */
    }




    .negrita{
      font-weight: bold;
      font-size: 12px;
    }
    .contendedor_general_pagos_egresos_ingresos{
      text-align: center;
      vertical-align: top; /* Añadir alineación vertical */
    }

    .contendedor_general_pagos_egresos_ingresos table {
      width: 100px;
      text-align: center;
    }

    .contendedor_general_pagos_egresos_ingresos .ingresos{
      width: 48%;
      display: inline-block;
    }

    .contendedor_general_pagos_egresos_ingresos .egresos{
      width: 48%;
      display: inline-block;
    }

    .ingresos, .egresos {
      width: 48%; /* Ancho de cada columna */
      vertical-align: top; /* Alineación vertical */
      text-align: center; /* Alineación horizontal */
  }


    </style>






    <div class="contendedor_general_pagos_egresos_ingresos">

      <div class="ingresos">
        <div class="titulo_dentro_cuadro">
          <h5>Ingresos</h5>

        </div>
        <div class="parte_productos">
          <div class="">
            <table class="parte_productos_solo_productos">
              <thead>
                <tr>
                  <th class="th_productos texto_asiento">Parámetro</th>
                  <th class="th_productos texto_asiento">Tiempo</th>
                  <th class="th_productos texto_asiento">Concepto</th>
                  <th class="th_productos texto_asiento">Valor</th>
                </tr>
              </thead>
              <tbody>

                <?php

                $total_valores_ingresos = 0 ;


                $query_consulta_ingresos = mysqli_query($conection, "SELECT *
                   FROM otros_ingresos_recuros_humanos
                   WHERE otros_ingresos_recuros_humanos.estatus = '1'
                   AND otros_ingresos_recuros_humanos.rol_pagos = '$codigo_rol_pagos'
                   AND otros_ingresos_recuros_humanos.recurso_humano ='$recurso_humano' ");

             while ($data_ingresos = mysqli_fetch_assoc($query_consulta_ingresos)) {

                 $total_valores_ingresos += $data_ingresos['valor']; // Sumar el valor actual
                 if ($data_ingresos['deducible']) {
                   $concepto = 'Deducible';
                 }

                 if ($data_ingresos['aportacion']) {
                   $concepto = 'Aportación';
                 }

                 ?>


                <tr>
                  <td class="td_productos"><?php echo $data_ingresos['nombre'] ?></td>
                  <td class="td_productos"><?php echo $data_ingresos['tipo_tiempo'] ?></td>
                  <td class="td_productos"><?php echo $concepto ?></td>
                  <td class="td_productos">$<?php echo number_format($data_ingresos['valor'],2) ?></td>
                </tr>
                 <?php
                 }
              ?>

              <tr>
                <td class="td_productos"></td>
                <td class="td_productos"></td>
                <td class="td_productos negrita">TOTALES</td>
                <td class="td_productos valores_asiento negrita">
                    $<?php echo round($total_valores_ingresos,2) ?>
                </td>
              </tr>
        </tbody>
      </table>
    </div>
    </div>

      </div>

      <div class="egresos">
        <div class="titulo_dentro_cuadro">
          <h5>Egresos</h5>

        </div>
        <div class="parte_productos">
          <div class="">
            <table class="parte_productos_solo_productos">
              <thead>
                <tr>
                  <th class="th_productos texto_asiento">Parámetro</th>
                  <th class="th_productos texto_asiento">Tiempo</th>
                  <th class="th_productos texto_asiento">Memo</th>
                  <th class="th_productos texto_asiento">Valor</th>
                </tr>
              </thead>
              <tbody>

                <?php

                $total_valores_egresos_permanentes = 0 ;


                $query_consulta_egresos = mysqli_query($conection, "SELECT *
                   FROM otros_egresos_recuros_humanos
                  WHERE otros_egresos_recuros_humanos.recurso_humano = '$recurso_humano'
                    AND otros_egresos_recuros_humanos.estatus = '1'
                    AND otros_egresos_recuros_humanos.rol_pagos = '$codigo_rol_pagos'
              ORDER BY `otros_egresos_recuros_humanos`.`fecha` desc ");

             while ($data_lista_otros_egresos_permantes = mysqli_fetch_assoc($query_consulta_egresos)) {

                   $valor = $data_lista_otros_egresos_permantes['valor'];


                 if ($data_lista_otros_egresos_permantes['memo']) {
                   $memo = 'Si';
                 }else {
                   $memo = 'No';
                 }


                 if ($data_lista_otros_egresos_permantes['tipo'] == 'APORTE IESS') {
                 //  echo "es iees";
                   $total_valores_ingresos_deducibles = 0;

                   $query_sueldo = mysqli_query($conection," SELECT *
                      FROM otros_ingresos_recuros_humanos
                     WHERE otros_ingresos_recuros_humanos.recurso_humano = '$recurso_humano'
                       AND otros_ingresos_recuros_humanos.estatus = '1'
                       AND otros_ingresos_recuros_humanos.rol_pagos = '$codigo_rol_pagos'
                       AND otros_ingresos_recuros_humanos.aportacion = 'checked'");

                       $resultado_ingreso_sueldo = mysqli_num_rows($query_sueldo);

                       if ($resultado_ingreso_sueldo > 0) {
                           while ($data_sueldo=mysqli_fetch_array($query_sueldo)) {
                           $total_valores_ingresos_deducibles += $data_sueldo['valor']; // Sumar el valor actual
                         }

                         $valor = round($total_valores_ingresos_deducibles*9.45/100,2);
                       }

                   // code...
                 }else {
                   $valor = $data_lista_otros_egresos_permantes['valor'];
                   //echo "no es iees";
                   // code...
                 }

                 $total_valores_egresos_permanentes += $valor; // Sumar el valor actual

                 ?>
                <tr>
                  <td class="td_productos"><?php echo $data_lista_otros_egresos_permantes['nombre'] ?></td>
                  <td class="td_productos"><?php echo $data_lista_otros_egresos_permantes['tipo_tiempo'] ?></td>
                  <td class="td_productos"><?php echo $memo ?></td>
                  <td class="td_productos">$<?php echo number_format($valor,2) ?></td>
                </tr>
                 <?php
                 }
              ?>

              <tr>
                <td class="td_productos"></td>
                <td class="td_productos"></td>
                <td class="td_productos negrita">TOTALES</td>
                <td class="td_productos valores_asiento negrita">
                    $<?php echo round($total_valores_egresos_permanentes,2) ?>
                </td>
              </tr>
        </tbody>
      </table>
    </div>
    </div>

      </div>

    </div>
    <br>

    <style media="screen">
      .total_pagar_sueldo{
        padding: 0px;
        margin: 0px;
        float: right;
      }
    </style>

        <hr style="border: 0.5px solid black; width: 100%;">
          <h5 class="total_pagar_sueldo" >Total a Recibir: $<?php echo round($total_valores_ingresos - $total_valores_egresos_permanentes, 2); ?> </h5>
          <br>
        <hr style="border: 0.5px solid black; width: 100%;">


        <div class="subtitulo_forma_pago">
          <h4 class="subrayado-cursiva" >Forma de Pago: Previzualización</h4>

        </div>

        <div class="informacion_comprobante">
          <div class="bloque_gene_comproban">
            <table>
              <tr>
                <td class="negrita">Monto</td>
                <td>$<?php echo round($total_valores_ingresos - $total_valores_egresos_permanentes, 2); ?> </td>
              </tr>

            </table>
          </div>


        </div>


        <style media="screen">
        .informacion_firmado{
          font-size: 13px;
          text-align: center;
        }

        .negrita{
          font-weight: bold;
        }

        .bloque_gene_firmado{
          width: 49%;
          display: inline-block;
          vertical-align: top; /* Añadir alineación vertical */
        }

        /* Asegúrate de que las tablas llenen el espacio del bloque contenedor */
        .bloque_gene_firmado table {
          text-align: center;
          width: 100%;
        }

        </style>

      <br><br>

        <div class="informacion_firmado">
          <div class="bloque_gene_firmado">
            <table>
              <tr>
                <td class="negrita">_____________________________________</td>
              </tr>
              <tr>
                <td class="negrita">Empleador</td>
              </tr>
            </table>
          </div>



          <div class="bloque_gene_firmado">
            <table>
              <tr>
                <td class="negrita">_____________________________________</td>
              </tr>
              <tr>
                <td class="negrita">Empleado</td>
              </tr>
            </table>
          </div>
        </div>





  </body>
</html>
