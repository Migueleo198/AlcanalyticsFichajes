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
       data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>
  </div>
</div>

<div class="container mt-4">

  <!-- ========================= -->
  <!-- AUSENCIAS REMUNERADAS -->
  <!-- ========================= -->
  <h3 class="mb-3">Ausencias remuneradas</h3>

  <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success">
      Solicitud registrada correctamente
    </div>
  <?php endif; ?>

  <div class="card p-3 mb-4">
    <!-- ✅ CAMBIADO endpoint -->
    <form method="POST" action="<?= RUTA_URL ?>/Ausencias/solicitarRemunerada">

      <div class="row">
        <div class="col-md-4">
          <label>Motivo</label>
          <select name="id_motivo" id="selectMotivoRemunerada" class="form-select" required>
            <option value="">Selecciona motivo</option>

            <?php foreach ($datos['motivos_remunerados'] as $motivo): ?>
              <option value="<?= $motivo['id_motivo'] ?>"
                data-limite="<?= htmlspecialchars($motivo['limite_horas_anual'] ?? '') ?>"
                data-nota="<?= htmlspecialchars($motivo['nota_limite'] ?? '') ?>"
                data-dias="<?= htmlspecialchars($motivo['dias'] ?? '') ?>">
                <?= htmlspecialchars($motivo['nombre']) ?>
              </option>
            <?php endforeach; ?>

          </select>
          <div id="avisoLimiteMotivo" class="alert alert-warning mt-2 d-none small py-2">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <span id="avisoLimiteTexto"></span>
          </div>
        </div>

        <div class="col-md-4">
          <label>Fecha inicio</label>
          <input type="date" name="fecha_inicio" class="form-control" required>
        </div>

        <div class="col-md-4">
          <label>Fecha fin</label>
          <input type="date" name="fecha_fin" class="form-control">
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12">
          <label>Descripción <span class="text-muted small">(opcional)</span></label>
          <textarea name="descripcion" class="form-control" rows="2"
                    placeholder="Añade detalles adicionales sobre la ausencia..."></textarea>
        </div>
      </div>

      <button class="btn btn-primary mt-3">
        Solicitar ausencia remunerada
      </button>

    </form>
  </div>


  <?php if ($_SESSION['rol'] === 'Administrador'): ?>

  <!-- ========================= -->
  <!-- AUSENCIAS NO REMUNERADAS -->
  <!-- ========================= -->
  <h3 class="mb-3">Ausencias no remuneradas</h3>

  <!-- FORM -->
  <div class="card p-3 mb-4">
    <form id="formAusencia">

      <div class="row">

        <div class="col-md-3">
          <label>Usuario</label>
          <select name="id_usuario" class="form-control" required>
            <option value="">Selecciona</option>
            <?php foreach ($datos['usuarios'] as $u): ?>
              <option value="<?= $u['id_usuario'] ?>">
                <?= $u['nombre'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Motivo</label>
          <input type="text" name="motivo_texto" class="form-control"
                 placeholder="Escribe el motivo..." maxlength="255" required>
        </div>

        <div class="col-md-3">
          <label>Desde</label>
          <input type="date" name="fecha_inicio" class="form-control" required>
        </div>

        <div class="col-md-3">
          <label>Hasta</label>
          <input type="date" name="fecha_fin" class="form-control" required>
        </div>

      </div>

      <button class="btn btn-primary mt-3">
        Crear ausencia no remunerada
      </button>

    </form>
  </div>

  <!-- TABLA -->
  <div class="card p-3">
    <table class="table table-striped" id="tablaAusencias">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Motivo</th>
          <th>Desde</th>
          <th>Hasta</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($datos['ausencias'] as $a): ?>
          <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['usuario']) ?></td>
            <td><?= htmlspecialchars($a['motivo']) ?></td>
            <td><?= htmlspecialchars($a['fecha_inicio']) ?></td>
            <td><?= htmlspecialchars($a['fecha_fin']) ?></td>
            <td class="d-flex gap-1">
              <button class="btn btn-outline-primary btn-sm btnEditarAusencia"
                data-id="<?= $a['id'] ?>"
                data-usuario="<?= $a['id_usuario'] ?>"
                data-motivo="<?= htmlspecialchars($a['motivo_personalizado'] ?? $a['motivo']) ?>"
                data-inicio="<?= $a['fecha_inicio'] ?>"
                data-fin="<?= $a['fecha_fin'] ?>">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-danger btn-sm btnEliminar"
                data-id="<?= $a['id'] ?>">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<!-- MODAL EDITAR AUSENCIA NO REMUNERADA -->
