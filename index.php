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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($nombre_empresa); ?></title>
  <link rel="icon" href="<?php echo htmlspecialchars($img_sistema); ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/estilos/footer.css?v=1.0">
  <link rel="stylesheet" href="/estilos/index.css?v=1.0">
  <link rel="stylesheet" href="https://guibis.com/home/estiloshome/load.css">

</head>
<body>
  <!-- Header/Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="https://guibis.com/img/guibis.png" alt="GUIBIS" height="40" class="me-2 floating">
        <span>Safecar</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="#inicio">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#servicios">Servicios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#nosotros">Sobre Nosotros</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contacto">Contacto</a>
          </li>
        </ul>
        <div class="d-flex">
          <a href="login" class="btn btn-outline-glow me-2">Iniciar Sesión</a>
          <a href="registro" class="btn btn-glow">Registrarse</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="inicio" class="hero-section">
    <div class="container hero-content">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="hero-title">Sistema de Parqueadero</h1>
          <p class="hero-subtitle">
            Administra tu parqueadero de forma sencilla y eficiente. Controla los vehículos registrados, gestiona las plazas disponibles y lleva un control de ingresos y salidas con reportes automáticos.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="login" class="btn btn-glow btn-lg">Entrar</a>
            <a href="registro" class="btn btn-outline-glow btn-lg">Registrar</a>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <img src="<?php echo $img_sistema ?>" alt="<?php echo $img_sistema ?>" class="img-fluid floating" style="max-height: 400px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">
        </div>
      </div>
    </div>
  </section>

  <!-- Servicios Section -->
  <section id="servicios" class="services-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Nuestros Servicios de API</h2>
        <p class="section-subtitle">Ofrecemos una amplia gama de APIs para verificación de datos con la máxima seguridad y precisión</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-id-card"></i>
            </div>
            <h4>Registro Civil</h4>
            <p>Verificación de datos de identificación civil con resultados precisos y en tiempo real.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-building"></i>
            </div>
            <h4>Consulta RUC</h4>
            <p>Obtención de información fiscal de empresas y contribuyentes mediante RUC.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-user-shield"></i>
            </div>
            <h4>Antecedentes</h4>
            <p>Consultas de antecedentes penales, policiales y judiciales de forma segura.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-car"></i>
            </div>
            <h4>Vehículos</h4>
            <p>Verificación de información vehicular, propiedad y antecedentes de multas.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Títulos Académicos</h4>
            <p>Validación de títulos profesionales y grados académicos emitidos por instituciones.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card">
            <div class="feature-icon">
              <i class="fas fa-file-contract"></i>
            </div>
            <h4>Documentos Legales</h4>
            <p>Verificación de autenticidad de documentos legales y registros públicos.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sobre Nosotros Section -->
  <section id="nosotros" class="about-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h2 class="section-title text-start">Sobre GUIBIS</h2>
          <p class="mb-4">GUIBIS es una empresa líder en soluciones tecnológicas para verificación de datos. Nuestro equipo de expertos ha desarrollado APIs confiables y seguras que permiten a empresas e instituciones acceder a información verificada de manera eficiente.</p>
          <p class="mb-4">Nos especializamos en consultas de registro civil, RUC, antecedentes y otros datos relevantes, garantizando la precisión y seguridad de la información proporcionada.</p>
          <ul class="feature-list">
            <li><i class="fas fa-check-circle"></i> Datos verificados y actualizados</li>
            <li><i class="fas fa-check-circle"></i> Respuesta en tiempo real</li>
            <li><i class="fas fa-check-circle"></i> Seguridad y encriptación garantizada</li>
            <li><i class="fas fa-check-circle"></i> Soporte técnico especializado</li>
            <li><i class="fas fa-check-circle"></i> Integración sencilla con tus sistemas</li>
          </ul>
        </div>
        <div class="col-lg-6">
          <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Sobre Nosotros" class="img-fluid about-img">
        </div>
      </div>
    </div>
  </section>

  <!-- Contacto Section -->
  <section id="contacto" class="contact-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Contáctanos</h2>
        <p class="section-subtitle">¿Tienes preguntas? Estamos aquí para ayudarte con tus necesidades de integración de APIs</p>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="contact-form">
            <form>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="nombre" class="form-label">Nombre</label>
                  <input type="text" class="form-control" id="nombre" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" required>
                </div>
                <div class="col-12">
                  <label for="asunto" class="form-label">Asunto</label>
                  <input type="text" class="form-control" id="asunto" required>
                </div>
                <div class="col-12">
                  <label for="mensaje" class="form-label">Mensaje</label>
                  <textarea class="form-control" id="mensaje" rows="5" required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-glow">Enviar Mensaje</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

    <?php include 'scripts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          window.scrollTo({
            top: target.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });
  </script>
</body>
</html>
