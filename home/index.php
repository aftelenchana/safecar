<?php
ob_start();
include "../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4'); //linea a colocar
session_start();


if (empty($_SESSION['active'])) {
    header('location:/');
} else {
    // Asumimos que la sesión está activa y tenemos la información del dominio
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $domain = $_SERVER['HTTP_HOST'];

    $query_doccumentos =  mysqli_query($conection, "SELECT * FROM  usuarios  WHERE  url_admin  = '$domain'");
    $result_documentos = mysqli_fetch_array($query_doccumentos);

    if ($result_documentos) {
        $url_img_upload = $result_documentos['url_img_upload'];
        $img_facturacion = $result_documentos['img_facturacion'];
        $nombre_empresa = $result_documentos['nombre_empresa'];

        // Asegúrate de que esta ruta sea correcta y corresponda con la estructura de tu sistema de archivos
        $img_sistema = $url_img_upload.'/home/img/uploads/'.$img_facturacion;
    } else {
        // Si no hay resultados, tal vez quieras definir una imagen por defecto
      $img_sistema = '/img/guibis.png';
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css?v=3">
  <style>

  </style>
</head>
<body>

  <?php
  if ($_SESSION['rol'] == 'cuenta_empresa') {
     require 'scripts/menu.php';
 }

   ?>


   <main class="content" id="contentWrapper">
       <h1 class="mb-4">Dashboard Analítico</h1>

       <!-- Filtros superiores -->
       <div class="row mb-4">
           <div class="col-md-12">
               <div class="card shadow-sm">
                   <div class="card-body">
                       <div class="row g-3 align-items-center">
                           <div class="col-md-3">
                               <label for="timeRange" class="form-label">Rango de tiempo</label>
                               <select class="form-select" id="timeRange">
                                   <option value="7">Últimos 7 días</option>
                                   <option value="30" selected>Últimos 30 días</option>
                                   <option value="90">Últimos 3 meses</option>
                                   <option value="365">Último año</option>
                                   <option value="custom">Personalizado</option>
                               </select>
                           </div>
                           <div class="col-md-3">
                               <label for="empresaFilter" class="form-label">Filtrar por empresa</label>
                               <select class="form-select" id="empresaFilter">
                                   <option value="all" selected>Todas las empresas</option>
                                   <option value="1">Empresa A</option>
                                   <option value="2">Empresa B</option>
                                   <!-- Opciones dinámicas -->
                               </select>
                           </div>
                           <div class="col-md-3">
                               <label for="estadoCheque" class="form-label">Estado de cheques</label>
                               <select class="form-select" id="estadoCheque">
                                   <option value="all" selected>Todos</option>
                                   <option value="pendiente">Pendientes</option>
                                   <option value="cobrado">Cobrados</option>
                                   <option value="rechazado">Rechazados</option>
                               </select>
                           </div>
                           <div class="col-md-3 d-flex align-items-end">
                               <button class="btn btn-primary w-100" id="applyFilters">
                                   <i class="fas fa-filter me-2"></i>Aplicar Filtros
                               </button>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Resumen rápido -->
       <div class="row g-4 mb-4">
           <div class="col-sm-6 col-lg-3">
               <div class="card text-bg-primary shadow-sm">
                   <div class="card-body">
                       <div class="d-flex justify-content-between align-items-start">
                           <div>
                               <h5 class="card-title"><i class="fas fa-building me-2"></i> Empresas</h5>
                               <p class="card-text fs-4">12 registradas</p>
                           </div>
                           <span class="badge bg-white text-primary fs-6">+2%</span>
                       </div>
                       <div class="mt-2">
                           <small class="text-white-50">Última empresa: 15/07/2023</small>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-sm-6 col-lg-3">
               <div class="card text-bg-success shadow-sm">
                   <div class="card-body">
                       <div class="d-flex justify-content-between align-items-start">
                           <div>
                               <h5 class="card-title"><i class="fas fa-piggy-bank me-2"></i> Cuentas</h5>
                               <p class="card-text fs-4">24 cuentas</p>
                           </div>
                           <span class="badge bg-white text-success fs-6">+5%</span>
                       </div>
                       <div class="mt-2">
                           <small class="text-white-50">Saldo total: $1,245,680</small>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-sm-6 col-lg-3">
               <div class="card text-bg-warning shadow-sm">
                   <div class="card-body">
                       <div class="d-flex justify-content-between align-items-start">
                           <div>
                               <h5 class="card-title"><i class="fas fa-users me-2"></i> Clientes</h5>
                               <p class="card-text fs-4">48 clientes</p>
                           </div>
                           <span class="badge bg-white text-warning fs-6">+8%</span>
                       </div>
                       <div class="mt-2">
                           <small class="text-white-50">5 nuevos este mes</small>
                       </div>
                   </div>
               </div>
           </div>
           <div class="col-sm-6 col-lg-3">
               <div class="card text-bg-danger shadow-sm">
                   <div class="card-body">
                       <div class="d-flex justify-content-between align-items-start">
                           <div>
                               <h5 class="card-title"><i class="fas fa-receipt me-2"></i> Cheques</h5>
                               <p class="card-text fs-4">156 emitidos</p>
                           </div>
                           <span class="badge bg-white text-danger fs-6">+12%</span>
                       </div>
                       <div class="mt-2">
                           <small class="text-white-50">Total: $3,456,200</small>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Gráficos principales -->
       <div class="row g-4 mb-4">
           <!-- Evolución de cheques por estado -->
           <div class="col-lg-8">
               <div class="card shadow-sm h-100">
                   <div class="card-header d-flex justify-content-between align-items-center">
                       <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Evolución de Cheques</h5>
                       <div class="btn-group btn-group-sm">
                           <button type="button" class="btn btn-outline-secondary active" data-metric="count">Cantidad</button>
                           <button type="button" class="btn btn-outline-secondary" data-metric="amount">Monto</button>
                       </div>
                   </div>
                   <div class="card-body">
                       <canvas id="chequesEvolutionChart" height="300"></canvas>
                   </div>
                   <div class="card-footer bg-transparent">
                       <small class="text-muted">Mostrando datos de los últimos 30 días. Haga clic en la leyenda para filtrar.</small>
                   </div>
               </div>
           </div>

           <!-- Distribución por bancos -->
           <div class="col-lg-4">
               <div class="card shadow-sm h-100">
                   <div class="card-header">
                       <h5 class="mb-0"><i class="fas fa-university me-2 text-success"></i> Cheques por Banco</h5>
                   </div>
                   <div class="card-body d-flex align-items-center justify-content-center">
                       <canvas id="bancosDonutChart" height="250"></canvas>
                   </div>
                   <div class="card-footer bg-transparent">
                       <div class="row">
                           <div class="col-6">
                               <small class="text-muted">Banco más usado:</small>
                               <div class="fw-bold">Banco Nacional (42%)</div>
                           </div>
                           <div class="col-6">
                               <small class="text-muted">Mayor monto:</small>
                               <div class="fw-bold">Banco Continental ($1.2M)</div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Segunda fila de gráficos -->
       <div class="row g-4 mb-4">
           <!-- Cheques próximos a vencer -->
           <div class="col-lg-6">
               <div class="card shadow-sm h-100">
                   <div class="card-header">
                       <h5 class="mb-0"><i class="fas fa-calendar-exclamation me-2 text-warning"></i> Cheques Próximos a Vencer</h5>
                   </div>
                   <div class="card-body p-0">
                       <div class="table-responsive">
                           <table class="table table-hover mb-0">
                               <thead class="table-light">
                                   <tr>
                                       <th>N° Cheque</th>
                                       <th>Banco</th>
                                       <th>Monto</th>
                                       <th>Vencimiento</th>
                                       <th>Días faltan</th>
                                       <th>Estado</th>
                                   </tr>
                               </thead>
                               <tbody>
                                   <tr class="table-warning">
                                       <td>CH-784512</td>
                                       <td>Banco Nacional</td>
                                       <td>$45,800</td>
                                       <td>28/07/2023</td>
                                       <td><span class="badge bg-warning">2 días</span></td>
                                       <td><span class="badge bg-secondary">Pendiente</span></td>
                                   </tr>
                                   <tr>
                                       <td>CH-784513</td>
                                       <td>Banco Continental</td>
                                       <td>$32,450</td>
                                       <td>30/07/2023</td>
                                       <td><span class="badge bg-warning">4 días</span></td>
                                       <td><span class="badge bg-secondary">Pendiente</span></td>
                                   </tr>
                                   <!-- Más filas -->
                               </tbody>
                           </table>
                       </div>
                   </div>
                   <div class="card-footer bg-transparent text-end">
                       <a href="#" class="btn btn-sm btn-outline-primary">Ver todos</a>
                   </div>
               </div>
           </div>

           <!-- Distribución por empresas -->
           <div class="col-lg-6">
               <div class="card shadow-sm h-100">
                   <div class="card-header d-flex justify-content-between align-items-center">
                       <h5 class="mb-0"><i class="fas fa-building me-2 text-info"></i> Distribución por Empresa</h5>
                       <div class="btn-group btn-group-sm">
                           <button type="button" class="btn btn-outline-secondary active" data-view="bar">Barras</button>
                           <button type="button" class="btn btn-outline-secondary" data-view="pie">Torta</button>
                       </div>
                   </div>
                   <div class="card-body">
                       <canvas id="empresasBarChart" height="250"></canvas>
                   </div>
                   <div class="card-footer bg-transparent">
                       <small class="text-muted">Empresa con más cheques: <strong>Importaciones XYZ (38 cheques)</strong></small>
                   </div>
               </div>
           </div>
       </div>

       <!-- Tercera fila - Alertas y estadísticas adicionales -->
       <div class="row g-4">
           <div class="col-lg-4">
               <div class="card shadow-sm border-warning">
                   <div class="card-header bg-warning bg-opacity-10">
                       <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Alertas Recientes</h5>
                   </div>
                   <div class="card-body">
                       <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                           <strong>CH-784512</strong> vence en 2 días ($45,800)
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>
                       <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                           <strong>CH-781234</strong> fue rechazado ($12,300)
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>
                       <div class="alert alert-success alert-dismissible fade show" role="alert">
                           <strong>CH-789876</strong> fue cobrado ($56,700)
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>
                   </div>
               </div>
           </div>

           <div class="col-lg-4">
               <div class="card shadow-sm">
                   <div class="card-header">
                       <h5 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i> Movimientos Recientes</h5>
                   </div>
                   <div class="card-body p-0">
                       <div class="list-group list-group-flush">
                           <a href="#" class="list-group-item list-group-item-action">
                               <div class="d-flex w-100 justify-content-between">
                                   <h6 class="mb-1">CH-789012</h6>
                                   <small class="text-success">+$32,450</small>
                               </div>
                               <p class="mb-1 small">Banco Nacional - Cliente: Distribuidora ABC</p>
                               <small class="text-muted">Hoy, 10:45 AM</small>
                           </a>
                           <a href="#" class="list-group-item list-group-item-action">
                               <div class="d-flex w-100 justify-content-between">
                                   <h6 class="mb-1">CH-789011</h6>
                                   <small class="text-danger">-$12,800</small>
                               </div>
                               <p class="mb-1 small">Banco Continental - Proveedor: Insumos S.A.</p>
                               <small class="text-muted">Ayer, 3:20 PM</small>
                           </a>
                           <!-- Más movimientos -->
                       </div>
                   </div>
               </div>
           </div>

           <div class="col-lg-4">
               <div class="card shadow-sm">
                   <div class="card-header">
                       <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-danger"></i> Indicadores Clave</h5>
                   </div>
                   <div class="card-body">
                       <div class="mb-3">
                           <h6 class="small">Tasa de rechazo</h6>
                           <div class="progress">
                               <div class="progress-bar bg-danger" role="progressbar" style="width: 8%" aria-valuenow="8" aria-valuemin="0" aria-valuemax="100">8%</div>
                           </div>
                           <small class="text-muted">↓ 2% vs mes anterior</small>
                       </div>
                       <div class="mb-3">
                           <h6 class="small">Tiempo promedio cobro</h6>
                           <div class="progress">
                               <div class="progress-bar bg-info" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">4.2 días</div>
                           </div>
                           <small class="text-muted">↓ 0.8 días vs mes anterior</small>
                       </div>
                       <div>
                           <h6 class="small">Clientes frecuentes</h6>
                           <div class="d-flex align-items-center">
                               <div class="flex-shrink-0">
                                   <span class="avatar avatar-sm bg-primary text-white rounded-circle">D</span>
                               </div>
                               <div class="flex-grow-1 ms-3">
                                   <p class="mb-0 fw-bold">Distribuidora ABC</p>
                                   <small class="text-muted">12 cheques este mes ($245,600)</small>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="java/dashboard.js" charset="utf-8"></script>

</body>
</html>
