// Función para previsualizar la imagen seleccionada
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "<?php echo $img_sistema; ?>";
    }
}

// Validación RUC existente
document.getElementById('ruc').addEventListener('input', function(e) {
    const ruc = e.target.value;
    const consultarBtn = document.getElementById('consultarSRI');

    if (ruc.length === 13 && /^\d+$/.test(ruc)) {
        consultarBtn.disabled = false;
        document.getElementById('rucFeedback').innerHTML = '<i class="fas fa-check-circle text-success"></i> Formato válido';
    } else {
        consultarBtn.disabled = true;
        document.getElementById('rucFeedback').innerHTML = '<i class="fas fa-exclamation-circle text-warning"></i> Ingrese 13 dígitos';
    }
});

// Simular consulta al SRI
document.getElementById('consultarSRI').addEventListener('click', function() {
    const sriResult = document.getElementById('sriResult');
    sriResult.classList.remove('d-none');

    // Aquí iría la lógica real de consulta al SRI
    document.getElementById('razonSocial').value = "TECNOLOGIAS AVANZADAS S.A.";
    document.getElementById('nombreComercial').value = "TECNOAVANZ";
    document.getElementById('razonSocialResult').textContent = "TECNOLOGIAS AVANZADAS S.A.";
    document.getElementById('nombreComercialResult').textContent = "TECNOAVANZ";
    document.getElementById('tipoContribuyenteResult').textContent = "SOCIEDAD";
});

// Validar tamaño y tipo de archivo antes de subir
document.getElementById('logoEmpresa').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (file) {
        if (!validTypes.includes(file.type)) {
            alert('Por favor seleccione una imagen válida (JPG, PNG o GIF)');
            this.value = '';
            return false;
        }

        if (file.size > maxSize) {
            alert('La imagen es demasiado grande. El tamaño máximo permitido es 2MB.');
            this.value = '';
            return false;
        }
    }
});

function sendData_agregar_empresa(){
  $('.alerta_agregar_empresas').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');

  var parametros = new  FormData($('#agregar_empresa')[0]);
  $.ajax({
    data: parametros,
    url: 'php/empresas.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      if (response =='error') {
        $('.alerta_agregar_empresas').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.noticia == 'insert_correct') {
        $('.alerta_agregar_empresas').html('<div class="alert alert-success" role="alert">Empresa registrada Correctamente!</div>');
      }

      if (info.noticia == 'error_insertar') {
      $('.alerta_agregar_empresas').html('<div class="alert alert-danger" role="alert">Error en el servidor '+info.contenido_error+' !</div>');

      }

      }

    }

  });

}
