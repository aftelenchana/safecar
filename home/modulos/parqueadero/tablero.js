$(document).ready(function() {
    // Inicialización de DataTable
    var tabla_categorias = $('#tabla_categorias').DataTable({
        "ajax": {
            "url": "/home/modulos/parqueadero/tablero.php",
            "type": "POST",
            "data": {
                "action": 'consultar_datos'
            },
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error al cargar los datos:', error);
                  console.log('Respuesta del servidor:', xhr.responseText); //
            }
        },
        "columns": [
          {
              "data": null,
              "render": function (data, type, row) {
                  return '<a href="javascript:void(0);" onclick="openPdfViewer(\'' + '/home/facturacion/facturacionphp/comprobantes/parqueo/pdf/' + data.clave_acceso + '.pdf\')"><img src="img/reacciones/pdf.png" width="35px" alt=""></a>';
              }
          },
          {"data": "estado" },
          {"data": "notas_extras" },
          {"data": "placa" },
          {"data": "minutos_servicio" },
          {"data": "fecha_inicio" },
          {"data": "nueva_fecha" },
          {"data": "intervalos" },
          {"data": "minutos_transcurridos" },
          {"data": "precio_cobrado" },
          {
              "data": "id",
              "render": function(data, type, row) {
                  if (row.estado === "INICIADO") {
                      return '<button type="button" parqueo="'+data+'" class="btn btn-warning sucursal_'+data+' call_parqueo"><i class="fas fa-edit"> Cobrar</i></button>';
                  } else {
                      return '';
                  }
              }
          }


        ],
        "dom": 'Bfrtip',
        "buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "language": {
            "url": "/home/guibis/data-table.json"
        },
        "order": [],
        "destroy": true
    });

    // Función para enviar datos del formulario
    function sendData_categoria(){
        $('.alerta_ingresar_parqueadero').html(' <div class="notificacion_negativa">'+
            '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
        '</div>');
        var parametros = new FormData($('#add_categoria')[0]);
        $.ajax({
            data: parametros,
            url: '/home/modulos/parqueadero/tablero.php',
            type: 'POST',
            contentType: false,
            processData: false,
            beforesend: function(){
            },
            success: function(response){
                console.log(response);
                if (response =='error') {
                    $('.alerta_ingresar_parqueadero').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
                } else {
                    var info = JSON.parse(response);

                                if (info.noticia == 'insert_correct') {
                                  tabla_categorias.ajax.reload(); // Recargar los datos en la tabla
                                  var id_creado = info.id_creado;
                                  var action = 'generar_parqueo';
                                  $.ajax({
                                  url: 'facturacion/facturacionphp/controladores/ctr_pdf_ingreso_vehiculos.php',
                                    type:'POST',
                                    async: true,
                                    data: {action:action,id_creado:id_creado},
                                    success: function(response){
                                      console.log(response);
                                        var info = JSON.parse(response);


                                        if (info.noticia =='insert_correct') {
                                              $('.alerta_ingresar_parqueadero').html('<div class="alert alert-success" role="alert">Vehiculo Ingresado Correctamente <a target="_blank" href="/home/facturacion/facturacionphp/comprobantes/parqueo/pdf/'+info.pdf+'.pdf">Descarga e Imprime</a> !</div>')

                                            }
                                            if (info.noticia =='pdf_generado') {
                                              $('.alerta_ingresar_parqueadero').html('<div class="alert alert-warning" role="alert">mira el pdf <a target="_blank" href="facturacion/facturacionphp/comprobantes/proformas/pdf/'+info.pdf+'">Generado Aqui</a> !</div>')

                                            }
                                         if (info.noticia == 'error_insertar') {
                                        $('.alerta_ingresar_parqueadero').html('<div class="alert alert-danger" role="alert">Error en el controlador!</div>');

                                        }


                                    },

                                     });


                                }
                  if (info.noticia == 'error_insertar') {
                      $('.alerta_agregar_categoria').html('<div class="alert alert-danger background-danger">'+
                          '<strong>Error!</strong>Error en el servidor'+
                      '</div>');
                  }
                }
            }
        });
    }


    $('#tabla_categorias').on('click', '.eliminar_categoria', function(){
        var categoria = $(this).attr('categoria');
        var action = 'eliminar_categoria';
        $.ajax({
            url: '/home/modulos/parqueadero/tablero.php',
            type: 'POST',
            async: true,
            data: {action: action, categoria: categoria},
            success: function(response){
                console.log(response);
                if (response != 'error') {
                    var info = JSON.parse(response);



                    if (info.noticia == 'error_insertar') {
                        // Código para manejar error al insertar
                    }
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });
    $('#tabla_categorias').on('click', '.call_parqueo', function(){
        $('#modal_editar_caregoria').modal();
        $(".alerta_editar_caregoria").html('');
        var parqueo = $(this).attr('parqueo');
        var action = 'buscar_informacion_parqueo';

        $.ajax({
            url: '/home/modulos/parqueadero/tablero.php',
            type: 'POST',
            async: true,
            data: {action: action, parqueo: parqueo},
            success: function(response){
                console.log(response);
                if (response != 'error') {
                    var info = JSON.parse(response);
                    if (info.estado == 'INICIADO') {
                     $('.respuesta_info_parqueo').html('<div class="row">'+
                               '<div class="col-12">'+
                                   '<div class="table-responsive">'+
                                       '<table class="table table-bordered">'+
                                         '  <thead>'+
                                             '  <tr>'+
                                                 '  <th>Hora Inicio</th>'+
                                                 '  <th>Intervalo Tiempo</th>'+
                                                 '  <th>Precio Total</th>'+

                                                 '  <th>Código</th>'+
                                                 '  <th>Estado</th>'+
                                                 '  <th>Servicio</th>'+
                                               '</tr>'+
                                         '  </thead>'+
                                         '  <tbody>'+
                                             '  <tr>'+
                                                   '<td>'+info.fecha_inicio+'</td>'+
                                                   '  <td>'+info.minutos_servicio+' min</td>'+
                                                 '  <td>$'+info.precio_servicio+'</td>'+
                                                   '<td>'+info.id_parqueo+'</td>'+
                                                   '<td>'+info.estado+'</td>'+
                                                     '<td>'+info.nombre_servicio+'</td>'+
                                             '  </tr>'+
                                           '</tbody>'+
                                     '  </table>'+
                                   '</div>'+
                             '  </div>'+
                           '</div>');
                           $('#id_categoria').val(info.id_parqueo);
                    }
                    if (info.estado == 'FINALIZADO') {
                      $('.formulario_cobrar_parqueo').html('<form  class="" method="post" name="cobrar_parqueo" id="cobrar_parqueo" onsubmit="event.preventDefault(); sendData_cobrar_parqueo();">'+
                                             '<input type="hidden" name="action" value="cobrar_parqueo">'+
                                             '<input type="hidden" name="idparqueo" value="'+info.id_parqueo+'">'+
                                             '<div class="alert alert-danger" role="alert">La cuenta de este parqueo ya ha sido Cobrada!</div>'+
                                             '<div class="modal-footer">'+
                                                  '<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>'+
                                            '</div>'+
                                             '<div class="notificacion_agregar_cobro_parquep">'+
                                             '</div>'+
                                           '</form>');
                    }

                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    // Función para editar_almacen
    function sendData_update_categoria(){
        $('.alerta_editar_caregoria').html(' <div class="notificacion_negativa">'+
            '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
        '</div>');
        var parametros = new FormData($('#update_categoria')[0]);
        $.ajax({
            data: parametros,
            url: '/home/modulos/parqueadero/tablero.php',
            type: 'POST',
            contentType: false,
            processData: false,
            beforesend: function(){
            },
            success: function(response){
                console.log(response);
                if (response =='error') {
                    $('.notificacion_agregar_sucursal').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
                } else {
                    var info = JSON.parse(response);
                    if (info.noticia == 'insert_correct') {

                                    //INGRESAR CODIGO PARA QUE SE GUARDE UN PDF DE SALIDA DEL AUTOMOVIL


                                    var idparqueo = info.idparqueo;
                                    var action = 'generar_pdf_salida';
                                    $.ajax({
                                    url: 'facturacion/facturacionphp/controladores/ctr_pdf_salida_vehiculos.php',
                                      type:'POST',
                                      async: true,
                                      data: {action:action,idparqueo:idparqueo},
                                      success: function(response){
                                        console.log(response);
                                          var info = JSON.parse(response);


                                          if (info.noticia =='comprobante_guardado') {
                                                $('.alerta_editar_caregoria').html('<div class="alert alert-success" role="alert">Salida correctamente Correctamente <a target="_blank" href="'+info.ruta_pdf+'">Descarga e Imprime</a> !</div>')

                                              }
                                              if (info.noticia =='pdf_generado') {
                                                $('.alerta_editar_caregoria').html('<div class="alert alert-warning" role="alert">Salida correctamente Correctamente, error al procesar el comprobante!</div>')

                                              }



                                      },

                                       });




                                    tabla_categorias.ajax.reload(); // Recargar los datos en la tabla
                                }
                  if (info.noticia == 'error_insertar') {
                      $('.alerta_editar_caregoria').html('<div class="alert alert-danger background-danger">'+
                          '<strong>Error!</strong>Error en el servidor'+
                      '</div>');
                  }
                }
            }
        });
    }

      // ediat_alacen
    $('#update_categoria').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto
        sendData_update_categoria();
    });



    // Evento submit del formulario
    $('#add_categoria').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto
        sendData_categoria();
    });
});

  $(function() {
    $('#boton_agregar_mesa').on('click', function() {
      $('#modal_agregar_categoria').modal();
      $("#placa").val('');
      $("#nota_extra").val('');
      $(".alerta_ingresar_parqueadero").html('');

    });
  });





  function sendData_ingresar_vehiculo_lavanderia_mensualidades(){
  $('.notificacion_ingreso_vehiuclo_lavanderia').html(' <div class="notificacion_negativa">'+
     '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
   '</div>');
  var parametros = new  FormData($('#ingreso_parqueo_mensualidades')[0]);
  $.ajax({
    data: parametros,
    url: 'jquery_empresa/sistema_lavado_autos.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
      console.log(response);

      if (response =='error') {
        $('.alert_general').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>')
      }else {
      var info = JSON.parse(response);
      if (info.noticia == 'insert_correct') {
        var id_creado = info.id_creado;
        var action = 'generar_parqueo';
        $.ajax({
        url: 'facturacion/facturacionphp/controladores/ctr_creador_pdf_parqueo_lavanderia_mensualidades.php',
          type:'POST',
          async: true,
          data: {action:action,id_creado:id_creado},
          success: function(response){
            console.log(response);
              var info = JSON.parse(response);


              if (info.noticia =='insert_correct') {
                $('.notificacion_ingreso_vehiuclo_lavanderia').html('<div class="alert alert-success" role="alert">Vehiculo Ingresado Correctamente <a target="_blank" href="/home/facturacion/facturacionphp/comprobantes/parqueadero/mensualidades/'+info.pdf+'.pdf">Descarga e Imprime</a> !</div>')

              }
              if (info.noticia =='pdf_generado') {
                $('.notificacion_ingreso_vehiuclo_lavanderia').html('<div class="alert alert-warning" role="alert">mira el pdf <a target="_blank" href="facturacion/facturacionphp/comprobantes/parqueadero/mensualidades/'+info.pdf+'">Generado Aqui</a> !</div>')

              }
              if (info.noticia == 'error_insertar') {
              $('.notificacion_ingreso_vehiuclo_lavanderia').html('<div class="alert alert-danger" role="alert">Error en el controlador!</div>');

              }


          },

           });


      }
      if (info.noticia == 'error_insertar') {
      $('.notificacion_ingreso_vehiuclo_lavanderia').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>');

      }

      }

    }

  });

}
