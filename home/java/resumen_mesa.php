<?php
ob_start();

/* =========================
   CONFIGURACIÓN INICIAL
   ========================= */
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
$domain   = $_SERVER['HTTP_HOST'];
$url      = $protocol . $domain;

require "coneccion.php";
mysqli_set_charset($conection, 'utf8mb4');

session_start();
if (!empty($_SESSION['active'])) {
  header('location:home/');
  exit;
}

// Token de sesión seguro
if (!isset($_COOKIE['session_token'])) {
  $token = bin2hex(random_bytes(32));
  setcookie('session_token', $token, time() + (86400 * 30), "/", null, true, true);
}

/* =========================
   CONFIGURACIÓN DEL SISTEMA
   ========================= */
$query_config = mysqli_query($conection, "SELECT * FROM configuraciones");
$config       = mysqli_fetch_array($query_config);
$ambito_area  = $config['ambito'] ?? 'produccion';

/* =========================
   OBTENER IP REAL
   ========================= */
function getRealIP() {
  if (!empty($_SERVER["HTTP_CLIENT_IP"])) return $_SERVER["HTTP_CLIENT_IP"];
  if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
  if (!empty($_SERVER["HTTP_X_FORWARDED"])) return $_SERVER["HTTP_X_FORWARDED"];
  if (!empty($_SERVER["HTTP_FORWARDED_FOR"])) return $_SERVER["FORWARDED_FOR"];
  if (!empty($_SERVER["HTTP_FORWARDED"])) return $_SERVER["HTTP_FORWARDED"];
  return $_SERVER["REMOTE_ADDR"];
}
$direccion_ip = ($ambito_area === 'prueba') ? '186.42.9.232' : getRealIP();

/* =========================
   DATOS DEL DOMINIO
   ========================= */
$query_user  = mysqli_query($conection, "SELECT * FROM usuarios WHERE url_admin = '$domain'");
$user_data   = mysqli_fetch_array($query_user);

if ($user_data) {
  $url_img_upload = $user_data['url_img_upload'];
  $img_facturacion = $user_data['img_facturacion'];
  $nombre_empresa  = $user_data['nombre_empresa'] ?: 'Guibis';
  $img_sistema     = $url_img_upload . '/home/img/uploads/' . $img_facturacion;
} else {
  $nombre_empresa  = 'Guibis';
  $img_sistema     = '/img/guibis.png';
}

/* =========================
   INFORMACIÓN DE MESA (URL canónica /resumen_mesa?mesa=TOKEN)
   ========================= */
$mesa_code   = isset($_GET['mesa']) ? trim($_GET['mesa']) : null;
$mesa_data   = null;
$mesa_items  = [];
$mesa_totales = ['subtotal'=>0,'iva'=>0,'descuento'=>0,'total'=>0];
$iduser      = null;

if ($mesa_code) {
  if (preg_match('#^https?://#i', $mesa_code)) {
    $parts = parse_url($mesa_code);
    $qs = [];
    if (!empty($parts['query'])) parse_str($parts['query'], $qs);
    $mesa_code = isset($qs['mesa']) ? trim($qs['mesa']) : '';
  }
  $qr_url_canonica = $url . '/resumen_mesa?mesa=' . $mesa_code;

  mysqli_query($conection, "SET lc_time_names = 'es_ES'");
  $mesa_query = mysqli_query(
    $conection,
    "SELECT id, iduser FROM rst_mesas WHERE BINARY qr_contenido = '$qr_url_canonica' LIMIT 1"
  );
  $mesa_data  = mysqli_fetch_array($mesa_query);

  if ($mesa_data) {
    $codigo_mesa = $mesa_data['id'];
    $iduser      = $mesa_data['iduser'];

    // Productos del pedido (resumen)
    $sql_items = "
      SELECT DATE_FORMAT(tp.fecha, '%W %d de %b %Y %H:%i:%s') AS fecha_f,
             tp.id, tp.nota_extra, tp.cantidad_producto,
             pv.nombre AS nombre_producto, pv.idproducto,
             pv.valor_unidad_final_con_impuestps AS punitario
      FROM tomar_pedidos_cafe_tech tp
      INNER JOIN producto_venta pv ON pv.idproducto = tp.id_producto
      WHERE tp.estatus = '1' AND tp.codigo_mesa = '$codigo_mesa'
      ORDER BY tp.id_producto ASC";
    $query_items = mysqli_query($conection, $sql_items);
    while ($item = mysqli_fetch_array($query_items)) {
      $item['pfinal'] = $item['cantidad_producto'] * $item['punitario'];
      $mesa_items[] = $item;
    }

    // Totales
    $sql_totales = "
      SELECT
        SUM(tp.cantidad_producto * tp.valor_unidad) AS subtotal,
        SUM(tp.iva_producto) AS iva,
        SUM(tp.descuento) AS descuento
      FROM tomar_pedidos_cafe_tech tp
      WHERE tp.codigo_mesa = '$codigo_mesa'";
    $query_totales = mysqli_query($conection, $sql_totales);
    $t = mysqli_fetch_array($query_totales) ?: ['subtotal'=>0,'iva'=>0,'descuento'=>0];
    $mesa_totales['subtotal']  = round($t['subtotal'], 2);
    $mesa_totales['iva']       = round($t['iva'], 2);
    $mesa_totales['descuento'] = round($t['descuento'], 2);
    $mesa_totales['total']     = $mesa_totales['subtotal'] + $mesa_totales['iva'] - $mesa_totales['descuento'];
  }
}

