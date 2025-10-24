<?php
include ('../facturacion/facturacionphp/lib/codigo_barras/barcode.inc.php');
include "../../coneccion.php";
$iduser = $_GET['id'];
$query_resultados_emmisor = mysqli_query($conection,"SELECT * FROM comprobantes
WHERE id_emisor= '$iduser'");
$data__emmisor=mysqli_fetch_array($query_resultados_emmisor);
  $celular_receptor_uwu        = $data__emmisor['celular_receptor'];
  $direccion_receptor_uwu      = $data__emmisor['direccion_reeptor'];
  $email_receptor_uwu          = $data__emmisor['email_reeptor'];
  $codigo_formas_pago          = $data__emmisor['formas_pago'];
  $nombre_producto             = $data__emmisor['nombre_producto'];





  if ($codigo_formas_pago == 01) {
    $nombre_formas_pago = 'SIN UTILIZACION DEL SISTEMA FINANCIERO';
  }

  if ($codigo_formas_pago == 15) {
    $nombre_formas_pago = 'COMPESACION DE DE DEUDAS';
  }

  if ($codigo_formas_pago == 16) {
    $nombre_formas_pago = 'TARJETA DE DEBITO';
  }

  if ($codigo_formas_pago == 17) {
    $nombre_formas_pago = 'DINERO ELECTRONICO';
  }

  if ($codigo_formas_pago == 18) {
    $nombre_formas_pago = 'TARJETA PREPAGO';
  }

  if ($codigo_formas_pago == 19) {
    $nombre_formas_pago = 'TARJETA DE CREDITO';
  }

  if ($codigo_formas_pago == 20) {
    $nombre_formas_pago = 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO';
  }

  if ($codigo_formas_pago == 21) {
    $nombre_formas_pago = 'ENDOSO DE TITULOS';
  }



$query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  id  = '$iduser'");
$result_documentos = mysqli_fetch_array($query_doccumentos);
$regimen = $result_documentos['regimen'];
$contabilidad          = $result_documentos['contabilidad'];
$email_empresa_uwu     = $result_documentos['email'];
$celular_empresa_uwu   = $result_documentos['celular'];
$telefono_empresa_uwu  = $result_documentos['telefono'];
$whatsapp                    = $result_documentos['whatsapp'];
$contribuyente_especial      = $result_documentos['contribuyente_especial'];

 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>FActura</title>
    <link rel="stylesheet" href="../estiloshome/factura.css?v=6">
  </head>
  <style media="screen">

.img_logo_empresa{
  text-align: center;
}

  .img_logo_empresa img{
    width: 100px;
  }
  .parte_superior{
    padding: 2px;
    margin: 2px;
    background:  #ff7474 ;
    height: 250px;
  }
  .td_bld{
    font-weight: bold;
  }
  .informacion_emisor th,td{
    padding: 0;
    margin: 0;
  }

  .informacion_emisor{
    padding: 10px;
    margin-bottom: 30px;
    margin-right: 5px;
    display: inline-block;
    width: 300px;
    background:  #9ff3e5 ;
  }
  .informacion_factura{
    padding: 10px;
    margin-bottom: 30px;
    width: 350px;
    display: inline-block;
    background:  #f39fd3
  }
  .informacion_factura table{
    margin: 0 auto;
  }


  .numero_autorzaxion{
    font-size: 11px;
  }
  .informacion_ghd{
    display: inline-block;
  }

  .informacion_financiero_bancario{
    display: inline-block;
  }


  </style>


  <body>
    <div class="parte_superior">
      <div class="bloque_superior_row informacion_emisor">
        <div class="img_logo_empresa">
          <img src="../img/reacciones/guibis.png" alt="">
        </div>
        <table>
          <tbody>
            <tr>
              <td class="td_bld">Emisor:</td>
              <td>FARMACEUTICA AMERICA DELLY</td>
            </tr>
            <tr>
              <td class="td_bld">Matriz:</td>
              <td>Panamericana norte kilometro 7 1/2</td>
            </tr>
            <tr>
              <td class="td_bld">Ruc:</td>
              <td>1804843900001</td>
            </tr>
            <tr>
              <td class="td_bld">Correo:</td>
              <td>alejiss401997@gmail.com</td>
            </tr>
            <tr>
              <td class="td_bld">Teléfono</td>
              <td>0960055956</td>
            </tr>
          </tbody>
        </table>

      </div>

      <?php
      $clave_acc_guardar = '2302202301171685757600120010010000003601234567813';
			new barCodeGenrator(''.$clave_acc_guardar.'', 1, 'barra.gif', 455, 60, false);
       ?>

      <div class="bloque_superior_row informacion_factura">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="td_bld">Factura No :001-001-000000360</td>
              </tr>
              <tr>
                <td class="td_bld">Número de Autorización</td>
              </tr>
              <tr>
                <td class="numero_autorzaxion">2302202301171685757600120010010000003601234567813</td>
              </tr>
              <tr>
                <td class="td_bld">Fecha Autorización</td>
              </tr>
              <tr>
                <td>23-02-2023</td>
              </tr>
              <tr>
                <td class="td_bld">Ambiente:PRODUCCIÓN</td>
              </tr>
              <tr>
                <td class="td_bld">EMISIÓN :NORMAL</td>
              </tr>
              <tr>
                <td class="td_bld">CLAVE ACCESO</td>
              </tr>
              <tr>
                <td> <img src="barra.gif" width="350px;" height="75px;" alt=""> </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <style media="screen">
    .parte_inermedia{
      background:  #74fffd ;
      padding: 2px;
    }
      .parte_inermedia table{
        padding: 0;
        margin: 0;
        background:  #f3ce9f ;
        width: 100%;

      }
    </style>


    <div class="parte_inermedia">
      <table>
        <tbody>
          <tr>
            <td> <span class="td_bld" >Razon Social del Comprador</span> :ALEX FERNADO TELENCHANA</td>
          </tr>
          <tr>
            <td> <span class="td_bld" >RUC/CI:</span> 1804843900</td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Dirección:</span> AMBATO , MARIANITAS DE JESUS Y VARGAS TORRES</td>
          </tr>
          <tr>
            <td> <span class="td_bld" >Teléfono:</span> 0960055956</td>
          </tr>
        </tbody>
      </table>
    </div>

<style media="screen">
  .parte_productos{
    background:   #7483ff ;
    padding: 1px;
  }

  .parte_productos table{
    width: 100%;
    background: #e8e8e8 ;
    padding: 0px;
    margin: 0px;
    text-align: center;

  }
</style>

    <div class="parte_productos">
      <div class="">
        <table>
          <thead>
            <tr>
              <th>Codigo</th>
              <th>Cantidad</th>
              <th>Descripcion</th>
              <th>P/U</th>
              <th>DSCT</th>
              <th>IVA</th>
              <th>Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>5896</td>
              <td>6</td>
              <td>este es una prueba de asxioed diwnedwi</td>
              <td>3</td>
              <td>$3</td>
              <td>$3</td>
              <td>$3</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <br><br>
    <style media="screen">
    .parte_inferior_informacion .informacion_ghd{
      width: 65%;
    }
    .parte_inferior_informacion .informacion_ghd table{
      width: 450px;
      background:   #e8e8e8  ;
      border-radius: 5px;
      padding: 10px;
    }
    .parte_inferior_informacion{
      width: 99%;
      margin: 0 auto;
    }
    .informacion_financiero_bancario{
      width: 200px;
      border-radius: 5px;
      padding: 5px;
      margin: 5px;
    }
    .informacion_financiero_bancario table{
      width: 190px;
      text-align: center;
      margin: 0 auto;
      background:  #e8e8e8 ;
      padding: 5px;
      border-radius: 5px;
    }

    </style>

    <div class="parte_inferior_informacion">
      <div class="informacion_ghd">
        <table>
          <tbody>
            <tr>
              <td> <span class="td_bld" >Email Empresa: </span> <span>alejiss401997@gmail.com</span> </td>
              <td></td>
            </tr>
            <tr>
              <td> <span class="td_bld" >Email Cliente:</span> <span>alejiss401997@gmail.com</span> </td>
            </tr>
            <tr>
              <td> <span class="td_bld" >Teléfono Empresa:</span>  <span>0324365149</span> </td>
            </tr>
            <tr>
              <td> <span class="td_bld" >Direccion Cliente: </span> <span>AMBATO , MARIANITAS DE JESUS VARGAS TORRES</span> </td>
            </tr>
            <tr>
              <td> <span class="td_bld" >Formas de Pago:</span> <span>SIN UTILIZACION DEL SISTEMAFINANCIERO</span> </td>
            </tr>

          </tbody>
        </table>

      </div>

      <div class="informacion_financiero_bancario">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="td_bld" >Subtotal</td>
                <td>$55.36</td>
              </tr>
              <tr>
                <td class="td_bld" >IVA 0</td>
                <td>$25</td>
              </tr>
              <tr>
                <td class="td_bld" >IVA 12</td>
                <td>$58</td>
              </tr>
              <tr>
                <td class="td_bld" >IVA</td>
                <td>$3.66</td>
              </tr>
              <tr>
                <td class="td_bld" >ICE</td>
                <td>$52.36</td>
              </tr>
              <tr>
                <td class="td_bld" >IRBPNR</td>
                <td>$2.36</td>
              </tr>
              <tr>
                <td class="td_bld" >Descuento</td>
                <td>$0.23</td>
              </tr>
              <tr>
                <td class="td_bld" >Total:</td>
                <td>23.63</td>
              </tr>
            </tbody>
          </table>

        </div>

      </div>

    </div>


  </body>
</html>
