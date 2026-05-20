<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar jornadas...">
    <div class="d-flex align-items-center">
      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= $_SESSION['id_usuario'] ?? '' ?>"></i>
    </div>
  </div>

  <!-- CARD -->
  <div class="card shadow-sm border-0 p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Gestión de Jornadas</h5>
      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus"></i> Nueva jornada
      </button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Empleado</th>
            <th>Horas/día</th>
            <th>Horas/semana</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="listaJornadas">
          <!-- JS RENDER -->
        </tbody>
      </table>
    </div>

  </div>

</div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nueva jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddJornada" data-validate>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Empleado <span class="text-danger">*</span></label>
            <select name="id_usuario" class="form-select" required>
              <option value="">— Seleccionar empleado —</option>
              <?php foreach ($datos['usuarios'] as $u): ?>
              <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-3 mb-1">
            <div class="col-6">
              <label class="form-label fw-semibold">Horas/día <span class="text-danger">*</span></label>
              <input type="number" step="0.5" min="0.5" max="24" name="horas_dia" class="form-control" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Horas/semana <span class="text-danger">*</span></label>
              <input type="number" step="0.5" min="0.5" max="168" name="horas_semana" class="form-control" required>
            </div>
          </div>
          <div class="row g-3 mb-1">
            <div class="col-6">
              <label class="form-label fw-semibold">Fecha inicio</label>
              <input type="date" name="fecha_inicio" id="add_inicio" class="form-control" data-no-valid>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Fecha fin</label>
              <input type="date" name="fecha_fin" class="form-control" data-date-after="add_inicio" data-no-valid>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditJornada">
        <input type="hidden" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Empleado</label>
            <select name="id_usuario" class="form-select">
              <?php foreach ($datos['usuarios'] as $u): ?>
              <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Horas día</label>
            <input type="number" step="0.1" min="0" name="horas_dia" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Horas semana</label>
            <input type="number" step="0.1" min="0" name="horas_semana" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL DELETE -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Eliminar jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formDeleteJornada">
        <input type="hidden" name="id">
        <div class="modal-body">
          <p>¿Seguro que quieres eliminar esta jornada?</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-danger" type="submit">Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  var RUTA_URL = "<?= RUTA_URL ?>";
  var USER_ROL = "<?= $_SESSION['rol'] ?? '' ?>";
  var USER_ID  = "<?= $_SESSION['id_usuario'] ?? '' ?>";
</script>

<!-- defer: executes after DOM + Bootstrap are ready -->
<script defer src="<?= RUTA_URL ?>/js/infrastructure/infrastructureAsignJornadas.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>
