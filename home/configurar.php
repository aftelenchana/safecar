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
  <title>Configurar Cuenta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos/index.css">
  <link rel="stylesheet" href="estilos/configurar.css?v=2">
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">

</head>
<body>

  <?php
  if ($_SESSION['rol'] == 'cuenta_empresa') {
     require 'scripts/menu.php';
 }

   ?>

   <main class="content" id="contentWrapper">
         <div class="container py-5">
           <div class="profile-container">
             <div class="profile-card">
               <!-- Encabezado con foto de perfil -->
               <form class="forms-sample" name="editar_cuenta" id="editar_cuenta" onsubmit="event.preventDefault(); sendData_editar_cuenta();" enctype="multipart/form-data">
                 <!-- Input oculto para la imagen -->
                 <input type="file" id="imagenPerfil" name="foto" accept="image/*" style="display: none;">

                 <div class="profile-header">
                   <div class="avatar-upload">
                     <div class="avatar-preview">
                       <img id="profileImage" src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_logo ?>" alt="Foto de perfil">
                     </div>
                     <button type="button" class="btn-upload" onclick="document.getElementById('imagenPerfil').click()">
                       <i class="fas fa-camera"></i>
                     </button>
                   </div>
                   <h3 id="profileName"><?php echo $nombres ?></h3>
                   <p class="mb-0">Administrador del sistema</p>
                 </div>

              <!-- Formulario de configuración -->
              <div class="form-section">
                <h4 class="section-title"><i class="fas fa-user-cog me-2"></i> Información Personal</h4>

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                      <input type="text" class="form-control" id="identificacion" placeholder="Identificación" value="<?php echo $numero_identidad ?>">
                      <label for="identificacion">Identificación</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-building"></i></span>
                      <input type="text" class="form-control" name="nombre_empresa" id="empresa" placeholder="Empresa" value="<?php echo $nombre_empresa ?>">
                      <label for="empresa">Empresa</label>
                    </div>
                  </div>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-md-12">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                      <input type="text" class="form-control" name="nombres" id="apellidos" placeholder="Nombres y Apellidos" value="<?php echo $nombres ?>">
                      <label for="nombres">Nombres y Apellidos</label>
                    </div>
                  </div>
                </div>

                <h4 class="section-title mt-5"><i class="fas fa-phone-alt me-2"></i> Contacto</h4>

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                      <input type="text" name="celular" class="form-control" id="celular" placeholder="Celular" value="<?php echo $celular_user ?>">
                      <label for="celular">Celular</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                      <input type="tel" class="form-control" id="telefono" placeholder="Teléfono" value="<?php echo $telefono_user ?>">
                      <label for="telefono">Teléfono</label>
                    </div>
                  </div>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-md-12">
                    <div class="input-group form-floating form-floating-custom">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                      <input type="email" class="form-control" name="email" readonly id="email" placeholder="Correo electrónico" value="<?php echo $email_user ?>">
                      <label for="email">Correo electrónico</label>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end mt-5">
                  <button class="btn btn-save text-white">
                    <i class="fas fa-save me-1"></i> Guardar Cambios
                  </button>
                </div>
              </div>

                <input type="hidden" name="action" value="editar_perfil">
            </form>

            <style media="screen">
              .alerta_editar_perfil{
                text-align: center;
                margin: 10px;
              }
            </style>

            <div class="alerta_editar_perfil"></div>
          </div>
        </div>
      </div>
   </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <script>
     // Evaluación de la imagen seleccionada
     document.getElementById('imagenPerfil').addEventListener('change', function(e) {
         const file = e.target.files[0];

         if (!file) return;

         // Validar tipo de archivo
         const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
         if (!validTypes.includes(file.type)) {
             alert('Por favor selecciona una imagen válida (JPEG, PNG o GIF)');
             return;
         }

         // Validar tamaño (ejemplo: máximo 2MB)
         const maxSize = 2 * 1024 * 1024; // 2MB
         if (file.size > maxSize) {
             alert('La imagen no debe exceder los 2MB');
             return;
         }

         // Crear vista previa
         const reader = new FileReader();
         reader.onload = function(event) {
             document.getElementById('profileImage').src = event.target.result;
         };
         reader.readAsDataURL(file);
     });

     // Actualizar nombre en el encabezado al modificar el campo
     document.getElementById('nombres').addEventListener('input', function() {
       const apellidos = document.getElementById('apellidos').value;
       document.getElementById('profileName').textContent = `${this.value} ${apellidos}`;
     });

     document.getElementById('apellidos').addEventListener('input', function() {
       const nombres = document.getElementById('nombres').value;
       document.getElementById('profileName').textContent = `${nombres} ${this.value}`;
     });
   </script>
  <script src="cuenta/perfil.js"></script>
</body>
</html>
