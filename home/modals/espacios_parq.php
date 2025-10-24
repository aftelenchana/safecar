<div class="modal fade" id="modal_editar_usuario" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header text-white" >
        <h5 class="modal-title" id="exampleModalLongTitle">
          <i class="fas fa-user-edit me-2" style="font-size: 1.2rem;"></i> Aplicación <span class="aplicacion_editar"></span>
        </h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="update_aplicaciones">

          <!-- Nombre -->
          <div class="mb-3">
            <label for="nombre_categoria" class="form-label">Nombre de la Categoría</label>
            <input type="text" class="form-control input-guibis-sm" name="nombre" id="nombre_edit" required placeholder="Ingrese el nombre">
          </div>

          <!-- Estado -->
          <div class="mb-3">
            <label for="estado_categoria" class="form-label">Estado</label>
            <select class="form-control input-guibis-sm" name="estado" id="estado_edit" required>
              <option value="" disabled selected>Seleccione un estado</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
          <!-- Footer del formulario -->
          <div class="modal-footer mt-4">
            <input type="hidden" name="action" value="editar_aplicacion" />
            <input type="hidden" name="aplicacion" id="aplicacion_edit" value="" />
            <button type="button" class="btn btn-danger btn-guibis-medium" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cerrar</button>
            <button type="submit" class="btn btn-primary btn-guibis-medium"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
          </div>

          <!-- Notificación -->
          <div class="notificacion_editar_aplicacion mt-3"></div>

        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="modal_agregar_aplicaciones" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <!-- Header -->
    <div class="modal-header">
      <h5 class="modal-title" id="categoriaModalLabel">Agregar Categoría</h5>
    </div>

    <!-- Body -->
    <div class="modal-body">
      <form action="" id="agregar_aplicacion">

        <!-- Nombre -->
        <div class="mb-3">
          <label for="nombre_categoria" class="form-label">Nombre</label>
          <input type="text" class="form-control input-guibis-sm" name="nombre" id="nombre" required placeholder="Ingrese el nombre">
        </div>

        <!-- Estado -->
        <div class="mb-3">
          <label for="estado_categoria" class="form-label">Estado</label>
          <select class="form-control input-guibis-sm" name="estado" id="estado" required>
            <option value="" disabled selected>Seleccione un estado</option>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <input type="hidden" name="action" value="agregar_categoria">
          <button type="button" class="btn btn-danger guibis-btn" data-bs-dismiss="modal">
            Cerrar <i class="fas fa-times-circle"></i>
          </button>
          <button type="submit" class="btn btn-primary guibis-btn">
            Guardar <i class="fas fa-plus"></i>
          </button>
        </div>

        <!-- Notificación -->
        <div class="notificacion_agregar_aplicacion"></div>
      </form>
    </div>
  </div>
</div>
</div>




<div class="modal fade" id="modal_agregar_categoria" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header " >
      <h5 class="modal-title" id="proveedorModalLabel">Agregar Espacios</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
    </div>
    <div class="modal-body">

      <form action=""  id="add_categoria" >
            <div class="mb-3">
              <label for="nombreCategoria" class="label-guibis-sm">Cantidad</label>
              <input type="text" class="form-control input-guibis-sm" id="cantidad" name="cantidad" required placeholder="Cantidad">
            </div>
            <div class="mb-3">
              <label for="nombreCategoria" class="label-guibis-sm">Descripción</label>
              <input type="text" class="form-control input-guibis-sm" id="descripcion" name="descripcion" required placeholder="Descripción">
            </div>
          <div class="modal-footer">
            <input type="hidden" name="action" value="agregar_categoria">
            <button type="button" class="btn btn-danger boton_guibis_enviar " data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary boton_guibis_enviar">Agregar Espacio</button>
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
            <div class="modal-header " >
              <h5 class="modal-title" id="proveedorModalLabel">Editar Espacio <span class="cod_categoria"></span> </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i class="fas fa-times-circle"></i> </button>
            </div>
            <div class="modal-body">

              <form action=""  id="update_categoria" >
                <div class="mb-3">
                  <label for="nombreCategoria" class="label-guibis-sm">Cantidad</label>
                  <input type="text" class="form-control input-guibis-sm" id="cantidad_update" name="cantidad" required placeholder="Cantidad">
                </div>
                <div class="mb-3">
                  <label for="nombreCategoria" class="label-guibis-sm">Descripción</label>
                  <input type="text" class="form-control input-guibis-sm" id="descripcion_update" name="descripcion" required placeholder="Descripción">
                </div>



                  <div class="modal-footer">
                    <input type="hidden" name="action" value="editar_caregoria">
                    <input type="hidden" name="id_categoria" id="id_categoria" value="">
                    <button type="button" class="btn btn-danger boton_guibis_enviar" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary boton_guibis_enviar">Editar Espacio</button>
                  </div>
                  <div class="alerta_editar_caregoria"></div>
                </form>
            </div>
          </div>
        </div>
      </div>
