



function sendData_comprar_articulo(){
  $('.notificacion_compra_articulo').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');
  var parametros = new  FormData($('#comprar_articulo_inicial')[0]);
  $.ajax({
    data: parametros,
    url: 'jquery_bancario/comprar_articulo.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      if (response =='error') {
        $('.notificacion_compra_articulo').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.resultado == 'intentos_maximos') {
          $('.notificacion_compra_articulo').html(
              '<div class="alert alert-danger d-flex align-items-center" role="alert">' +
                  '<i class="fas fa-user-lock me-2"></i>' + // FontAwesome icon for user lock
                  '<div>' +
                      '<strong>Error:</strong> Tu cuenta ha sido inhabilitada por el ingreso repetido de contraseñas incorrectas.' +
                  '</div>' +
              '</div>'
          );
      }

      if (info.resultado == 'contrasena_incorrecta') {
          $('.notificacion_compra_articulo').html(
              '<div class="alert alert-danger d-flex align-items-center" role="alert">' +
                  '<i class="fas fa-exclamation-circle me-2"></i>' + // FontAwesome icon
                  '<div>' +
                      '<strong>Error:</strong> Contraseña Incorrecta. Por favor, inténtalo de nuevo.' +
                  '</div>' +
              '</div>'
          );
      }

      if (info.resultado == 'contrasena_incorrecta_no_registrada') {
        $('.notificacion_compra_articulo').html('<div class="alert alert-danger" role="alert">Contraseña incorrecta, por motivos de seguridad, ponte en contacto con nuestro equipo urgente con error 4185!</div>');
      }
      if (info.resultado == 'saldo_insuficiente') {
          $('.notificacion_compra_articulo').html(
              '<div class="alert alert-warning d-flex align-items-center" role="alert">' +
                  '<i class="fas fa-wallet me-2"></i>' + // FontAwesome icon for wallet
                  '<div>' +
                      '<strong>Advertencia:</strong> Saldo insuficiente. Por favor, ' +
                      'recarga tu cuenta <a href="cuenta.php" class="alert-link">aquí</a>.' +
                  '</div>' +
              '</div>'
          );
      }

      if (info.resultado == 'compra_exitosa') {
          // Reproducir sonido de éxito

          // Mostrar notificación de éxito
          $('.notificacion_compra_articulo').html(
              '<div class="alert alert-success d-flex align-items-center" role="alert">' +
                  '<i class="fas fa-check-circle me-2"></i>' + // FontAwesome icon for success
                  '<div>' +
                      'Compra exitosa. Por favor, revisa la consola de <strong>mis compras</strong>.' +
                  '</div>' +
              '</div>'
          );
                    document.getElementById('successSound').play();
      }



      }

    }

  });
}
