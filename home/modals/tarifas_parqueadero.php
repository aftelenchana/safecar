<div class="modal fade" id="modal_agregar_categoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" >
        <h5 class="modal-title" id="proveedorModalLabel">Agregar Tarifas Parqueadero</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
      </div>
      <div class="modal-body">

        <form action=""  id="add_categoria" >
              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Nombre</label>
                <input type="text" class="form-control input-guibis-sm" id="nombre" name="nombre_tarifa" required placeholder="Nombre">
              </div>
              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Intervalo tiempo (minutos)</label>
                <input type="text" class="form-control input-guibis-sm" id="intervalo_tiempo_minutos" name="intervalo_tiempo_minutos" required placeholder="Tiempo Minutos">
              </div>
              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Costo (Dolares)</label>
                <input type="text" class="form-control input-guibis-sm" id="valor_servicio" name="valor_servicio" required placeholder="Costo Dolares">
              </div>
              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Tiempo Espera</label>
                <input type="text" class="form-control input-guibis-sm" id="timpo_espera" name="timpo_espera" required placeholder="Tiempo Espera">
              </div>

              <div class="mb-3">
                <label for="nombreCategoria" class="label-guibis-sm">Recargo</label>
                <input type="text" class="form-control input-guibis-sm" id="valor_recargo" name="valor_recargo" required placeholder="Valor Recargo">
              </div>
            <div class="modal-footer">
              <input type="hidden" name="action" value="agregar_categoria">
              <button type="button" class="btn btn-danger boton_guibis_enviar " data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary boton_guibis_enviar">Agregar Categoria</button>
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
                <h5 class="modal-title" id="proveedorModalLabel">Editar Categoria <span class="cod_categoria"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
              </div>
              <div class="modal-body">

                <form action=""  id="update_categoria" >
                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Nombre</label>
                    <input type="text" class="form-control input-guibis-sm" id="nombre" name="nombre_tarifa" required placeholder="Nombre">
                  </div>
                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Intervalo tiempo (minutos)</label>
                    <input type="text" class="form-control input-guibis-sm" id="intervalo_tiempo_minutos" name="intervalo_tiempo_minutos" required placeholder="Tiempo Minutos">
                  </div>
                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Costo (Dolares)</label>
                    <input type="text" class="form-control input-guibis-sm" id="valor_servicio" name="valor_servicio" required placeholder="Costo Dolares">
                  </div>
                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Tiempo Espera</label>
                    <input type="text" class="form-control input-guibis-sm" id="timpo_espera" name="timpo_espera" required placeholder="Tiempo Espera">
                  </div>

                  <div class="mb-3">
                    <label for="nombreCategoria" class="label-guibis-sm">Recargo</label>
                    <input type="text" class="form-control input-guibis-sm" id="valor_recargo" name="valor_recargo" required placeholder="Valor Recargo">
                  </div>



                    <div class="modal-footer">
                      <input type="hidden" name="action" value="editar_caregoria">
                      <input type="hidden" name="id_categoria" id="id_categoria" value="">
                      <button type="button" class="btn btn-danger boton_guibis_enviar" data-bs-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-primary boton_guibis_enviar">Editar Categoria</button>
                    </div>
                    <div class="alerta_editar_caregoria"></div>
                  </form>
              </div>
            </div>
          </div>
        </div>
