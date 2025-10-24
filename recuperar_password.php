<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VeriCheque | Recuperar Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body, html {
      height: 100%;
      margin: 0;
      padding: 0;
      background-image: url('img/fondo.jpg'); /* Cambia por el fondo que te guste */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .forgot-card {
      background: rgba(255, 255, 255, 0.92);
      padding: 2.5rem 3rem;
      border-radius: 1rem;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 500px;
    }

    .brand-title {
      font-size: 2rem;
      font-weight: bold;
      color: #0d6efd;
    }

    .icon {
      font-size: 3rem;
      color: #0d6efd;
    }

    .form-label {
      font-weight: 500;
    }
  </style>
</head>
<body>

  <div class="forgot-card text-center">
    <div class="mb-4">
      <i class="fas fa-unlock-keyhole icon"></i>
      <div class="brand-title">VeriCheque</div>
      <small class="text-muted">Recuperar acceso a tu cuenta</small>
    </div>
    <form id="forgotForm">
      <div class="mb-3 text-start">
        <label for="email" class="form-label">Correo electrónico registrado</label>
        <input type="email" class="form-control" id="email" required>
      </div>
      <div class="d-grid mt-3">
        <button type="submit" class="btn btn-primary btn-lg">Enviar enlace de recuperación</button>
      </div>
    </form>
    <div class="mt-3">
      <a href="/" class="text-decoration-none">Volver al inicio de sesión</a>
    </div>
  </div>

  <script>
    document.getElementById('forgotForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value;
      alert("Se enviará un enlace de recuperación a: " + email + "\n(Esto es una simulación frontend 😅)");
    });
  </script>

</body>
</html>
