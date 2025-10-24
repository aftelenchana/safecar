
<?php
/* -------------------------------------------------------
   Reporte HTML (Dompdf-ready) para JSON de consultas
   Ruta base: ../../archivos/consultas/
--------------------------------------------------------*/

// ============== Utilidades básicas ==============
function h($v) { return htmlspecialchars((string)$v ?? '', ENT_QUOTES, 'UTF-8'); }
function val($arr, $path, $def = '—') {
  $p = explode('.', $path);
  $x = $arr;
  foreach ($p as $k) {
    if (!is_array($x) || !array_key_exists($k, $x)) return $def;
    $x = $x[$k];
  }
  if ($x === null || $x === '') return $def;
  return $x;
}
function money($n) {
  if ($n === null || $n === '') return '—';
  return '$ ' . number_format((float)$n, 2);
}
function int0($n) {
  if ($n === null || $n === '') return '0';
  return number_format((int)$n, 0, '.', ',');
}
function boolBadge($b) { return $b ? 'Sí' : 'No'; }

// ============== Sanitización de entrada ==============
$f = isset($_GET['f']) ? (string)$_GET['f'] : '';
if ($f === '') {
  http_response_code(400);
  echo 'Falta el parámetro ?f=archivo.json';
  exit;
}
$baseDir = realpath(__DIR__ . '/../../archivos/consultas');
$target  = realpath($baseDir . '/' . basename($f));
if (!$baseDir || !$target || strncmp($target, $baseDir, strlen($baseDir)) !== 0) {
  http_response_code(400);
  echo 'Ruta inválida.';
  exit;
}
if (!is_file($target)) {
  http_response_code(404);
  echo 'Archivo no encontrado.';
  exit;
}

$jsonRaw = file_get_contents($target);
$data    = json_decode($jsonRaw, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
  http_response_code(422);
  echo 'JSON inválido.';
  exit;
}

// ============== Variables del reporte ==============
$status        = strtoupper(val($data, 'status', '—'));
$busqueda      = $data['busqueda'] ?? [];
$payload       = $data; // referencia global
$generadoEn    = date('Y-m-d H:i:s');
$fileMtime     = @filemtime($target);
$archivoFecha  = $fileMtime ? date('Y-m-d H:i:s', $fileMtime) : '—';

// Colores (paleta profesional)
$COL = [
  'bg'       => '#F8FAFC', // slate-50
  'paper'    => '#FFFFFF',
  'ink'      => '#0F172A', // slate-900
  'muted'    => '#475569', // slate-600
  'line'     => '#E2E8F0', // slate-200
  'primary'  => '#2563EB', // blue-600
  'accent'   => '#7C3AED', // violet-600
  'ok'       => '#16A34A', // green-600
  'warn'     => '#CA8A04', // amber-600
  'danger'   => '#DC2626', // red-600
  'chip'     => '#EEF2FF', // indigo-50
];

// Derivados del JSON (con tolerancia a faltantes)
$parametro          = val($payload, 'busqueda.parametro');
$tipoPersona        = val($payload, 'busqueda.tipoPersona');
$resultados         = val($payload, 'busqueda.resultados', '—');
$coincidencias      = val($payload, 'busqueda.coincidencias', '—');
$esUnico            = (bool)($payload['busqueda']['es_unico'] ?? false);
$modo               = val($payload, 'busqueda.modo');
$metodoLog          = val($payload, 'busqueda.metodo_log');

$identDetectada     = val($payload, 'data.identificacion_detectada');
$tipoDetectado      = val($payload, 'data.tipoPersona_detectado');
$rucBase10          = val($payload, 'data.ruc_base10');

$ruc                = $payload['data']['detalle']['ruc']     ?? [];
$cedula             = $payload['data']['detalle']['cedula']  ?? [];
$establecimientos   = $ruc['establecimientos'] ?? [];

$original           = $payload['data']['original'] ?? [];

$quota              = $payload['quota']   ?? [];
$billing            = $payload['billing'] ?? [];

// Estado → color
$estadoBadgeColor = $COL['muted'];
if ($status === 'SUCCESS') $estadoBadgeColor = $COL['ok'];
elseif ($status === 'ERROR' || $status === 'FAIL') $estadoBadgeColor = $COL['danger'];

