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
  <title>Cuenta Bancaria</title>
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link rel="stylesheet" href="estilos/index.css">
   <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
  <style>

  </style>
</head>
<body>

  <?php
    require 'scripts/menu.php';

    $query = mysqli_query($conection, "SELECT * FROM `saldo_total_leben`
     INNER JOIN usuarios ON saldo_total_leben.idusuario = usuarios.id
     WHERE usuarios.id = $iduser ");
     $result_bq_r = mysqli_fetch_array($query);
     $qr_bancario     =  $result_bq_r['qr_bancario'];
     $url_qr_bancario = $result_bq_r['url_qr_bancario'];

    $query = mysqli_query($conection, "SELECT * FROM usuarios WHERE usuarios.id = $iduser");
    $result = mysqli_fetch_array($query);
    $email_usuario = $result['email'];
    $nombres_usuario = $result['nombres'];
    $apellidos_usuario = $result['apellidos'];
    $cuenta_bancaria = $result['banco_pichincha'];
    $banco_guayaquil = $result['banco_guayaquil'];
    $banco_produbanco = $result['banco_produbanco'];
    $banco_pacifico = $result['banco_pacifico'];
    $camara_comercio_ambato = $result['camara_comercio_ambato'];
    $mushuc_runa = $result['mushuc_runa'];
   ?>


   <main class="content" id="contentWrapper">

     <div class="row">
       <div class="col-12">
         <div class="card border-0">

           <!-- Acciones principales -->
           <div class="container py-4">
             <div class="row g-4">
               <div class="col-12 col-md-6">
                 <div class="card h-100 text-center shadow-sm border-0">
                   <div class="card-body p-4">
                     <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info-subtle p-3 mb-3">
                       <i class="fa-solid fa-circle-plus fa-2x text-info"></i>
                     </div>
                     <h5 class="card-title mb-2">Recargar en mi Cuenta</h5>
                     <p class="card-text text-body-secondary mb-3">
                       Recarga mediante nuestras cuentas bancarias para comprar y vender en todas las tiendas del sitio.
                     </p>
                     <button type="button" class="btn btn-primary btn-sm px-3" id="boton_deposito_guibis">
                       <i class="fa-solid fa-arrow-rotate-right me-2"></i>Recargar en mi Cuenta
                     </button>
                   </div>
                 </div>
               </div>
             </div>
           </div>

           <!-- QR / Resumen del usuario -->
           <div class="container">
             <div class="row justify-content-center">
               <div class="col-12">
                 <div class="text-center mb-3">
                   <a href="/home/active-token" class="d-inline-block">
                     <?php if (empty($qr_bancario) || empty($url_qr_bancario)): ?>
                       <?php
                         $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
                         $domain = $_SERVER['HTTP_HOST'];
                         $url = $protocol . $domain;

                         $img_nombre = 'guibis_user' . md5(date('d-m-Y H:m:s'));
                         $qr_img = $img_nombre . '.png';
                         $contenido = md5(date('d-m-Y H:m:s') . $iduser);

                         $direccion = 'img/qr/';
                         $filename = $direccion . $qr_img;
                         $tamanio = 7;
                         $level = 'H';
                         $frameSize = 5;

                         require 'QR/phpqrcode/qrlib.php';
                         QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);

                         $query_insert = mysqli_query($conection,"UPDATE usuarios SET url_qr_bancario='$url',qr_bancario='$qr_img' WHERE id = '$iduser'");
                       ?>
                       <img class="imagen_token_seguridad img-fluid rounded-3 shadow-sm"
                            src="<?php echo $url ?>/home/img/qr/<?php echo $qr_img ?>"
                            alt="QR de seguridad">
                     <?php endif; ?>

                     <?php if (!empty($qr_bancario)): ?>
                       <img class="imagen_token_seguridad img-fluid rounded-3 shadow-sm"
                            src="<?php echo $url_qr_bancario ?>/home/img/qr/<?php echo $qr_bancario ?>"
                            alt="QR de seguridad">
                     <?php endif; ?>
                   </a>
                 </div>

                 <div class="card shadow-sm border-0">
                   <div class="card-header bg-body-tertiary border-0">
                     <div class="d-flex align-items-center gap-2">
                       <i class="fa-solid fa-user-shield"></i>
                       <span class="fw-semibold">Resumen de Cuenta</span>
                     </div>
                   </div>
                   <div class="card-body">
                     <div class="table-responsive">
                       <table class="table align-middle mb-0">
                         <tbody>
                           <tr>
                             <td class="py-3">
                               <i class="fa-solid fa-user me-2 text-primary"></i>
                               <span class="fw-semibold">Nombre:</span>
                               <?php echo $result['nombres'] . " " . $result['apellidos']; ?>
                             </td>
                             <td class="py-3">
                               <i class="fa-solid fa-wallet me-2 text-success"></i>
                               <span class="fw-semibold">Saldo Total:</span>
                               <span class="badge bg-success-subtle text-success ms-1 saldo_total">
                                 $<?php echo round($result_bq_r['cantidad'], 2) ?>
                               </span>
                             </td>
                             <td class="py-3">
                               <i class="fa-solid fa-envelope me-2 text-secondary"></i>
                               <span class="fw-semibold">Email:</span>
                               <?php echo $result['email']; ?>
                             </td>
                           </tr>
                           <tr>
                             <td class="py-3">
                               <i class="fa-solid fa-id-card me-2 text-secondary"></i>
                               <span class="fw-semibold">Cédula:</span>
                               <?php echo $result['numero_identidad']; ?>
                             </td>
                             <td class="py-3">
                               <i class="fa-solid fa-mobile-screen-button me-2 text-secondary"></i>
                               <span class="fw-semibold">Celular:</span>
                               <?php echo $result['celular']; ?>
                             </td>
                             <td class="py-3">
                               <i class="fa-solid fa-phone me-2 text-secondary"></i>
                               <span class="fw-semibold">Teléfono:</span>
                               <?php echo $result['telefono']; ?>
                             </td>
                           </tr>
                         </tbody>
                       </table>
                     </div>
                   </div>
                 </div>

               </div>
             </div>
           </div>

           <!-- Accesos rápidos -->
           <div class="histo_banca">
             <div class="container py-4">
               <div class="row row-cols-1 row-cols-md-2 g-4 mb-2">
                 <div class="col">
                   <a href="/home/active-token" class="card h-100 text-center text-decoration-none shadow-sm border-0">
                     <div class="card-body">
                       <i class="fa-solid fa-qrcode fa-2x mb-2 text-warning"></i>
                       <h6 class="card-title m-0">Activar Token</h6>
                     </div>
                   </a>
                 </div>
                 <div class="col">
                   <a href="creditos" class="card h-100 text-center text-decoration-none shadow-sm border-0">
                     <div class="card-body">
                       <i class="fa-solid fa-wallet fa-2x mb-2 text-success"></i>
                       <h6 class="card-title m-0">Créditos Directos</h6>
                     </div>
                   </a>
                 </div>
               </div>

               <div class="row row-cols-1 row-cols-md-2 g-4">
                 <div class="col">
                   <a href="historial-depositos" class="card h-100 text-center text-decoration-none shadow-sm border-0">
                     <div class="card-body">
                       <i class="fa-solid fa-file-invoice-dollar fa-2x mb-2 text-warning"></i>
                       <h6 class="card-title m-0">Historial Depósitos</h6>
                     </div>
                   </a>
                 </div>
                 <div class="col">
                   <a href="historial-retiros" class="card h-100 text-center text-decoration-none shadow-sm border-0">
                     <div class="card-body">
                       <i class="fa-solid fa-hand-holding-dollar fa-2x mb-2 text-success"></i>
                       <h6 class="card-title m-0">Historial Retiros</h6>
                     </div>
                   </a>
                 </div>
               </div>
             </div>
           </div>

           <!-- Historial bancario -->
           <div class="contenedor_fgt">
             <div class="card shadow-sm border-0">
               <div class="card-header bg-body-tertiary border-0">
                 <div class="d-flex align-items-center gap-2">
                   <i class="fa-solid fa-clock-rotate-left"></i>
                   <span class="fw-semibold">Historial bancario</span>
                 </div>
               </div>
               <div class="card-body">
                 <div class="table-responsive">
                   <table class="table table-striped table-hover align-middle">
                     <thead class="table-light">
                       <tr>
                         <th>Código</th>
                         <th>Total</th>
                         <th>Subtotal</th>
                         <th>Comisión</th>
                         <th>Fecha</th>
                         <th>Acción</th>
                         <th>Reportar</th>
                       </tr>
                     </thead>
                     <tbody>
                       <?php
                         $sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM historial_bancario
                           INNER JOIN usuarios ON usuarios.id = historial_bancario.id_admin
                           WHERE id_usuario = $iduser");
                         $result_register = mysqli_fetch_array($sql_registe);
                         $total_registro = $result_register['total_registro'];
                         $por_pagina = 20;
                         $pagina = empty($_GET['pagina']) ? 1 : (int)$_GET['pagina'];
                         $desde = ($pagina-1)*$por_pagina;
                         $total_paginas = max(1, ceil($total_registro/$por_pagina));

                         mysqli_query($conection,"SET lc_time_names = 'es_ES'");
                         $query_lista = mysqli_query($conection,"SELECT historial_bancario.cantidad,historial_bancario.cantidad_parcial,historial_bancario.id,
                               historial_bancario.cantidad_comision, DATE_FORMAT(historial_bancario.fecha, '%W %d de %b %Y %H:%i:%s') as 'fecha',
                               historial_bancario.accion, usuarios.nombres, usuarios.apellidos
                             FROM historial_bancario
                             INNER JOIN usuarios ON usuarios.id = historial_bancario.id_admin
                             WHERE id_usuario = $iduser
                             ORDER BY historial_bancario.fecha DESC
                             LIMIT $desde,$por_pagina");
                         while ($data_lista = mysqli_fetch_array($query_lista)) {
                           $id = $data_lista['id'];
                           $accion = $data_lista['accion'];
                       ?>
                       <tr>
                         <td data-titulo="Código">
                           <?php if ($accion == 'Compra'): ?>
                             <a class="link-primary text-decoration-none" href="detalles-movimiento-compra?movimiento_compra=<?php echo $id ?>">
                               #<?php echo $data_lista['id'] ?>
                             </a>
                           <?php elseif ($accion == 'Venta'): ?>
                             <a class="link-primary text-decoration-none" href="detalles-movimiento-venta?movimiento_venta=<?php echo $id ?>">
                               #<?php echo $data_lista['id'] ?>
                             </a>
                           <?php else: ?>
                             #<?php echo $data_lista['id'] ?>
                           <?php endif; ?>
                         </td>
                         <td data-titulo="Total">
                           $<?php echo number_format($data_lista['cantidad'],2)?>
                         </td>
                         <td data-titulo="Subtotal">
                           $<?php echo number_format($data_lista['cantidad_parcial'],2)?>
                         </td>
                         <td data-titulo="Comisión">
                           $<?php echo number_format($data_lista['cantidad_comision'],2)?>
                         </td>
                         <td data-titulo="Fecha">
                           <?php echo mb_strtoupper($data_lista['fecha']); ?>
                         </td>
                         <td data-titulo="Acción">
                           <span class="badge rounded-pill
                             <?php echo $accion==='Compra' ? 'text-bg-primary' : ($accion==='Venta' ? 'text-bg-success' : 'text-bg-secondary'); ?>">
                             <?php echo $accion; ?>
                           </span>
                         </td>
                         <td data-titulo="Reportar">
                           <?php
                             $query_movimiento = mysqli_query($conection, "SELECT 1 FROM reporte_movimientos WHERE iduser = $iduser AND idmovimiento = '$id' LIMIT 1");
                             $result_movimientos = mysqli_num_rows($query_movimiento);
                           ?>
                           <?php if ($result_movimientos > 0): ?>
                             <button type="button"
                                     class="btn btn-warning btn-sm boton_reportar_movimiento"
                                     movimiento="<?php echo $data_lista['id'] ?>"
                                     id="boton_reportar_movimiento">
                               <i class="fa-solid fa-flag me-1"></i>Reportado #<?php echo $data_lista['id'] ?>
                             </button>
                           <?php else: ?>
                             <button type="button"
                                     class="btn btn-info btn-sm text-white boton_reportar_movimiento"
                                     movimiento="<?php echo $data_lista['id'] ?>"
                                     id="boton_reportar_movimiento">
                               <i class="fa-solid fa-bullhorn me-1"></i>Reportar #<?php echo $data_lista['id'] ?>
                             </button>
                           <?php endif; ?>
                         </td>
                       </tr>
                       <?php } ?>
                     </tbody>
                   </table>
                 </div>

                 <!-- Paginación Bootstrap -->
                 <nav aria-label="Paginación de historial" class="mt-3">
                   <ul class="pagination pagination-sm justify-content-center mb-0">
                     <?php if ($pagina > 1): ?>
                       <li class="page-item">
                         <a class="page-link" href="?pagina=1" aria-label="Primera">
                           <span aria-hidden="true">&laquo;|</span>
                         </a>
                       </li>
                       <li class="page-item">
                         <a class="page-link" href="?pagina=<?php echo $pagina-1; ?>" aria-label="Anterior">
                           <span aria-hidden="true">&laquo;</span>
                         </a>
                       </li>
                     <?php endif; ?>

                     <?php
                       // Ventana de páginas centrada
                       $start = max(1, $pagina - 2);
                       $end = min($total_paginas, $pagina + 2);
                       for ($i=$start; $i <= $end; $i++):
                     ?>
                       <li class="page-item <?php echo $i==$pagina ? 'active' : ''; ?>">
                         <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                       </li>
                     <?php endfor; ?>

                     <?php if ($pagina < $total_paginas): ?>
                       <li class="page-item">
                         <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>" aria-label="Siguiente">
                           <span aria-hidden="true">&raquo;</span>
                         </a>
                       </li>
                       <li class="page-item">
                         <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>" aria-label="Última">
                           <span aria-hidden="true">|&raquo;</span>
                         </a>
                       </li>
                     <?php endif; ?>
                   </ul>
                 </nav>

               </div>
             </div>
           </div>

         </div>
       </div>
     </div>

     <style>
       /* Imagen QR responsiva */
       .imagen_token_seguridad{ width: 14%; max-width: 160px; }
       @media (max-width: 768px){ .imagen_token_seguridad{ width: 48%; } }

       /* Bloque accesos rápidos con fondo suave */
       .histo_banca .container{
         max-width: 100%;
         background: linear-gradient(90deg, #ffffff 0%, #f6f7fb 100%);
         border-radius: .75rem;
       }

       .histo_banca .card{
         background: #fff;
         transition: transform .15s ease, box-shadow .15s ease;
       }
       .histo_banca .card:hover{
         transform: translateY(-2px);
         box-shadow: 0 .5rem 1rem rgba(0,0,0,.08);
       }

       /* Compactar celdas en móviles */
       @media (max-width: 576px){
         .table td, .table th{ white-space: nowrap; }
       }
     </style>



     <div class="modal fade" id="modal_depisito_guibis" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
       <div class="modal-content shadow-lg border-0">

         <div class="modal-header bg-light border-bottom">
         <div class="d-flex align-items-center gap-2 text-dark">
           <i class="fa-solid fa-circle-plus text-primary"></i>
           <h5 class="modal-title mb-0" id="proveedorModalLabel">Recargar en mi Cuenta</h5>
         </div>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
       </div>

         <!-- Body -->
         <div class="modal-body p-4">
           <form action="" method="post" name="add_comprobante" id="add_comprobante" onsubmit="event.preventDefault(); sendData_add_comprobante2();">

             <!-- Comprobante (imagen) -->
             <div class="mb-3">
               <label for="file_boucher_deposito" class="form-label fw-semibold">
                 Comprobante de depósito <span class="text-danger">*</span>
               </label>
               <div class="input-group">
                 <span class="input-group-text"><i class="fa-solid fa-file-image"></i></span>
                 <input
                   type="file"
                   class="form-control"
                   name="foto"
                   id="file_boucher_deposito"
                   accept="image/png, image/jpeg"
                   required
                 />
               </div>
               <div class="form-text">
                 Formatos permitidos: <code>.png</code>, <code>.jpg</code>, <code>.jpeg</code>. Tamaño legible y sin recortes.
               </div>
             </div>

             <!-- Banco -->
             <div class="mb-3">
               <label for="tipo_banco" class="form-label fw-semibold">
                 Elija el banco a depositar <span class="text-danger">*</span>
               </label>
               <div class="input-group">
                 <span class="input-group-text"><i class="fa-solid fa-building-columns"></i></span>
                 <select class="form-select input-guibis-sm" name="tipo_banco" id="tipo_banco" required>
                   <?php
                     $query_cuentas_bancarias = mysqli_query($conection, "SELECT cuentas_bancarias_factu.id,
                       cuentas_bancarias_factu.numero_cuenta,
                       cuentas_bancarias_factu.titular_cuenta,
                       cuentas_bancarias_factu.tipo_cuenta,
                       empresas_registradas.email,
                       cuentas_bancarias_factu.nombre_cuenta
                       FROM cuentas_bancarias_factu
                       INNER JOIN empresas_registradas ON empresas_registradas.id = cuentas_bancarias_factu.empresa
                       WHERE cuentas_bancarias_factu.iduser= '279'
                         AND cuentas_bancarias_factu.estatus = 1");
                     while ($data_cuenta_bancaria = mysqli_fetch_array($query_cuentas_bancarias)) {
                       echo '<option value="' . $data_cuenta_bancaria['id'] . '">'
                           . $data_cuenta_bancaria['nombre_cuenta'] . ' — '
                           . $data_cuenta_bancaria['numero_cuenta'] . ' — '
                           . $data_cuenta_bancaria['titular_cuenta'] . ' — '
                           . $data_cuenta_bancaria['tipo_cuenta']
                           . '</option>';
                     }
                   ?>
                 </select>
               </div>
             </div>

             <!-- Aviso -->
             <div class="alert alert-success d-flex align-items-start gap-2 mb-3" role="alert">
               <i class="fa-solid fa-circle-info mt-1"></i>
               <div>
                 <strong>Alerta:</strong> al realizar una transferencia, ingres ael siguiente correo
                 <a href="mailto:financiero@guibis.com" class="alert-link">financiero@guibis.com</a>.
               </div>
             </div>

             <!-- Monto -->
             <div class="mb-3">
               <label for="cantidad_deposito" class="form-label fw-semibold">Cantidad del depósito <span class="text-danger">*</span></label>
               <div class="input-group">
                 <span class="input-group-text"><i class="fa-solid fa-dollar-sign"></i></span>
                 <input
                   type="number"
                   step="0.01"
                   min="0"
                   class="form-control input-guibis-sm"
                   id="cantidad_deposito"
                   name="cantidad"
                   placeholder="12.34"
                   required
                 />
               </div>
               <div class="form-text">Usa punto decimal. Ej: 25.50</div>
             </div>

             <!-- Número único -->
             <div class="mb-4">
               <label for="numero_unico" class="form-label fw-semibold">Número único del depósito <span class="text-danger">*</span></label>
               <div class="input-group">
                 <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                 <input
                   type="text"
                   inputmode="numeric"
                   pattern="[0-9]{5,}"
                   class="form-control input-guibis-sm"
                   id="numero_unico"
                   name="numero_unico"
                   placeholder="1234567"
                   required
                 />
               </div>
               <div class="form-text">Solo números, mínimo 5 dígitos (incluye ceros a la izquierda si aplica).</div>
             </div>

             <!-- Footer -->
             <div class="modal-footer border-0 pt-0">
               <input type="hidden" name="action" value="deposito_comprobante" required>


               <button type="button" class="btn btn-outline-secondary btn-guibis-medium" data-bs-dismiss="modal">
                 <i class="fa-solid fa-xmark me-1"></i>Cerrar
               </button>
               <button type="submit" id="accionar_formulario_re" class="btn btn-primary btn-guibis-medium">
                 <i class="fa-solid fa-paper-plane me-1"></i>Realizar Depósito
               </button>
             </div>

             <div class="notificacion_deposito_guibis me-auto"></div>

           </form>
         </div>

       </div>
     </div>
   </div>

   <style>
     /* Encabezado con marca */
     .bg-guibis-dark{
       background: #263238;
       position: relative;
       overflow: hidden;
     }
     .bg-guibis-dark::after{
       content:"";
       position:absolute; inset:0;
       background:
         radial-gradient(120px 120px at 100% 100%, rgba(255,255,255,.08), transparent 70%),
         radial-gradient(160px 160px at 0% 0%, rgba(255,255,255,.06), transparent 70%);
       pointer-events:none;
     }

     /* Inputs y grupos */
     .input-group-text{
       background: #f7f8fa;
     }

     /* Botones */
     .btn-guibis-medium{
       padding: .5rem 1rem;
       border-radius: .5rem;
     }
     .notificacion_deposito_guibis{
       text-align: center;
     }

     /* Modal */
     #modal_depisito_guibis .modal-content{
       border-radius: .8rem;
     }
   </style>



   </main>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java/index.js" charset="utf-8"></script>
  <script type="text/javascript" src="jquery_bancario/depositos.js?v=4"></script>


</body>
</html>
