<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar fichajes...">

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
    <i class="bi bi-person-circle fs-5 profile" 
     data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>
    </div>
</div>

  <!-- HEADER -->
  <h3>Bienvenido, <?php echo $_SESSION['nombre']?> </h3>
  <p>Resumen de fichajes de empleados</p>

  <!-- CARDS KPI -->
  <div class="row g-3 mb-4">

    <div class="col-md-3">
    <a href="<?= RUTA_URL ?>/empleado/index" class="text-decoration-none">
    <div class="card card-custom p-3 cardhov">
      <h6>Empleados activos</h6>
      <h3><?= $datos['empleadosActivos'] ?? 0 ?></h3>
    </div>
  </a>
  </div>
  

    <div class="col-md-3">
      <div class="card card-custom p-3 cardhov">
        <h6>Fichajes hoy</h6>
        <h3><?= $datos['fichajesHoy'] ?? 0 ?></h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-custom p-3 cardhov">
        <h6>Horas trabajadas</h6>
        <h3><?= round($datos['horasHoy'] ?? 0, 2) ?>h</h3>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card card-custom p-3 cardhov">
        <h6>Retrasos</h6>
        <h3><?= $datos['retrasosHoy'] ?? 0 ?></h3>
      </div>
    </div>

  </div>

  <!-- TABLA -->
  <div class="card card-custom p-3">
    <div class="d-flex justify-content-between mb-3">
      <h5>Listado de Fichajes</h5>
      <button class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-funnel"></i> Filtrar
      </button> 
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Empleado</th>
          <th>Fecha</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id='lista'>
        <!-- Se rellena por JS -->
      </tbody>
    </table>
  </div>

</div>
</div>

<!-- MODAL INCIDENCIA -->
<div class="modal fade" id="incidenciaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Nueva Incidencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="<?= RUTA_URL ?>/incidencias/addIncidencia" method="POST">
         
          <div class="mb-3">
            <label class="form-label">Empleado</label>
            <input type="text" class="form-control" id="inc_usuario" disabled>
          </div>

          <input type="hidden" name="id_fichaje" id="inc_id_fichaje">

          <div class="mb-3">
            <label class="form-label">Mensaje</label>
            <textarea class="form-control" name="mensaje" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Respuesta</label>
            <textarea class="form-control" name="respuesta" readonly></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
              <option value="pendiente">Pendiente</option>
              <option value="resuelto">Resuelto</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="datetime-local" class="form-control" name="fecha" required>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- MODAL VER INCIDENCIAS -->
<div class="modal fade" id="verIncidenciaModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Incidencias del fichaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div id="listaIncidencias">

          <div class="text-center text-muted">
            Cargando incidencias...
          </div>

        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<script>
const RUTA_URL = "<?= RUTA_URL ?>";
</script>

<!-- JS -->
<script>
const fichajeActivo = <?php echo json_encode($datos['fichajeActivo'] ?? null); ?>;
</script>

<script type='module' src="<?= RUTA_URL ?>/js/application/homeLoadCounterUseCases.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureHome.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>