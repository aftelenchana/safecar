
function sendDatapassword(){
  $('.alert_recuperar_contrasena').html('<div class="proceso">'+
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
  '</div>');
  var parametros = new  FormData($('#add_form_password')[0]);
  $.ajax({
    data: parametros,
    url: 'java/recuperar_password.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
        console.log(response);
      if (response =='error') {
        $('.alert_recuperar_contrasena').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
        var info = JSON.parse(response);
        if (info.resp_password == 'positiva') {
          $('.resultado_input_recuperar_contrasena').html('<div class="form-floating form-floating-outline mb-5">'+
                    '<input type="text" required class="form-control" id="codigo" name="codigo_recuperacion" placeholder="Ingresa el codigo enviado a tu Email" />'+
                    '<label for="email">Código</label>'+
                '  </div>'+
                '  <div class="mb-5 form-password-toggle">'+
                    '<div class="input-group input-group-merge">'+
                    '  <div class="form-floating form-floating-outline">'+
                    '    <input required'+
                      '    type="password"'+
                      '    id="password"'+
                      '    class="form-control"'+
                      '    name="new_password"'+
                      '    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"'+
                      '    aria-describedby="password" />'+
                      '  <label for="password">Nueva Contraseña</label>'+
                      '</div>'+
                    '  <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>'+
                  '  </div><input type="hidden" name="idsuer" id="idsuer" value="'+info.iduser+'">'+
                '  </div>');

                $('.alert_recuperar_contrasena').html('');
                $('#action').val('verificar_codigo');


        }
        if (info.resp_password == 'error_enviar_codigo') {
          $('.alert_recuperar_contrasena').html('<p style="background: #ff481c;border-radius: 5px;">Error al enviar el codigo contactanos en nuestras lineas directas.</p>');
        }

        if (info.resp_password == 'no_existe_email') {
          $('.alert_recuperar_contrasena').html('<p style="background: #ff481c;border-radius: 5px;">El email que ingresaste no existe.</p>');

        }


        if (info.resp_password == 'ingrese_codigo_valido') {
          $('.alert_recuperar_contrasena').html('<p style="background: #ff481c;border-radius: 5px;">Ingrese un código válido.</p>');

        }

        if (info.resp_password == 'contrasena_cambiada') {
          $('.alert_recuperar_contrasena').html('<div class="alert alert-success" role="alert">Contraseña Cambiada Correctamente!</div>');

        }

      }

    }

  });

}




function sendDataverifi_pasword_fin(){
   $('.alert_verrifi_codigo').html('<div class="proceso">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');
  var parametros = new  FormData($('#add_form_repera_pasword_fin')[0]);
  $.ajax({
    data: parametros,
    url: 'java/confirmar_codigo.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
      console.log(response);

      if (response =='error') {
        $('.alert_general').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.resp_password == 'positiva') {
        $('.alert_verrifi_codigo').html('<div class="alert alert-success background-success">'+
        '</button>'+
        '<strong>Contraseña!</strong> cambiada Correctamente'+
        '</div>');
      }

      if (info.resp_password == 'ingrese_codigo_valido') {
        $('.alert_verrifi_codigo').html('<p>Codigo Incorrecto.</p>');
      }

      if (info.resp_password == 'cuenta_inexistente') {
        $('.alert_verrifi_codigo').html('<p>Cuenta Inexistente.</p>');
      }


      }

    }

  });

}
function closeModal_verificar_codigo(){
  $('.modal_password').fadeOut();
}
