$(document).ready(function() {
    // Inicialización de DataTable
    var tabla_categorias = $('#tabla_categorias').DataTable({
        "ajax": {
            "url": "/home/modulos/parqueadero/tipo_vehiculo.php",
            "type": "POST",
            "data": {
                "action": 'consultar_datos'
            },
            "dataSrc": "data",
            "error": function(xhr, error, thrown) {
                console.error('Error al cargar los datos:', error);
            }
        },
        "columns": [
            { "data": "id", "render": function(data, type, row) {
                return '<button type="button" categoria="'+data+'" class="btn btn-danger sucursal_'+data+' eliminar_categoria"><i class="fas fa-trash-alt"></i></button>' +
                       '<button type="button" categoria="'+data+'" class="btn btn-warning sucursal_'+data+' editar_caregoria"><i class="fas fa-edit"></i></button>';
            }},
              { "data": "nombre" },
              {"data": "fecha" },

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
        $('.alerta_agregar_categoria').html(' <div class="notificacion_negativa">'+
            '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
        '</div>');
        var parametros = new FormData($('#add_categoria')[0]);
        $.ajax({
            data: parametros,
            url: '/home/modulos/parqueadero/tipo_vehiculo.php',
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
                                    $('.alerta_agregar_categoria').html('<div class="alert alert-success background-success">'+
                                        '<strong>Datos!</strong>Ingresados Correctamente'+
                                    '</div>');
                                    tabla_categorias.ajax.reload(); // Recargar los datos en la tabla
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
            url: '/home/modulos/parqueadero/tipo_vehiculo.php',
            type: 'POST',
            async: true,
            data: {action: action, categoria: categoria},
            success: function(response){
                console.log(response);
                if (response != 'error') {
                    var info = JSON.parse(response);
                    if (info.noticia == 'insert_correct') {
                        // Código para manejar inserción correcta
                        tabla_categorias.ajax.reload(); // Recargar los datos en la tabla
                    }
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
    $('#tabla_categorias').on('click', '.editar_caregoria', function(){
        $('#modal_editar_caregoria').modal('show');
        $(".alerta_editar_caregoria").html('');
        var categoria = $(this).attr('categoria');
        var action = 'info_categoria';

        $.ajax({
            url: '/home/modulos/parqueadero/tipo_vehiculo.php',
            type: 'POST',
            async: true,
            data: {action: action, categoria: categoria},
            success: function(response){
                console.log(response);
                if (response != 'error') {
                    var info = JSON.parse(response);
                    $("#nombre_update").val(info.nombre);
                    $("#id_categoria").val(info.id);
                    $(".cod_categoria").html(info.id);
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
            url: '/home/modulos/parqueadero/tipo_vehiculo.php',
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
                                    $('.alerta_editar_caregoria').html('<div class="alert alert-success background-success">'+
                                        '<strong>Datos!</strong> Editados Correctamente'+
                                    '</div>');
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
      $('#modal_agregar_categoria').modal('show');
      $("#nombre").val('');
      $(".alerta_agregar_categoria").html('');

    });
  });
