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
  <title>Tipos de Vehículos</title>
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
</head>

<body>
  <?php require 'scripts/menu.php'; ?>

  <main class="content guibis-parqueadero" id="contentWrapper">
    <div class="row">
      <div class="col-sm-12">
        <div class="card tarjeta_bloque_hr">
          <div class="card-header-guibis">
            <h5>Búsqueda Espacios</h5>
          </div>

          <div class="card-block">
            <form action="" id="procesar_datos" autocomplete="off">
              <div class="row g-3">
                <div class="col-12 col-md-3">
                  <div class="form-group d-flex align-items-center">
                    <label class="label-guibis-sm">Filtro</label>
                    <input type="text" name="filtro" class="form-control input-guibis-sm"
                           required id="filtro" placeholder="Filtro" oninput="cargarTabla()" />
                  </div>
                </div>

                <div class="col-12 col-md-3">
                  <div class="form-group d-flex align-items-center">
                    <label class="label-guibis-sm">Rol:</label>
                    <select class="form-control input-guibis-sm" name="categoria_recursos_humanos"
                            id="categoria_recursos_humanos" onchange="cargarTabla()">
                      <option value="Todos">Todos</option>
                    </select>
                  </div>
                </div>

                <div class="col-12 col-md-3">
                  <div class="form-group d-flex align-items-center">
                    <label class="label-guibis-sm">Parametros Extras:</label>
                    <select class="form-control input-guibis-sm" name="tipo_cliente" required
                            id="tipo_cliente" onchange="cargarTabla()">
                      <option value="Todos">Todos</option>
                      <option value="Natural">Natural</option>
                      <option value="Júridico">Júridico</option>
                      <option value="Sin RUC/Cl">Sin RUC/Cl</option>
                    </select>
                  </div>
                </div>

                <div class="col-12 col-md-3">
                  <div class="form-group d-flex align-items-center">
                    <label class="label-guibis-sm">Estado:</label>
                    <select class="form-control input-guibis-sm" name="estado" required
                            id="estado" onchange="cargarTabla()">
                      <option value="Todos">Todos</option>
                      <option value="Activo">Activo</option>
                      <option value="Inactivo">Inactivo</option>
                    </select>
                  </div>
                </div>
              </div>

              <input type="hidden" name="action" value="consultar_datos" />
              <input type="hidden" name="codigo_recurso_humano" id="codigo_recurso_humano" value="" />
            </form>

            <button type="button" class="btn btn-primary boton_guibis_enviar"
                    id="boton_agregar_mesa" name="button">
              Agregar Espacios <i class="fas fa-plus"></i>
            </button>

            <div class="dt-responsive table-responsive">
              <table id="tabla_categorias" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Acciones</th>
                    <th>Nombre</th>
                    <th>fecha</th>

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

  <?php include "modals/tipos_vehiculos.php"; ?>

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
  <script src="modulos/parqueadero/tipo_vehiculo.js?v=4"></script>
</body>
</html>
