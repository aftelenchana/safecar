<?php
$iduser= $_SESSION['id'];
$id_generacion =  $iduser;
$user_in= $_SESSION['user_in'];



//INFORMACION PARA SACAR DE LA EMPRESA  EL CUAL USAURIO SE HA REGISTRADO
$query_user_in = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$user_in");
$data_user_in=mysqli_fetch_array($query_user_in);
$nombre_empresa_empresa    = $data_user_in['nombre_empresa'];
$img_logo_empresa          = $data_user_in['img_facturacion'];
$url_img_upload_empresa    = $data_user_in['url_img_upload'];
$url_admin                 = $data_user_in['url_admin'];
$descripcion_empresa       = $data_user_in['descripcion'];




$query = mysqli_query($conection, "SELECT * FROM usuarios    WHERE usuarios.id =$iduser");
$result=mysqli_fetch_array($query);
$nombres           = $result['nombres'];
$direccion         = $result['direccion'];
$codigo_sri        = $result['codigo_sri'];
$img_logo          = $result['img_facturacion'];
$url_img_upload    = $result['url_img_upload'];

$email_user           = $result['email'];
$fecha                = $result['fecha_creacion'];
$ciudad_user          = $result['ciudad'];
$telefono_user        = $result['telefono'];
$celular_user         = $result['celular'];
$nombre_empresa       = $result['nombre_empresa'];
$razon_social         = $result['razon_social'];
$numero_identidad     = $result['numero_identidad'];

$whatsapp             = $result['whatsapp'];
$instagram            = $result['instagram'];
$facebook             = $result['facebook'];
$pagina_web           = $result['pagina_web'];
$descripcion_usuerio  = $result['descripcion'];
$latitud             = $result['latitud'];
$longitud            = $result['longitud'];
$id_desarrolador     = $result['id_e'];
$password_user       = $result['password'];
$key_user            = $result['codigo_registro'];
$cuota               = $result['cuota'];
$plan_whatsaapp      = $result['plan_whatsaapp'];
$api_gpt             = $result['api_gpt'];

$carpeta_drive       = $result['carpeta_drive'];
$json_google_drive   = $result['json_google_drive'];


$query_configuracioin = mysqli_query($conection, "SELECT * FROM configuraciones ");
$result_configuracion = mysqli_fetch_array($query_configuracioin);
$ambito_area          =  $result_configuracion['ambito'];
$envio_wsp            =  $result_configuracion['envio_wsp'];
$url_conect_wsp       =  $result_configuracion['url_wsp'];

?>

<header class="topbar">
  <div class="logo-container">
    <img src="https://guibis.com/img/guibis.png" alt="Logo">
    <button class="btn btn-primary" id="toggleMenuBtn">
      <i class="fas fa-bars" id="toggleIcon"></i>
    </button>
  </div>
  <div class="user-menu ms-auto" id="userMenu">

    <div style="display:flex; align-items:center; column-gap:16px; padding:0 10px;">
      <!-- Notificaciones -->
      <a href="notificaciones" class="text-decoration-none text-dark position-relative">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          3
        </span>
      </a>

      <!-- Cuenta bancaria -->
      <a href="cuenta/historial" class="text-decoration-none text-dark">
        <i class="fas fa-university fa-lg"></i>
      </a>

      <img src="<?php echo $url_img_upload ?>/home/img/uploads/<?php echo $img_logo ?>" class="user-avatar" id="avatarToggle">
      <ul class="dropdown-menu mt-2 p-2">
        <li><a class="dropdown-item" href="#">Perfil</a></li>
        <li><a class="dropdown-item" href="configurar">Configurar</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="salir">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</header>
<aside class="sidebar" id="sidebar">
  <ul class="nav nav-pills flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="/"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    </li>

    <!-- Mis Aplicaciones -->
    <li class="nav-item">
      <a class="nav-link" href="espacios_parq"><i class="fas fa-th-large me-2"></i>Espacios</a>
    </li>

    <!-- Mi cuenta bancaria -->
    <li class="nav-item">
      <a class="nav-link" href="tarifas_parqueadero"><i class="fas fa-building-columns me-2"></i>Tarifas Parqueadero</a>
    </li>


    <li class="nav-item">
      <a class="nav-link" href="tipos_vehiculos"><i class="fas fa-building-columns me-2"></i>Tipos de Vehículos</a>
    </li>


    <li class="nav-item">
      <a class="nav-link" href="tablero_parqueadero"><i class="fas fa-building-columns me-2"></i>Tablero</a>
    </li>

    <!-- API RUC -->
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-id-card me-2"></i> API RUC <i class="fas fa-chevron-down float-end"></i></a>
      <ul class="submenu">
        <li><a class="nav-link" href="consulta_ruc"><i class="fas fa-magnifying-glass me-2"></i> Consulta</a></li>
        <li><a class="nav-link" href="api/ruc/historial"><i class="fas fa-clock-rotate-left me-2"></i> Historial</a></li>
      </ul>
    </li>


  </ul>
</aside>
