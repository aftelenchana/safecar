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
  <title>Nuevo Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css">
   <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
   <link rel="stylesheet" href="estilos/nuevo_cliente.css?v=2">
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

    <form  name="agregar_nuevo_usuario" id="agregar_nuevo_usuario" onsubmit="event.preventDefault(); sendData_agregar_nueva_usuario();" enctype="multipart/form-data">
      <!-- Header con breadcrumbs y acciones -->
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-3 mb-4 border-bottom">
          <div class="d-flex align-items-center">
              <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0">
                      <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#">Clientes</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Nuevo Cliente</li>
                  </ol>
              </nav>
          </div>
          <div class="btn-toolbar mb-2 mb-md-0">
              <div class="btn-group">
                  <button class="btn btn-sm btn-outline-secondary">
                      <i class="fas fa-times me-1"></i> Cancelar
                  </button>
                  <button class="btn btn-sm btn-primary">
                      <i class="fas fa-save me-1"></i> Guardar Cliente
                  </button>
              </div>
          </div>
      </div>

      <!-- Sección de imagen de perfil circular -->
      <div class="row mb-4">
          <div class="col-md-12 d-flex justify-content-center">
              <div class="profile-image-container text-center">
                  <div class="profile-image-wrapper">
                      <img id="profileImagePreview" src="https://guibis.com/home/img/uploads/usuario.png" alt="Imagen de perfil" class="profile-image">
                      <label for="profileImageUpload" class="profile-image-upload-label">
                          <i class="fas fa-camera"></i>
                      </label>
                      <input type="file" id="profileImageUpload" name="foto" accept="image/*" style="display: none;">
                  </div>
                  <div class="mt-2">
                      <button id="removeProfileImage" class="btn btn-sm btn-outline-danger" style="display: none;">
                          <i class="fas fa-trash"></i> Eliminar imagen
                      </button>
                  </div>
              </div>
          </div>
      </div>

       <!-- Tarjeta principal del formulario -->
       <div class="card shadow-lg border-success">
           <div class="card-header bg-success text-white">
               <h5 class="mb-0">
                   <i class="fas fa-id-card me-2"></i> Identificación del Usuario
               </h5>
               <small class="opacity-75">Complete los datos básicos del cliente</small>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Tipo de identificación -->
                   <div class="col-md-3">
                       <label for="tipoIdentificacion" class="form-label">Tipo Identificación <span class="text-danger">*</span></label>
                       <select class="form-select" name="tipo_identificacion " id="tipoIdentificacion" required>
                           <option value="" selected disabled>Seleccione...</option>
                           <option value="05">Cédula</option>
                           <option value="04">RUC</option>
                       </select>
                   </div>

                   <!-- Número de identificación con validación -->
                   <div class="col-md-5">
                     <label for="identificacion" class="form-label">Número de Identificación <span class="text-danger">*</span></label>
                     <input type="text" class="form-control" name="identificacion" id="identificacion" placeholder="Ej: 1234567890" required>
                   </div>

                   <!-- Nombres y apellidos -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Nombres" required>
                           <label for="nombres">Nombres y apellidos <span class="text-danger">*</span></label>
                       </div>
                   </div>


                   <!-- Correo y teléfono -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico">
                           <label for="email">Correo electrónico <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="form-floating">
                           <input type="tel" class="form-control" name="celular" id="celular" placeholder="Celular">
                           <label for="telefono">Celular <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Fecha nacimiento y género -->
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="date" class="form-control" name="fecha_nacimiento" id="fechaNacimiento" placeholder="Fecha nacimiento">
                           <label for="fechaNacimiento">Fecha nacimiento</label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <select class="form-select" name="genero" id="genero">
                               <option value="" selected disabled>Seleccione...</option>
                               <option value="MASCULINO">Masculino</option>
                               <option value="FEMENINO">Femenino</option>
                               <option value="OTRO">Otro</option>
                               <option value="NO_ESPECIFICA">Prefiero no decir</option>
                           </select>
                           <label for="genero">Género</label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="text" class="form-control" name="nacionalidad" id="nacionalidad" placeholder="Nacionalidad">
                           <label for="nacionalidad">Nacionalidad</label>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       <!-- Segunda tarjeta - Información de contacto -->
       <div class="card shadow-sm mt-4 border-primary">
           <div class="card-header bg-primary bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-map-marker-alt me-2"></i> Información de Contacto
               </h5>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Dirección principal -->
                   <div class="col-md-6">
                       <div class="form-floating">
                           <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección principal">
                           <label for="direccion">Dirección principal <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Ciudad, provincia, país -->
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="text" name="ciudad" class="form-control" id="ciudad" placeholder="Ciudad">
                           <label for="ciudad">Ciudad <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <input type="text" class="form-control" name="provincia" id="provincia" placeholder="Provincia">
                           <label for="provincia">Provincia <span class="text-danger">*</span></label>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-floating">
                           <select class="form-select" name="pais" id="pais">
                               <option value="ECUADOR" selected>Ecuador</option>
                               <option value="COLOMBIA">Colombia</option>
                               <option value="PERU">Perú</option>
                               <option value="OTRO">Otro</option>
                           </select>
                           <label for="pais">País <span class="text-danger">*</span></label>
                       </div>
                   </div>

                   <!-- Referencias -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <textarea class="form-control" name="referencias" placeholder="Referencias de ubicación" id="referencias" style="height: 80px"></textarea>
                           <label for="referencias">Referencias de ubicación</label>
                       </div>
                   </div>
               </div>
           </div>
       </div>



               <!-- Tercera tarjeta - Información comercial -->
             <div class="card shadow-sm mt-4 border-warning">
                 <div class="card-header bg-warning bg-opacity-10">
                     <h5 class="mb-0">
                         <i class="fas fa-briefcase me-2"></i> Información Comercial
                     </h5>
                 </div>
                 <div class="card-body">
                     <div class="row g-3">
                         <!-- Roles del usuario -->
                         <div class="col-md-12 mb-3">
                             <label class="form-label">Roles del usuario <span class="text-danger">*</span></label>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" name="cliente" type="checkbox" id="rolCliente">
                                 <label class="form-check-label" for="rolCliente">
                                     <i class="fas fa-user-tag me-1"></i> Cliente
                                 </label>
                             </div>
                             <div class="form-check form-check-inline">
                                 <input class="form-check-input" name="proveedor" type="checkbox" id="rolProveedor">
                                 <label class="form-check-label" for="rolProveedor">
                                     <i class="fas fa-truck me-1"></i> Proveedor
                                 </label>
                             </div>
                             <div class="invalid-feedback">Debe seleccionar al menos un rol</div>
                         </div>

                         <!-- Tipo de cliente -->
                         <div class="col-md-4">
                             <div class="form-floating">
                                 <select class="form-select" name="tipoCliente" id="tipoCliente">
                                     <option value="" selected disabled>Seleccione...</option>
                                     <option value="NATURAL">Persona Natural</option>
                                     <option value="JURIDICA">Persona Jurídica</option>
                                     <option value="GUBERNAMENTAL">Gubernamental</option>
                                 </select>
                                 <label for="tipoCliente">Tipo de Cliente <span class="text-danger">*</span></label>
                             </div>
                         </div>

                         <!-- Categoría y segmento -->
                         <div class="col-md-4">
                             <div class="form-floating">
                                 <select class="form-select" name="categoriaCliente" id="categoriaCliente">
                                     <option value="" selected disabled>Seleccione...</option>
                                     <option value="PREMIUM">Premium</option>
                                     <option value="REGULAR">Regular</option>
                                     <option value="OCASIONAL">Ocasional</option>
                                 </select>
                                 <label for="categoriaCliente">Categoría</label>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="form-floating">
                                 <select class="form-select" name="segmento" id="segmento">
                                     <option value="" selected disabled>Seleccione...</option>
                                     <option value="MINORISTA">Minorista</option>
                                     <option value="MAYORISTA">Mayorista</option>
                                     <option value="DISTRIBUIDOR">Distribuidor</option>
                                 </select>
                                 <label for="segmento">Segmento</label>
                             </div>
                         </div>

                         <!-- Límite de crédito -->
                         <div class="col-md-6">
                             <div class="form-floating">
                                 <input type="text" class="form-control" name="limiteCredito" id="limiteCredito" placeholder="Límite de crédito">
                                 <label for="limiteCredito">Límite de crédito ($)</label>
                             </div>
                         </div>


                         <div class="col-md-6">
                             <div class="form-floating">
                                 <select class="form-select" name="empresa" id="empresaRelacionada" required>
                                     <option value="" selected disabled>Seleccione...</option>
                                     <?php
                                      $query_empresas = mysqli_query($conection, "SELECT * FROM empresas_registradas
                                         WHERE   empresas_registradas.iduser = '$iduser' ");
                                         while ($data_empresas = mysqli_fetch_array($query_empresas)) {
                                          echo '<option value="'.$data_empresas['id'].'">'.$data_empresas['nombre_empresa'].'</option>';
                                      }

                                     ?>
                                 </select>
                                 <label for="empresaRelacionada">Empresa Relacionada</label>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

       <!-- Cuarta tarjeta - Documentos y observaciones -->
       <div class="card shadow-sm mt-4 border-info">
           <div class="card-header bg-info bg-opacity-10">
               <h5 class="mb-0">
                   <i class="fas fa-paperclip me-2"></i> Documentos y Observaciones
               </h5>
           </div>
           <div class="card-body">
               <div class="row g-3">
                   <!-- Subida de documentos -->
                   <div class="col-md-6">
                       <label for="documentos" class="form-label">Documentos adjuntos</label>
                       <div class="file-drop-area">
                           <span class="file-msg">Arrastre documentos aquí o haga clic para seleccionar</span>
                           <input class="file-input" name="file" type="file" id="documentos" multiple>
                       </div>
                       <div id="documentosPreview" class="mt-2"></div>
                   </div>

                   <!-- Estado y notificaciones -->
                   <div class="col-md-6">
                       <div class="form-check form-switch mb-3">
                           <input class="form-check-input" type="checkbox" id="clienteActivo" checked>
                           <label class="form-check-label" for="clienteActivo">Cliente activo</label>
                       </div>
                       <div class="form-check form-switch mb-3">
                           <input class="form-check-input" type="checkbox" id="recibirPromociones" checked>
                           <label class="form-check-label" for="recibirPromociones">Recibir promociones</label>
                       </div>
                       <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="notificacionesWhatsapp" checked>
                           <label class="form-check-label" for="notificacionesWhatsapp">Notificaciones por WhatsApp</label>
                       </div>
                   </div>

                   <!-- Observaciones -->
                   <div class="col-md-12">
                       <div class="form-floating">
                           <textarea class="form-control" name="observaciones" placeholder="Observaciones" id="observaciones" style="height: 100px"></textarea>
                           <label for="observaciones">Observaciones</label>
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
               <button class="btn btn-success">
                   <i class="fas fa-save me-1"></i> Guardar Cliente
               </button>
           </div>
       </div>

       <input type="hidden" name="action" value="agregar_nuevo_usuario">

       </form>


       <style media="screen">
         .alerta_agregar_nuevo_usuario{
           text-align: center;
           margin: 10px;
         }
       </style>

       <div class="alerta_agregar_nuevo_usuario"></div>


   </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="java/nuevo_usuario.js?v=9"></script>


</body>
</html>
