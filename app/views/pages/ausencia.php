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
    <div class="alert alert-success">Solicitud registrada correctamente</div>
  <?php endif; ?>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'limite'): ?>
    <div class="alert alert-danger">
      <i class="bi bi-x-circle-fill me-1"></i>
      <strong>Límite superado:</strong> Para el motivo
      <em><?= htmlspecialchars($_GET['motivo'] ?? '') ?></em>
      solo te quedan <strong><?= round((float)($_GET['restantes'] ?? 0), 2) ?>h</strong>
      de las <?= round((float)($_GET['limite'] ?? 0), 2) ?>h anuales retribuidas.
      Ajusta las horas o solicítala como ausencia no remunerada.
    </div>
  <?php endif; ?>

  <div class="card p-3 mb-4">
    <form method="POST" action="<?= RUTA_URL ?>/Ausencias/solicitarRemunerada">

      <div class="row g-3">
        <div class="col-md-4">
          <label>Motivo</label>
          <select name="id_motivo" id="selectMotivoRemunerada" class="form-select" required>
            <option value="">Selecciona motivo</option>
            <?php foreach ($datos['motivos_remunerados'] as $motivo): ?>
              <?php
                $etiqueta = htmlspecialchars($motivo['nombre']);
                if (!empty($motivo['limite_horas_anual'])) {
                    $etiqueta .= ' — ⏱ máx. ' . $motivo['limite_horas_anual'] . 'h/año';
                } elseif (!empty($motivo['dias'])) {
                    $etiqueta .= ' (' . $motivo['dias'] . ' día' . ($motivo['dias'] != 1 ? 's' : '') . ')';
                }
              ?>
              <option value="<?= $motivo['id_motivo'] ?>"
                data-limite="<?= htmlspecialchars($motivo['limite_horas_anual'] ?? '') ?>"
                data-nota="<?= htmlspecialchars($motivo['nota_limite'] ?? '') ?>"
                data-dias="<?= htmlspecialchars($motivo['dias'] ?? '') ?>">
                <?= $etiqueta ?>
              </option>
            <?php endforeach; ?>
          </select>

          <div id="avisoLimiteMotivo" class="alert alert-warning mt-2 d-none small py-2 mb-0">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <span id="avisoLimiteTexto"></span>
          </div>
        </div>

        <!-- Campo horas: solo visible para motivos con límite en horas -->
        <div class="col-md-2 d-none" id="colHoras">
          <label>Horas <span class="text-danger">*</span></label>
          <input type="number" name="horas" id="inputHoras" class="form-control"
                 min="0.25" max="24" step="0.25" placeholder="0">
          <div id="contadorHoras" class="small mt-1"></div>
        </div>

        <div class="col-md-3">
          <label>Fecha inicio</label>
          <input type="date" name="fecha_inicio" id="remFechaInicio" class="form-control" required>
        </div>

        <div class="col-md-3">
          <label>Fecha fin</label>
          <input type="date" name="fecha_fin" id="remFechaFin" class="form-control">
          <small id="infoDiasMotivo" class="text-muted d-none mt-1 d-block"></small>
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

