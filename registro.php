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
  <title>Registrarte a <?php echo htmlspecialchars($nombre_empresa); ?></title>
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
      <div class="logo-text"><a href="/">API GUIBIS</a> </div>
      <div class="logo-subtitle">APIs Confiables para Verificación de Datos</div>
    </div>

    <div class="login-container">
      <div class="login-card">
        <i class="fas fa-shield-check app-icon"></i>

        <form name="registerForm" name="registrar_usuario_at" id="registrar_usuario_at"   onsubmit="event.preventDefault(); sendData_registrar_usuario_at();">
    <div class="mb-3">
      <label for="nombre_completo" class="form-label">Nombres y Apellidos</label>
      <div class="input-group">
        <input type="text" class="form-control" name="nombres" id="nombre_completo" required placeholder="Tu nombre completo">
        <i class="fas fa-user input-icon"></i>
      </div>
      <div class="error-message" id="nombre_completo-error"></div>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <div class="input-group">
        <input type="email" class="form-control" name="mail_user" id="email" required placeholder="usuario@ejemplo.com">
        <i class="fas fa-envelope input-icon"></i>
      </div>
      <div class="error-message" id="email-error"></div>
    </div>

    <div class="mb-3">
      <label for="celular" class="form-label">Celular</label>
      <div class="input-group">
        <input type="tel" class="form-control" name="celular" id="celular" required placeholder="0998855160">
        <i class="fas fa-phone input-icon"></i>
      </div>
      <div class="error-message" id="celular-error"></div>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <div class="input-group">
        <input type="password" class="form-control" name="password1" id="password" required placeholder="Ingresa tu contraseña">
        <i class="fas fa-lock input-icon"></i>
      </div>
      <div class="error-message" id="password-error"></div>
    </div>

    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
      <div class="input-group">
        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required placeholder="Confirma tu contraseña">
        <i class="fas fa-lock input-icon"></i>
      </div>
      <div class="error-message" id="confirm-password-error"></div>
    </div>

    <button type="submit" class="btn-login">Registrarse</button>
  </form>


      <style media="screen">
        .alerta_registro_usuario_ast{
          text-align: center;
          margin: 8px;
        }
      </style>
      <div class="alerta_registro_usuario_ast">

      </div>

        <div class="login-links">
          <a href="recuperar_password">
            <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
          </a>
          <a href="login">
            <i class="fas fa-user-plus"></i> Ya tienes una cuenta? Inicia Sesión
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php include 'scripts/footer.php'; ?>
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="/java/registrar_usuario.js?v=1.0"></script>
  <script>
  function validateForm() {
      let isValid = true;

      if (!validateNombreCompleto()) isValid = false;
      if (!validateEmail()) isValid = false;
      if (!validateCelular()) isValid = false;
      if (!validatePassword()) isValid = false;
      if (!validateConfirmPassword()) isValid = false;

      if (isValid) {
          alert('Formulario válido. Puedes proceder con el envío.');
      }
  }

  function validateNombreCompleto() {
      const nombre = document.getElementById('nombre_completo').value.trim();
      const errorElement = document.getElementById('nombre_completo-error');

      if (nombre.length < 5) {
          showError(errorElement, 'El nombre completo debe tener al menos 5 caracteres');
          return false;
      }

      if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{5,}$/.test(nombre)) {
          showError(errorElement, 'Solo se permiten letras y espacios');
          return false;
      }

      hideError(errorElement);
      return true;
  }

  function validateEmail() {
      const email = document.getElementById('email').value.trim();
      const errorElement = document.getElementById('email-error');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(email)) {
          showError(errorElement, 'Por favor ingresa un email válido');
          return false;
      }

      hideError(errorElement);
      return true;
  }

  function validateCelular() {
      const celular = document.getElementById('celular').value.trim();
      const errorElement = document.getElementById('celular-error');
      const celularRegex = /^[\+]?[0-9\s\-\(\)]{8,}$/;

      if (!celularRegex.test(celular)) {
          showError(errorElement, 'Por favor ingresa un número de celular válido');
          return false;
      }

      hideError(errorElement);
      return true;
  }

  function validatePassword() {
      const password = document.getElementById('password').value;
      const errorElement = document.getElementById('password-error');

      if (password.length < 8) {
          showError(errorElement, 'La contraseña debe tener al menos 8 caracteres');
          return false;
      }

      const hasUpperCase = /[A-Z]/.test(password);
      const hasLowerCase = /[a-z]/.test(password);
      const hasNumbers = /\d/.test(password);

      if (!hasUpperCase || !hasLowerCase || !hasNumbers) {
          showError(errorElement, 'Debe tener mayúsculas, minúsculas y números');
          return false;
      }

      hideError(errorElement);
      return true;
  }

  function validateConfirmPassword() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const errorElement = document.getElementById('confirm-password-error');

      if (password !== confirmPassword) {
          showError(errorElement, 'Las contraseñas no coinciden');
          return false;
      }

      hideError(errorElement);
      return true;
  }

  function showError(errorElement, message) {
      errorElement.textContent = message;
      errorElement.style.display = 'block';
  }

  function hideError(errorElement) {
      errorElement.style.display = 'none';
  }

  // Validación en tiempo real
  document.getElementById('nombre_completo').addEventListener('input', validateNombreCompleto);
  document.getElementById('email').addEventListener('input', validateEmail);
  document.getElementById('celular').addEventListener('input', validateCelular);
  document.getElementById('password').addEventListener('input', validatePassword);
  document.getElementById('confirm_password').addEventListener('input', validateConfirmPassword);
  </script>
</body>
</html>
