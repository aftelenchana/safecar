<div class="modal fade" id="modal_agregar_categoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" >
        <h5 class="modal-title" id="proveedorModalLabel">Agregar tipo Vehiculo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
      </div>
      <div class="modal-body">

        <form action=""  id="add_categoria" >
              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Nombre</label>
                <input type="text" class="form-control input-guibis-sm" id="nombre" name="nombre" required placeholder="Nombre">
              </div>
            <div class="modal-footer">
              <input type="hidden" name="action" value="agregar_categoria">
              <button type="button" class="btn btn-danger boton_guibis_enviar " data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary boton_guibis_enviar">Agregar Tipo Vehiculo</button>
            </div>
            <div class="alerta_agregar_categoria"></div>
          </form>
      </div>
    </div>
  </div>
</div>




        <div class="modal fade" id="modal_editar_caregoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header" >
                <h5 class="modal-title" id="proveedorModalLabel">Editar tipo Vehiculo <span class="cod_categoria"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
              </div>
              <div class="modal-body">

                <form action=""  id="update_categoria" >
                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Nombre</label>
                    <input type="text" class="form-control input-guibis-sm" id="nombre_update" name="nombre" required placeholder="Nombre">
                  </div>


                    <div class="modal-footer">
                      <input type="hidden" name="action" value="editar_caregoria">
                      <input type="hidden" name="id_categoria" id="id_categoria" value="">
                      <button type="button" class="btn btn-danger boton_guibis_enviar" data-bs-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-primary boton_guibis_enviar">Editar Tipo Vehiculo</button>
                    </div>
                    <div class="alerta_editar_caregoria"></div>
                  </form>
              </div>
            </div>
          </div>
        </div>