document.addEventListener("DOMContentLoaded", function () {

  // ── HORAS + AVISO AL SELECCIONAR MOTIVO REMUNERADO ───────────────
  var selMotivo   = document.getElementById("selectMotivoRemunerada");
  var colHoras    = document.getElementById("colHoras");
  var inputHoras  = document.getElementById("inputHoras");
  var cntHoras    = document.getElementById("contadorHoras");
  var avisoBloque = document.getElementById("avisoLimiteMotivo");
  var avisoTexto  = document.getElementById("avisoLimiteTexto");

  if (selMotivo) {
    selMotivo.addEventListener("change", function () {
      var opt    = selMotivo.options[selMotivo.selectedIndex];
      var nota   = (opt && opt.dataset.nota)   ? opt.dataset.nota   : '';
      var limite = (opt && opt.dataset.limite) ? opt.dataset.limite : '';
      var id     = (opt && opt.value)          ? opt.value          : '';

      // Nota de límite
      if (nota) {
        avisoTexto.textContent = nota;
        avisoBloque.classList.remove("d-none");
      } else {
        avisoBloque.classList.add("d-none");
      }

      // Campo horas
      if (limite && id) {
        colHoras.classList.remove("d-none");
        inputHoras.required = true;
        cntHoras.textContent = "Consultando horas disponibles…";
        cntHoras.className   = "small mt-1 text-muted";

        fetch(RUTA_URL + "/Ausencias/checkLimiteHoras?id_motivo=" + id)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (!data.tiene_limite) return;
            if (data.restantes <= 0) {
              cntHoras.textContent = "⛔ Sin horas retribuidas disponibles este año (" + data.usadas + "h usadas de " + data.limite + "h). Esta solicitud será no remunerada.";
              cntHoras.className   = "small mt-1 text-danger";
            } else {
              cntHoras.textContent = "✔ Disponibles: " + data.restantes + "h de " + data.limite + "h anuales (usadas: " + data.usadas + "h)";
              cntHoras.className   = "small mt-1 text-success";
              inputHoras.max       = data.restantes;
            }
          })
          .catch(function () {
            cntHoras.textContent = "No se pudo verificar el límite";
            cntHoras.className   = "small mt-1 text-warning";
          });

      } else {
        colHoras.classList.add("d-none");
        inputHoras.required = false;
        inputHoras.value    = '';
        cntHoras.textContent = '';
      }
    });
  }

  // ── LÍMITE DE DÍAS EN FECHAS ────────────────────────────────────
  var inputInicio   = document.getElementById("remFechaInicio");
  var inputFin      = document.getElementById("remFechaFin");
  var infoDias      = document.getElementById("infoDiasMotivo");

  function sumarDias(fechaStr, dias) {
    var d = new Date(fechaStr + 'T00:00:00');
    d.setDate(d.getDate() + dias - 1);
    return d.toISOString().split('T')[0];
  }

  function aplicarLimiteDias() {
    if (!selMotivo || !inputInicio || !inputFin) return;
    var opt  = selMotivo.options[selMotivo.selectedIndex];
    var dias = opt && opt.dataset.dias ? parseInt(opt.dataset.dias) : 0;

    if (dias > 0 && inputInicio.value) {
      var maxFecha = sumarDias(inputInicio.value, dias);
      inputFin.value = maxFecha;
      inputFin.max   = maxFecha;
      inputFin.min   = inputInicio.value;
      infoDias.textContent = 'Máximo ' + dias + ' día' + (dias !== 1 ? 's' : '') + ' según convenio (hasta ' + maxFecha + ')';
      infoDias.className = 'text-info small mt-1 d-block';
    } else if (dias > 0) {
      // Motivo con días fijo pero aún no hay fecha inicio
      inputFin.removeAttribute('max');
      inputFin.removeAttribute('min');
      infoDias.textContent = 'Este motivo tiene un máximo de ' + dias + ' día' + (dias !== 1 ? 's' : '') + ' según convenio';
      infoDias.className = 'text-muted small mt-1 d-block';
    } else {
      inputFin.removeAttribute('max');
      inputFin.removeAttribute('min');
      infoDias.textContent = '';
      infoDias.className = 'd-none';
    }
  }

  if (selMotivo) {
    selMotivo.addEventListener("change", aplicarLimiteDias);
  }

  if (inputInicio) {
    inputInicio.addEventListener("change", aplicarLimiteDias);
  }

  // Bloquear manualmente escribir una fecha fin mayor al límite
  if (inputFin) {
    inputFin.addEventListener("change", function () {
      if (this.max && this.value > this.max) {
        this.value = this.max;
      }
    });
  }

  // ── CREAR NO REMUNERADA ──────────────────────────────────────────
  var formAusencia = document.getElementById("formAusencia");
  if (formAusencia) {
    formAusencia.onsubmit = function (e) {
      e.preventDefault();
      fetch(RUTA_URL + "/Ausencias/crearNoRemunerada", {
        method: "POST",
        body: new FormData(this)
      })
      .then(function (r) { return r.json(); })
      .then(function () { location.reload(); });
    };
  }

  // ── ELIMINAR NO REMUNERADA ───────────────────────────────────────
  document.querySelectorAll(".btnEliminar").forEach(function (btn) {
    btn.onclick = function () {
      if (!confirm("¿Eliminar ausencia?")) return;
      fetch(RUTA_URL + "/Ausencias/eliminarNoRemunerada", {
        method: "POST",
        body: new URLSearchParams({ id: this.dataset.id })
      })
      .then(function (r) { return r.json(); })
      .then(function () { location.reload(); });
    };
  });

  // ── EDITAR NO REMUNERADA ─────────────────────────────────────────
  var modalEl = document.getElementById("modalEditarAusencia");
  var bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;

  document.querySelectorAll(".btnEditarAusencia").forEach(function (btn) {
    btn.onclick = function () {
      document.getElementById("editId").value      = this.dataset.id;
      document.getElementById("editUsuario").value = this.dataset.usuario;
      document.getElementById("editMotivo").value  = this.dataset.motivo;
      document.getElementById("editInicio").value  = this.dataset.inicio;
      document.getElementById("editFin").value     = this.dataset.fin;
      document.getElementById("editFeedback").className = "d-none mb-3";
      if (bsModal) bsModal.show();
    };
  });

  var formEditarAusencia = document.getElementById("formEditarAusencia");
  if (formEditarAusencia) {
    formEditarAusencia.onsubmit = function (e) {
      e.preventDefault();
      var feedback  = document.getElementById("editFeedback");
      var submitBtn = this.querySelector('[type="submit"]');
      submitBtn.disabled    = true;
      submitBtn.textContent = "Guardando…";

      fetch(RUTA_URL + "/Ausencias/editarNoRemunerada", {
        method: "POST",
        body: new FormData(this)
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.ok) {
          location.reload();
        } else {
          feedback.className    = "alert alert-danger mb-3";
          feedback.textContent  = "Error al guardar los cambios";
          submitBtn.disabled    = false;
          submitBtn.textContent = "Guardar cambios";
        }
      })
      .catch(function () {
        feedback.className    = "alert alert-danger mb-3";
        feedback.textContent  = "Error de red";
        submitBtn.disabled    = false;
        submitBtn.textContent = "Guardar cambios";
      });
    };
  }

});
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>