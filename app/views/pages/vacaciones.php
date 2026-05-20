<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <h5 class="mb-0">Vacaciones</h5>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#vacacionesModal">
        <i class="bi bi-plus-lg me-1"></i>Solicitar vacaciones
      </button>
    </div>
    <div class="d-flex align-items-center">
      <!-- NOTIFICACIONES -->
      <div class="dropdown me-3 position-relative">
        <i class="bi bi-bell fs-5 shake-loop" id="notificacionesIcon"
           data-bs-toggle="dropdown" style="cursor:pointer;"></i>
        <span id="contadorNotificaciones"
              class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
          0
        </span>
        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;" id="listaNotificaciones">
          <li class="fw-bold mb-2">Notificaciones</li>
        </ul>
      </div>
      <!-- PERFIL -->
      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= htmlspecialchars($_SESSION['id_usuario'] ?? '') ?>"></i>
    </div>
  </div>

  <?php
  $r      = $datos['resumen'] ?? [];
  $esAdmin = $datos['esAdmin'] ?? false;

  // Mensajes de feedback por querystring
  if (!empty($_GET['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php if ($_GET['error'] === 'solapamiento'): ?>
      Ya tienes vacaciones solicitadas en ese período.
    <?php else: ?>
      Revisa las fechas introducidas (la fecha de inicio no puede ser posterior a la de fin).
    <?php endif; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if (!empty($_GET['ok'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    Solicitud enviada correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="vac-kpi vac-kpi-yellow">
        <div class="vac-kpi-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="vac-kpi-val"><?= (int)($r['pendientes'] ?? 0) ?></div>
        <div class="vac-kpi-label">Pendientes</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="vac-kpi vac-kpi-green">
        <div class="vac-kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
        <div class="vac-kpi-val"><?= (int)($r['aprobadas'] ?? 0) ?></div>
        <div class="vac-kpi-label">Aprobadas</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="vac-kpi vac-kpi-red">
        <div class="vac-kpi-icon"><i class="bi bi-x-circle-fill"></i></div>
        <div class="vac-kpi-val"><?= (int)($r['rechazadas'] ?? 0) ?></div>
        <div class="vac-kpi-label">Rechazadas</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="vac-kpi vac-kpi-blue">
        <div class="vac-kpi-icon"><i class="bi bi-calendar-check-fill"></i></div>
        <div class="vac-kpi-val"><?= (int)($r['dias_aprobados'] ?? 0) ?></div>
        <div class="vac-kpi-label">Días aprobados</div>
      </div>
    </div>
  </div>

  <!-- TABLA VACACIONES -->
  <div class="vac-card">
    <div class="vac-card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-table me-2"></i>
        <?= $esAdmin ? 'Solicitudes de todos los empleados' : 'Mis solicitudes de vacaciones' ?>
      </span>
      <input type="text" id="buscadorVac" class="form-control form-control-sm w-auto"
             placeholder="Buscar…" style="min-width:200px;">
    </div>

    <div class="table-responsive">
      <table class="table vac-table mb-0" id="tablaVac">
        <thead>
          <tr>
            <?php if ($esAdmin): ?>
            <th>Empleado</th>
            <?php endif; ?>
            <th>F. Solicitud</th>
            <th>Fecha inicio</th>
            <th>Fecha fin</th>
            <th>Días</th>
            <th>Estado</th>
            <th>Comentario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datos['vacaciones'])): ?>
          <tr>
            <td colspan="<?= $esAdmin ? 8 : 7 ?>" class="text-center text-muted py-4">
              <i class="bi bi-inbox fs-4 d-block mb-2"></i>
              No hay solicitudes de vacaciones
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($datos['vacaciones'] as $v):
            $estado = $v['estado'] ?? 'pendiente';
            $badgeClass = match($estado) {
              'aprobada'  => 'bg-success',
              'rechazada' => 'bg-danger',
              default     => 'bg-warning text-dark',
            };
          ?>
          <tr>
            <?php if ($esAdmin): ?>
            <td class="fw-semibold"><?= htmlspecialchars(($v['nombre'] ?? '') . ' ' . ($v['apellidos'] ?? '')) ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($v['fecha_solicitud'] ?? 'now'))) ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($v['fecha_inicio']))) ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($v['fecha_fin']))) ?></td>
            <td class="text-center"><span class="badge bg-light text-dark border"><?= (int)$v['dias_solicitados'] ?></span></td>
            <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span></td>
            <td class="text-muted small"><?= htmlspecialchars($v['comentario'] ?? '—') ?></td>
            <td>
              <div class="d-flex gap-1 flex-wrap">

                <?php if ($esAdmin && $estado === 'pendiente'): ?>
                <form method="POST" action="<?= RUTA_URL ?>/Vacaciones/updateEstado">
                  <input type="hidden" name="id_vacacion" value="<?= $v['id_vacacion'] ?>">
                  <input type="hidden" name="estado" value="aprobada">
                  <button class="btn btn-success btn-sm" title="Aprobar">
                    <i class="bi bi-check-lg"></i>
                  </button>
                </form>
                <form method="POST" action="<?= RUTA_URL ?>/Vacaciones/updateEstado">
                  <input type="hidden" name="id_vacacion" value="<?= $v['id_vacacion'] ?>">
                  <input type="hidden" name="estado" value="rechazada">
                  <button class="btn btn-danger btn-sm" title="Rechazar">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </form>
                <?php endif; ?>

                <?php if ($esAdmin || $estado === 'pendiente'): ?>
                <a href="<?= RUTA_URL ?>/Vacaciones/delete/<?= $v['id_vacacion'] ?>"
                   class="btn btn-outline-secondary btn-sm"
                   title="Eliminar"
                   onclick="return confirm('¿Eliminar esta solicitud?')">
                  <i class="bi bi-trash"></i>
                </a>
                <?php endif; ?>

              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</div>

