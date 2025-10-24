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
  <title>Nuevo Cheque</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css">
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
       <!-- Header con breadcrumbs y acciones -->
       <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3 mb-4 border-bottom">
           <div class="d-flex align-items-center">
               <nav aria-label="breadcrumb">
                   <ol class="breadcrumb mb-0">
                       <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                       <li class="breadcrumb-item"><a href="#">Cheques</a></li>
                       <li class="breadcrumb-item active" aria-current="page">Nuevo Cheque</li>
                   </ol>
               </nav>
           </div>
           <div class="btn-toolbar mb-2 mb-md-0">
               <div class="btn-group">
                   <button class="btn btn-sm btn-outline-secondary">
                       <i class="fas fa-times me-1"></i> Cancelar
                   </button>
                   <button class="btn btn-sm btn-primary">
                       <i class="fas fa-save me-1"></i> Registrar Cheque
                   </button>
               </div>
           </div>
       </div>

       <!-- Tarjeta principal del formulario -->
       <div class="card shadow-lg border-primary">
           <div class="card-header bg-primary text-white">
               <h5 class="mb-0">
                   <i class="fas fa-money-check-alt me-2"></i> Datos del Cheque
               </h5>
               <small class="opacity-75">Complete la información básica del cheque</small>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Serie y número -->
                   <div class="col-md-3">
                       <div class="form-floating">
                           <input type="text" class="form-control" id="serie" placeholder="Serie" required>
                           <label for="serie">Serie <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-floating">
                           <input type="text" class="form-control" id="numero" placeholder="Número" required>
                           <label for="numero">Número <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Banco y cuenta -->
                   <div class="col-md-6">
                       <label for="banco" class="form-label">Banco <span class="text-danger">*</span></label>
                       <select class="form-select select-banco" id="banco" required>
                           <option value="" selected disabled>Seleccione un banco...</option>
                           <option value="BANCO_PICHINCHA" data-logo="pichincha-logo.png">Banco Pichincha</option>
                           <option value="BANCO_GUAYAQUIL" data-logo="guayaquil-logo.png">Banco de Guayaquil</option>
                           <option value="BANCO_PACIFICO" data-logo="pacifico-logo.png">Banco del Pacífico</option>
                       </select>
                   </div>

                   <!-- Cuenta asociada -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <select class="form-select" id="cuentaBancaria" required>
                               <option value="" selected disabled>Seleccione cuenta...</option>
                               <option value="1">****4582 - Cuenta Corriente (Banco Pichincha)</option>
                               <option value="2">****7812 - Cuenta Ahorros (Banco Guayaquil)</option>
                           </select>
                           <label for="cuentaBancaria">Cuenta Bancaria <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Fechas importantes -->
                   <div class="col-md-3">
                       <div class="form-floating">
                           <input type="date" class="form-control" id="fechaEmision" placeholder="Fecha Emisión" required>
                           <label for="fechaEmision">Fecha Emisión <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-floating">
                           <input type="date" class="form-control" id="fechaCobro" placeholder="Fecha Cobro" required>
                           <label for="fechaCobro">Fecha Cobro <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-floating">
                           <input type="date" class="form-control" id="fechaVencimiento" placeholder="Fecha Vencimiento">
                           <label for="fechaVencimiento">Fecha Vencimiento</label>
                       </div>
                   </div>

                   <!-- Monto y moneda -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <input type="text" class="form-control text-end" id="monto" placeholder="Monto" required>
                           <label for="monto">Monto <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-floating">
                           <select class="form-select" id="moneda" required>
                               <option value="USD" selected>Dólares (USD)</option>
                               <option value="EUR">Euros (EUR)</option>
                               <option value="PEN">Soles (PEN)</option>
                           </select>
                           <label for="moneda">Moneda <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Estado del cheque -->
                   <div class="col-md-3">
                       <div class="form-floating">
                           <select class="form-select" id="estado" required>
                               <option value="PENDIENTE" selected>Pendiente</option>
                               <option value="COBRADO">Cobrado</option>
                               <option value="RECHAZADO">Rechazado</option>
                               <option value="ANULADO">Anulado</option>
                           </select>
                           <label for="estado">Estado <span class="text-danger">*</span></label>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Segunda tarjeta - Beneficiario y origen -->
       <div class="card shadow-sm mt-4 border-info">
           <div class="card-header bg-info bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-user-tie me-2"></i> Beneficiario y Origen
               </h5>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Beneficiario -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <select class="form-select" id="beneficiario" required>
                               <option value="" selected disabled>Seleccione beneficiario...</option>
                               <option value="1">Juan Pérez (C.I. 1234567890)</option>
                               <option value="2">Empresa XYZ (RUC 1234567890001)</option>
                           </select>
                           <label for="beneficiario">Beneficiario <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Tipo de beneficiario -->
                   <div class="col-md-3">
                       <div class="form-floating">
                           <select class="form-select" id="tipoBeneficiario" required>
                               <option value="" selected disabled>Seleccione...</option>
                               <option value="CLIENTE">Cliente</option>
                               <option value="PROVEEDOR">Proveedor</option>
                               <option value="EMPLEADO">Empleado</option>
                               <option value="OTRO">Otro</option>
                           </select>
                           <label for="tipoBeneficiario">Tipo <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Empresa asociada -->
                   <div class="col-md-3">
                       <div class="form-floating">
                           <select class="form-select" id="empresa" required>
                               <option value="" selected disabled>Seleccione empresa...</option>
                               <option value="1">TECNOLOGIAS AVANZADAS S.A.</option>
                               <option value="2">IMPORTADORA XYZ</option>
                           </select>
                           <label for="empresa">Empresa <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Concepto -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <input type="text" class="form-control" id="concepto" placeholder="Concepto" required>
                           <label for="concepto">Concepto <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Descripción -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <textarea class="form-control" placeholder="Descripción" id="descripcion" style="height: 80px"></textarea>
                           <label for="descripcion">Descripción adicional</label>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Tercera tarjeta - Imágenes del cheque -->
       <div class="card shadow-sm mt-4 border-warning">
           <div class="card-header bg-warning bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-images me-2"></i> Imágenes del Cheque
               </h5>
               <small class="opacity-75">Suba imágenes del cheque (frente y dorso)</small>
           </div>
           <div class="card-body">
               <div class="row">
                   <!-- Área de subida -->
                   <div class="col-md-6">
                       <div class="file-drop-area p-4 text-center border rounded-3">
                           <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                           <h5>Arrastre las imágenes aquí</h5>
                           <p class="small text-muted">Formatos: JPG, PNG, PDF (Máx. 5MB cada una)</p>
                           <button class="btn btn-outline-primary mt-2">
                               <i class="fas fa-folder-open me-1"></i> Seleccionar archivos
                           </button>
                           <input type="file" id="imagenesCheque" class="d-none" multiple accept="image/*,.pdf">
                       </div>
                   </div>

                   <!-- Vista previa -->
                   <div class="col-md-6">
                       <div class="preview-container">
                           <h6 class="mb-3">Vista previa</h6>
                           <div id="imagenesPreview" class="row g-2">
                               <div class="col-6 placeholder-image">
                                   <div class="ratio ratio-1x1 bg-light rounded">
                                       <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                           <i class="fas fa-image fa-2x mb-2"></i>
                                           <small>Frente del cheque</small>
                                       </div>
                                   </div>
                               </div>
                               <div class="col-6 placeholder-image">
                                   <div class="ratio ratio-1x1 bg-light rounded">
                                       <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                           <i class="fas fa-image fa-2x mb-2"></i>
                                           <small>Dorso del cheque</small>
                                       </div>
                                   </div>
                               </div>
                           </div>
                           <div class="progress mt-3 d-none" id="uploadProgress">
                               <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Cuarta tarjeta - Configuraciones adicionales -->
       <div class="card shadow-sm mt-4 border-secondary">
           <div class="card-header bg-secondary bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-cogs me-2"></i> Configuraciones Adicionales
               </h5>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Notificaciones -->
                   <div class="col-md-6">
                       <div class="form-check form-switch mb-3">
                           <input class="form-check-input" type="checkbox" id="notificarVencimiento" checked>
                           <label class="form-check-label" for="notificarVencimiento">Notificar vencimiento</label>
                       </div>
                       <div class="form-check form-switch mb-3">
                           <input class="form-check-input" type="checkbox" id="notificarCobro" checked>
                           <label class="form-check-label" for="notificarCobro">Notificar al cobrar</label>
                       </div>
                       <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="recordatorioWhatsapp">
                           <label class="form-check-label" for="recordatorioWhatsapp">Recordatorio por WhatsApp</label>
                       </div>
                   </div>

                   <!-- Método de notificación -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <select class="form-select" id="metodoNotificacion">
                               <option value="EMAIL" selected>Correo electrónico</option>
                               <option value="WHATSAPP">WhatsApp</option>
                               <option value="SMS">SMS</option>
                               <option value="TODOS">Todos los métodos</option>
                           </select>
                           <label for="metodoNotificacion">Método de notificación</label>
                       </div>
                       <div class="form-floating mt-3">
                           <input type="text" class="form-control" id="diasRecordatorio" placeholder="Días recordatorio" value="3">
                           <label for="diasRecordatorio">Días de recordatorio previo</label>
                       </div>
                   </div>

                   <!-- Observaciones -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <textarea class="form-control" placeholder="Observaciones" id="observaciones" style="height: 100px"></textarea>
                           <label for="observaciones">Observaciones internas</label>
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
                   <i class="fas fa-save me-1"></i> Registrar Cheque
               </button>
           </div>
       </div>


       <!-- Estilos adicionales -->
       <style>
           .select-banco {
               background-image: url('assets/img/bancos/default-logo.png');
               background-repeat: no-repeat;
               background-position: right 0.75rem center;
               background-size: 24px;
               padding-right: 2.5rem;
           }

           .file-drop-area {
               border: 2px dashed #dee2e6;
               border-radius: 5px;
               transition: all 0.3s;
               cursor: pointer;
           }

           .file-drop-area:hover {
               border-color: #0d6efd;
               background-color: rgba(13, 110, 253, 0.05);
           }

           .placeholder-image {
               opacity: 0.7;
           }

           .object-fit-cover {
               object-fit: cover;
           }

           .card-header {
               border-bottom: none;
           }
       </style>
   </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <!-- Scripts para funcionalidad -->
  <script>
      // Mostrar logo del banco seleccionado
      document.getElementById('banco').addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          if (selectedOption.dataset.logo) {
              this.style.backgroundImage = `url('assets/img/bancos/${selectedOption.dataset.logo}')`;
          } else {
              this.style.backgroundImage = '';
          }
      });

      // Formatear monto
      document.getElementById('monto').addEventListener('blur', function() {
          if (this.value) {
              const value = parseFloat(this.value.replace(/[^0-9.]/g, ''));
              this.value = value.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
              });
          }
      });

      // Manejar subida de imágenes
      document.querySelector('.file-drop-area').addEventListener('click', function() {
          document.getElementById('imagenesCheque').click();
      });

      document.getElementById('imagenesCheque').addEventListener('change', function(e) {
          const preview = document.getElementById('imagenesPreview');
          preview.innerHTML = '';

          Array.from(e.target.files).forEach((file, index) => {
              const reader = new FileReader();

              reader.onload = function(event) {
                  const col = document.createElement('div');
                  col.className = 'col-6';

                  const imgContainer = document.createElement('div');
                  imgContainer.className = 'ratio ratio-1x1 bg-light rounded overflow-hidden';

                  const img = document.createElement('img');
                  img.src = event.target.result;
                  img.className = 'object-fit-cover w-100 h-100';
                  img.alt = `Imagen cheque ${index + 1}`;

                  imgContainer.appendChild(img);
                  col.appendChild(imgContainer);
                  preview.appendChild(col);
              };

              if (file.type.match('image.*')) {
                  reader.readAsDataURL(file);
              } else if (file.type === 'application/pdf') {
                  // Mostrar icono para PDF
                  const col = document.createElement('div');
                  col.className = 'col-6';

                  const pdfContainer = document.createElement('div');
                  pdfContainer.className = 'ratio ratio-1x1 bg-light rounded d-flex flex-column align-items-center justify-content-center';

                  pdfContainer.innerHTML = `
                      <i class="fas fa-file-pdf text-danger fa-3x mb-2"></i>
                      <small class="text-muted">${file.name}</small>
                  `;

                  col.appendChild(pdfContainer);
                  preview.appendChild(col);
              }
          });

          // Mostrar barra de progreso (simulada)
          const progressBar = document.getElementById('uploadProgress');
          progressBar.classList.remove('d-none');

          let progress = 0;
          const interval = setInterval(() => {
              progress += 10;
              progressBar.querySelector('.progress-bar').style.width = `${progress}%`;

              if (progress >= 100) {
                  clearInterval(interval);
                  setTimeout(() => progressBar.classList.add('d-none'), 500);
              }
          }, 200);
      });

      // Drag and drop
      const fileDropArea = document.querySelector('.file-drop-area');

      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
          fileDropArea.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
          e.preventDefault();
          e.stopPropagation();
      }

      ['dragenter', 'dragover'].forEach(eventName => {
          fileDropArea.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
          fileDropArea.addEventListener(eventName, unhighlight, false);
      });

      function highlight() {
          fileDropArea.classList.add('border-primary');
          fileDropArea.style.backgroundColor = 'rgba(13, 110, 253, 0.05)';
      }

      function unhighlight() {
          fileDropArea.classList.remove('border-primary');
          fileDropArea.style.backgroundColor = '';
      }

      fileDropArea.addEventListener('drop', handleDrop, false);

      function handleDrop(e) {
          const dt = e.dataTransfer;
          const files = dt.files;
          document.getElementById('imagenesCheque').files = files;

          // Disparar evento change manualmente
          const event = new Event('change');
          document.getElementById('imagenesCheque').dispatchEvent(event);
      }
  </script>


</body>
</html>
