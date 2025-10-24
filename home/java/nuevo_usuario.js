// Formatear límite de crédito
document.getElementById('limiteCredito').addEventListener('blur', function() {
    if (this.value) {
        const value = parseFloat(this.value.replace(/[^0-9.]/g, ''));
        this.value = value.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});

// Manejar subida de documentos
document.getElementById('documentos').addEventListener('change', function(e) {
    const preview = document.getElementById('documentosPreview');
    preview.innerHTML = '';

    Array.from(e.target.files).forEach(file => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'd-flex align-items-center mb-2';
        fileDiv.innerHTML = `
            <i class="fas fa-file-pdf text-danger me-2"></i>
            <span class="small">${file.name}</span>
            <span class="ms-auto small text-muted">${(file.size/1024).toFixed(2)} KB</span>
        `;
        preview.appendChild(fileDiv);
    });
});

// Manejo de la imagen de perfil
document.getElementById('profileImageUpload').addEventListener('change', function(e) {
const file = e.target.files[0];
if (file) {
const reader = new FileReader();
reader.onload = function(event) {
document.getElementById('profileImagePreview').src = event.target.result;
document.getElementById('removeProfileImage').style.display = 'inline-block';
}
reader.readAsDataURL(file);
}
});

// Botón para eliminar imagen
document.getElementById('removeProfileImage').addEventListener('click', function() {
document.getElementById('profileImagePreview').src = '/img/default-avatar.png';
document.getElementById('profileImageUpload').value = '';
this.style.display = 'none';
});

// Click en la imagen también abre el selector de archivos
document.querySelector('.profile-image-wrapper').addEventListener('click', function(e) {
if (e.target !== document.querySelector('.profile-image-upload-label')) {
document.getElementById('profileImageUpload').click();
}
});


document.addEventListener("DOMContentLoaded", function () {
    const inputRUC = document.getElementById("identificacion");
    const inputNombre = document.getElementById("nombres");
    const inputDireccion = document.getElementById("direccion");

		const inputCelular = document.getElementById("celular");
		const inputDEmail  = document.getElementById("email");

    const tipoIdentificacion  = document.getElementById("tipoIdentificacion");

    inputRUC.addEventListener("input", async function () {
        const valor = this.value.trim();

        console.log("Valor ingresado:", valor);

        if (valor.length === 13 || valor.length === 10) {
            console.log("Longitud válida, iniciando consulta...");

            try {
                const payload = {
                    parametro_busqueda: valor,
                    KEY: "92F286B0062415C8ED8DB513FCA05869"
                };

                console.log("Payload enviado:", payload);

                const response = await fetch("https://guibis.com/dev/general/", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                console.log("Respuesta cruda:", response);

                if (!response.ok) {
                    console.error("Error HTTP:", response.status);
                    return;
                }

                const result = await response.json();
                console.log("Resultado JSON:", result);

                if (result.status === "success") {
                    if (valor.length === 13) {
                        console.log("Modo RUC activado");
												// Obtener datos
											    const nombreComercial = result.data.nombreComercialMatriz || "";
											    const razonSocial = result.data.razonSocial || "";
											    const direccion = result.data.direccionMatriz || "";

											    // Selección de nombre: primero comercial, luego razón social
											    inputNombre.value = nombreComercial !== "" ? nombreComercial : razonSocial;
											    inputDireccion.value = direccion;
													inputCelular.value = data.celular || "";
													inputDEmail.value = data.mail || "";

                          tipoIdentificacion.value = "04";


                    } else if (valor.length === 10) {
                      tipoIdentificacion.value = "05";
                        console.log("Modo Cédula activado");
                        const data = result.data || {};
                        inputNombre.value  = data.Nombre || "";
												inputCelular.value = data.celular || "";
												inputDEmail.value  = data.mail || "";
                        inputDireccion.value = `${data.Domicilio || ""} / ${data.CalleDomicilio || ""} / ${data.NumeroDomicilio || ""}`;
                    }

                    // Convertir a mayúsculas
                    inputNombre.value = inputNombre.value.toUpperCase();
                    inputDireccion.value = inputDireccion.value.toUpperCase();

                    console.log("Nombre final:", inputNombre.value);
                    console.log("Dirección final:", inputDireccion.value);

                } else {
                    console.warn("Respuesta con error del servidor:", result);
                }
            } catch (error) {
                console.error("Excepción al consultar:", error);
            }
        } else {
            console.log("Esperando que se completen 10 o 13 dígitos...");
        }
    });
});


function sendData_agregar_nueva_usuario(){
  $('.agregar_nuevo_usuario').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');

  var parametros = new  FormData($('#agregar_nuevo_usuario')[0]);
  $.ajax({
    data: parametros,
    url: 'php/usuarios.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      if (response =='error') {
        $('.alerta_agregar_nuevo_usuario').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
      }else {
      var info = JSON.parse(response);
      if (info.noticia == 'insert_correct') {
        $('.alerta_agregar_nuevo_usuario').html('<div class="alert alert-success" role="alert">Usuaurio registrado Correctamente!</div>');
      }

      if (info.noticia == 'error_insertar') {
      $('.alerta_agregar_nuevo_usuario').html('<div class="alert alert-danger" role="alert">Error en el servidor '+info.contenido_error+' !</div>');

      }

      if (info.noticia == 'usuario_existente') {
      $('.alerta_agregar_nuevo_usuario').html('<div class="alert alert-danger" role="alert">Usuario Existente, ID '+info.id+' !</div>');

      }

      }

    }

  });

}
