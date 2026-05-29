<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?> 

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

<!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar ausencias...">

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

<div class="container mt-4">
  <h3><?= $datos['title'] ?></h3>

  <div class="card card-custom p-3">

 <div class="d-flex justify-content-between mb-3 align-items-center">
  <h5>Gestión de Ausencias</h5>

  <div class="dropdown">
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
      <i class="bi bi-funnel"></i> Filtrar
    </button>

    <div class="dropdown-menu p-3" style="width: 300px;">

      <!-- EMPLEADO -->
      <div class="mb-2">
        <label class="form-label">Empleado</label>
        <input type="text" id="filterEmpleado" class="form-control" placeholder="Empleado...">
      </div>

      <!-- MOTIVO -->
      <div class="mb-2">
        <label class="form-label">Motivo</label>
        <input type="text" id="filterMotivo" class="form-control" placeholder="Motivo...">
      </div>

      <!-- ESTADO -->
      <div class="mb-2">
        <label class="form-label">Estado</label>
        <select id="filterEstado" class="form-select">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="aprobada">Aprobada</option>
          <option value="rechazada">Rechazada</option>
        </select>
      </div>

      <!-- FECHA INICIO -->
      <div class="mb-2">
        <label class="form-label">Fecha inicio</label>
        <input type="date" id="filterFechaInicio" class="form-control">
      </div>

      <!-- CLEAR -->
      <div class="d-flex justify-content-between mt-3">
        <button id="clearFilters" class="btn btn-sm btn-secondary">Limpiar</button>
      </div>

    </div>
  </div>
</div>

  <?php if (empty($datos['bajas'])): ?>

    <div class="alert alert-info">
      No hay ausencias registradas.
    </div>

  <?php else: ?>

    <table class="table align-middle">
      <thead>
        <tr>
          <th>Empleado</th>
          <th>Motivo</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>

      <?php foreach ($datos['bajas'] as $baja): ?>

        <tr>
          <td><?= htmlspecialchars($baja['usuario'] ?? '') ?></td>
          <td><?= htmlspecialchars($baja['motivo'] ?? '') ?></td>
          <td><?= htmlspecialchars($baja['fecha_inicio'] ?? '') ?></td>
          <td><?= htmlspecialchars($baja['fecha_fin'] ?? '-') ?></td>

          <td>
            <?php
              switch ($baja['estado']) {
                case 'aprobada':
                  echo '<span class="badge bg-success">Aprobada</span>';
                  break;
                case 'rechazada':
                  echo '<span class="badge bg-danger">Rechazada</span>';
                  break;
                default:
                  echo '<span class="badge bg-primary">Pendiente</span>';
              }
            ?>
          </td>

          <td class="d-flex gap-1 flex-wrap">
            <button class="btn btn-outline-primary btn-sm btnEditarBaja"
              data-id="<?= $baja['id'] ?>"
              data-motivo="<?= $baja['id_motivo'] ?>"
              data-inicio="<?= htmlspecialchars($baja['fecha_inicio']) ?>"
              data-fin="<?= htmlspecialchars($baja['fecha_fin'] ?? '') ?>"
              data-descripcion="<?= htmlspecialchars($baja['descripcion'] ?? '') ?>"
              data-estado="<?= htmlspecialchars($baja['estado']) ?>">
              <i class="bi bi-pencil"></i>
            </button>

            <button class="btn btn-outline-danger btn-sm btnEliminarBaja"
              data-id="<?= $baja['id'] ?>"
              data-nombre="<?= htmlspecialchars($baja['usuario'] ?? '') ?>">
              <i class="bi bi-trash"></i>
            </button>

            <?php if ($baja['estado'] == 'pendiente'): ?>
              <a href="<?= RUTA_URL ?>/Bajas/cambiarEstado/<?= $baja['id'] ?>/aprobada"
                 class="btn btn-success btn-sm">Aprobar</a>
              <a href="<?= RUTA_URL ?>/Bajas/cambiarEstado/<?= $baja['id'] ?>/rechazada"
                 class="btn btn-danger btn-sm">Rechazar</a>
            <?php endif; ?>
          </td>

        </tr>

      <?php endforeach; ?>

      </tbody>
    </table>

  <?php endif; ?>

</div>
            </div>

          </div>
          </div>


<!-- MODAL EDITAR AUSENCIA REMUNERADA (ADMIN) -->
<div class="modal fade" id="modalEditarBaja" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar ausencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarBaja">
          <input type="hidden" name="id" id="editBajaId">

          <div class="mb-3">
            <label class="form-label">Motivo</label>
            <select name="id_motivo" id="editBajaMotivo" class="form-select" required>
              <?php foreach ($datos['motivos'] as $m): ?>
                <option value="<?= $m['id_motivo'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="fecha_inicio" id="editBajaInicio" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="fecha_fin" id="editBajaFin" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" id="editBajaDesc" class="form-control" rows="2"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" id="editBajaEstado" class="form-select">
              <option value="pendiente">Pendiente</option>
              <option value="aprobada">Aprobada</option>
              <option value="rechazada">Rechazada</option>
            </select>
          </div>

          <div id="editBajaFeedback" class="d-none mb-3"></div>
          <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
var RUTA_URL = "<?= RUTA_URL ?>";

document.addEventListener('DOMContentLoaded', () => {
    document.dispatchEvent(new Event('tableReady'));

    const modal    = new bootstrap.Modal(document.getElementById('modalEditarBaja'));
    const feedback = document.getElementById('editBajaFeedback');

    document.querySelectorAll('.btnEliminarBaja').forEach(btn => {
        btn.addEventListener('click', () => {
            const nombre = btn.dataset.nombre || 'este registro';
            if (!confirm(`¿Eliminar la ausencia de ${nombre}? Esta acción no se puede deshacer.`)) return;

            fetch(RUTA_URL + '/Bajas/eliminarBaja', {
                method: 'POST',
                body: new URLSearchParams({ id: btn.dataset.id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) location.reload();
                else alert('Error al eliminar la ausencia');
            })
            .catch(() => alert('Error de red'));
        });
    });

    document.querySelectorAll('.btnEditarBaja').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('editBajaId').value      = btn.dataset.id;
            document.getElementById('editBajaMotivo').value  = btn.dataset.motivo;
            document.getElementById('editBajaInicio').value  = btn.dataset.inicio;
            document.getElementById('editBajaFin').value     = btn.dataset.fin;
            document.getElementById('editBajaDesc').value    = btn.dataset.descripcion;
            document.getElementById('editBajaEstado').value  = btn.dataset.estado;
            feedback.className = 'd-none mb-3';
            modal.show();
        });
    });

    document.getElementById('formEditarBaja').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando…';

        fetch(RUTA_URL + '/Bajas/editarBaja', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                location.reload();
            } else {
                feedback.className = 'alert alert-danger mb-3';
                feedback.textContent = 'Error al guardar los cambios';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guardar cambios';
            }
        })
        .catch(() => {
            feedback.className = 'alert alert-danger mb-3';
            feedback.textContent = 'Error de red';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Guardar cambios';
        });
    });
});
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtroBajas.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>