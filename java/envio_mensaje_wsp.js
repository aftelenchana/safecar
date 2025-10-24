
function sendData_envio_mensajes_wsp(){
  $('.alerta_envio_mensaje').html('<div class="proceso">'+
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
  '</div>');
  var parametros = new  FormData($('#envio_mensajes_wsp')[0]);
  $.ajax({
    data: parametros,
    url: 'java/enviar_mensaje_wsp.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
        console.log(response);
      if (response =='error') {
        $('.alerta_envio_mensaje').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>')
      }else {
        var info = JSON.parse(response);

        if (info.respuesta == 'mensaje_enviado_correctamente') {
          $('.alerta_envio_mensaje').html('<div class="alert alert-success" role="alert">Mensaje enviado Correctamente!</div>')
        }
        if (info.respuesta == 'no_se_envio_mensaje') {
          $('.alerta_envio_mensaje').html('<div class="alert alert-danger" role="alert">No se pudo Enviar el Mensaje!</div>')
        }

        if (info.respuesta == 'respuesta_diferente') {
          $('.alerta_envio_mensaje').html('<div class="alert alert-warning" role="alert">Error Interno!</div>')
        }




      }

    }

  });

}
