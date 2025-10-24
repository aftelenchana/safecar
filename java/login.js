

function sendData_login_usuarios() {
  $('.alerta_ingreso_sistema').html('<div class="proceso">' +
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
    '</div>');
  var parametros = new FormData($('#login_usuarios')[0]);
  $.ajax({
    data: parametros,
    url: 'java/login.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforeSend: function() {
      // Acciones a realizar antes del envío, como mostrar un spinner.
    },
    success: function(response) {
      console.log(response);
      try {
        var respuesta = JSON.parse(response);
        console.log(response);

        // Verificamos si la respuesta es un mensaje específico
        if (respuesta.respuesta) {
          switch (respuesta.respuesta) {
            case 'no_existe_resultados':
              console.log('No hay usuarios registrados.');
              $('.alerta_ingreso_sistema').html('<div class="alert alert-warning background-warning">'+
              '<strong>No tienes una cuenta en nuestro sistema </strong>Regístrate gratuitamente'+
              '</div>');
              break;
            case 'password_exitoso':
            $('.alerta_ingreso_sistema').html('<div class="alert alert-success background-success">'+
            'Ingreso Correcto Redirigiendo al sistema'+
            '</div>');
            window.location.reload(true);
              console.log('Contraseña correcta, acceso exitoso.');
              // Aquí puedes redirigir al usuario o actualizar la UI como sea necesario
              break;
            case 'contraea_incorrecta':

            $('.alerta_ingreso_sistema').html('<div class="alert alert-danger background-danger">'+
            '<strong>Contraseña </strong>incorrecta, recupera tu contraseña si lo olvidaste'+
            '</div>');
              console.log('La contraseña es incorrecta.');
              // Aquí puedes mostrar un mensaje al usuario o permitirle intentarlo de nuevo
              break;

              case 'intentos_maximos':

              $('.alerta_ingreso_sistema').html('<div class="alert alert-danger background-danger">'+
              '<strong>Intentos Máximos permitidos</strong> comunícate con soporte. '+
              '</div>');
                console.log('Intentos Máximos.');
                // Aquí puedes mostrar un mensaje al usuario o permitirle intentarlo de nuevo
                break;

                case 'plataforma_no_habilitada':

                $('.alerta_ingreso_sistema').html('<div class="alert alert-danger background-danger">'+
                '<strong>Estimado Abogado permitidos</strong> su cuenta no tiene permiso de ingresar al sistema,comuniquese con el Administrador. '+
                '</div>');
                  console.log('Intentos Máximos.');
                  // Aquí puedes mostrar un mensaje al usuario o permitirle intentarlo de nuevo
                  break;

            default:

            $('.alerta_ingreso_sistema').html('<div class="alert alert-danger background-danger">'+
            '<strong>Error </strong>'+respuesta.respuest+' '+
            '</div>');
          }
        } else {
          // Aquí manejas la lista de usuarios como lo hacías antes
          var html = '';
          for (var i = 0; i < respuesta.length; i++) {
            var data_lista = respuesta[i];
            // Aquí validamos el rol antes de construir el HTML
            if (data_lista.rol === 'cuenta_empresa') {
              html += '<div class="col-4 tarjeta_presentacion_usuario"  nombres="'+data_lista.nombres+'" codigo="'+data_lista.id+'" rol="'+data_lista.rol+'" url_img_upload="'+data_lista.url_img_upload+'" img_facturacion="'+data_lista.img_facturacion+'">' +
                        '<div class="card user-card">' +
                          '<img src="'+data_lista.url_img_upload+'/home/img/uploads/'+data_lista.img_facturacion+'" class="card-img-top" alt="User Image" />' +
                          '<div class="card-body">' +
                            '<p class="card-text">' + data_lista.nombres + '</p>' +
                            '<p class="card-text">Abogado</p>' +
                          '</div>' +
                        '</div>' +
                      '</div>';
            }



            if (data_lista.rol === 'Cliente') {
              html += '<div class="col-4 tarjeta_presentacion_cliente" nombres="'+data_lista.nombres+'"  codigo="'+data_lista.id+'" rol="'+data_lista.rol+'" url_img_upload="'+data_lista.url_img_upload+'" foto="'+data_lista.foto+'">' +
                        '<div class="card user-card">' +
                          '<img src="/home/img/uploads/'+data_lista.foto+'" class="card-img-top" alt="User Image" />' +
                          '<div class="card-body">' +
                            '<p class="card-text">' + data_lista.nombres + '</p>' +
                            '<p class="card-text">' + data_lista.rol + '</p>' +
                          '</div>' +
                        '</div>' +
                      '</div>';
            }

          }
          $('.alerta_ingreso_sistema').html('');
          $('#resultado_usuarios_cuenta_empresa').html(html);
          document.getElementById('contendedor_elementos_desaparecer').style.display = "none";
        }
      } catch (e) {
        console.error('Error al analizar la respuesta del servidor:', e);
        $('.alerta_login_usuario').html('<div class="alert alert-danger" role="alert">Error al procesar la respuesta del servidor.</div>');
      }
    },
    error: function(xhr, status, error) {
      // Manejo de errores
      $('.alerta_login_usuario').html('<div class="alert alert-danger" role="alert">Ha ocurrido un error: ' + error + '</div>');
    }
  });
}





