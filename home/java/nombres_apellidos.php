<?php
declare(strict_types=1);

// === CONFIG ===
$REMOTE_ENDPOINT = 'https://api.guibis.com/dev/ruc-cedula/nombres_apellidos';
$SAVE_DIR_REL    = '/home/archivos/consultas'; // carpeta pública
$FOLDER_PERMS    = 0755;

// === HEADERS ===
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// === HELPERS ===
function body_json(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $arr = json_decode($raw, true);
  return (json_last_error() === JSON_ERROR_NONE && is_array($arr)) ? $arr : [];
}
function base_url(): string {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
  return "$scheme://$host";
}
function to_upper_utf8(string $s): string {
  return function_exists('mb_strtoupper') ? mb_strtoupper($s,'UTF-8') : strtoupper($s);
}

// === INPUT ===
$in    = body_json();
$param = trim((string)($in['parametro_busqueda'] ?? ''));
$key   = trim((string)($in['KEY'] ?? ''));
$app   = trim((string)($in['_app_name'] ?? 'app'));

// Forzar MAYÚSCULAS también en backend
$param = to_upper_utf8($param);

if ($param === '' || $key === '') {
  http_response_code(400);
  echo json_encode(['error'=>'Faltan parametro_busqueda o KEY'], JSON_UNESCAPED_UNICODE);
  exit;
}

// === CONSULTA API REMOTA ===
$payload = json_encode(['parametro_busqueda'=>$param, 'KEY'=>$key], JSON_UNESCAPED_UNICODE);
$ch = curl_init($REMOTE_ENDPOINT);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
  CURLOPT_POSTFIELDS     => $payload,
  CURLOPT_CONNECTTIMEOUT => 10,
  CURLOPT_TIMEOUT        => 25,
]);
$resp = curl_exec($ch);
$errno = curl_errno($ch);
$err   = curl_error($ch);
$http  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($errno !== 0) {
  http_response_code(502);
  echo json_encode(['error'=>"Error al consultar API: $err"], JSON_UNESCAPED_UNICODE);
  exit;
}

$data = json_decode((string)$resp, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  $data = ['raw'=>(string)$resp];
}

// === GUARDADO JSON ===
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$saveDir = $docRoot . $SAVE_DIR_REL;
if (!is_dir($saveDir)) { @mkdir($saveDir, $FOLDER_PERMS, true); }

$unique   = date('Ymd-His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6);
$filename = "consulta_{$unique}.json";
$fullPath = "$saveDir/$filename";

@file_put_contents($fullPath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$url = rtrim(base_url(), '/') . $SAVE_DIR_REL . "/$filename";

// === RESPUESTA ===
$out = $data; // mantienes tu estructura original
$out['_saved'] = [
  'ok'       => true,
  'filename' => $filename,
  'url'      => $url,
  'path'     => $fullPath,
  'http'     => $http
];

http_response_code($http ?: 200);
echo json_encode($out, JSON_UNESCAPED_UNICODE);
