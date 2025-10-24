$(document).ready(function() {
    // Inicialización de DataTable
    var tabla_categorias = $('#tabla_categorias').DataTable({
        "ajax": {
            "url": "/home/modulos/parqueadero/espacios.php",
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
          {
            data: "id",
            render: function (data, type, row) {
              const icoTrash = `
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                  <path d="M9 3h6a1 1 0 0 1 1 1v1h4a1 1 0 1 1 0 2h-1.1l-1.2 12.1A3 3 0 0 1 14.72 22H9.28a3 3 0 0 1-2.98-2.9L5.1 7H4a1 1 0 1 1 0-2h4V4a1 1 0 0 1 1-1Zm1 2h4V4h-4v1Zm7.9 2H6.1l1.1 11.2A1 1 0 0 0 8.28 20h5.44a1 1 0 0 0 1.08-.8L17.9 7ZM10 9a1 1 0 0 1 1 1v7a1 1 0 1 1-2 0v-7a1 1 0 0 1 1-1Zm4 0a1 1 0 0 1 1 1v7a1 1 0 1 1-2 0v-7a1 1 0 0 1 1-1Z"/>
                </svg>`;
              const icoPen = `
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                  <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm18-10.5a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0L15.13 4.7l3.75 3.75L21 6.75Z"/>
                </svg>`;

              return `
                <button type="button" categoria="${data}" class="btn btn-danger sucursal_${data} eliminar_categoria" title="Eliminar">
                  ${icoTrash}
                </button>
                <button type="button" categoria="${data}" class="btn btn-warning sucursal_${data} editar_caregoria" title="Editar">
                  ${icoPen}
                </button>
              `;
            }
          },

              { "data": "cantidad" },
              {"data": "descripcion" },
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
            url: '/home/modulos/parqueadero/espacios.php',
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
            url: '/home/modulos/parqueadero/espacios.php',
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
            url: '/home/modulos/parqueadero/espacios.php',
            type: 'POST',
            async: true,
            data: {action: action, categoria: categoria},
            success: function(response){
                console.log(response);
                if (response != 'error') {
                    var info = JSON.parse(response);
                    $("#cantidad_update").val(info.cantidad);
                    $("#descripcion_update").val(info.descripcion);

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
            url: '/home/modulos/parqueadero/espacios.php',
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
      $("#nombre_almacen").val('');
      $("#responsable").val('');
      $("#direccion_almacen").val('');
      $("#descripcion").val('');
      $(".alerta_almacen").html('');

    });
  });