<!-- MODAL SOLICITAR VACACIONES -->
<div class="modal fade" id="vacacionesModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-airplane me-2"></i>Solicitar vacaciones</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= RUTA_URL ?>/Vacaciones/add" data-validate>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Fecha inicio <span class="text-danger">*</span></label>
            <input type="date" name="fecha_inicio" id="vac_inicio" class="form-control" required
                   min="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Fecha fin <span class="text-danger">*</span></label>
            <input type="date" name="fecha_fin" class="form-control" required
                   data-date-after="vac_inicio" min="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Comentario <span class="text-muted fw-normal">(opcional)</span></label>
            <textarea name="comentario" class="form-control" rows="2" placeholder="Motivo o notas…" data-no-valid></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Enviar solicitud</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Buscador de tabla
document.getElementById('buscadorVac')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#tablaVac tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>

<style>
.vac-kpi{border-radius:14px;padding:18px 16px;display:flex;flex-direction:column;gap:4px;box-shadow:0 2px 10px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;}
.vac-kpi:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.12);}
.vac-kpi-icon{font-size:1.5rem;opacity:.8;}
.vac-kpi-val{font-size:1.9rem;font-weight:700;line-height:1.1;}
.vac-kpi-label{font-size:.78rem;font-weight:500;opacity:.75;}
.vac-kpi-yellow{background:linear-gradient(135deg,#fef9c3,#fefce8);color:#854d0e;}
.vac-kpi-green{background:linear-gradient(135deg,#dcfce7,#f0fdf4);color:#14532d;}
.vac-kpi-red{background:linear-gradient(135deg,#fee2e2,#fef2f2);color:#7f1d1d;}
.vac-kpi-blue{background:linear-gradient(135deg,#dbeafe,#eff6ff);color:#1e3a8a;}

.vac-card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.vac-card-header{padding:14px 20px;border-bottom:2px solid #e5e7eb;background:#f8fafc;font-weight:600;font-size:.9rem;color:#374151;}

.vac-table thead tr th{background:#f1f5f9;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;border-bottom:2px solid #e2e8f0;white-space:nowrap;padding:10px 14px;}
.vac-table tbody tr td{font-size:.875rem;vertical-align:middle;padding:10px 14px;border-bottom:1px solid #f1f5f9;}
.vac-table tbody tr:last-child td{border-bottom:none;}
.vac-table tbody tr:hover{background:#f8fafc;}
</style>

<script>var RUTA_URL = "<?= RUTA_URL ?>";</script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
