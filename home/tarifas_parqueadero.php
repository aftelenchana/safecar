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
  <title>Tarfas Parqueadero</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="estilos/index.css">
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/css/style.css" />
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/pages/data-table/css/buttons.dataTables.min.css" />
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">

  <style>
  /* =========================================================
     GUISBIS PARQUEADERO - THEME (ENCAPSULADO)
     No tocamos el <body>. Solo dentro de .guibis-parqueadero
     ========================================================= */

  :root {
    /* Paleta */
    --gp-primary: #259bd6;
    --gp-primary-600: #1b7fb1;
    --gp-primary-700: #16749f;
    --gp-accent: #00d4ff;
    --gp-bg: #ffffff;
    --gp-bg-soft: #f6f9fb;
    --gp-text: #25323a;
    --gp-muted: #6c7a86;
    --gp-border: #d8e1e8;
    --gp-shadow: 0 6px 20px rgba(0,0,0,.08);

    /* Dimensiones / radios */
    --gp-radius: 12px;
    --gp-radius-sm: 10px;
    --gp-pad: 16px;
    --gp-gap: 12px;

    /* Tipografía */
    --gp-h: 700;
    --gp-sb: 600;
    --gp-r: 500;
    --gp-font: "Inter", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Ubuntu", sans-serif;
  }

  /* -------- scope -------- */
  .guibis-parqueadero * {
    font-family: var(--gp-font) !important;
    box-sizing: border-box !important;
  }

  /* Card principal */
  .guibis-parqueadero .tarjeta_bloque_hr {
    border: 1px solid var(--gp-border) !important;
    border-radius: var(--gp-radius) !important;
    background: var(--gp-bg) !important;
    box-shadow: var(--gp-shadow) !important;
    margin: 22px 10px !important;
  }

  /* Header card */
  .guibis-parqueadero .card-header-guibis {
    padding: 12px 16px !important;
    background: linear-gradient(90deg, var(--gp-primary) 0%, var(--gp-primary-600) 100%) !important;
    color: #fff !important;
    font-weight: var(--gp-h) !important;
    border-top-left-radius: var(--gp-radius) !important;
    border-top-right-radius: var(--gp-radius) !important;
  }
  .guibis-parqueadero .card-header-guibis h5 {
    margin: 0 !important;
    font-size: 18px !important;
    letter-spacing: .2px !important;
  }

  /* Contenido card */
  .guibis-parqueadero .card-block {
    padding: 18px var(--gp-pad) var(--gp-pad) var(--gp-pad) !important;
    background: linear-gradient(180deg, var(--gp-bg) 0%, var(--gp-bg-soft) 100%) !important;
    border-bottom-left-radius: var(--gp-radius) !important;
    border-bottom-right-radius: var(--gp-radius) !important;
  }

  /* Filtros */
  .guibis-parqueadero #procesar_datos .form-group {
    gap: 8px !important;
  }
  .guibis-parqueadero .label-guibis-sm {
    font-weight: var(--gp-sb) !important;
    color: var(--gp-text) !important;
    white-space: nowrap !important;
    margin: 0 6px 0 0 !important;
  }
  .guibis-parqueadero .input-guibis-sm,
  .guibis-parqueadero .form-control {
    border: 1px solid var(--gp-border) !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    box-shadow: none !important;
    outline: none !important;
    height: 38px !important;
    background: #fff !important;
  }
  .guibis-parqueadero .form-control:focus {
    border-color: var(--gp-primary) !important;
    box-shadow: 0 0 0 3px rgba(37,155,214,.15) !important;
  }

  /* Botón Agregar (centrado y grande) */
  .guibis-parqueadero #boton_agregar_mesa {
    display: block !important;
    margin: 18px auto 14px auto !important;
    padding: 12px 28px !important;
    background: linear-gradient(135deg, var(--gp-primary), var(--gp-primary-600)) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 12px !important;
    font-weight: var(--gp-sb) !important;
    font-size: 16px !important;
    letter-spacing: .2px !important;
    box-shadow: 0 8px 18px rgba(37,155,214,.25) !important;
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease !important;
  }
  .guibis-parqueadero #boton_agregar_mesa:hover {
    transform: translateY(-2px) !important;
    filter: brightness(1.02) !important;
    box-shadow: 0 10px 20px rgba(27,127,177,.28) !important;
  }
  .guibis-parqueadero #boton_agregar_mesa i {
    margin-left: 6px !important;
  }

  /* DataTable — ocultar botones default que no quieres */
  .guibis-parqueadero .dt-buttons { display: none !important; }
  .guibis-parqueadero #tabla_clientes_filter { display: none !important; }

  /* Tabla tipo “row-card” */
  .guibis-parqueadero .table {
    border-collapse: separate !important;
    border-spacing: 0 10px !important;
    background: transparent !important;
    margin-top: 6px !important;
  }
  .guibis-parqueadero .table thead th {
    background: #eef3f7 !important;
    color: var(--gp-text) !important;
    font-weight: var(--gp-sb) !important;
    border: none !important;
    text-transform: none !important;
    text-align: center !important;
    padding: 10px !important;
    border-radius: 10px !important;
  }
  .guibis-parqueadero .table tbody tr {
    background: #fff !important;
    border-radius: 12px !important;
    box-shadow: 0 2px 10px rgba(0,0,0,.06) !important;
  }
  .guibis-parqueadero .table tbody tr:hover {
    transform: scale(1.005) !important;
    box-shadow: 0 6px 16px rgba(0,0,0,.08) !important;
  }
  .guibis-parqueadero .table tbody td {
    border: none !important;
    text-align: center !important;
    vertical-align: middle !important;
    padding: 12px 10px !important;
  }

  /* Badge de estado dentro de la columna “fecha” si lo usas */
  .guibis-parqueadero .gp-badge {
    display: inline-block !important;
    padding: 4px 10px !important;
    border-radius: 999px !important;
    font-size: 12px !important;
    font-weight: var(--gp-sb) !important;
    line-height: 1 !important;
  }
  .guibis-parqueadero .gp-badge--ok {
    background: rgba(21, 192, 115, .12) !important;
    color: #15c073 !important;
    border: 1px solid rgba(21,192,115,.25) !important;
  }
  .guibis-parqueadero .gp-badge--warn {
    background: rgba(255, 193, 7, .12) !important;
    color: #b98900 !important;
    border: 1px solid rgba(255,193,7,.25) !important;
  }

  /* Paginación DataTable */
  .guibis-parqueadero .dataTables_paginate .pagination {
    gap: 6px !important;
  }
  .guibis-parqueadero .page-item .page-link {
    border-radius: 10px !important;
    border: 1px solid var(--gp-border) !important;
    color: var(--gp-text) !important;
  }
  .guibis-parqueadero .page-item.active .page-link {
    background: var(--gp-primary) !important;
    border-color: var(--gp-primary) !important;
    color: #fff !important;
    box-shadow: 0 6px 14px rgba(37,155,214,.3) !important;
  }

  /* Acciones (iconitos editar/eliminar si los usas) */
  .guibis-parqueadero .gp-actions .btn {
    padding: 6px 10px !important;
    border-radius: 8px !important;
    border: 1px solid var(--gp-border) !important;
    background: #fff !important;
  }
  .guibis-parqueadero .gp-actions .btn:hover {
    border-color: var(--gp-primary) !important;
    box-shadow: 0 0 0 3px rgba(37,155,214,.13) !important;
  }
  </style>
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
                    <th>Minutos</th>
                    <th>Valor</th>
                    <th>Tiempo Espera</th>
                    <th>Precio Sobrecargo</th>
                    <th>Fecha</th>
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

  <?php include "modals/tarifas_parqueadero.php"; ?>

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
  <script src="modulos/parqueadero/tarifas_parqueo.js?v=2"></script>
</body>
</html>
