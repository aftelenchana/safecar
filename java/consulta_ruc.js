
function sendDataconsultar_ruc(){
  $('.resultado_busqueda_general').html('<div class="proceso">'+
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'+
  '</div>');
  var parametros = new  FormData($('#consultar_ruc')[0]);
  $.ajax({
    data: parametros,
    url: 'java/consulta_ruc.php',
    type: 'POST',
    contentType: false,
    processData: false,
    beforesend: function(){

    },
    success: function(response){
        console.log(response);
      if (response =='error') {
        $('.notificacion_ruc_consultado').html('<div class="alert alert-danger" role="alert">Error en el servidor!</div>')
      }else {
        var info = JSON.parse(response);
        if (info.noticia == 'consulta_exitosa') {
          $('.resultado_busqueda_general').html('<div class="row consulta_ruc_general">'+
              '<div class="col-12">'+
                '  <h1>Resultado de Búsqueda</h1>'+
                '  <div class="table-responsive">'+
                      '<table class="table table-bordered">'+
                        '  <thead>'+
                              '<tr>'+
                                '  <th>RUC</th>'+
                                  '<th>RAZÓN SOCIAL</th>'+
                                '  <th>NOMBRE COMERCIAL</th>'+
                                  '<th>ESTADO CONTRIBUYENTE</th>'+
                                  '<th>CLASE CONTRIBUYENTE</th>'+
                                  '<th>FECHA INICIO ACTIVIDADES</th>'+
                                  '<th>OBLIGADO A LLEVAR CONTABILIDAD</th>'+
                                  '<th>TIPO CONTIBUYENTE</th>'+
                                  '<th>PROVINCIA</th>'+
                                  '<th>CANTON</th>'+
                                  '<th>ACTIVIDAD ECONÓMICA</th>'+
                              '</tr>'+
                          '</thead>'+
                        '  <tbody>'+
                            '  <tr>'+
                                '  <td>'+info.NUMERO_RUC+'</td>'+
                                '  <td>'+info.RAZON_SOCIAL+'</td>'+
                                '  <td>'+info.NOMBRE_COMERCIAL+'</td>'+
                                '  <td>'+info.ESTADO_CONTRIBUYENTE+'</td>'+
                                '  <td></td>'+
                                '  <td></td>'+
                                '  <td></td>'+
                                '  <td>1</td>'+
                                  '<td>'+info.DESCRIPCION_PROVINCIA+'</td>'+
                                '  <td>'+info.DESCRIPCION_CANTON+'</td>'+
                                  '<td>'+info.ACTIVIDAD_ECONOMICA+'</td>'+
                              '</tr>'+
                          '</tbody>'+
                      '</table>'+
                '  </div>'+
              '</div>'+
          '</div>');

          $('.notificacion_ruc_consultado').html('');

        }


        if (info.noticia == 'consulta_no_existente') {
          $('.resultado_busqueda_general').html('<div class="alert alert-danger" role="alert">NO SE ENCUENTRA NINGUN  REGISTRO PARA ESTA BUSQUEDA!</div>');

          $('.notificacion_ruc_consultado').html('');

        }




      }

    }

  });

}
