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
      <i class="bi bi-bell fs-5 shake-loop" id="notificacionesIcon" 
   data-bs-toggle="dropdown" 
   style="cursor:pointer;"></i>
      
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
    <i class="bi bi-person-circle fs-5 profile" 
     data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>
    </div>
</div>

  <!-- 🔵 CARD -->
  <div class="card shadow-sm border-0 p-3">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Listado de Empleados</h5>

      <div class="d-flex gap-2">
       <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
  <i class="bi bi-funnel"></i> Filtrar
</button>

<div class="dropdown-menu p-3" style="width: 300px;">

  <div class="mb-2">
    <label class="form-label">Nombre</label>
    <input type="text" id="filterNombre" class="form-control">
  </div>

  <div class="mb-2">
    <label class="form-label">Usuario</label>
    <input type="text" id="filterUsuario" class="form-control">
  </div>

  <div class="mb-2">
    <label class="form-label">DNI</label>
    <input type="text" id="filterDni" class="form-control">
  </div>

  <div class="mb-2">
    <label class="form-label">Rol</label>
    <select id="filterRol" class="form-select">
      <option value="">Todos</option>
      <option value="Administrador">Administrador</option>
      <option value="Trabajador">Trabajador</option>
      <option value="Practicas">Practicas</option>
    </select>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <button id="clearFilters" class="btn btn-sm btn-secondary">Limpiar</button>
  </div>

</div>

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
            <th>Fecha_Nac</th>
            <th>Matrículas</th>
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

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/user/addUser" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Añadir empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
          <input type="text" name="apellidos" class="form-control mb-2" placeholder="Apellidos" required>
          <input type="text" name="usuario" class="form-control mb-2" placeholder="Usuario" required>
          <input type="password" name="contraseña" class="form-control mb-2" placeholder="Contraseña" required>
          <input type="text" name="dni" class="form-control mb-2" placeholder="DNI" required>
          <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono">
          <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

          <!-- ✅ IMPORTANT FIX -->
          <label class="form-label">Fecha nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control mb-2">

          <input type="text" name="matriculas" class="form-control mb-2" placeholder="Matrículas (separadas por coma)">

          <select name="rol" class="form-select mb-2" required>
            <option value="Administrador">Administrador</option>
            <option value="Trabajador">Trabajador</option>
            <option value="Practicas">Practicas</option>
          </select>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/user/editUser" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Editar empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id" id="edit_id">

          <input type="text" name="nombre" class="form-control mb-2">
          <input type="text" name="apellidos" class="form-control mb-2">
          <input type="text" name="usuario" class="form-control mb-2">
          <input type="text" name="dni" class="form-control mb-2">
          <input type="text" name="telefono" class="form-control mb-2">
          <input type="email" name="email" class="form-control mb-2">

          
          <label class="form-label">Fecha nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control mb-2">

          <!-- MATRÍCULAS -->
          <div class="mb-2">
            <label class="form-label">Matrículas</label>

            <select id="editMatriculasSelect" class="form-select mb-2"></select>

            <input type="text" id="editMatriculaInput" class="form-control mb-2" placeholder="Editar matrícula">

            <button type="button" class="btn btn-sm btn-primary" id="saveMatriculaBtn">
              Guardar matrícula
            </button>
          </div>

          <select name="rol" class="form-select mb-2">
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


<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="/user/removeUser" method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Eliminar empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id" id="delete_id">

          <input type="text" name="nombre" class="form-control mb-2" disabled>
          <input type="text" name="apellidos" class="form-control mb-2" disabled>
          <input type="text" name="usuario" class="form-control mb-2" disabled>
          <input type="text" name="dni" class="form-control mb-2" disabled>
          <input type="text" name="telefono" class="form-control mb-2" disabled>
          <input type="email" name="email" class="form-control mb-2" disabled>

          <!-- ✅ FIX -->
          <label class="form-label">Fecha nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control mb-2" disabled>

          <!-- MATRÍCULAS DISPLAY -->
          <div id="deleteMatriculasContainer" class="mb-2"></div>

          <select name="rol" class="form-select mb-2" disabled>
            <option value="Administrador">Administrador</option>
            <option value="Trabajador">Trabajador</option>
            <option value="Practicas">Practicas</option>
          </select>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>

      </form>

    </div>
  </div>
</div>



<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureEmpleados.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/filtrosEmpleados.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>