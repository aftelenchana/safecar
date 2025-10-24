
function sendData_registrar_usuario_at(){
  $('.alerta_registro_usuario_ast').html('<br><div class="proceso">'+
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
  '</div>');
  var parametros = new  FormData($('#registrar_usuario_at')[0]);
  $.ajax({
    data: parametros,
    url: 'java/registrar_cliente.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
        console.log(response);
      if (response =='error') {
        $('.alerta_registro_usuario_ast').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>')
      }else {
        var info = JSON.parse(response);
        if (info.noticia == 'cuenta_existente') {
          $('.alerta_registro_usuario_ast').html('<div class="alert alert-danger" role="alert">Este correo ya se encuentra registrado, si olvidaste tu contraseña dale en recuperar contraseña!</div>')
        }
        if (info.noticia == 'cuenta_creaqda') {
          $('.alerta_registro_usuario_ast').html('<div class="alert alert-success" role="alert">Cuenta Creada Correctamente!</div>')
        }
        if (info.noticia == 'errror_servidor') {
          $('.alerta_registro_usuario_ast').html('<div class="alert alert-danger" role="alert">Error en el servidor contacta con soporte!</div>')
        }

        if (info.noticia == 'codigo_referido_no_encontrado') {
          $('.alerta_registro_usuario_ast').html('<div class="alert alert-warning" role="alert">Código Referido no Encontrado, Verifíca o dejalo vacio!</div>')
        }




      }

    }

  });

}

document.getElementById("terminos").addEventListener("change", function() {
  document.getElementById("btn-registrar").disabled = !this.checked;
});
