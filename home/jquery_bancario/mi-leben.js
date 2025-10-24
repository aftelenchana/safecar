

$(document).ready(function(){
  var usuario = 1;
  var action = 'infoUsuario';
    $.ajax({
      url:'jquery_bancario/estado_financiero.php',
      type:'POST',
      async: true,
      data: {action:action,usuario:usuario},
       success: function(response){
         if (response != 'error') {
           var info = JSON.parse(response);
               $('.usuario_general').html('<table style="margin: 0 auto;width: 80%;">'+
                     '<tr>'+
                       '<th>Nombre:'+info.nombres+' '+info.apellidos+'</th>'+
                       '<th>Fecha: '+info.fecha_creacion+'</th>'+
                       '<th class="cantidad_existente">$'+info.cantidad+'</th>'+
                     '</tr>'+
                     '<tr>'+
                       '<th>Cedula: '+info.numero_identidad+'</th>'+
                       '<th>Telefono:'+info.telefono+'</th>'+
                       '<th>Celular: '+info.celular+'</th>'+
                     '</tr>'+
                     '<tr>'+
                       '<th>Cuenta Bancaria:'+info.cuenta_bancaria+'</th>'+
                       '<th>Cuenta Pay Pal:'+info.cuenta_paypal+'</th>'+
                       '<th> <img src="img/qr_bancario/'+info.qr_bancario+'" alt=""> </th>'+
                     '</tr>'+
                   '</table>');



         }

       },
       error:function(error){
         console.log(error);
         }

       });

});