$(document).on("click", ".tarjeta_presentacion_usuario", function() {

    var codigo = $(this).attr('codigo');
    var nombres = $(this).attr('nombres');
    var rol = $(this).attr('rol');
    var url_img_upload = $(this).attr('url_img_upload');
    var img_facturacion = $(this).attr('img_facturacion');
    document.getElementById('resultado_usuarios_cuenta_empresa').style.display = "none";
    document.getElementById('contendedor_elementos_desaparecer').style.display = "block";
    $('.resultado_nombres_users').html('Ingresar a cuenta de '+nombres+'');
    $('#contendedor_elementos_desaparecer').html('<div class="row g-3 ">' +
                          '<div class="col-4 " style="margin: 0 auto;">' +
                                  '<div class="card user-card">' +
                                    '<img src="'+url_img_upload+'/home/img/uploads/'+img_facturacion+'" class="card-img-top" alt="User Image" />' +
                                    '<div class="card-body">' +
                                      '<p class="card-text">Abogado</p>' +
                                      '<p class="card-text">' +nombres + '</p>' +
                                    '</div>' +
                                  '</div>' +
                                '</div>'+
                          '</div>'+
                              ' <br><div class="form-group form-primary">'+
                              '<div class="form-group">' +
                                '  <div class="input-group">' +
                                      '<div class="input-group-prepend">' +
                                          '<i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility()"></i>' +
                                      '</div>' +
                                      '<input type="password" name="password_user" placeholder="Contraseña" class="form-control" autocomplete="off" required>' +
                                  '</div>' +
                              '</div>' +
                              '<div class="m-account--actions">' +
                                  '<a href="#" class="btn-link">Olvidaste tu Contraseña?</a>' +
                                  '<input type="hidden" name="rol" value="'+rol+'">'+
                                '  <input type="hidden" name="action" value="ingresar_login">'+
                                '  <input type="hidden" name="id_user" value="'+codigo+'">'+
                                  '<button type="submit" class="btn btn-rounded btn-info">Ingresar</button>' +
                              '</div>');
});







$(document).on("click", ".tarjeta_presentacion_cliente", function() {
    var codigo = $(this).attr('codigo');
    var nombres = $(this).attr('nombres');
    var rol = $(this).attr('rol');
    var foto = $(this).attr('foto');
    document.getElementById('resultado_usuarios_cuenta_empresa').style.display = "none";
    document.getElementById('contendedor_elementos_desaparecer').style.display = "block";
    $('.resultado_nombres_users').html('Ingresar a cuenta de '+nombres+'');
    $('#contendedor_elementos_desaparecer').html('<div class="row g-3 ">' +
                          '<div class="col-4 " style="margin: 0 auto;">' +
                                  '<div class="card user-card">' +
                                    '<img src="/home/img/uploads/'+foto+'" class="card-img-top" alt="User Image" />' +
                                    '<div class="card-body">' +
                                    '<p class="card-text">' +nombres + '</p>' +
                                      '<p class="card-text">' +rol + '</p>' +
                                    '</div>' +
                                  '</div>' +
                                '</div>'+
                          '</div>'+
                          ' <br><div class="form-group form-primary">'+
                          '<div class="form-group">' +
                            '  <div class="input-group">' +
                                  '<div class="input-group-prepend">' +
                                      '<i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility()"></i>' +
                                  '</div>' +
                                  '<input type="password" name="password_user" placeholder="Contraseña" class="form-control" autocomplete="off" required>' +
                              '</div>' +
                          '</div>' +
                          '<div class="m-account--actions">' +
                              '<a href="#" class="btn-link">Olvidaste tu Contraseña?</a>' +
                              '<input type="hidden" name="rol" value="'+rol+'">'+
                            '  <input type="hidden" name="action" value="ingresar_login">'+
                            '  <input type="hidden" name="id_user" value="'+codigo+'">'+
                              '<button type="submit" class="btn btn-rounded btn-info">Ingresar</button>' +
                          '</div>');

});
