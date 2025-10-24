function sendData_agregar_producto_guibis_pay(){
  $('.respuesta_producto_agregado_pay_guibis').html(' <div class="notificacion_negativa">'+
   '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
 '</div>');
  var parametros = new  FormData($('#formulario_agregar_producto')[0]);
  $.ajax({
    data: parametros,
    url:"jquery_bancario/guibispay.php",
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){
    },
    success: function(response){
      console.log(response);
      var info = JSON.parse(response);
      if (info.respuesta == 'no_existe_producto') {
        $('.respuesta_producto_agregado_pay_guibis').html('<div class="alert alert-danger" role="alert">No Existe este producto !</div>');
      }else {

        if (info.respuesta == 'agregado_primera_vez') {
          $('.respuesta_agregar_productos_guibispay').html(info.resumen_tabla_producto);
          $('.resumen_financiero').html(info.resumen_pago);
          $('.respuesta_producto_agregado_pay_guibis').html('<div class="alert alert-success" role="alert">Producto Agregado Correctamente !</div>');
        }

        if (info.respuesta == 'producto_existente') {
          $('.respuesta_agregar_productos_guibispay').html(info.resumen_tabla_producto);
          $('.resumen_financiero').html(info.resumen_pago);
          $('.respuesta_producto_agregado_pay_guibis').html('<div class="alert alert-success" role="alert">Producto Agregado Correctamente !</div>');
        }




      }

    }

  });

}




$('.limpiar_consola_guibis_pay').on('click',function(){
       var action = 'limpiar_consola';
       $.ajax({
         url:'jquery_bancario/guibispay.php',
         type:'POST',
         async: true,
         data: {action:action},
          success: function(response){
            console.log(response);
            var info = JSON.parse(response);
            if (info.respuesta == 'consola_limpia') {
              $('.respuesta_agregar_productos_guibispay').html(info.resumen_tabla_producto);
              $('.resumen_financiero').html(info.resumen_pago);
            }
            if (info.respuesta == 'error_limpiar_consola') {
                $('.respuesta_limpiar_consola').html('<div class="alert alert-danger" role="alert">Error en el servidor !</div>');

            }

          },
          error:function(error){
            console.log(error);
            }
          });
 });



  (function(){
    $(function(){
      $('#boton_pagar_total').on('click',function(){
        $('#modal_pagar_guibis').modal();
        var action = 'infomracion_venta';
        $.ajax({
          url:'jquery_bancario/guibispay.php',
          type:'POST',
          async: true,
          data: {action:action},
           success: function(response){
             console.log(response);
             var info = JSON.parse(response);
             if (info.respuesta == 'existencia_correcta') {
                 $('.respuesta_productos_pago_guibis').html(info.resumen_tabla_producto);
                 $('.respuesta_financiero_pagar_jhg').html(info.resumen_pago);
                 $('.respuesta_cuenta_encontrada').html('<form class="" method="post" name="verificar_email_comprador" id="verificar_email_comprador" onsubmit="event.preventDefault(); sendData_verificar_email_comprador();" >'+
                                 '<div class="form-group">'+
                                     '<label for="exampleInputEmail1">Ibgresa el Email del Usuario Comprador</label>'+
                                    ' <input type="email" name="email_comprador" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ingresa tu email">'+
                                    ' <small id="emailHelp" class="form-text text-muted">Al ingresar el email se verificará esta información .</small>'+
                                ' </div>'+
                                 '<div class="">'+
                                   '<input type="hidden" name="action" value="verificar_email_comprador">'+
                                   '<button type="submit" class="btn btn-success">Verificar Email</button>'+
                                 '</div>'+
                                 '<div class="resultado_verificacion_email">'+
                                 '</div>'+
                               '</form>');


             }



           },
           error:function(error){
             console.log(error);
             }
           });
      });


    });

  }());



  function sendData_verificar_email_comprador(){
    $('.resultado_verificacion_email').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');
    var parametros = new  FormData($('#verificar_email_comprador')[0]);
    $.ajax({
      data: parametros,
      url:"jquery_bancario/guibispay.php",
      type: 'POST',
      contentType: false,
      processData: false,
      beforesend: function(){
      },
      success: function(response){
        console.log(response);
        var info = JSON.parse(response);
        if (info.respuesta == 'no_existe_usuario') {
          $('.resultado_verificacion_email').html('<div class="alert alert-danger" role="alert">Este usuario no existe!</div>');
        }
        if (info.respuesta == 'error_servidor') {
          $('.resultado_verificacion_email').html('<div class="alert alert-danger" role="alert">Error en el servidor !</div>');
        }

        if (info.respuesta == 'envio_exitoso') {
          $('.respuesta_cuenta_encontrada').html('<form class="" method="post" name="verificar_codigo_enviado" id="verificar_codigo_enviado" onsubmit="event.preventDefault(); sendData_verifi_codigo_seguriddd();" >'+
                          '<div class="form-group">'+
                              '<label for="exampleInputEmail1">Agrega el Código de seguridad</label>'+
                              '<input type="number" name="codigo_seguridad" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ingresa el Codigo de Seguridad">'+
                          '</div>'+
                          '<div class="">'+
                            '<input type="hidden" name="action" value="verificar_codigo_seguridad">'+
                            '<button type="submit" class="btn btn-success">Enviar Código de Seguridad</button>'+
                          '</div>'+
                          '<div class="resultado_verificacion_codigo_recibido">'+
                          '</div>'+
                        '</form>');
        }

      }

    });

  }


    function sendData_verifi_codigo_seguriddd(){
      $('.resultado_verificacion_codigo_recibido').html(' <div class="notificacion_negativa">'+
       '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
     '</div>');
      var parametros = new  FormData($('#verificar_codigo_enviado')[0]);
      $.ajax({
        data: parametros,
        url:"jquery_bancario/guibispay.php",
        type: 'POST',
        contentType: false,
        processData: false,
        beforesend: function(){
        },
        success: function(response){
          console.log(response);
          var info = JSON.parse(response);
          if (info.respuesta == 'no_existe_dato') {
            $('.resultado_verificacion_codigo_recibido').html('<div class="alert alert-danger" role="alert">No existe dato!</div>');
          }
          if (info.respuesta == 'error_servidor') {
            $('.resultado_verificacion_codigo_recibido').html('<div class="alert alert-danger" role="alert">Error en el servidor !</div>');
          }
          if (info.respuesta == 'existe_dato') {
            $('#modal_pagar_guibis').modal('hide');
              $('#modal_abrir_qrimg').modal();

          }

        }

      });

    }


    (function(){
  $(function(){
    $('#btn-ventana').on('click',function(){
      $('#modal_abrir_qrimg').modal();
    });


  });

}());
