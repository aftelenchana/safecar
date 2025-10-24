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
  <title><?php echo $nombre_empresa ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css">
   <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/css/style.css" />
   <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css" />
   <link rel="stylesheet" type="text/css" href="https://guibis.com/home/files/assets/pages/data-table/css/buttons.dataTables.min.css" />
   <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">


  <style>

  </style>
</head>
<body>

  <?php
    require 'scripts/menu.php';
   ?>


   <main class="content" id="contentWrapper">


     <style media="screen">
       .tarjeta_bloque_hr{
         border: 2px;
         margin: 10px;
       }
       .card-header-guibis{
         padding: 3px;
         background: #259bd6 ;
         color: #fff;
         font-weight: bold;
       }
       .card-header-guibis h5{
         padding: 1px;
         margin: 1px;
       }

       .boton_agregar_datos{
         float: right;
       }
       .dt-buttons{
         display: none;
       }
       #tabla_clientes_filter{
         display: none;
       }
     </style>
     <div class="row">
         <div class="col-sm-12">
             <div class="card tarjeta_bloque_hr">

                 <div class="card-header-guibis">
                     <h5>Búsqueda Empresas

                       <button class="btn btn-secondary create-new btn-primary waves-effect waves-light guibis-btn" type="button" id="boton_agregar_aplicaciones">
                         <span>
                           <i class="ri-add-line ri-16px me-sm-2"></i>
                           <span class="d-none d-sm-inline-block">Nueva Aplicación</span>
                         </span>
                       </button>

                      </h5>
                 </div>
                 <div class="card-block">
                   <form action=""  id="procesar_datos" >
                     <div class="row">
       <div class="col-12 col-md mb-3">
           <div class="form-group d-flex align-items-center">
               <label class="label-guibis-sm mr-2">Filtro</label>
               <input type="text" name="filtro" class="form-control input-guibis-sm" required id="filtro" placeholder="Filtro" oninput="cargarTabla()">
           </div>
       </div>
       <div class="col-12 col-md mb-3">
           <div class="form-group d-flex align-items-center">
               <label class="label-guibis-sm mr-2">Rol:</label>
               <select class="form-control input-guibis-sm" name="categoria_recursos_humanos" id="categoria_recursos_humanos" onchange="cargarTabla()">
                 <?php if ($iduser == '279'): ?>
                   <option value="Todos">Todos</option>
                   <option value="Distribuidor">Distribuidor</option>

                 <?php endif; ?>

                 <?php if ($iduser == $user_in): ?>
                   <option value="Todos">Todos</option>
                 <?php endif; ?>


               </select>
           </div>
       </div>
       <div class="col-12 col-md mb-3">
           <div class="form-group d-flex align-items-center">
               <label class="label-guibis-sm mr-2">Parametros Extras:</label>
               <select class="form-control input-guibis-sm" name="tipo_cliente" required id="tipo_cliente" onchange="cargarTabla()">
                   <option value="Todos">Todos</option>
                   <option value="Natural">Natural</option>
                   <option value="Júridico">Júridico</option>
                   <option value="Sin RUC/Cl">Sin RUC/Cl</option>
               </select>
           </div>
       </div>
       <div class="col-12 col-md mb-3">
           <div class="form-group d-flex align-items-center">
               <label class="label-guibis-sm mr-2">Estado:</label>
               <select class="form-control input-guibis-sm" name="estado" required id="estado" onchange="cargarTabla()">
                   <option value="Todos">Todos</option>
                   <option value="Activo">Activo</option>
                   <option value="Inactivo">Inactivo</option>
               </select>
           </div>
       </div>
   </div>


                     <input type="hidden" name="action" value="consultar_datos">
                     <input type="hidden" name="codigo_recurso_humano" id="codigo_recurso_humano" value="">
                   </form>

                   <div class="dt-responsive table-responsive">
                     <style media="screen">
                     #tabla_clientes td {
                       white-space: normal; /* Permite saltos de línea */
                       word-wrap: break-word; /* Asegura que las palabras largas no desborden la celda */
                     }

                     #tabla_clientes td:nth-child(0) { /* Asegúrate de que el índice sea correcto */
                       max-width: 100px; /* Ajusta el ancho máximo según tus necesidades */
                     }

                     </style>
                     <table id="tabla_clientes" class="table table-striped table-bordered nowrap">
                       <thead>
                         <tr>
                           <th></th>
                           <th>#</th>
                           <th>Nombre</th>
                           <th>Estado</th>
                           <th>Clave</th>

                         </tr>
                       </thead>
                       <tbody>

                       </tbody>

                     </table>
                   </div>

                 </div>
                 </div>

             </div>
         </div>


         <div class="modal fade" id="modal_editar_usuario" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
           <div class="modal-dialog modal-xl">
             <div class="modal-content">
               <!-- Header -->
               <div class="modal-header text-white" style="background-color: #263238;">
                 <h5 class="modal-title" id="exampleModalLongTitle">
                   <i class="fas fa-user-edit me-2" style="font-size: 1.2rem;"></i> Aplicación <span class="aplicacion_editar"></span>
                 </h5>
                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;">
                   <span aria-hidden="true">&times;</span>
                 </button>
               </div>

               <!-- Body -->
               <div class="modal-body">
                 <form id="update_aplicaciones">

                   <!-- Nombre -->
                   <div class="mb-3">
                     <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
                     <input type="text" class="form-control input-guibis-sm" name="nombre" id="nombre_edit" required placeholder="Ingrese el nombre">
                   </div>

                   <!-- Estado -->
                   <div class="mb-3">
                     <label for="estado_categoria" class="form-label">Estado</label>
                     <select class="form-control input-guibis-sm" name="estado" id="estado_edit" required>
                       <option value="" disabled selected>Seleccione un estado</option>
                       <option value="activo">Activo</option>
                       <option value="inactivo">Inactivo</option>
                     </select>
                   </div>
                   <!-- Footer del formulario -->
                   <div class="modal-footer mt-4">
                     <input type="hidden" name="action" value="editar_aplicacion" />
                     <input type="hidden" name="aplicacion" id="aplicacion_edit" value="" />
                     <button type="button" class="btn btn-danger btn-guibis-medium" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cerrar</button>
                     <button type="submit" class="btn btn-primary btn-guibis-medium"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                   </div>

                   <!-- Notificación -->
                   <div class="notificacion_editar_aplicacion mt-3"></div>

                 </form>
               </div>
             </div>
           </div>
         </div>



         <div class="modal fade" id="modal_agregar_aplicaciones" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
         <div class="modal-dialog">
           <div class="modal-content">

             <!-- Header -->
             <div class="modal-header">
               <h5 class="modal-title" id="categoriaModalLabel">Agregar Categoría</h5>
             </div>

             <!-- Body -->
             <div class="modal-body">
               <form action="" id="agregar_aplicacion">

                 <!-- Nombre -->
                 <div class="mb-3">
                   <label for="nombre_categoria" class="form-label">Nombre</label>
                   <input type="text" class="form-control input-guibis-sm" name="nombre" id="nombre" required placeholder="Ingrese el nombre">
                 </div>

                 <!-- Estado -->
                 <div class="mb-3">
                   <label for="estado_categoria" class="form-label">Estado</label>
                   <select class="form-control input-guibis-sm" name="estado" id="estado" required>
                     <option value="" disabled selected>Seleccione un estado</option>
                     <option value="activo">Activo</option>
                     <option value="inactivo">Inactivo</option>
                   </select>
                 </div>

                 <!-- Footer -->
                 <div class="modal-footer">
                   <input type="hidden" name="action" value="agregar_categoria">
                   <button type="button" class="btn btn-danger guibis-btn" data-bs-dismiss="modal">
                     Cerrar <i class="fas fa-times-circle"></i>
                   </button>
                   <button type="submit" class="btn btn-primary guibis-btn">
                     Guardar <i class="fas fa-plus"></i>
                   </button>
                 </div>

                 <!-- Notificación -->
                 <div class="notificacion_agregar_aplicacion"></div>
               </form>
             </div>
           </div>
         </div>
       </div>



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
  <script src="java/empresas_all.js?v=8" charset="utf-8"></script>


</body>
</html>
