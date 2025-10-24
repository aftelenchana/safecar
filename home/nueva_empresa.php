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
  <title>Agregar Empresa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos/index.css">
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
  <link rel="stylesheet" href="estilos/nueva_empresa.css">
</head>
<body>
    <?php
    if ($_SESSION['rol'] == 'cuenta_empresa') {
       require 'scripts/menu.php';
   }
     ?>

   <main class="content" id="contentWrapper">
     <form class="forms-sample" name="agregar_empresa" id="agregar_empresa" onsubmit="event.preventDefault(); sendData_agregar_empresa();" enctype="multipart/form-data">

     <!-- Header con breadcrumbs y acciones -->
     <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3 mb-4 border-bottom">
         <div class="d-flex align-items-center">
             <nav aria-label="breadcrumb">
                 <ol class="breadcrumb mb-0">
                     <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                     <li class="breadcrumb-item"><a href="#">Empresas</a></li>
                     <li class="breadcrumb-item active" aria-current="page">Nueva Empresa</li>
                 </ol>
             </nav>
         </div>
         <div class="btn-toolbar mb-2 mb-md-0">
             <div class="btn-group">
                 <button class="btn btn-sm btn-outline-secondary">
                     <i class="fas fa-times me-1"></i> Cancelar
                 </button>
                 <button type="submit" class="btn btn-sm btn-primary">
                     <i class="fas fa-save me-1"></i> Guardar
                 </button>
             </div>
         </div>
     </div>

     <!-- Sección para subir logo de la empresa -->
     <div class="card shadow-sm mb-4">
         <div class="card-header bg-primary text-white">
             <h5 class="mb-0">
                 <i class="fas fa-image me-2"></i> Logo de la Empresa
             </h5>
             <small class="opacity-75">Suba la imagen representativa de la empresa</small>
         </div>
         <div class="card-body">
             <div class="row">
                 <div class="col-md-4 mx-auto">
                     <div class="logo-container">
                         <div class="logo-title">Imagen del Logo</div>
                         <div class="image-upload-container">
                             <img id="imagePreview" src="<?php echo $img_sistema; ?>" alt="Previsualización de imagen" class="image-preview mb-3">
                             <div class="d-grid gap-2">
                                 <button type="button" class="btn btn-primary upload-btn">
                                     <i class="fas fa-upload me-2"></i> Seleccionar Imagen
                                     <input type="file" id="logoEmpresa" name="foto" accept="image/*" class="form-control" onchange="previewImage(this);">
                                 </button>
                             </div>
                             <small class="text-muted">Formatos soportados: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Resto del formulario (mantener todo el contenido existente) -->
     <!-- Tarjeta principal del formulario -->
     <div class="card shadow-lg border-0">
         <div class="card-header bg-primary text-white">
             <h5 class="mb-0">
                 <i class="fas fa-fingerprint me-2"></i> Identificación Fiscal
             </h5>
             <small class="opacity-75">Complete los datos requeridos por el SRI</small>
         </div>
         <div class="card-body">
             <!-- Sección RUC con validación en tiempo real -->
             <div class="row mb-4">
                 <div class="col-md-6">
                     <div class="form-floating">
                         <input type="text" class="form-control" id="ruc" name="identificacion" placeholder="RUC" maxlength="13"
                                pattern="[0-9]{13}" required>
                         <label for="ruc">RUC <span class="text-danger">*</span></label>
                         <div class="invalid-feedback">El RUC debe tener 13 dígitos</div>
                     </div>
                 </div>
                 <div class="col-md-6">
                     <button id="consultarSRI" class="btn btn-outline-primary mt-1" type="button" disabled>
                         <i class="fas fa-search me-1"></i> Consultar SRI
                     </button>
                     <div id="rucFeedback" class="small mt-1 text-muted"></div>
                 </div>
             </div>

             <!-- Resultados de consulta SRI (se muestra dinámicamente) -->
             <div id="sriResult" class="alert alert-info d-none">
                 <div class="d-flex justify-content-between">
                     <div>
                         <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i> RUC válido</h6>
                         <hr class="my-2">
                         <div class="row">
                             <div class="col-md-6">
                                 <p class="mb-1"><strong>Razón Social:</strong> <span id="razonSocialResult">-</span></p>
                                 <p class="mb-1"><strong>Nombre Comercial:</strong> <span id="nombreComercialResult">-</span></p>
                             </div>
                             <div class="col-md-6">
                                 <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-success">ACTIVO</span></p>
                                 <p class="mb-1"><strong>Contribuyente:</strong> <span id="tipoContribuyenteResult">-</span></p>
                             </div>
                         </div>
                     </div>
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                 </div>
             </div>

             <!-- Sección de información básica -->
             <div class="row g-3">
           <div class="col-md-8">
               <div class="form-floating">
                   <input type="text" name="razon_social" class="form-control" id="razonSocial" placeholder="Razón Social" required>
                   <label for="razonSocial">Razón Social <span class="text-danger">*</span></label>
               </div>
           </div>
           <div class="col-md-4">
               <div class="form-floating">
                   <input type="text" name="nombre_empresa" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                   <label for="nombreComercial">Nombre Comercial</label>
               </div>
           </div>


           <div class="col-md-12">
               <div class="form-floating">
                   <textarea class="form-control" name="actividadEconomica" placeholder="Describa la actividad económica" id="actividadEconomica" style="height: 120px;" ></textarea>
                   <label for="actividadEconomica">Actividad Económica <span class="text-danger">*</span></label>
               </div>
           </div>
       </div>
         </div>
     </div>

     <!-- Resto del formulario existente... -->
     <!-- Segunda tarjeta - Información de contacto -->
     <div class="card shadow-sm mt-4 border-primary">
       <div class="card-header bg-primary bg-opacity-10">
           <h5 class="mb-0">
               <i class="fas fa-address-book me-2"></i> Información de Contacto
           </h5>
       </div>
       <div class="card-body">
           <div class="row g-3">
               <div class="col-md-6">
                   <div class="form-floating">
                       <input type="email" class="form-control" name="email" id="email" placeholder="Correo Electrónico" required>
                       <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                   </div>
               </div>
               <div class="col-md-6">
                   <div class="form-floating">
                       <input type="text" class="form-control" id="representanteLegal" placeholder="Representante Legal">
                       <label for="representanteLegal">Representante Legal</label>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="form-floating">
                       <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección Matriz">
                       <label for="direccion">Dirección Matriz</label>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="form-floating">
                       <input type="text" class="form-control" name="celular" id="alertasWhatsapp" placeholder="Número de WhatsApp para alertas">
                       <label for="alertasWhatsapp">Alertas WhatsApp</label>
                   </div>
               </div>

               <div class="col-md-4">
                   <div class="form-floating">
                       <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono">
                       <label for="alertasWhatsapp">Teléfono</label>
                   </div>
               </div>
           </div>
       </div>
   </div>

     <!-- Tercera tarjeta - Configuración adicional -->
     <div class="card shadow-sm mt-4 border-info">
         <div class="card-header bg-info bg-opacity-10">
             <h5 class="mb-0">
                 <i class="fas fa-cogs me-2"></i> Configuración Adicional
             </h5>
         </div>
         <div class="card-body">
             <div class="row g-3">
                 <div class="col-md-12">
                     <div class="form-floating">
                         <textarea class="form-control" name="observaciones" placeholder="Observaciones" id="observaciones" style="height: 100px"></textarea>
                         <label for="observaciones">Observaciones</label>
                     </div>
                 </div>
                 <div class="col-md-6">
                     <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="activa" checked>
                         <label class="form-check-label" for="activa">Empresa activa</label>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Sección de acciones finales -->
     <div class="d-flex justify-content-between mt-4">
         <button class="btn btn-outline-danger">
             <i class="fas fa-trash-alt me-1"></i> Eliminar
         </button>
         <div>
             <button class="btn btn-outline-secondary me-2">
                 <i class="fas fa-times me-1"></i> Cancelar
             </button>
             <button type="submit" class="btn btn-primary">
                 <i class="fas fa-save me-1"></i> Guardar Empresa
             </button>
         </div>
     </div>
     <input type="hidden" name="action" value="agregar_empresas">
     </form>

     <style media="screen">
       .alerta_agregar_empresas{
         text-align: center;
         margin: 10px;
       }
     </style>

     <div class="alerta_agregar_empresas"></div>
 </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <script src="java/agregar_empresas.js?v=13"></script>


</body>
</html>
