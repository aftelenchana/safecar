<?php
// Inicializamos la sesión
session_start();

// Incluimos la conexión a la base de datos
require "coneccion.php";

// Establecemos el conjunto de caracteres a utf8mb4
mysqli_set_charset($conection, 'utf8mb4');

// Redirigimos si la sesión está activa
if (!empty($_SESSION['active'])) {
    header('location:home/');
    exit();
}

// Gestionamos el token de sesión
if (isset($_COOKIE['session_token'])) {
    $sessionToken = $_COOKIE['session_token'];
} else {
    $sessionToken = bin2hex(random_bytes(32));
    setcookie('session_token', $sessionToken, time() + (86400 * 30), "/", null, true, true);
}

// Función para obtener la IP real del cliente
function getRealIP() {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (!empty($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

// Definir la IP según el entorno
$domain = $_SERVER['HTTP_HOST'];
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
$url = $protocol . $domain;

$direccion_ip = ($url == 'http://localhost') ? '186.42.9.232' : getRealIP();

// Insertamos la dirección IP en la base de datos
$query_insert_busqueda = mysqli_query($conection, "INSERT INTO vivitas_generales(direccion_ip) VALUES('$direccion_ip')");

// Verificamos la existencia del dominio en la base de datos
$query_doccumentos = mysqli_query($conection, "SELECT * FROM usuarios WHERE url_admin = '$domain'");
$result_documentos = mysqli_fetch_array($query_doccumentos);

// Si existe, asignamos las variables correspondientes
if ($result_documentos) {
    $url_img_upload = $result_documentos['url_img_upload'];
    $img_facturacion = $result_documentos['img_facturacion'];
    $nombre_empresa = $result_documentos['nombre_empresa'];
    $celular = $result_documentos['celular'];
    $email = $result_documentos['email'];
    $facebook = $result_documentos['facebook'];
    $instagram = $result_documentos['instagram'];
    $whatsapp = $result_documentos['whatsapp'];

    // Ruta de la imagen del sistema
    $img_sistema = $url_img_upload . '/home/img/uploads/' . $img_facturacion;
} else {
    // Imagen por defecto si no hay resultados
    $img_sistema = '/img/guibis.png';
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar a <?php echo htmlspecialchars($nombre_empresa); ?></title>
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/estilos/login.css?v=1.0">
  <link rel="stylesheet" href="/estilos/footer.css?v=1.0">
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">
</head>
<body>
  <div class="main-container">
    <div class="logo-container">
      <a href="/"><img src="<?php echo htmlspecialchars($img_sistema); ?>" alt="GUIBIS" class="logo-img"></a>
      <div class="logo-text"><a href="/"><?php echo $nombre_empresa ?></a> </div>
      <div class="logo-subtitle">APIs Confiables para Verificación de Datos</div>
    </div>

    <div class="login-container">
      <div class="login-card">
        <i class="fas fa-shield-check app-icon"></i>

        <form name="loginForm"  name="loginForm" id="loginForm"   onsubmit="event.preventDefault(); sendData_login_usuario();">
          <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <input type="email" class="form-control" name="email" id="email" required placeholder="usuario@ejemplo.com">
              <i class="fas fa-envelope input-icon"></i>
            </div>
          </div>

          <div class="mb-4">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
              <input type="password" class="form-control" name="password" id="password" required placeholder="Ingresa tu contraseña">
              <i class="fas fa-lock input-icon"></i>
            </div>
          </div>

          <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <style media="screen">
          .notificacion_login_usuario{
            text-align: center;
            margin: 8px;
          }
        </style>
        <div class="notificacion_login_usuario">

        </div>

        <div class="login-links">
          <a href="recuperar_password">
            <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
          </a>
          <a href="registro">
            <i class="fas fa-user-plus"></i> ¿No tienes una cuenta? Regístrate
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'scripts/footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="java/login_usuario.js?v=7"></script>
</body>
</html>
