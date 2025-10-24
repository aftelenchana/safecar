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
  <title>Nueva Cuenta Bancaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css">
   <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
   <link rel="stylesheet" href="estilos/nueva_cuenta_bancaria.css">
  <style>

  </style>
</head>
<body>

  <?php
  if ($_SESSION['rol'] == 'cuenta_empresa') {
     require 'scripts/menu.php';
 }
   ?>

   <!-- Estilos adicionales -->
   <style>
       .select-banco {
           background-image: url('assets/img/bancos/default-logo.png');
           background-repeat: no-repeat;
           background-position: right 0.75rem center;
           background-size: 24px;
           padding-right: 2.5rem;
       }

       .card-header {
           border-bottom: none;
       }

       .border-primary {
           border-width: 2px !important;
       }
   </style>
   <main class="content" id="contentWrapper">
     <form class="forms-sample" name="nueva_cuenta_bancaria" id="nueva_cuenta_bancaria" onsubmit="event.preventDefault(); sendData_nueva_cuenta_bancaria();">


       <!-- Header con breadcrumbs y acciones -->
       <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3 mb-4 border-bottom">
           <div class="d-flex align-items-center">
               <nav aria-label="breadcrumb">
                   <ol class="breadcrumb mb-0">
                       <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                       <li class="breadcrumb-item active" aria-current="page">Nueva Cuenta Bancaria</li>
                   </ol>
               </nav>
           </div>
           <div class="btn-toolbar mb-2 mb-md-0">
               <div class="btn-group">
                   <button class="btn btn-sm btn-outline-secondary">
                       <i class="fas fa-times me-1"></i> Cancelar
                   </button>
                   <button class="btn btn-sm btn-primary">
                       <i class="fas fa-save me-1"></i> Guardar Cuenta
                   </button>
               </div>
           </div>
       </div>

       <!-- Tarjeta principal del formulario -->
       <div class="card shadow-lg border-primary">
           <div class="card-header bg-primary text-white">
               <h5 class="mb-0">
                   <i class="fas fa-piggy-bank me-2"></i> Información Bancaria Básica
               </h5>
               <small class="opacity-75">Complete los datos principales de la cuenta</small>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Selector de Banco con logos -->
                   <div class="col-md-6">
                       <label for="banco" class="form-label">Banco <span class="text-danger">*</span></label>
                       <select class="form-select select-banco" name="nombreBanco" id="banco" required>
                           <option value="" selected disabled>Seleccione un banco...</option>
                           <option value="BANCO PICHINCHA" data-logo="pichincha-logo.png">BANCO PICHINCHA</option>
                           <option value="BANCO GUAYAQUIL" data-logo="guayaquil-logo.png">BANCO GUAYAQUIL</option>
                           <option value="BANCO PACIFICO" data-logo="pacifico-logo.png">BANCO PACIFICO</option>
                           <option value="BANCO PRODUBANCO" data-logo="produbanco-logo.png">BANCO PRODUBANCO</option>
                           <option value="BANCO BOLIVARIANO" data-logo="bolivariano-logo.png">BANCO BOLIVARIANO</option>
                           <option value="BANCO INTERNACIONAL" data-logo="internacional-logo.png">BANCO INTERNACIONAL</option>
                       </select>
                       <div class="mt-2" id="banco-logo-container" style="height: 40px; display: none;">
                           <img id="banco-logo" src="" alt="Logo banco" class="h-100">
                       </div>
                   </div>

                   <!-- Tipo de Cuenta -->
                   <div class="col-md-6">
                       <label for="tipoCuenta" class="form-label">Tipo de Cuenta <span class="text-danger">*</span></label>
                       <select class="form-select" name="tipo_cuenta" id="tipoCuenta" required>
                           <option value="" selected disabled>Seleccione tipo...</option>
                           <option value="AHORROS">Ahorros</option>
                           <option value="CORRIENTE">Corriente</option>
                           <option value="PLAZO_FIJO">Plazo Fijo</option>
                           <option value="PROGRAMADA">Programada</option>
                       </select>
                   </div>

                   <!-- Número de Cuenta con validación -->
                   <div class="col-md-6">
                       <label for="numeroCuenta" class="form-label">Número de Cuenta <span class="text-danger">*</span></label>
                       <div class="input-group">
                           <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                           <input type="text" class="form-control" name="numero_cuenta" id="numeroCuenta" placeholder="Ej: 1234567890" required
                                  pattern="[0-9]{10,20}" maxlength="20">
                           <button class="btn btn-outline-secondary" type="button" id="validarCuenta">
                               <i class="fas fa-check-circle"></i> Validar
                           </button>
                       </div>
                       <div class="invalid-feedback">El número de cuenta debe tener entre 10 y 20 dígitos</div>
                       <div id="cuentaFeedback" class="small mt-1 text-muted"></div>
                   </div>

                   <!-- Moneda -->
                   <div class="col-md-6">
                       <label for="moneda" class="form-label">Moneda <span class="text-danger">*</span></label>
                       <select class="form-select" name="moneda" id="moneda" required>
                           <option value="" selected disabled>Seleccione moneda...</option>
                           <option value="USD">Dólares Americanos (USD)</option>
                           <option value="EUR">Euros (EUR)</option>
                           <option value="PEN">Soles Peruanos (PEN)</option>
                       </select>
                   </div>

                   <div class="col-md-6">
                       <label for="tipoCuenta" class="form-label">Empresa <span class="text-danger">*</span></label>
                       <select class="form-select" id="empresa" name="empresa"  required>
                           <option value="" selected disabled>Seleccione tipo...</option>
                           <?php
                            $query_empresas = mysqli_query($conection, "SELECT * FROM empresas_registradas
                               WHERE   empresas_registradas.iduser = '$iduser' ");
                               while ($data_empresas = mysqli_fetch_array($query_empresas)) {
                                echo '<option value="'.$data_empresas['id'].'">'.$data_empresas['nombre_empresa'].'</option>';
                            }

                           ?>
                       </select>
                   </div>

                   <!-- Saldo Inicial -->
                   <div class="col-md-12">
                       <label for="saldoInicial" class="form-label">Saldo Inicial</label>
                       <div class="input-group">
                           <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                           <input type="text" class="form-control text-end" name="saldo_inicial" id="saldoInicial" placeholder="0.00">
                           <span class="input-group-text">.00</span>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Segunda tarjeta - Configuración adicional -->
       <div class="card shadow-sm mt-4 border-info">
           <div class="card-header bg-info bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-sliders-h me-2"></i> Configuración Avanzada
               </h5>
               <small class="opacity-75">Opciones adicionales para la cuenta</small>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Límites y autorizados -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <input type="text" class="form-control" id="limiteDiario" name="limiteDiario" placeholder="Límite diario">
                           <label for="limiteDiario">Límite diario de transferencia</label>
                       </div>
                   </div>

                   <!-- Fechas importantes -->
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="date" class="form-control" id="fechaApertura" name="fechaApertura" placeholder="Fecha de apertura">
                           <label for="fechaApertura">Fecha de apertura</label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="date" class="form-control" id="fechaCierre" name="fechaCierre" placeholder="Fecha de cierre">
                           <label for="fechaCierre">Fecha de cierre (si aplica)</label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="text" class="form-control" id="interes" name="interes" placeholder="Tasa de interés">
                           <label for="interes">Tasa de interés anual (%)</label>
                       </div>
                   </div>

                   <!-- Opciones de cheques -->
                   <div class="col-md-12">
                       <div class="border p-3 rounded">
                           <h6 class="mb-3"><i class="fas fa-money-check-alt me-2"></i> Configuración de Cheques</h6>
                           <div class="row">
                               <div class="col-md-4">
                                   <div class="form-check form-switch">
                                       <input class="form-check-input" type="checkbox" id="permiteCheques" name="permiteCheques" checked>
                                       <label class="form-check-label" for="permiteCheques">Permitir emisión de cheques</label>
                                   </div>
                               </div>
                               <div class="col-md-4">
                                   <div class="form-floating">
                                       <input type="text" class="form-control" id="serieCheques" name="serieCheques" placeholder="Serie de cheques">
                                       <label for="serieCheques">Serie inicial de cheques</label>
                                   </div>
                               </div>
                               <div class="col-md-4">
                                   <div class="form-floating">
                                       <input type="number" class="form-control" id="cantidadCheques" name="cantidadCheques" placeholder="Cantidad disponible" min="0">
                                       <label for="cantidadCheques">Cantidad disponible</label>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>

                   <!-- Notas y estado -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <textarea class="form-control" placeholder="Notas adicionales" id="notas" name="notas" style="height: 80px"></textarea>
                           <label for="notas">Notas adicionales</label>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="cuentaActiva" name="cuentaActiva" checked>
                           <label class="form-check-label" for="cuentaActiva">Cuenta activa</label>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="notificaciones">
                           <label class="form-check-label" for="notificaciones">Recibir notificaciones</label>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Sección de acciones finales -->
       <div class="d-flex justify-content-between mt-4">
           <button class="btn btn-outline-danger">
               <i class="fas fa-trash-alt me-1"></i> Descartar
           </button>
           <div>
               <button class="btn btn-outline-secondary me-2">
                   <i class="fas fa-times me-1"></i> Cancelar
               </button>
               <button class="btn btn-primary">
                   <i class="fas fa-save me-1"></i> Guardar Cuenta Bancaria
               </button>
           </div>
       </div>

        <input type="hidden" name="action" value="agregar_cuenta_bancaria">

     </form>

     <style media="screen">
       .alerta_agregar_cuenta_bancaria{
         text-align: center;
         margin: 10px;
       }
     </style>

     <div class="alerta_agregar_cuenta_bancaria"></div>

   </main>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
   <script src="java/index.js" charset="utf-8"></script>
   <script src="java/nueva_cuenta_bancaria.js?v=16"></script>


</body>
</html>