<div class="modal fade" id="modalEditarAusencia" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar ausencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarAusencia">
          <input type="hidden" name="id" id="editId">

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <select name="id_usuario" id="editUsuario" class="form-select" required>
              <?php foreach ($datos['usuarios'] as $u): ?>
                <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Motivo</label>
            <input type="text" name="motivo_texto" id="editMotivo" class="form-control"
                   placeholder="Escribe el motivo..." maxlength="255" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_inicio" id="editInicio" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_fin" id="editFin" class="form-control" required>
          </div>

          <div id="editFeedback" class="d-none mb-3"></div>
          <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
var RUTA_URL = "<?= RUTA_URL ?>";

// Aviso de límite al seleccionar motivo remunerado
document.getElementById("selectMotivoRemunerada")?.addEventListener("change", function() {
  const opt    = this.options[this.selectedIndex];
  const nota   = opt?.dataset?.nota   || '';
  const limite = opt?.dataset?.limite || '';
  const aviso  = document.getElementById("avisoLimiteMotivo");
  const texto  = document.getElementById("avisoLimiteTexto");

  if (nota) {
    texto.textContent = nota;
    aviso.classList.remove("d-none");
  } else {
    aviso.classList.add("d-none");
  }
});

// CREAR NO REMUNERADA
document.getElementById("formAusencia").onsubmit = function(e) {
  e.preventDefault();
  fetch(RUTA_URL + "/Ausencias/crearNoRemunerada", {
    method: "POST",
    body: new FormData(this)
  })
  .then(r => r.json())
  .then(() => location.reload());
};

// ELIMINAR
document.querySelectorAll(".btnEliminar").forEach(btn => {
  btn.onclick = function() {
    if (!confirm("¿Eliminar ausencia?")) return;
    fetch(RUTA_URL + "/Ausencias/eliminarNoRemunerada", {
      method: "POST",
      body: new URLSearchParams({ id: this.dataset.id })
    })
    .then(r => r.json())
    .then(() => location.reload());
  };
});

// EDITAR — abrir modal
const modalEl  = document.getElementById("modalEditarAusencia");
const bsModal  = modalEl ? new bootstrap.Modal(modalEl) : null;

document.querySelectorAll(".btnEditarAusencia").forEach(btn => {
  btn.onclick = function() {
    document.getElementById("editId").value      = this.dataset.id;
    document.getElementById("editUsuario").value = this.dataset.usuario;
    document.getElementById("editMotivo").value  = this.dataset.motivo;
    document.getElementById("editInicio").value  = this.dataset.inicio;
    document.getElementById("editFin").value     = this.dataset.fin;
    document.getElementById("editFeedback").className = "d-none mb-3";
    bsModal.show();
  };
});

// EDITAR — guardar
document.getElementById("formEditarAusencia").onsubmit = function(e) {
  e.preventDefault();
  const feedback = document.getElementById("editFeedback");
  const btn = this.querySelector('[type="submit"]');
  btn.disabled = true;
  btn.textContent = "Guardando…";

  fetch(RUTA_URL + "/Ausencias/editarNoRemunerada", {
    method: "POST",
    body: new FormData(this)
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      location.reload();
    } else {
      feedback.className = "alert alert-danger mb-3";
      feedback.textContent = "Error al guardar los cambios";
      btn.disabled = false;
      btn.textContent = "Guardar cambios";
    }
  })
  .catch(() => {
    feedback.className = "alert alert-danger mb-3";
    feedback.textContent = "Error de red";
    btn.disabled = false;
    btn.textContent = "Guardar cambios";
  });
};
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>