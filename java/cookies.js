$(document).ready(function(){
  $.ajax({
    url:'java/cookies.php',
    type:'POST',
    async: true,
     success: function(response){
       console.log(response);
      var respuesta = JSON.parse(response);
      if (respuesta.respuesta != 'no_existe_resultados') {
         $('#modal_acceder_cuentas_iniciadas').modal();
        var html = '';
        for (var i = 0; i < respuesta.length; i++) {
          var data_lista = respuesta[i];
          // Aquí validamos el rol antes de construir el HTML
          if (data_lista.rol === 'Cuenta Empresa') {
            html += '<div class="col-4 iniciar_sesion_usuario"  nombres="'+data_lista.nombres+'" rol_interno="" codigo="'+data_lista.id+'" rol="'+data_lista.rol+'" url_img_upload="'+data_lista.url_img_upload+'" img_facturacion="'+data_lista.img_facturacion+'">' +
                      '<div class="card user-card">' +
                        '<img src="'+data_lista.url_img_upload+'/home/img/uploads/'+data_lista.img_facturacion+'" class="card-img-top" alt="User Image" />' +
                        '<div class="card-body">' +
                          '<p class="card-text">' + data_lista.nombres + '</p>' +
                          '<p class="card-text">' + data_lista.rol + '</p>' +
                        '</div>' +
                      '</div>' +
                    '</div>';
          }

          if (data_lista.rol === 'Usuario Venta') {
            html += '<div class="col-4 iniciar_sesion_usuario" nombres="'+data_lista.nombres+'" rol_interno=""  codigo="'+data_lista.id+'" rol="'+data_lista.rol+'" url_img_upload="'+data_lista.url_img_upload+'" foto="'+data_lista.foto+'">' +
                      '<div class="card user-card">' +
                        '<img src="'+data_lista.url_img_upload+'/home/img/uploads/'+data_lista.foto+'" class="card-img-top" alt="User Image" />' +
                        '<div class="card-body">' +
                          '<p class="card-text">' + data_lista.nombres + '</p>' +
                          '<p class="card-text">' + data_lista.rol + '</p>' +
                        '</div>' +
                      '</div>' +
                    '</div>';
          }

          if (data_lista.rol === 'Recursos Humanos') {
            html += '<div class="col-4 iniciar_sesion_usuario" nombres="'+data_lista.nombres+'"  rol_interno="'+data_lista.nombre+'"  codigo="'+data_lista.id+'" rol="'+data_lista.rol+'" url_img_upload="'+data_lista.url_img_upload+'" foto="'+data_lista.foto+'">' +
                      '<div class="card user-card">' +
                        '<img src="'+data_lista.url_img_upload+'/home/img/uploads/'+data_lista.foto+'" class="card-img-top" alt="User Image" />' +
                        '<div class="card-body">' +
                          '<p class="card-text">' + data_lista.nombres + '</p>' +
                          '<p class="card-text">' + data_lista.nombre + '</p>' +
                        '</div>' +
                      '</div>' +
                    '</div>';
          }

        }
        $('#resultado_cookies_existentes').html(html);

      }

     },
     error:function(error){
       console.log(error);
       }

     });

});





$(document).on("click", ".iniciar_sesion_usuario", function() {
    var codigo = $(this).attr('codigo');
    var rol = $(this).attr('rol');
    var rol_interno = $(this).attr('rol_interno');
    var action = 'iniciar_sesion_rapida';
    $.ajax({
      type:"post",
      url:"java/inicio_rapido.php",
      data: {action:action,rol:rol,codigo:codigo,rol_interno:rol_interno},
      success:function(response){
        console.log(response);
        if (response =='error') {
          $('.alert_general').html('<p class="alerta_negativa">Error al Cargar</p>')
        }else {
          var info = JSON.parse(response);
          if (info.respuesta == 'password_exitoso') {
            $('.alerta_ingreso_sistema_rapido').html('<div class="alert alert-success background-success">'+
            '<strong>Ingreso Correcto </strong>Redirigiendo al sistema'+
            '</div>');
            window.location.reload(true);

          }

        }

      }

    })



});
