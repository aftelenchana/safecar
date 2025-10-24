// Mostrar logo del banco seleccionado
document.getElementById('banco').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const logoContainer = document.getElementById('banco-logo-container');
    const logoImg = document.getElementById('banco-logo');

    if (selectedOption.dataset.logo) {
        logoImg.src = `img/reacciones/${selectedOption.dataset.logo}`;
        logoContainer.style.display = 'block';
    } else {
        logoContainer.style.display = 'none';
    }
});

// Validación de número de cuenta
document.getElementById('validarCuenta').addEventListener('click', function() {
    const cuentaInput = document.getElementById('numeroCuenta');
    const feedback = document.getElementById('cuentaFeedback');

    if (cuentaInput.checkValidity()) {
        feedback.innerHTML = '<i class="fas fa-check-circle text-success"></i> Formato de cuenta válido';
        // Aquí iría la validación real con el banco
    } else {
        feedback.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Número de cuenta inválido';
    }
});

// Formatear saldo inicial
document.getElementById('saldoInicial').addEventListener('blur', function() {
    if (this.value) {
        const value = parseFloat(this.value.replace(/[^0-9.]/g, ''));
        this.value = value.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});



function sendData_nueva_cuenta_bancaria(){
  $('.alerta_agregar_cuenta_bancaria').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');

  var parametros = new  FormData($('#nueva_cuenta_bancaria')[0]);
  $.ajax({
    data: parametros,
    url: 'php/cuentas_bancarias.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      if (response =='error') {
        $('.alerta_agregar_cuenta_bancaria').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.noticia == 'insert_correct') {
        $('.alerta_agregar_cuenta_bancaria').html('<div class="alert alert-success" role="alert">Cuenta Bancaria Agregada Correctamente!</div>');
      }

      if (info.noticia == 'error_insertar') {
      $('.alerta_agregar_cuenta_bancaria').html('<div class="alert alert-danger" role="alert">Error en el servidor '+info.contenido_error+' !</div>');

      }

      }

    }

  });

}
