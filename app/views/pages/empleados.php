<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="main-wrapper container-fluid">
<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar empleados...">

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
    <i class="bi bi-person-circle fs-5 profile"></i>
  </div>
</div>

  <!-- 🔵 CARD -->
  <div class="card shadow-sm border-0 p-3">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Listado de Empleados</h5>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-funnel"></i> Filtrar
        </button>

        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
          <i class="bi bi-person-plus"></i> Añadir
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Rol</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody id="lista">
          <?php if(!empty($datos['usuarios'])): ?>
            <?php foreach($datos['usuarios'] as $usuario): ?>
              <tr>
                <td><?php echo $usuario->id_usuario; ?></td>

                <td class="fw-semibold">
                  <?php echo $usuario->nombre . ' ' . $usuario->apellidos; ?>
                </td>

                <td><?php echo $usuario->nombre_usuario; ?></td>
                <td><?php echo $usuario->dni; ?></td>
                <td><?php echo $usuario->telefono; ?></td>
                <td><?php echo $usuario->email; ?></td>

                <td>
                  <span class="badge bg-primary">
                    <?php echo $usuario->rol; ?>
                  </span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">

                    <button class="btn btn-sm text-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn btn-sm text-danger" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center text-muted">
                No hay empleados registrados
              </td>
            </tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>

  </div>

</div>
</div>
</div>

<!--Modal Añadir-->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Añadir empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
  <form action="/user/addUser" method="POST">

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Apellidos</label>
      <input type="text" name="apellidos" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Usuario</label>
      <input type="text" name="usuario" class="form-control" required>
    </div>

     <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="text" name="contraseña" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">DNI</label>
      <input type="text" name="dni" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Teléfono</label>
      <input type="text" name="telefono" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Rol</label>
      <select name="rol" class="form-select" required>
        <option value="">Seleccionar rol</option>
        <option value="Administrador">Administrador</option>
        <option value="Trabajador">Trabajador</option>
        <option value="Practicas">Practicas</option>
      </select>
    </div>

     <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>

  </form>
</div>

     

    </div>
  </div>
</div>


<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form action="/user/editUser" method="POST">

          <input type="hidden" name="id" id="edit_id">

          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">DNI</label>
            <input type="text" name="dni" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" required>
              <option value="">Seleccionar rol</option>
              <option value="Administrador">Administrador</option>
              <option value="Trabajador">Trabajador</option>
              <option value="Practicas">Practicas</option>
            </select>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Eliminar empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form action="/user/removeUser" method="POST">

          <input type="hidden" name="id" id="edit_id">

          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellidos" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">DNI</label>
            <input type="text" name="dni" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" disabled >
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" disabled>
          </div>

          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="rol" class="form-select" disabled>
              <option value="">Seleccionar rol</option>
              <option value="Administrador">Administrador</option>
              <option value="Trabajador">Trabajador</option>
            </select>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>



<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureEmpleados.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>