/* ============================================================
   ENDPOINTS AJAX (JSON) — SOLO SI YA TENEMOS $iduser
   ============================================================ */
function money_fmt($n){ return number_format((float)$n,2,'.',''); }

if (isset($_GET['action']) && $iduser) {
  header('Content-Type: application/json; charset=utf-8');
  $action = $_GET['action'];

  // Categorías
  if ($action === 'cats') {
    $cats = [];
    $q = mysqli_query($conection, "
      SELECT DISTINCT TRIM(pv.categorias) AS cat
      FROM producto_venta pv
      WHERE pv.estatus = 1 AND pv.id_usuario = '$iduser' AND pv.categorias IS NOT NULL AND pv.categorias <> ''
      ORDER BY cat ASC
    ");
    while ($r = mysqli_fetch_assoc($q)) $cats[] = $r['cat'];
    echo json_encode(['ok'=>true,'cats'=>$cats]); exit;
  }

  // Búsqueda productos
  if ($action === 'search') {
    $qtxt = isset($_GET['q']) ? trim($_GET['q']) : '';
    $cat  = isset($_GET['cat']) ? trim($_GET['cat']) : '';
    $w = ["pv.estatus = 1", "pv.id_usuario = '$iduser'"];
    if ($qtxt !== '') {
      $esc = mysqli_real_escape_string($conection, $qtxt);
      $w[] = "(pv.nombre LIKE '%$esc%' OR pv.descripcion LIKE '%$esc%')";
    }
    if ($cat !== '') {
      $esc = mysqli_real_escape_string($conection, $cat);
      $w[] = "pv.categorias = '$esc'";
    }
    $where = implode(' AND ', $w);

    $res = [];
    $sql = "
      SELECT pv.idproducto, pv.nombre, pv.precio, pv.foto, pv.porcentaje, pv.marca, pv.categorias,
             pv.url_upload_img, pv.video_explicativo, pv.url_video, u.nombre_empresa
      FROM producto_venta pv
      INNER JOIN usuarios u ON pv.id_usuario = u.id
      WHERE $where
      ORDER BY pv.idproducto DESC
      LIMIT 200";
    $qp = mysqli_query($conection, $sql);

    $ids = [];
    while ($r = mysqli_fetch_assoc($qp)) {
      $main = rtrim($r['url_upload_img'],'/').'/home/img/uploads/'.ltrim($r['foto'],'/');
      $hasV = !empty($r['video_explicativo']);
      $res[] = [
        'id'       => (int)$r['idproducto'],
        'nombre'   => $r['nombre'],
        'empresa'  => $r['nombre_empresa'],
        'marca'    => $r['marca'],
        'precio'   => (float)$r['precio'],
        'porc'     => (float)$r['porcentaje'],
        'main'     => $main,
        'hasVideo' => $hasV
      ];
      $ids[] = (int)$r['idproducto'];
    }

    // Thumbnails por lote
    $thumbs = [];
    if (count($ids)>0) {
      $idList = implode(',', $ids);
      $qth = mysqli_query($conection, "SELECT idp, url, img FROM img_producto WHERE estatus=1 AND idp IN ($idList) ORDER BY id ASC");
      while ($t = mysqli_fetch_assoc($qth)) {
        $thumbs[(int)$t['idp']][] = rtrim($t['url'],'/').'/home/img/uploads/'.ltrim($t['img'],'/');
      }
    }
    foreach ($res as &$p) {
      $pid = $p['id'];
      $p['thumbs'] = isset($thumbs[$pid]) ? array_slice($thumbs[$pid], 0, 6) : [];
    }

    echo json_encode(['ok'=>true,'items'=>$res]); exit;
  }

  // Perfil producto
  if ($action === 'product' && isset($_GET['pid'])) {
    $pid = (int)$_GET['pid'];
    $q = mysqli_query($conection, "
      SELECT pv.idproducto, pv.nombre, pv.descripcion, pv.precio, pv.foto, pv.porcentaje,
             pv.marca, pv.categorias, pv.subcategorias, pv.url_upload_img,
             pv.video_explicativo, pv.url_video,
             u.nombre_empresa, u.img_logo
      FROM producto_venta pv
      INNER JOIN usuarios u ON pv.id_usuario = u.id
      WHERE pv.estatus=1 AND pv.id_usuario='$iduser' AND pv.idproducto='$pid'
      LIMIT 1
    ");
    $p = mysqli_fetch_assoc($q);
    if (!$p) { echo json_encode(['ok'=>false]); exit; }

    $main = rtrim($p['url_upload_img'],'/').'/home/img/uploads/'.ltrim($p['foto'],'/');
    $vid  = (!empty($p['video_explicativo'])) ? (rtrim($p['url_video'],'/').'/home/img/videos/'.ltrim($p['video_explicativo'],'/')) : '';
    $thumbs = [];
    $qs = mysqli_query($conection, "SELECT url, img FROM img_producto WHERE estatus=1 AND idp='$pid' ORDER BY id ASC");
    while ($t = mysqli_fetch_assoc($qs)) $thumbs[] = rtrim($t['url'],'/').'/home/img/uploads/'.ltrim($t['img'],'/');

    echo json_encode([
      'ok'=>true,
      'id'       => (int)$p['idproducto'],
      'nombre'   => $p['nombre'],
      'empresa'  => $p['nombre_empresa'],
      'logo'     => $p['img_logo'],
      'marca'    => $p['marca'],
      'categoria'=> $p['categorias'],
      'subcat'   => $p['subcategorias'],
      'desc'     => $p['descripcion'],
      'precio'   => (float)$p['precio'],
      'porc'     => (float)$p['porcentaje'],
      'main'     => $main,
      'thumbs'   => $thumbs,
      'video'    => $vid
    ]); exit;
  }

  echo json_encode(['ok'=>false,'err'=>'invalid']); exit;
}

/* =========================
   HELPER FRONT
   ========================= */
function money($v){ return number_format((float)$v,2); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>
    <?php echo $mesa_code ? "Mesa — Catálogo | " : ""; ?>
    Entrar a <?php echo htmlspecialchars($nombre_empresa); ?>
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:title" content="<?php echo htmlspecialchars($nombre_empresa); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($img_sistema); ?>">
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>">
  <link rel="stylesheet" href="home/files/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background:#263238; color:#fff; font-family: 'Signika Negative', sans-serif; }
    /* ==== VIDEO DE FONDO (SE MANTIENE) ==== */
    .bg-video{
      position:fixed; inset:0; width:100%; height:100%; object-fit:cover;
      filter:brightness(0.35); z-index:-1;
    }
    .wrap { display:flex; justify-content:center; padding:24px 12px; }
    .box { width:100%; max-width:1200px; background:rgba(255,255,255,.08); backdrop-filter: blur(8px); border-radius:16px; padding:20px; box-shadow:0 0 20px rgba(0,0,0,.5); }

    .table th, .table td { color:#f1f1f1; border-color:rgba(255,255,255,.12)!important; }
    .card { background:rgba(255,255,255,.06); border:none; }
    .chip { display:inline-block; background:rgba(255,255,255,.1); padding:4px 10px; border-radius:20px; font-size:13px; margin-right:6px; }

    /* ====== Menú Café (debajo del resumen) ====== */
    .caf-nav{
      position:sticky; top:0; z-index:10; margin-top:18px;
      background: linear-gradient(180deg, rgba(59,47,47,.92), rgba(34,34,34,.88));
      border:1px solid rgba(255,255,255,.12); border-radius:12px; padding:12px;
    }
    .caf-brand{ color:#F7EFE5; font-weight:900; letter-spacing:.2px; }
    .caf-pill{
      background:rgba(255,255,255,.06); color:#fff; border:1px solid rgba(255,255,255,.12);
      border-radius:999px; padding:.35rem .75rem; font-size:.9rem; transition:.15s; white-space:nowrap;
    }
    .caf-pill.active, .caf-pill:hover{ background:#C59D5F; color:#2b2b2b; border-color:transparent; }
    .caf-search{ background:#2a2f31; border:1px solid rgba(255,255,255,.15); color:#fff; }
    .caf-search::placeholder{ color:#c7d0d6; }

    .grid{ display:grid; gap:14px; margin-top:16px; grid-template-columns:repeat(1,minmax(0,1fr)); }
    @media (min-width:480px){ .grid{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (min-width:768px){ .grid{ grid-template-columns:repeat(3,minmax(0,1fr)); } }
    @media (min-width:1200px){ .grid{ grid-template-columns:repeat(4,minmax(0,1fr)); } }

    .card-cafe{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:16px; overflow:hidden; transition:transform .1s, box-shadow .2s; }
    .card-cafe:hover{ transform:translateY(-2px); box-shadow:0 16px 30px rgba(0,0,0,.35); }
    .card-media{ position:relative; aspect-ratio:4/3; background:#111; }
    .card-media img{ width:100%; height:100%; object-fit:cover; display:block; }
    .badge-off{ position:absolute; left:10px; top:10px; background:#C59D5F; color:#2b2b2b; border-radius:999px; padding:.2rem .55rem; font-weight:800; font-size:.75rem; }
    .badge-video{ position:absolute; right:10px; top:10px; background:rgba(0,0,0,.55); color:#fff; border-radius:999px; padding:.2rem .55rem; font-weight:700; font-size:.75rem; border:1px solid rgba(255,255,255,.25);}
    .card-body{ padding:12px; }
    .title{ font-weight:800; font-size:1rem; color:#fff; margin:0 0 6px; }
    .brand{ color:#e9d7c7; font-size:.85rem; }
    .price{ display:flex; gap:8px; align-items:center; margin-top:8px; }
    .price-now{ font-weight:900; color:#fff; }
    .price-old{ text-decoration:line-through; color:#cbb; font-size:.9rem; }
    .thumbs{ display:flex; gap:6px; margin-top:10px; overflow:auto; }
    .thumbs img{ width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,.15); cursor:pointer; }

    /* Overlay Perfil */
    .overlay{ position:fixed; inset:0; background:rgba(0,0,0,.65); display:none; align-items:center; justify-content:center; z-index:50; padding:16px; }
    .overlay.show{ display:flex; }
    .profile{ width:100%; max-width:980px; background:linear-gradient(180deg, rgba(59,47,47,.98), rgba(34,34,34,.98)); border:1px solid rgba(255,255,255,.12); border-radius:16px; overflow:hidden; }
    .profile-header{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.12); }
    .profile-title{ font-weight:900; }
    .profile-body{ padding:14px; }
    .galeria-main{ aspect-ratio:4/3; background:#000; border-radius:12px; overflow:hidden; }
    .galeria-main img, .galeria-main video{ width:100%; height:100%; object-fit:cover; display:block; }
    .galeria-strip{ display:flex; gap:8px; overflow:auto; margin-top:8px; }
    .galeria-strip img{ width:72px; height:72px; object-fit:cover; border-radius:8px; border:1px solid rgba(255,255,255,.12); cursor:pointer; }
    .tab-nav{ display:flex; gap:8px; margin-top:14px; }
    .tab-btn{ background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#fff; border-radius:8px; padding:.35rem .7rem; font-weight:700; cursor:pointer; }
    .tab-btn.active{ background:#C59D5F; color:#2b2b2b; border-color:transparent; }
    .btn-ghost{ background:transparent; color:#fff; border:1px solid rgba(255,255,255,.25); border-radius:10px; padding:.4rem .7rem; }
  </style>
</head>
<body>

<!-- VIDEO DE FONDO -->
<video class="bg-video" autoplay muted loop playsinline>
  <source src="img/guibis.mp4" type="video/mp4">
</video>

<div class="wrap">
  <div class="box">
    <?php if (!$mesa_code): ?>
      <h4>Bienvenido a <?php echo htmlspecialchars($nombre_empresa); ?></h4>
      <p class="text-light mb-3">Escanea una mesa para ver su resumen y el menú del restaurante.</p>
      <div class="mt-3">
        <span class="chip"><i class="fas fa-map-marker-alt"></i> IP: <?php echo $direccion_ip; ?></span>
      </div>

    <?php else: ?>
      <h4 class="mb-1">Resumen de Mesa</h4>
      <p class="text-light small mb-3">Código: <strong><?php echo htmlspecialchars($mesa_code); ?></strong></p>

      <?php if (!$mesa_data): ?>
        <div class="alert alert-warning">No se encontró información para esta mesa.</div>
      <?php else: ?>

        <?php if (count($mesa_items) > 0): ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Comentarios</th>
                  <th class="text-center">Cant.</th>
                  <th class="text-end">P.Unit</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($mesa_items as $p): ?>
                  <tr>
                    <td>
                      <span class="text-white"><?php echo $p['nombre_producto']; ?></span>
                      <div class="small text-muted"><?php echo $p['fecha_f']; ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($p['nota_extra']); ?></td>
                    <td class="text-center"><?php echo $p['cantidad_producto']; ?></td>
                    <td class="text-end">$<?php echo number_format($p['punitario'],2); ?></td>
                    <td class="text-end fw-bold">$<?php echo number_format($p['pfinal'],2); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="card mt-3">
            <div class="card-body">
              <div class="d-flex justify-content-between"><span>Subtotal</span><strong>$<?php echo number_format($mesa_totales['subtotal'],2); ?></strong></div>
              <div class="d-flex justify-content-between"><span>IVA</span><strong>$<?php echo number_format($mesa_totales['iva'],2); ?></strong></div>
              <div class="d-flex justify-content-between"><span>Descuento</span><strong>$<?php echo number_format($mesa_totales['descuento'],2); ?></strong></div>
              <hr>
              <div class="d-flex justify-content-between h5"><span>Total</span><span>$<?php echo number_format($mesa_totales['total'],2); ?></span></div>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-info">Esta mesa no tiene pedidos activos.</div>
        <?php endif; ?>

        <!-- =======================
             MENÚ / CATÁLOGO (debajo del resumen)
             ======================= -->
        <?php if ($iduser): ?>
          <div class="caf-nav">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <div class="caf-brand"><i class="fa-solid fa-mug-saucer me-1"></i> Menú del Restaurante</div>
              <div class="ms-auto d-flex gap-2 w-100 w-md-auto">
                <input id="q" class="form-control form-control-sm caf-search" placeholder="Buscar (ej. capuchino, sandwich…)" />
                <button id="btn-clear" class="btn btn-sm btn-ghost" title="Limpiar"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div id="chips" class="d-flex flex-wrap gap-2 mt-2"></div>
          </div>

          <div id="grid" class="grid"></div>
        <?php endif; ?>

      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Overlay Perfil (sin botón Elegir) -->
<div id="overlay" class="overlay" role="dialog" aria-modal="true">
  <div class="profile">
    <div class="profile-header">
      <div class="profile-title" id="prof-title">Producto</div>
      <div class="d-flex gap-2">
        <button class="btn-ghost" id="prof-close"><i class="fa fa-xmark"></i></button>
      </div>
    </div>
    <div class="profile-body">
      <div class="row g-3">
        <div class="col-md-7">
          <div id="prof-main" class="galeria-main"></div>
          <div id="prof-strip" class="galeria-strip"></div>
          <div class="tab-nav">
            <button id="tab-gal" class="tab-btn active">Galería</button>
            <button id="tab-vid" class="tab-btn">Video</button>
          </div>
        </div>
        <div class="col-md-5">
          <div class="h5 mb-1" id="prof-name"></div>
          <div class="small text-light mb-2" id="prof-brand"></div>
          <div class="d-flex align-items-baseline gap-2 mb-2">
            <div class="h4 mb-0" id="prof-price">$0.00</div>
            <div class="text-muted text-decoration-line-through" id="prof-old" style="display:none">$0.00</div>
          </div>
          <div class="small" id="prof-desc" style="white-space:pre-wrap"></div>
          <div class="mt-3 d-flex gap-2">
            <button class="btn-ghost" id="prof-close-2">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const IDUSER   = <?php echo $iduser ? (int)$iduser : 'null'; ?>;
  const MESA     = <?php echo $mesa_code ? json_encode($mesa_code) : 'null'; ?>;

  if (!IDUSER) return;

  const grid     = document.getElementById('grid');
  const chips    = document.getElementById('chips');
  const qInput   = document.getElementById('q');
  const btnClear = document.getElementById('btn-clear');

  // Overlay elements
  const overlay   = document.getElementById('overlay');
  const profTitle = document.getElementById('prof-title');
  const profName  = document.getElementById('prof-name');
  const profBrand = document.getElementById('prof-brand');
  const profPrice = document.getElementById('prof-price');
  const profOld   = document.getElementById('prof-old');
  const profDesc  = document.getElementById('prof-desc');
  const profMain  = document.getElementById('prof-main');
  const profStrip = document.getElementById('prof-strip');
  const tabGal    = document.getElementById('tab-gal');
  const tabVid    = document.getElementById('tab-vid');
  const btnClose  = document.getElementById('prof-close');
  const btnClose2 = document.getElementById('prof-close-2');

  let currentCat = '';
  let debounce   = null;

  // Utils
  const fmt = (n)=> {
    const v = Number(n||0);
    return v.toLocaleString('es-EC',{minimumFractionDigits:2, maximumFractionDigits:2});
  };

  const cardTpl = (p) => {
    const old = (p.porc > 0) ? p.precio/(1-(p.porc/100)) : 0;
    const thumbs = (p.thumbs||[]).map(t => `<img loading="lazy" src="${t}" alt="thumb" data-main="${t}" class="th">`).join('');
    return `
      <div class="card-cafe">
        <div class="card-media">
          <img loading="lazy" src="${p.main}" alt="${p.nombre}">
          ${p.porc>0 ? `<span class="badge-off">${parseInt(p.porc)}% OFF</span>` : ``}
          ${p.hasVideo ? `<span class="badge-video"><i class="fa fa-play"></i> Video</span>` : ``}
        </div>
        <div class="card-body">
          <div class="title">${p.nombre}</div>
          <div class="brand"><i class="fa-solid fa-store me-1"></i>${p.empresa}${p.marca ? ' · '+p.marca : ''}</div>
          <div class="price">
            <div class="price-now">$${fmt(p.precio)}</div>
            ${old>0 ? `<div class="price-old">$${fmt(old)}</div>`:''}
          </div>
          ${thumbs ? `<div class="thumbs">${thumbs}</div>`:''}
          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-outline-light btn-sm btn-view" data-id="${p.id}"><i class="fa fa-eye me-1"></i> Ver</button>
          </div>
        </div>
      </div>`;
  };

  const loadCats = async () => {
    const r = await fetch(`?mesa=${encodeURIComponent(MESA)}&action=cats`, {cache:'no-store'});
    const j = await r.json();
    chips.innerHTML = '';
    const all = document.createElement('a');
    all.href = '#';
    all.className = `caf-pill ${!currentCat ? 'active':''}`;
    all.textContent = 'Todo';
    all.onclick = (e)=>{ e.preventDefault(); currentCat=''; render(); };
    chips.appendChild(all);
    if (j.ok && j.cats) {
      j.cats.forEach(c => {
        const a = document.createElement('a');
        a.href = '#';
        a.className = `caf-pill ${currentCat===c ? 'active':''}`;
        a.textContent = c;
        a.onclick = (e)=>{ e.preventDefault(); currentCat=c; render(); };
        chips.appendChild(a);
      });
    }
  };

  const render = async () => {
    grid.innerHTML = `<div class="text-center py-3">Cargando…</div>`;
    const q = qInput.value.trim();
    const params = new URLSearchParams({ mesa: MESA, action:'search' });
    if (q) params.append('q', q);
    if (currentCat) params.append('cat', currentCat);
    const r = await fetch(`?${params.toString()}`, {cache:'no-store'});
    const j = await r.json();
    if (!j.ok) { grid.innerHTML = `<div class="alert alert-warning">No se pudo cargar el catálogo.</div>`; return; }
    if (!j.items || j.items.length===0) { grid.innerHTML = `<div class="alert alert-secondary">Sin resultados.</div>`; return; }
    grid.innerHTML = j.items.map(cardTpl).join('');
  };

  // Thumbnails → cambian imagen principal de la tarjeta
  grid.addEventListener('click', (e)=>{
    const th = e.target.closest('.thumbs img.th');
    if (th) {
      const card = th.closest('.card-cafe');
      const media = card.querySelector('.card-media img');
      const src = th.getAttribute('data-main');
      if (media && src) media.src = src;
    }
    const btn = e.target.closest('.btn-view');
    if (btn) openProfile(btn.getAttribute('data-id'));
  });

  // Búsqueda en vivo (debounce 280ms)
  qInput.addEventListener('input', ()=>{
    clearTimeout(debounce);
    debounce = setTimeout(render, 280);
  });
  btnClear.addEventListener('click',(e)=>{ e.preventDefault(); qInput.value=''; render(); });

  // Perfil (overlay)
  const openProfile = async (pid) => {
    const r = await fetch(`?mesa=${encodeURIComponent(MESA)}&action=product&pid=${encodeURIComponent(pid)}`, {cache:'no-store'});
    const j = await r.json();
    if (!j.ok) return;

    const old = (j.porc>0) ? j.precio/(1-(j.porc/100)) : 0;

    profTitle.textContent = j.nombre;
    profName.textContent  = j.nombre;
    profBrand.textContent = (j.empresa || '') + (j.marca ? ' · '+j.marca : '');
    profPrice.textContent = '$'+fmt(j.precio);
    if (old>0) { profOld.style.display='inline-block'; profOld.textContent='$'+fmt(old); } else { profOld.style.display='none'; }
    profDesc.textContent  = j.desc || '';

    // Galería principal
    profMain.innerHTML = `<img src="${j.main}" alt="${j.nombre}">`;
    // Strip
    const thumbs = (j.thumbs||[]);
    let stripHtml = `<img src="${j.main}" alt="main">`;
    thumbs.forEach(t => stripHtml += `<img src="${t}" alt="thumb">`);
    profStrip.innerHTML = stripHtml;

    // Tabs
    tabGal.classList.add('active');
    tabVid.classList.remove('active');

    // Video
    if (j.video) {
      tabVid.style.display = 'inline-block';
    } else {
      tabVid.style.display = 'none';
    }

    overlay.classList.add('show');

    // Eventos de strip
    profStrip.querySelectorAll('img').forEach(img=>{
      img.addEventListener('click', ()=> {
        profMain.innerHTML = `<img src="${img.src}" alt="img">`;
        tabGal.classList.add('active');
        tabVid.classList.remove('active');
      });
    });

    // Tabs actions
    tabGal.onclick = ()=> {
      tabGal.classList.add('active'); tabVid.classList.remove('active');
      profMain.innerHTML = `<img src="${j.main}" alt="${j.nombre}">`;
    };
    tabVid.onclick = ()=> {
      if (!j.video) return;
      tabVid.classList.add('active'); tabGal.classList.remove('active');
      profMain.innerHTML = `
        <video controls playsinline>
          <source src="${j.video}" type="video/mp4">
        </video>`;
    };
  };

  const closeProfile = ()=> overlay.classList.remove('show');
  document.getElementById('prof-close').addEventListener('click', closeProfile);
  document.getElementById('prof-close-2').addEventListener('click', closeProfile);
  overlay.addEventListener('click', (e)=> { if (e.target===overlay) closeProfile(); });
  document.addEventListener('keydown',(e)=>{ if(e.key==='Escape') closeProfile(); });

  // Init
  (async function init(){
    await loadCats();
    await render();
  })();
})();
</script>
</body>
</html>
<?php ob_end_flush(); ?>
