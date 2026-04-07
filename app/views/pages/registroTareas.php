<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar tareas...">

  <div class="d-flex align-items-center">

    <!-- NOTIFICACIONES -->
    <div class="dropdown me-3 position-relative">
      <i class="bi bi-bell fs-5" id="notificacionesIcon" data-bs-toggle="dropdown" style="cursor:pointer;"></i>
      
      <!-- Badge -->
      <span id="contadorNotificaciones" 
      class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
       3
      </span>

      <!-- Dropdown -->
      <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;" id="listaNotificaciones">
        <li class="fw-bold mb-2">Notificaciones</li>
        <li class="dropdown-item">Nuevo fichaje registrado</li>
        <li class="dropdown-item">Contrato por vencer</li>
        <li class="dropdown-item">Actualización completada</li>
      </ul>
    </div>

    <!-- PERFIL -->
    <!-- PERFIL -->
    <i class="bi bi-person-circle fs-5 profile" 
     data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>
    </div>
</div>

  <div class="card card-custom p-3">

    <div class="d-flex justify-content-between mb-3">
      <h5>Listado de Tareas</h5>

      <!-- BOTÓN AÑADIR -->
      <button 
        class="btn btn-success btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#addModal"
      >
        + Añadir tarea
      </button>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>id_fichaje</th>
          <th>usuario</th>
          <th>titulo</th>
          <th>descripcion</th>
          <th>hora_inicio</th>
          <th>hora_fin</th>
          <th>tiempo_total</th>
          <th>estado</th>
          <th>fecha</th>
          <th>tipo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="lista"></tbody>
    </table>

  </div>

</div>
</div>


<!-- ========================= -->
<!-- MODAL AÑADIR -->
<!-- ========================= -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/RegistroTareas/createTask" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Nueva Tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-2">
            <label>ID Fichaje</label>
            <input type="text" name="id_fichaje" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
          </div>

          <div class="mb-2">
            <label>Hora inicio</label>
            <input type="time" name="hora_inicio" class="form-control">
          </div>

          <div class="mb-2">
            <label>Hora fin</label>
            <input type="time" name="hora_fin" class="form-control">
          </div>

          <div class="mb-2">
            <label>Tiempo total</label>
            <input type="text" name="tiempo_total" class="form-control">
          </div>

          <div class="mb-2">
            <label>Estado</label>
            <select name="estado" class="form-control">
              <option value="pendiente">Pendiente</option>
              <option value="en_proceso">En proceso</option>
              <option value="completado">Completado</option>
            </select>
          </div>

          <div class="mb-2">
            <label>Fecha</label>
            <input type="datetime-local" name="fecha" class="form-control">
          </div>

          <div class="mb-2">
            <label>Tipo</label>
            <input type="text" name="id_tipo" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Crear</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- ========================= -->
<!-- MODAL EDITAR -->
<!-- ========================= -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/RegistroTareas/updateTask" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Editar Tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id">

          <div class="mb-2">
            <label>ID Fichaje</label>
            <input type="text" name="id_fichaje" class="form-control" readonly>
          </div>

          <div class="mb-2">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control">
          </div>

          <div class="mb-2">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
          </div>

          <div class="mb-2">
            <label>Hora inicio</label>
            <input type="time" name="hora_inicio" class="form-control">
          </div>

          <div class="mb-2">
            <label>Hora fin</label>
            <input type="time" name="hora_fin" class="form-control">
          </div>

          <div class="mb-2">
            <label>Tiempo total</label>
            <input type="text" name="tiempo_total" class="form-control">
          </div>

          <div class="mb-2">
            <label>Estado</label>
            <select name="estado" class="form-control">
              <option value="pendiente">Pendiente</option>
              <option value="en_proceso">En proceso</option>
              <option value="completado">Completado</option>
            </select>
          </div>

          <div class="mb-2">
            <label>Fecha</label>
            <input type="datetime-local" name="fecha" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">Guardar cambios</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- ========================= -->
<!-- MODAL ELIMINAR -->
<!-- ========================= -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/RegistroTareas/deleteTask" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Eliminar Tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id">

          <div class="mb-2">
            <label>ID Fichaje</label>
            <input type="text" name="id_fichaje" class="form-control" disabled>
          </div>

          <div class="mb-2">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" disabled>
          </div>

          <div class="mb-2">
            <label>Descripción</label>
            <textarea name="descripcion" class="form-control" disabled></textarea>
          </div>

          <div class="mb-2">
            <label>Estado</label>
            <input type="text" name="estado" class="form-control" disabled>
          </div>

          <div class="mb-2">
            <label>Fecha</label>
            <input type="datetime-local" name="fecha" class="form-control" disabled>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>

      </form>

    </div>
  </div>
</div>


<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureTareas.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>