// Pequeña utilidad de celda
function tr($k, $v) {
  return '<tr><th>'.h($k).'</th><td>'.h($v).'</td></tr>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Reporte de Consulta • <?=h($parametro)?></title>
<style>
  /* ===== Reseteo & tipografía amigable para Dompdf ===== */
  @page { margin: 24pt; }
  * { box-sizing: border-box; }
  html, body { padding:0; margin:0; }
  body {
    background: <?=$COL['bg']?>;
    color: <?=$COL['ink']?>;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
    font-size: 11pt; line-height: 1.45;
  }

  /* ===== Layout ===== */
  .wrap { background: <?=$COL['paper']?>; border:1px solid <?=$COL['line']?>; border-radius: 12px; padding: 20pt; }
  .header {
    border-radius: 12px;
    padding: 16pt 18pt;
    background: linear-gradient(135deg, <?=$COL['primary']?>, <?=$COL['accent']?>);
    color: #fff;
    margin-bottom: 18pt;
  }
  .header h1 { margin: 0 0 6pt 0; font-size: 18pt; letter-spacing: .2pt; }
  .meta { font-size: 9pt; opacity:.9; }

  .grid-2 { width:100%; }
  .grid-2 .col { width:49%; display:inline-block; vertical-align: top; }
  .spacer { height: 10pt; }

  /* ===== Tarjetas / secciones ===== */
  .card { border:1px solid <?=$COL['line']?>; border-radius: 10px; margin-bottom: 14pt; }
  .card h2 {
    margin:0; padding:10pt 12pt; font-size: 12pt; color: <?=$COL['ink']?>;
    background: <?=$COL['bg']?>; border-bottom:1px solid <?=$COL['line']?>;
  }
  .card .content { padding: 10pt 12pt; }

  /* ===== Tablas ===== */
  table.kv { width:100%; border-collapse: collapse; }
  table.kv th, table.kv td { text-align: left; vertical-align: top; padding: 7pt 8pt; }
  table.kv th {
    width: 38%;
    background: #fff; color: <?=$COL['muted']?>; font-weight: 600;
    border-bottom:1px solid <?=$COL['line']?>; border-right:1px solid <?=$COL['line']?>;
  }
  table.kv td {
    background: #fff; border-bottom:1px solid <?=$COL['line']?>;
  }
  .table-note { font-size: 9pt; color: <?=$COL['muted']?>; margin-top:6pt; }

  /* ===== Badges & chips ===== */
  .badge {
    display:inline-block; padding:4pt 8pt; border-radius: 999px;
    font-size: 9pt; font-weight: 700; letter-spacing:.2pt; color:#fff;
  }
  .badge-muted { background: <?=$COL['muted']?>; }
  .badge-ok    { background: <?=$COL['ok']?>; }
  .badge-warn  { background: <?=$COL['warn']?>; }
  .badge-err   { background: <?=$COL['danger']?>; }

  .chip {
    display:inline-block; padding:3pt 8pt; border-radius: 999px;
    font-size: 9pt; background: <?=$COL['chip']?>; color: <?=$COL['ink']?>; border:1px solid <?=$COL['line']?>;
    margin-right: 6pt; margin-bottom: 4pt;
  }

  /* ===== Secciones específicas ===== */
  .status-row { margin-top: 8pt; }
  .small { font-size: 9pt; color: <?=$COL['muted']?>; }
  .hl { color: <?=$COL['primary']?>; font-weight: 700; }

  .table-compact th, .table-compact td { padding:5pt 6pt; font-size: 10pt; }

  /* ===== Títulos menores ===== */
  .subtitle { font-size: 10pt; color: <?=$COL['muted']?>; margin: 4pt 0 10pt 0; }
</style>
</head>
<body>
  <div class="wrap">
    <!-- Encabezado -->
    <div class="header">
      <h1>Reporte de Consulta</h1>
      <div class="meta">
        Archivo: <strong><?=h(basename($target))?></strong> •
        Modificado: <strong><?=h($archivoFecha)?></strong> •
        Generado: <strong><?=h($generadoEn)?></strong>
      </div>
      <div class="status-row">
        <?php
          $cls = 'badge-muted';
          if ($status === 'SUCCESS') $cls = 'badge-ok';
          elseif ($status === 'ERROR' || $status === 'FAIL') $cls = 'badge-err';
        ?>
        <span class="badge <?=$cls?>">ESTADO: <?=h($status)?></span>
        <?php if ($esUnico): ?>
          <span class="chip">Resultado único</span>
        <?php else: ?>
          <span class="chip">Múltiples coincidencias: <?=h($coincidencias)?></span>
        <?php endif; ?>
        <span class="chip">Modo: <?=h($modo)?></span>
        <span class="chip">Método: <?=h($metodoLog)?></span>
      </div>
    </div>

    <!-- Resumen de la búsqueda -->
    <div class="card">
      <h2>Resumen de Búsqueda</h2>
      <div class="content">
        <table class="kv">
          <?= tr('Parámetro', $parametro) ?>
          <?= tr('Tipo de persona (entrada)', $tipoPersona) ?>
          <?= tr('Resultados (tope)', $resultados) ?>
          <?= tr('Coincidencias', $coincidencias) ?>
          <?= tr('Es único', $esUnico ? 'Sí' : 'No') ?>
        </table>
        <p class="table-note">Este bloque resume los parámetros y el contexto de la consulta realizada.</p>
      </div>
    </div>

    <!-- Identificación detectada -->
    <div class="card">
      <h2>Identificación Detectada</h2>
      <div class="content">
        <table class="kv">
          <?= tr('Identificación', $identDetectada) ?>
          <?= tr('Tipo detectado', $tipoDetectado) ?>
          <?= tr('RUC base 10', $rucBase10) ?>
        </table>
      </div>
    </div>

    <!-- RUC -->
    <div class="card">
      <h2>Detalle RUC</h2>
      <div class="content">
        <div class="subtitle">Información consolidada del contribuyente</div>
        <table class="kv table-compact">
          <?= tr('Número RUC', val($ruc, 'numeroRuc')) ?>
          <?= tr('Razón social', val($ruc, 'razonSocial')) ?>
          <?= tr('Estado del contribuyente', val($ruc, 'estadoContribuyenteRuc')) ?>
          <?= tr('Actividad principal', val($ruc, 'actividadEconomicaPrincipal')) ?>
          <?= tr('Tipo de contribuyente', val($ruc, 'tipoContribuyente')) ?>
          <?= tr('Régimen', val($ruc, 'regimen')) ?>
          <?= tr('Categoría', val($ruc, 'categoria')) ?>
          <?= tr('Obligado a llevar contabilidad', val($ruc, 'obligadoLlevarContabilidad')) ?>
          <?= tr('Agente de retención', val($ruc, 'agenteRetencion')) ?>
          <?= tr('Contribuyente especial', val($ruc, 'contribuyenteEspecial')) ?>
          <?= tr('Motivo cancelación/suspensión', val($ruc, 'motivoCancelacionSuspension')) ?>
          <?= tr('Fantasma', val($ruc, 'contribuyenteFantasma')) ?>
          <?= tr('Transacciones inexistentes', val($ruc, 'transaccionesInexistente')) ?>
          <?= tr('Dirección matriz', val($ruc, 'direccionMatriz')) ?>
          <?= tr('Nombre comercial matriz', val($ruc, 'nombreComercialMatriz')) ?>
          <?= tr('F. inicio actividades', val($ruc, 'informacionFechasContribuyente.fechaInicioActividades')) ?>
          <?= tr('F. cese', val($ruc, 'informacionFechasContribuyente.fechaCese')) ?>
          <?= tr('F. reinicio', val($ruc, 'informacionFechasContribuyente.fechaReinicioActividades')) ?>
          <?= tr('F. actualización', val($ruc, 'informacionFechasContribuyente.fechaActualizacion')) ?>
        </table>

        <?php if (!empty($establecimientos) && is_array($establecimientos)): ?>
          <div class="spacer"></div>
          <div class="subtitle">Establecimientos</div>
          <table class="kv table-compact">
            <tr>
              <th>Nombre Fantasía</th>
              <th>Tipo</th>
              <th>Dirección</th>
              <th>Estado</th>
              <th>N°</th>
              <th>Matriz</th>
            </tr>
            <?php foreach ($establecimientos as $e): ?>
              <tr>
                <td><?=h($e['nombreFantasiaComercial'] ?? '—')?></td>
                <td><?=h($e['tipoEstablecimiento'] ?? '—')?></td>
                <td><?=h($e['direccionCompleta'] ?? '—')?></td>
                <td><?=h($e['estado'] ?? '—')?></td>
                <td><?=h($e['numeroEstablecimiento'] ?? '—')?></td>
                <td><?=h($e['matriz'] ?? '—')?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Cédula -->
    <div class="card">
      <h2>Detalle Cédula</h2>
      <div class="content">
        <table class="kv table-compact">
          <?= tr('Cédula', val($cedula, 'Cedula')) ?>
          <?= tr('Nombre', val($cedula, 'Nombre')) ?>
          <?= tr('Género', val($cedula, 'Genero')) ?>
          <?= tr('Estado civil', val($cedula, 'EstadoCivil')) ?>
          <?= tr('Profesión', trim(val($cedula, 'Profesion'))) ?>
          <?= tr('Fecha Nacimiento', val($cedula, 'FechaNacimiento')) ?>
          <?= tr('Fecha Cedulación', val($cedula, 'FechaCedulacion')) ?>
          <?= tr('Domicilio', val($cedula, 'Domicilio')) ?>
          <?= tr('Calle', val($cedula, 'CalleDomicilio')) ?>
          <?= tr('N° Domicilio', val($cedula, 'NumeroDomicilio')) ?>
          <?= tr('Lugar Nac.', val($cedula, 'LugarNacimiento')) ?>
          <?= tr('Nacionalidad', val($cedula, 'Nacionalidad')) ?>
          <?= tr('Instrucción', val($cedula, 'Instruccion')) ?>
          <?= tr('Condición', val($cedula, 'CondicionCedulado')) ?>
          <?= tr('Nombre Madre', val($cedula, 'NombreMadre')) ?>
          <?= tr('Nombre Padre', val($cedula, 'NombrePadre')) ?>
          <?= tr('Cónyuge', val($cedula, 'Conyuge')) ?>
          <?= tr('Código Error', val($cedula, 'CodigoError')) ?>
          <?= tr('Error', val($cedula, 'Error')) ?>
          <?= tr('OK', val($cedula, 'ok')) ?>
        </table>
      </div>
    </div>

    <!-- Original -->
    <div class="card">
      <h2>Original (fuente primaria)</h2>
      <div class="content">
        <table class="kv table-compact">
          <?= tr('Identificación', val($original, 'identificacion')) ?>
          <?= tr('Denominación', val($original, 'denominacion')) ?>
          <?= tr('Tipo', val($original, 'tipo')) ?>
          <?= tr('Clase', val($original, 'clase')) ?>
          <?= tr('Tipo identificación', val($original, 'tipoIdentificacion')) ?>
          <?= tr('Resolución', val($original, 'resolucion')) ?>
          <?= tr('Nombre comercial', val($original, 'nombreComercial')) ?>
          <?= tr('Dirección matriz', val($original, 'direccionMatriz')) ?>
          <?= tr('Fecha información', val($original, 'fechaInformacion')) ?>
          <?= tr('Mensaje', val($original, 'mensaje')) ?>
          <?= tr('Estado', val($original, 'estado')) ?>
        </table>
      </div>
    </div>

    <!-- Quota & Billing en dos columnas -->
    <div class="grid-2">
      <div class="col">
        <div class="card">
          <h2>Quota</h2>
          <div class="content">
            <table class="kv table-compact">
              <?= tr('Mensual (máx.)', val($quota, 'mensual_max', '—')) ?>
              <?= tr('Usadas en el mes', int0($quota['usadas_mes'] ?? 0)) ?>
              <?= tr('Gratis restantes', int0($quota['gratis_restantes'] ?? 0)) ?>
              <?= tr('¿Es pago?', ($quota['es_pago'] ?? 0) ? 'Sí' : 'No') ?>
              <?= tr('Rango del mes', is_array($quota['rango_mes'] ?? null) ? implode(' → ', $quota['rango_mes']) : '—') ?>
            </table>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <h2>Billing</h2>
          <div class="content">
            <table class="kv table-compact">
              <?= tr('ID producto API', val($billing, 'id_producto_api')) ?>
              <?= tr('Cantidad', int0($billing['cantidad'] ?? 0)) ?>
              <?= tr('Costo unitario', money($billing['costo_unitario'] ?? 0)) ?>
              <?= tr('Costo total', money($billing['costo_total'] ?? 0)) ?>
              <?= tr('Saldo inicio', money($billing['saldo_inicio'] ?? 0)) ?>
              <?= tr('Saldo restante', money($billing['saldo_restante'] ?? 0)) ?>
            </table>
            <p class="table-note">Los importes se expresan en USD. Verifique las condiciones vigentes de su plan.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>
