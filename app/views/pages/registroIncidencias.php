<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar incidencias...">

    <div class="d-flex align-items-center">

      <!-- NOTIFICACIONES -->
      <div class="dropdown me-3 position-relative">
        <i class="bi bi-bell fs-5 shake-loop" id="notificacionesIcon"
           data-bs-toggle="dropdown"
           style="cursor:pointer;"></i>

        <span id="contadorNotificaciones"
              class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
          3
        </span>

        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;" id="listaNotificaciones">
          <li class="fw-bold mb-2">Notificaciones</li>
          <li class="dropdown-item">Nuevo fichaje registrado</li>
          <li class="dropdown-item">Contrato por vencer</li>
          <li class="dropdown-item">Actualización completada</li>
        </ul>
      </div>

      <!-- PERFIL -->
      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>

    </div>
  </div>

  <div class="card card-custom p-3">
    <div class="d-flex justify-content-between mb-3 align-items-center">
      <h5>Listado de Incidencias</h5>

      <div class="d-flex gap-2">

        <!-- FILTROS DROPDOWN -->
        <div class="dropdown">
          <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-funnel"></i> Filtrar
          </button>

          <div class="dropdown-menu p-3" style="width: 300px;">

            <div class="mb-2">
              <label class="form-label">ID Fichaje</label>
              <input type="text" id="filterIdFichaje" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">Mensaje</label>
              <input type="text" id="filterMensaje" class="form-control">
            </div>

            <div class="mb-2">
              <label class="form-label">Estado</label>
              <select id="filterEstado" class="form-select">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="en proceso">En proceso</option>
                <option value="resuelta">Resuelta</option>
              </select>
            </div>

            <div class="mb-2">
              <label class="form-label">Fecha</label>
              <input type="date" id="filterFecha" class="form-control">
            </div>

            <div class="d-flex justify-content-between mt-3">
              <button id="clearFilters" class="btn btn-sm btn-secondary">Limpiar</button>
            </div>

          </div>
        </div>

      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>id_fichaje</th>
          <th>usuario</th>
          <th>mensaje</th>
          <th>respuesta</th>
          <th>estado</th>
          <th>fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="lista">
      </tbody>
    </table>
  </div>

</div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Eliminar Incidencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="/incidencias/removeIncidencia" method="POST">

          <input type="hidden" name="id" id="edit_id">

          <div class="mb-3">
            <label class="form-label">Id_fichaje</label>
            <input type="text" name="id_fichaje" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Mensaje</label>
            <input type="text" name="mensaje" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Respuesta</label>
            <input type="text" name="respuesta" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Estado</label>
            <input type="text" name="estado" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="text" name="fecha" class="form-control" disabled>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Eliminar</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Editar Incidencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="/incidencias/updateIncidencia" method="POST">

          <input type="hidden" name="id" id="edit_id">

          <div class="mb-3">
            <label class="form-label">Id_fichaje</label>
            <input type="text" name="id_fichaje" id="edit_id_fichaje" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" id="edit_usuario" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Mensaje</label>
            <input type="text" name="mensaje" id="edit_mensaje" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Respuesta</label>
            <input type="text" name="respuesta" id="edit_respuesta" class="form-control">
          </div>

          <!-- ESTADO DESPLEGABLE -->
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" id="edit_estado" class="form-select">
              <option value="pendiente">Pendiente</option>
              <option value="resuelta">Resuelta</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="datetime-local" name="fecha" id="edit_fecha" class="form-control">
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Actualizar</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<script>
  var RUTA_URL    = "<?= RUTA_URL ?>";
  var USER_ROL    = "<?= $_SESSION['rol']         ?? '' ?>";
  var USER_ID     = "<?= $_SESSION['id_usuario']  ?? '' ?>";
  var USER_NOMBRE = "<?= $_SESSION['nombre']      ?? '' ?>";
</script>

<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureIncidencias.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/filtrosIncidencias.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>