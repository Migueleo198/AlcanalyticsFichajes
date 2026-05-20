<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<?php
$mesActual = date('Y-m');

$fichajesMes = array_filter($fichajes ?? [], function($f) use ($mesActual) {
    return isset($f['fecha']) && strpos($f['fecha'], $mesActual) === 0;
});
?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">

    <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar fichajes...">

    <div class="d-flex align-items-center">

      <div class="dropdown me-3 position-relative">
        <i class="bi bi-bell fs-5 shake-loop" data-bs-toggle="dropdown" style="cursor:pointer;"></i>

        <span class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
          3
        </span>

        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;">
          <li class="fw-bold mb-2">Notificaciones</li>
          <li class="dropdown-item">Nuevo fichaje registrado</li>
          <li class="dropdown-item">Contrato por vencer</li>
          <li class="dropdown-item">Actualización completada</li>
        </ul>
      </div>

      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>

    </div>
  </div>

  <!-- HEADER -->
  <h3>Detalle de Fichajes</h3>
  <p class="text-muted">Consulta y filtra los fichajes de los empleados</p>

  <!-- CARDS -->
  <div class="row g-3 mb-4">

    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Total fichajes este mes</h6>
        <h3><?= count($fichajesMes) ?></h3>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Activos este mes</h6>
        <h3>
          <?= count(array_filter($fichajesMes, fn($f) => ($f['estado'] ?? '') === 'abierto')) ?>
        </h3>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Cerrados este mes</h6>
        <h3>
          <?= count(array_filter($fichajesMes, fn($f) => ($f['estado'] ?? '') === 'cerrado')) ?>
        </h3>
      </div>
    </div>

  </div>

  <!-- TABLE -->
  <div class="card card-custom p-3">

    <div class="d-flex justify-content-between mb-3">
      <h5>Listado de Fichajes</h5>

      <!-- FILTERS -->
      <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-funnel"></i> Filtrar
        </button>

        <div class="dropdown-menu p-3" style="width: 300px;">

          <!-- EMPLEADO -->
          <div class="mb-2">
            <label class="form-label">Empleado</label>

            <select id="buscadorTablaF" class="form-select">
              <option value="">Todos los empleados</option>

              <?php 
                $empleadosUnicos = [];

                foreach ($fichajes ?? [] as $f) {
                    $nombreCompleto = trim(($f['nombre'] ?? '') . ' ' . ($f['apellidos'] ?? ''));
                    if ($nombreCompleto !== '') {
                        $empleadosUnicos[$nombreCompleto] = $nombreCompleto;
                    }
                }

                ksort($empleadosUnicos);

                foreach ($empleadosUnicos as $nombre):
              ?>
                <option value="<?= strtolower($nombre) ?>">
                  <?= htmlspecialchars($nombre) ?>
                </option>
              <?php endforeach; ?>
            </select>

          </div>

          <!-- ESTADO -->
          <div class="mb-2">
            <label class="form-label">Estado</label>
            <select id="filterEstado" class="form-select">
              <option value="">Todos</option>
              <option value="cerrado">Cerrado</option>
              <option value="abierto">Abierto</option>
            </select>
          </div>

          <!-- FECHA -->
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

    <!-- TABLE -->
    <table id="tablaFichajes" class="table table-hover align-middle" data-pagination="true">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Entrada</th>
          <th>Salida</th>
          <th>Estado</th>
          <th>Descansos</th>
        </tr>
      </thead>

      <tbody id="lista">

        <?php foreach ($fichajes ?? [] as $f): ?>
          <tr>

            <td><?= htmlspecialchars($f['fecha'] ?? '') ?></td>

            <td>
              <?= htmlspecialchars(($f['nombre'] ?? '') . ' ' . ($f['apellidos'] ?? '')) ?>
            </td>

            <td><?= htmlspecialchars($f['hora_entrada'] ?? '') ?></td>

            <td><?= htmlspecialchars($f['hora_salida'] ?? 'En curso') ?></td>

            <td>
              <?php
                $estado = $f['estado'] ?? '';
                if ($estado === 'cerrado') {
                    echo '<span class="badge bg-success">Cerrado</span>';
                } elseif ($estado === 'abierto') {
                    echo '<span class="badge bg-warning">Abierto</span>';
                } else {
                    echo '<span class="badge bg-secondary">'.htmlspecialchars($estado).'</span>';
                }
              ?>
            </td>

            <td>
              <?php if (!empty($f['descansos'])): ?>
                <?php foreach ($f['descansos'] as $d): ?>
                  <div>
                    <?= htmlspecialchars($d['hora_inicio'] ?? '') ?>
                    →
                    <?= htmlspecialchars($d['hora_fin'] ?? 'En curso') ?>
                    (<?= htmlspecialchars($d['motivo'] ?? '') ?>)
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <span class="text-muted">Sin pausas</span>
              <?php endif; ?>
            </td>

          </tr>
        <?php endforeach; ?>

      </tbody>
    </table>

  </div>

</div>
</div>

<!-- SCRIPTS -->
<script>
var RUTA_URL = "<?= RUTA_URL ?>";
</script>

<!-- FIX: ensure tableReady always triggers AFTER DOM + modules -->
<script>
window.addEventListener("load", () => {
    setTimeout(() => {
        document.dispatchEvent(new Event("tableReady"));
    }, 50);
});
</script>

<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/filtroFichajesDetalle.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>