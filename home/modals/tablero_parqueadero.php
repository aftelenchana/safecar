<div class="modal fade" id="modal_agregar_categoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" >
        <h5 class="modal-title" id="proveedorModalLabel">Agregar Tarifas Parqueadero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
      </div>
      <div class="modal-body">

        <form action=""  id="add_categoria" >

          <div class="form-group">
              <label class="label-guibis-sm">Tarifa</label>
              <select class="form-control input-guibis-sm" name="tarifas_parqueo" required id="tarifas_parqueo">
                <?php
                $query_planes_parqueo = mysqli_query($conection,"SELECT * FROM tarifas_parqueo WHERE  tarifas_parqueo.iduser= '$iduser'   AND tarifas_parqueo.estatus = 1");
                while ($planes = mysqli_fetch_array($query_planes_parqueo)) {
                  echo '<option  value="'.$planes['id'].'">Tarifa '.$planes['nombre_servicio'].'/  '.$planes['minutos_servicio'].' Minutos / Precio: $'.$planes['valor_servicio'].' Dolares </option>';
                }
                 ?>
              </select>
          </div>

          <div class="form-group">
              <label class="label-guibis-sm">Tipo Vehiculo</label>
              <select class="form-control input-guibis-sm" name="tipo_vehiculo" required id="tipo_vehiculo">
                <?php
                $query_planes_parqueo = mysqli_query($conection,"SELECT * FROM tipo_vehiculo_guibis WHERE  tipo_vehiculo_guibis.iduser= '$iduser'   AND tipo_vehiculo_guibis.estatus = 1");
                while ($planes = mysqli_fetch_array($query_planes_parqueo)) {
                  echo '<option  value="'.$planes['id'].'"> '.$planes['nombre'].' </option>';
                }
                 ?>
              </select>
          </div>
          <div class="mb-3">
            <label for="nombreCategoria" class="label-guibis-sm">Placa</label>
            <input type="text" class="form-control input-guibis-sm" id="placa" name="placa" required placeholder="Placa">
          </div>

          <div class="mb-3">
            <label for="nombreCategoria" class="label-guibis-sm">Notas Extras</label>
            <input type="text" class="form-control input-guibis-sm" id="nota_extra" name="nota_extra" required placeholder="Nota Extra">
          </div>


            <div class="modal-footer">
              <input type="hidden" name="action" value="agregar_categoria">
              <button type="button" class="btn btn-danger boton_guibis_enviar " data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary boton_guibis_enviar">Ingreso de Vihiculos</button>
            </div>
            <div class="alerta_ingresar_parqueadero"></div>
          </form>
      </div>
    </div>
  </div>
</div>





               <div class="modal fade" id="modal_editar_caregoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
                 <div class="modal-dialog">
                   <div class="modal-content">
                     <div class="modal-header" >
                       <h5 class="modal-title" id="proveedorModalLabel">Cobrar Parqueo <span class="cod_categoria"></span> </h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
                     </div>
                     <div class="modal-body">

                       <form action=""  id="update_categoria" >
                         <div class="respuesta_info_parqueo">
                         </div>
                         <div class="form-group">
                           <label for="exampleFormControlSelect1">Métodos de Pago</label>
                           <select class="form-control input-guibis-sm"  name="metodos_pago" id="metodos_pago">
                            <option value="Efectivo">Efectivo</option>
                             <option value="Ahorita">Ahorita</option>
                             <option value="DeUna">DeUna</option>
                             <option value="Coopmego">Coopmego</option>
                           </select>
                         </div>

                           <div class="modal-footer">
                             <input type="hidden" name="action" value="cobrar_parqueo">
                             <input type="hidden" name="id_categoria" id="id_categoria" value="">
                             <button type="button" class="btn btn-danger boton_guibis_enviar" data-bs-dismiss="modal">Cerrar</button>
                             <button type="submit" class="btn btn-primary boton_guibis_enviar">Cobrar Parqueo</button>
                           </div>
                           <div class="alerta_editar_caregoria"></div>
                         </form>
                     </div>
                   </div>
                 </div>
               </div>
