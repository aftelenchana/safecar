
(function(){
  $(function(){
    $('#btn-ventana-retiros').on('click',function(){
      $('#exampleModalCenter-retiros').modal();
      $('#exampleFormControlFile1').val('');
      var usuario = $(this).attr('usuario');
      var action = 'infoUsuario';
      $.ajax({
        type:"post",
          url:'jquery/general.php',
          data: {action:action},
        success:function(response){
          console.log('agregar_informacion para el deposito');
          if (response =='error') {
            $('.alert_general').html('<p class="alerta_negativa">Error al Cargar</p>')
          }else {
          var info = JSON.parse(response);



          }
        }

      })
    });


  });

}());

        function sendData_retiro_comprobante2(){
          $('#informacion-retiro-bancario').html(' <div class="">'+
             '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
           '</div>');
           var parametros = new  FormData($('#retiro_comprobante2')[0]);
          $.ajax({
            data: parametros,
            url: 'jquery_bancario/depositos.php',
            type: 'POST',
            contentType: false,
            processData: false,
            beforesend: function(){

            },
            success: function(response){
              console.log(response);

              if (response =='error') {
                $('#informacion-retiro-bancario').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
              }else {
              var info = JSON.parse(response);
              if (info.noticia == 'retiro_agregado') {
                $('#informacion-retiro-bancario').html('<div class="alert alert-success" role="alert">Retiro Exitoso! Espera unos minutos y verifica tu cuenta bancaria</div>');
              }

              if (info.noticia == 'contrasena_incorrecta') {
                $('#informacion-retiro-bancario').html('<div class="alert alert-danger" role="alert">Contraseña Incorrecta, recuerda que el sistema bloquea tu cuenta si detecta movimiento inusuales!</div>');
              }
              if (info.noticia == 'saldo_insuficiente') {
                $('#informacion-retiro-bancario').html('<div class="alert alert-danger" role="alert">No tienes fondos suficientes para realizar esta transacción!</div>');
              }
              if (info.noticia == 'cuenta_bancaria_inactiva') {
                $('#informacion-retiro-bancario').html('<div class="alert alert-danger" role="alert">Tu cuenta esta inactiva para este proceso, ve a cuenta y activa los movimientos bancarios!</div>');

              }
              if (info.noticia == 'menos_24_horas_sin_compra') {
                $('#informacion-retiro-bancario').html('<div class="alert alert-danger" role="alert">El sistema a detectado que deseas hacer una transferencia interbancaria, realiza una compra o espera 24 horas desde tu ultimo depósito.!</div>');

              }




              }

            }

          });

        }
