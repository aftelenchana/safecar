<?php
ob_start();
include "../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');
session_start();

if (empty($_SESSION['active'])) {
    header('location:/');
} else {
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $domain = $_SERVER['HTTP_HOST'];

    $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
    $result_documentos = mysqli_fetch_array($query_doccumentos);

    if ($result_documentos) {
        $url_img_upload = $result_documentos['url_img_upload'];
        $img_facturacion = $result_documentos['img_facturacion'];
        $nombre_empresa = $result_documentos['nombre_empresa'];
        $img_sistema = $url_img_upload.'/home/img/uploads/'.$img_facturacion;
    } else {
        $img_sistema = '/img/guibis.png';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tablero</title>
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos/index.css">
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/css/style.css" />
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/pages/data-table/css/buttons.dataTables.min.css" />
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <link rel="stylesheet" href="estilos/tipos_vehiculos.css">
    <link rel="stylesheet" href="https://guibis.com/home/estiloshome/relog_digital.css">
</head>

<body>
  <?php require 'scripts/menu.php'; ?>

  <main class="content guibis-parqueadero" id="contentWrapper">
    <div class="row">
      <div class="col-sm-12">
        <div class="card tarjeta_bloque_hr">
          <div class="card-header-guibis">
            <h5>Tablero del Parqueadero</h5>
          </div>

          <div class="card-block">

            <div class="row">
              <div class="relog_digital">
                <div class="reloj">
                    <p class="fecha"></p>
                    <p class="tiempo"></p>
                </div>
              </div>
            </div>

            <button type="button" class="btn btn-primary boton_guibis_enviar"
                    id="boton_agregar_mesa" name="button">
              Agregar Espacios <i class="fas fa-plus"></i>
            </button>

            <div class="dt-responsive table-responsive">
              <table id="tabla_categorias" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Placa</th>
                    <th>Minutos Servicio</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha F.</th>
                    <th>Intervalos</th>
                    <th>Minutos T.</th>
                    <th>Precio C.</th>
                    <th>Acciones</th>

                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include "modals/tablero_parqueadero.php"; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
  <script src="https://guibis.com/home/files/assets/pages/data-table/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="https://guibis.com/home/files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

  <script src="java/index.js" charset="utf-8"></script>
  <script type="text/javascript" src="https://guibis.com/home/java/relog_digital.js"></script>
  <script src="modulos/parqueadero/tablero.js?v=9"></script>

  <script type="text/javascript">
      function openPdfViewer(fileName) {
          var url = "vizualizar_factura?archivo=" + encodeURIComponent(fileName);
          var width = 800;
          var height = 600;
          var left = (window.screen.width / 2) - (width / 2);
          var top = (window.screen.height / 2) - (height / 2);
          var windowFeatures = "resizable=yes, scrollbars=yes, status=yes, width=" + width + ", height=" + height + ", top=" + top + ", left=" + left;
          window.open(url, "popUp", windowFeatures);
      }
  </script>
</body>
</html>
