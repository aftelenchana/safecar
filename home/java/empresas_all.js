
$(document).ready(function() {
    var tabla_clientes = $('#tabla_clientes').DataTable({
        "ajax": {
            "url": "java/empresas_all.php",
            "type": "POST",
            "data": function(d) {
                d.action = 'consultar_datos';
                d.filtro = $('#filtro').val();
                d.categoria_recursos_humanos = $('#categoria_recursos_humanos').val();
                d.tipo_cliente = $('#tipo_cliente').val();
                d.estado = $('#estado').val();
            },
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error al cargar los datos:', error);
                console.log('Respuesta del servidor:', xhr.responseText); // Añadido para depuración
            }
        },
        "columns": [
          { "data": "id", "render": function(data, type, row) {
            return '<button type="button" empresa="'+data+'" class="btn btn-warning producto_'+data+' editar_usuario"><i class="fas fa-edit"></i></button>';
          }},
            { "data": "id" },
            { "data": "nombre" },
            { "data": "estado" },
            { "data": "key_api" },

        ],
        "dom": 'Bfrtip',
        "language": {
            "url": "/home/guibis/data-table.json"
        },
        "order": [],
        "destroy": true,
        "autoWidth": false,
        "lengthMenu": [[30, -1], [50, "Todos"]],
    });

    // Función para cargar la tabla
    window.cargarTabla = function() {
        tabla_clientes.ajax.reload(); // Recargar la tabla con los nuevos filtros

        console.log('estamos ocupando el cargar la tabla');
    }



            $('#tabla_clientes').on('click', '.editar_usuario', function(){
            $('#modal_editar_usuario').modal('show');
            $(".notificacion_editar_usuario").html('');
            $("#firma_electronocia").val('');
            var aplicacion = $(this).attr('empresa');
            var action = 'informacion_aplicacion';
            $.ajax({
                url: 'java/empresas_all.php',
                type: 'POST',
                async: true,
                data: {action: action, aplicacion: aplicacion},
                success: function(response){
                    console.log(response);
                    if (response != 'error') {
                        var info = JSON.parse(response);
                        $("#nombre_edit").val(info.nombre);
                        $("#estado_edit").val(info.estado);
                        $("#aplicacion_edit").val(info.id);
                        $(".aplicacion_editar").html(info.id);
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });


        // ediat_alacen
        $('#update_aplicaciones').on('submit', function(e) {
          e.preventDefault(); // Prevenir el envío del formulario por defecto
          sendData_update_aplicaciones();
        });


        // Función para editar_usuario
        function sendData_update_aplicaciones(){
            $('.notificacion_editar_aplicacion').html(' <div class="notificacion_negativa">'+
                '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
            '</div>');
            var parametros = new FormData($('#update_aplicaciones')[0]);
            $.ajax({
                data: parametros,
                url: 'java/empresas_all.php',
                type: 'POST',
                contentType: false,
                processData: false,
                beforesend: function(){
                },
                success: function(response){
                    console.log(response);
                    if (response =='error') {
                        $('.notificacion_editar_aplicacion').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
                    } else {
                        var info = JSON.parse(response);
                        if (info.noticia == 'insert_correct') {
                                        $('.notificacion_editar_aplicacion').html('<div class="alert alert-success background-success">'+
                                            '<strong>Aplicación!</strong> Editada Correctamente'+
                                        '</div>');


                                        tabla_clientes.ajax.reload(); // Recargar los datos en la tabla
                                    }
                      if (info.noticia == 'error_insertar') {
                          $('.notificacion_editar_aplicacion').html('<div class="alert alert-danger background-danger">'+
                              '<strong>Error!</strong>Error en el servidor'+
                          '</div>');
                      }
                    }
                }
            });
        }



            // Evento submit del formulario
            $('#agregar_aplicacion').on('submit', function(e) {
              e.preventDefault(); // Prevenir el envío del formulario por defecto
              sendData_aplicaciones();
            });

            // Función para enviar datos del formulario
            function sendData_aplicaciones(){
              $('.notificacion_agregar_aplicacion').html(' <div class="notificacion_negativa">'+
                  '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
              '</div>');
              var parametros = new FormData($('#agregar_aplicacion')[0]);
              $.ajax({
                  data: parametros,
                  url: 'java/empresas_all.php',
                  type: 'POST',
                  contentType: false,
                  processData: false,
                  beforesend: function(){
                  },
                  success: function(response){
                      console.log(response);
                      if (response =='error') {
                          $('.notificacion_agregar_aplicacion').html('<p class="alerta_negativa">Error al Editar el Contraseña</p>')
                      } else {
                          var info = JSON.parse(response);
                          if (info.noticia == 'insert_correct') {
                                          $('.notificacion_agregar_aplicacion').html('<div class="alert alert-success background-success">'+
                                              '<strong>Aplicación !</strong> Creada Correctamente'+
                                          '</div>');
                                          cargarTabla(); // Volver a cargar la tabla con los nuevos datos
                                      }
                        if (info.noticia == 'error_insertar') {
                            $('.notificacion_agregar_aplicacion').html('<div class="alert alert-danger background-danger">'+
                                '<strong>Error!</strong>Error en el servidor'+
                            '</div>');
                        }
                      }
                  }
              });
            }



});

$(function() {
  $('#boton_agregar_aplicaciones').on('click', function() {
    $('#modal_agregar_aplicaciones').modal('show');
    $("#direccion_sucursal").val('');
    $("#punto_emision").val('');
    $("#establecimiento").val('');
    $(".notificacion_agregar_aplicacion").html('');

  });
});
