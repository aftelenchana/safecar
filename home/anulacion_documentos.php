<?php
ob_start();
include "../coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');
session_start();

if (empty($_SESSION['active'])) {
    header('location:/');
    exit;
} else {
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $domain   = $_SERVER['HTTP_HOST'];

    $query_doccumentos  = mysqli_query($conection, "SELECT * FROM usuarios WHERE url_admin = '$domain'");
    $result_documentos  = mysqli_fetch_array($query_doccumentos);

    if ($result_documentos) {
        $url_img_upload  = $result_documentos['url_img_upload'];
        $img_facturacion = $result_documentos['img_facturacion'];
        $nombre_empresa  = $result_documentos['nombre_empresa'];
        $img_sistema     = $url_img_upload.'/home/img/uploads/'.$img_facturacion;
    } else {
        $img_sistema = '/img/guibis.png';
    }
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Consola — Anulación de Facturas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilos/index.css">
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
  <link rel="stylesheet" href="estilos/consulta.css">
  <style>
    /* Ajustes ligeros para inputs sensibles */
    #clave[type="password"] { letter-spacing: 0.04em; }
    .pill{display:inline-block;padding:.35rem .6rem;border-radius:999px;background:#f1f3f5}
    pre.code{background:#0f172a;color:#e2e8f0;border-radius:.5rem;padding:1rem;overflow:auto}
    .dev-wrap{background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem}
    .dev-header{background:#fff;border-bottom:1px solid #e2e8f0;border-top-left-radius:.75rem;border-top-right-radius:.75rem}
  </style>
</head>
<body>

<?php require 'scripts/menu.php';
// Endpoint ANULACIÓN FACTURAS
$ENDPOINT_API = "https://api.guibis.com/dev/ANULACION-FACTURAS/";

// Cargar aplicaciones del usuario (para obtener KEY)
$apps = [];
if (!empty($iduser) && $iduser > 0) {
  $res = mysqli_query($conection, "SELECT id, nombre, estado, key_api FROM aplicaciones_api WHERE iduser = '$iduser' AND estado = 'activo' ORDER BY id DESC");
  while ($row = mysqli_fetch_assoc($res)) $apps[] = $row;
}
?>

<main class="content" id="contentWrapper">
  <section id="consola-apis" class="p-0" data-endpoint="<?= htmlspecialchars($ENDPOINT_API) ?>">
    <div class="dev-wrap">
      <div class="dev-header p-3 d-flex align-items-center justify-content-between flex-wrap">
        <div class="me-2">
          <h2 class="h5 mb-0"><i class="fa-solid fa-terminal me-2"></i>Consola de APIs — Anulación de Facturas</h2>
          <small class="text-muted">Selecciona tu aplicación, completa los campos y envía (POST JSON)</small>
        </div>

        <!-- KEY completa + botón copiar -->
        <div id="keyInfo" class="d-none d-flex align-items-center gap-2 mt-2 mt-sm-0">
          <span class="badge text-bg-light" id="keyBadge" title="KEY de la aplicación">
            <i class="fa-solid fa-key me-1"></i> KEY: <code id="keyPreview"></code>
          </span>
          <button id="btnCopyKey" class="btn btn-sm btn-outline-secondary" title="Copiar KEY">
            <i class="fa-regular fa-clone me-1"></i> Copiar KEY
          </button>
        </div>
      </div>

      <div class="p-3">
        <!-- Aplicación -->
        <div class="mb-3">
          <label class="form-label fw-semibold"><i class="fa-solid fa-cubes-stacked me-2"></i>Aplicación</label>
          <select id="selectApp" class="form-select">
            <option value="">— Selecciona una aplicación —</option>
            <?php foreach($apps as $app): ?>
              <option
                value="<?= htmlspecialchars($app['key_api']) ?>"
                data-nombre="<?= htmlspecialchars($app['nombre'] ?: ('APP #'.$app['id'])) ?>"
                data-estado="<?= htmlspecialchars($app['estado'] ?? 'ACTIVO') ?>"
              >
                <?= htmlspecialchars($app['nombre'] ?: ('APP #'.$app['id'])) ?> (<?= strtoupper($app['estado'] ?? 'ACTIVO') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Área de datos de anulación -->
        <div id="searchArea" class="row g-3 d-none">
          <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="fa-solid fa-id-card me-2"></i>Usuario SRI</label>
            <input type="text" id="usuario" class="form-control" placeholder="Ej: 0123456789">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="fa-solid fa-lock me-2"></i>Clave SRI</label>
            <input type="password" id="clave" class="form-control" placeholder="TuPasswordSRI#2025">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold"><i class="fa-solid fa-barcode me-2"></i>Clave de Acceso</label>
            <input type="text" id="claveAcceso" class="form-control" placeholder="0107202501010291...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa-solid fa-address-card me-2"></i>Identificación</label>
            <input type="text" id="identificacion" class="form-control" placeholder="Cédula/RUC (10 o 13 dígitos)">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa-solid fa-envelope me-2"></i>Correo</label>
            <input type="email" id="correo" class="form-control" placeholder="tucorreo@dominio.com">
          </div>

          <div class="col-12">
            <button id="btnEnviar" class="btn btn-primary">
              <i class="fa-solid fa-paper-plane me-2"></i>Enviar anulación
            </button>
          </div>
        </div>

        <!-- Endpoint visible -->
        <div class="mt-3">
          <span class="pill"><i class="fa-solid fa-link me-1"></i>
            Endpoint: <code><?= htmlspecialchars($ENDPOINT_API) ?></code>
          </span>
        </div>

        <!-- Alertas / Resumen / JSON -->
        <div id="alertArea" class="mt-3 d-none"></div>
        <div id="summary" class="mt-3 d-none"></div>

        <!-- JSON ENVIADO -->
        <div id="jsonReqBox" class="mt-3 d-none">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0"><i class="fa-solid fa-paper-plane me-2"></i>JSON enviado</h6>
            <div class="d-flex gap-2">
              <button id="btnCopyReq" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-copy me-1"></i>Copiar
              </button>
              <button id="btnDownloadReq" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-download me-1"></i>Descargar
              </button>
            </div>
          </div>
          <pre id="jsonReqRaw" class="code">{}</pre>
        </div>

        <!-- JSON RESPUESTA -->
        <div id="jsonBox" class="mt-3 d-none">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0"><i class="fa-solid fa-code me-2"></i>Respuesta JSON</h6>
            <div class="d-flex gap-2">
              <button id="btnCopy" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-copy me-1"></i>Copiar
              </button>
              <button id="btnDownload" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-download me-1"></i>Descargar
              </button>
            </div>
          </div>
          <pre id="jsonRaw" class="code">{}</pre>
        </div>
      </div>
    </div>
  </section>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="java/index.js" charset="utf-8"></script>
<script src="java/anulacion_documentos.js?v=2" charset="utf-8"></script>

</body>
</html>
