


function sendData_login_usuario(){
  $('.notificacion_login_supercias').html('<div class="proceso">'+
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
  '</div>');
  var parametros = new  FormData($('#loginForm')[0]);
  $.ajax({
    data: parametros,
    url: 'java/login_usuario.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
        console.log(response);
      if (response =='error') {
        $('.notificacion_login_supercias').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>')
      }else {
        var info = JSON.parse(response);
        if (info.noticia == 'cuenta_existente') {
          $('.notificacion_login_supercias').html('<div class="alert alert-danger" role="alert">Este correo ya se encuentra registrado, si olvidaste tu contraseña dale en recuperar contraseña!</div>')
        }
        if (info.noticia == 'login_exitoso') {
          $('.notificacion_login_supercias').html('<div class="alert alert-success" role="alert">Ingreso Correcto Redirigiendo al sistema !</div>')

          window.location.reload(true);
              console.log('Contraseña correcta, acceso exitoso.');
        }
        if (info.noticia == 'password_incorrecto') {
          $('.notificacion_login_supercias').html('<div class="alert alert-danger" role="alert">Error en credenciales!</div>')
        }

        if (info.noticia == 'no_existe_usuario') {
          $('.notificacion_login_supercias').html('<div class="alert alert-warning" role="alert">No estas en nuestro sistema, registrate!</div>')
        }




      }

    }

  });

}
