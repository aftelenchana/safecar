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

    $query_doccumentos = mysqli_query($conection, "SELECT * FROM usuarios WHERE url_admin = '$domain'");
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
  <title>Cuentas Bancarias</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="estilos/empresas.css?v=2">
  <link rel="stylesheet" href="estilos/index.css">
</head>
<body>

<?php
if ($_SESSION['rol'] == 'cuenta_empresa') {
   require 'scripts/menu.php';
}
?>

<main class="content" id="contentWrapper">
  <div class="container-fluid py-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center table-header">
        <h2 class="mb-0"><i class="fas fa-piggy-bank me-2"></i> Gestión de Cuentas Bancarias</h2>
        <button class="btn btn-light btn-sm">
          <i class="fas fa-plus me-1"></i> Nueva Cuenta
        </button>
      </div>
      <div class="p-4">
        <table id="empresasTable" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>Banco</th>
              <th>Tipo Cuenta</th>
              <th>No. Cuenta</th>
              <th>Titular</th>
              <th>Moneda</th>
              <th>Saldo Inicial</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<!-- Modals -->
<div class="modal fade" id="modalAccionEmpresa" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEmpresaTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalEmpresaBody">
        <!-- contenido dinámico -->
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="java/index.js" charset="utf-8"></script>

<script>
$(document).ready(function() {
  const modal = new bootstrap.Modal(document.getElementById('modalAccionEmpresa'));

  const tabla = $('#empresasTable').DataTable({
    ajax: {
      url: 'php/cuentas_bancarias.php',
      type: 'POST',
      data: { action: 'consultar_datos_cuentas_bancarias' }
    },
    columns: [
      { data: 'nombreBanco' },
      { data: 'tipo_cuenta' },
      { data: 'numero_cuenta' },
      { data: 'titular_cuenta' },
      { data: 'moneda' },
      { data: 'saldo_inicial' },
      {
        data: 'estatus',
        render: function(data) {
          if (data == '1') return '<span class="badge bg-success">Activa</span>';
          if (data == '0') return '<span class="badge bg-secondary">Inactiva</span>';
          return '<span class="badge bg-warning text-dark">Pendiente</span>';
        }
      },
      {
        data: null,
        className: 'text-end',
        orderable: false,
        render: function (data, type, row) {
          return `
            <button class="btn btn-sm btn-primary me-1 btn-ver" data-id="${row.id}"><i class="fas fa-eye"></i></button>
            <button class="btn btn-sm btn-warning me-1 btn-editar" data-id="${row.id}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}"><i class="fas fa-trash"></i></button>
          `;
        }
      }
    ],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    }
  });

  $('#empresasTable tbody').on('click', '.btn-ver', function() {
    const id = $(this).data('id');
    $('#modalEmpresaTitle').text('Ver Cuenta Bancaria');
    $('#modalEmpresaBody').html(`<p>Cargando datos de la cuenta ID: ${id}</p>`);
    modal.show();
  });

  $('#empresasTable tbody').on('click', '.btn-editar', function() {
    const id = $(this).data('id');
    $('#modalEmpresaTitle').text('Editar Cuenta Bancaria');
    $('#modalEmpresaBody').html(`<p>Formulario para editar la cuenta ID: ${id}</p>`);
    modal.show();
  });

  $('#empresasTable tbody').on('click', '.btn-eliminar', function() {
    const id = $(this).data('id');
    $('#modalEmpresaTitle').text('Eliminar Cuenta Bancaria');
    $('#modalEmpresaBody').html(`
      <p>¿Estás seguro que deseas eliminar esta cuenta?</p>
      <button class="btn btn-danger">Confirmar Eliminación</button>
    `);
    modal.show();
  });
});
</script>

</body>
</html>
