function sendData_editar_cuenta(){
  $('.alerta_editar_perfil').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');

  var parametros = new  FormData($('#editar_cuenta')[0]);
  $.ajax({
    data: parametros,
    url: 'cuenta/perfil.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      if (response =='error') {
        $('.alerta_editar_perfil').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.noticia == 'insert_correct') {
          $('.alerta_editar_perfil').html('<div class="alert alert-success" role="alert">Perfil Editado Correctamente!</div>');
      }
      if (info.noticia == 'error_insertar') {
      $('.alerta_editar_perfil').html('<div class="alert alert-danger" role="alert">Error en el servidor '+info.contenido_error+' !</div>');

      }

      }

    }

  });

}


// Seleccionar el input y la imagen
const uploadInput = document.getElementById('upload');
const uploadedAvatar = document.getElementById('uploadedAvatar');

// Agregar evento para detectar cambios en el input
uploadInput.addEventListener('change', function(event) {
  const file = event.target.files[0]; // Obtener el archivo seleccionado

  if (file) {
    const reader = new FileReader(); // Crear un objeto FileReader

    // Configurar el evento onload para cuando se lea el archivo
    reader.onload = function(e) {
      uploadedAvatar.src = e.target.result; // Establecer la imagen como el resultado del archivo leído
    };

    reader.readAsDataURL(file); // Leer el archivo como una URL de datos
  }
});
