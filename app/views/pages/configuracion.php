<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>Configuración</h5>
    </div>
    <div class="d-flex align-items-center">
      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= htmlspecialchars($_SESSION['id_usuario'] ?? '') ?>"></i>
    </div>
  </div>

  <?php
  $tab     = $datos['tab']     ?? 'cuenta';
  $ok      = $datos['ok']      ?? null;
  $err     = $datos['error']   ?? null;
  $perfil  = $datos['perfil']  ?? [];
  $config  = $datos['config']  ?? [];
  $tipos   = $datos['tipos']   ?? [];
  $esAdmin = $datos['esAdmin'] ?? false;

  $mensajes_ok = [
    'perfil'     => 'Perfil actualizado correctamente.',
    'password'   => 'Contraseña cambiada correctamente.',
    'sistema'    => 'Configuración del sistema guardada.',
    'tipo_add'   => 'Tipo de tarea añadido.',
    'tipo_edit'  => 'Tipo de tarea actualizado.',
    'tipo_del'   => 'Tipo de tarea eliminado.',
  ];
  $mensajes_err = [
    'perfil'          => 'Error al guardar el perfil.',
    'password'        => 'Error al cambiar la contraseña.',
    'campos_vacios'   => 'Rellena todos los campos de contraseña.',
    'no_coinciden'    => 'La nueva contraseña y la confirmación no coinciden.',
    'muy_corta'       => 'La contraseña debe tener al menos 6 caracteres.',
    'pass_incorrecta' => 'La contraseña actual es incorrecta.',
    'nombre_vacio'    => 'El nombre del tipo no puede estar vacío.',
    'datos_invalidos' => 'Datos inválidos.',
  ];
  ?>

  <?php if ($ok && isset($mensajes_ok[$ok])): ?>
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= $mensajes_ok[$ok] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if ($err && isset($mensajes_err[$err])): ?>
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $mensajes_err[$err] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- TABS -->
  <div class="cfg-tabs mb-4">
    <a href="<?= RUTA_URL ?>/Configuracion/index?tab=cuenta"
       class="cfg-tab <?= $tab === 'cuenta' ? 'active' : '' ?>">
      <i class="bi bi-person-fill me-1"></i>Mi cuenta
    </a>
    <?php if ($esAdmin): ?>
    <a href="<?= RUTA_URL ?>/Configuracion/index?tab=sistema"
       class="cfg-tab <?= $tab === 'sistema' ? 'active' : '' ?>">
      <i class="bi bi-sliders me-1"></i>Sistema
    </a>
    <a href="<?= RUTA_URL ?>/Configuracion/index?tab=tipos"
       class="cfg-tab <?= $tab === 'tipos' ? 'active' : '' ?>">
      <i class="bi bi-tags me-1"></i>Tipos de tarea
    </a>
    <?php endif; ?>
  </div>

  <!-- ============================================================ -->
  <!-- TAB: MI CUENTA -->
  <!-- ============================================================ -->
  <?php if ($tab === 'cuenta'): ?>

  <div class="row g-4">

    <!-- PERFIL -->
    <div class="col-lg-6">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-person-vcard me-2 text-primary"></i>Información personal
        </div>
        <div class="cfg-card-body">
          <form method="POST" action="<?= RUTA_URL ?>/Configuracion/actualizarPerfil" data-validate>

            <div class="row g-3">
              <div class="col-6">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>" required>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Apellidos</label>
                <input type="text" name="apellidos" class="form-control"
                       value="<?= htmlspecialchars($perfil['apellidos'] ?? '') ?>" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($perfil['email'] ?? '') ?>" required>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                       value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>">
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Fecha nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control"
                       value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '') ?>">
              </div>
            </div>

            <!-- Read-only fields -->
            <div class="row g-3 mt-1">
              <div class="col-6">
                <label class="form-label fw-semibold text-muted">Usuario</label>
                <input type="text" class="form-control bg-light"
                       value="<?= htmlspecialchars($perfil['nombre_usuario'] ?? '') ?>" disabled>
                <div class="form-text">No se puede cambiar</div>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold text-muted">Rol</label>
                <input type="text" class="form-control bg-light"
                       value="<?= htmlspecialchars($perfil['rol'] ?? '') ?>" disabled>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i>Guardar cambios
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- CONTRASEÑA -->
    <div class="col-lg-6">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-shield-lock me-2 text-warning"></i>Cambiar contraseña
        </div>
        <div class="cfg-card-body">
          <form method="POST" action="<?= RUTA_URL ?>/Configuracion/cambiarPassword" data-validate>

            <div class="mb-3">
              <label class="form-label fw-semibold">Contraseña actual</label>
              <div class="input-group">
                <input type="password" name="password_actual" id="passActual"
                       class="form-control" required minlength="1" autocomplete="current-password">
                <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="passActual">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Nueva contraseña</label>
              <div class="input-group">
                <input type="password" name="password_nueva" id="passNueva"
                       class="form-control" required autocomplete="new-password" minlength="6" data-no-valid>
                <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="passNueva">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div class="form-text">Mínimo 6 caracteres</div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Confirmar contraseña</label>
              <div class="input-group">
                <input type="password" name="password_confirma" id="passConfirma"
                       class="form-control" required autocomplete="new-password"
                       data-match="passNueva" data-no-valid>
                <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="passConfirma">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Strength indicator -->
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Fortaleza de la contraseña</small>
                <small id="strengthLabel" class="text-muted">—</small>
              </div>
              <div class="progress" style="height:5px;">
                <div id="strengthBar" class="progress-bar" style="width:0%"></div>
              </div>
            </div>

            <button type="submit" class="btn btn-warning px-4">
              <i class="bi bi-lock me-1"></i>Cambiar contraseña
            </button>

          </form>
        </div>
      </div>

      <!-- INFO BADGE -->
      <div class="cfg-card mt-4">
        <div class="cfg-card-header">
          <i class="bi bi-info-circle me-2 text-info"></i>Información de sesión
        </div>
        <div class="cfg-card-body">
          <div class="d-flex flex-column gap-2">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Usuario</span>
              <span class="fw-semibold"><?= htmlspecialchars($perfil['nombre_usuario'] ?? '') ?></span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Rol</span>
              <span class="badge <?= $perfil['rol'] === 'Administrador' ? 'bg-danger' : 'bg-primary' ?>">
                <?= htmlspecialchars($perfil['rol'] ?? '') ?>
              </span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">DNI</span>
              <span class="fw-semibold"><?= htmlspecialchars($perfil['dni'] ?? '') ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php endif; ?>

  <!-- ============================================================ -->
  <!-- TAB: SISTEMA (admin only) -->
  <!-- ============================================================ -->
  <?php if ($tab === 'sistema' && $esAdmin): ?>

  <form method="POST" action="<?= RUTA_URL ?>/Configuracion/guardarSistema">
  <div class="row g-4">

    <!-- EMPRESA -->
    <div class="col-lg-6">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-building me-2 text-primary"></i>Empresa
        </div>
        <div class="cfg-card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nombre de la empresa</label>
            <input type="text" name="nombre_empresa" class="form-control"
                   value="<?= htmlspecialchars($config['nombre_empresa'] ?? 'Alcanalytics') ?>" required>
            <div class="form-text">Aparecerá en informes y emails.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- JORNADA -->
    <div class="col-lg-6">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-clock me-2 text-success"></i>Jornada laboral
        </div>
        <div class="cfg-card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Hora oficial de inicio</label>
              <input type="time" name="hora_inicio_jornada" class="form-control"
                     value="<?= htmlspecialchars($config['hora_inicio_jornada'] ?? '09:00') ?>" required>
              <div class="form-text">Hora a partir de la cual se calcula si hay retraso.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">
                Umbral de retraso
                <span class="badge bg-warning text-dark ms-1"><?= (int)($config['umbral_retraso_minutos'] ?? 30) ?> min</span>
              </label>
              <input type="range" name="umbral_retraso_minutos" class="form-range"
                     min="0" max="60" step="5"
                     value="<?= (int)($config['umbral_retraso_minutos'] ?? 30) ?>"
                     id="umbralRange"
                     oninput="document.getElementById('umbralVal').textContent = this.value + ' min';
                              this.previousElementSibling.querySelector('.badge').textContent = this.value + ' min'">
              <div class="d-flex justify-content-between">
                <small class="text-muted">0 min (puntual)</small>
                <small id="umbralVal" class="text-muted"><?= (int)($config['umbral_retraso_minutos'] ?? 30) ?> min</small>
                <small class="text-muted">60 min</small>
              </div>
              <div class="form-text">Minutos de margen desde la hora de inicio antes de marcar el fichaje como retraso.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- JORNADA DEFECTO -->
    <div class="col-lg-6">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-calendar-check me-2 text-indigo"></i>Valores por defecto al asignar jornadas
        </div>
        <div class="cfg-card-body">
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Horas/día</label>
              <input type="number" name="horas_jornada_defecto" class="form-control"
                     min="0" max="24" step="0.5"
                     value="<?= htmlspecialchars($config['horas_jornada_defecto'] ?? '7.5') ?>" required>
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Horas/semana</label>
              <input type="number" name="horas_semana_defecto" class="form-control"
                     min="0" max="168" step="0.5"
                     value="<?= htmlspecialchars($config['horas_semana_defecto'] ?? '37.5') ?>" required>
            </div>
          </div>
          <div class="form-text mt-2">Estos valores se usan como sugerencia al crear una jornada nueva.</div>
        </div>
      </div>
    </div>

    <!-- RETRASO INFO -->
    <div class="col-lg-6">
      <div class="cfg-card h-100">
        <div class="cfg-card-header">
          <i class="bi bi-info-circle me-2 text-info"></i>Cómo se calculan los retrasos
        </div>
        <div class="cfg-card-body">
          <p class="text-muted small mb-3">
            Un fichaje se considera <strong>retraso</strong> cuando la hora de entrada supera la
            <em>hora de inicio + umbral</em> y se trata de un fichaje matutino.
          </p>
          <?php
          $horaInicio = $config['hora_inicio_jornada'] ?? '09:00';
          $umbral     = (int)($config['umbral_retraso_minutos'] ?? 30);
          $limite     = date('H:i', strtotime($horaInicio . ' + ' . $umbral . ' minutes'));
          ?>
          <div class="cfg-formula">
            <div class="cfg-formula-row">
              <span>Hora inicio</span><strong><?= $horaInicio ?></strong>
            </div>
            <div class="cfg-formula-row">
              <span>Umbral</span><strong>+ <?= $umbral ?> min</strong>
            </div>
            <div class="cfg-formula-row border-top pt-2 mt-2">
              <span>Límite puntualidad</span>
              <strong class="text-danger"><?= $limite ?></strong>
            </div>
          </div>
          <p class="text-muted small mt-3 mb-0">
            Los fichajes antes de las <?= $limite ?> no se marcan como retraso.
          </p>
        </div>
      </div>
    </div>

  </div>

  <div class="mt-4">
    <button type="submit" class="btn btn-primary px-5">
      <i class="bi bi-save me-1"></i>Guardar configuración del sistema
    </button>
  </div>

  </form>

  <?php endif; ?>

  <!-- ============================================================ -->
  <!-- TAB: TIPOS DE TAREA (admin only) -->
  <!-- ============================================================ -->
  <?php if ($tab === 'tipos' && $esAdmin): ?>

  <div class="row g-4">

    <!-- AÑADIR TIPO -->
    <div class="col-lg-4">
      <div class="cfg-card">
        <div class="cfg-card-header">
          <i class="bi bi-plus-circle me-2 text-success"></i>Nuevo tipo de tarea
        </div>
        <div class="cfg-card-body">
          <form method="POST" action="<?= RUTA_URL ?>/Configuracion/addTipo">
            <div class="mb-3">
              <label class="form-label fw-semibold">Nombre</label>
              <input type="text" name="nombre" class="form-control"
                     placeholder="Ej: Desarrollo, Reunión, Formación…" required maxlength="100">
            </div>
            <button type="submit" class="btn btn-success w-100">
              <i class="bi bi-plus me-1"></i>Añadir tipo
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- LISTADO TIPOS -->
    <div class="col-lg-8">
      <div class="cfg-card">
        <div class="cfg-card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-list-task me-2 text-primary"></i>Tipos registrados</span>
          <span class="badge bg-light text-dark border"><?= count($tipos) ?> tipo<?= count($tipos) !== 1 ? 's' : '' ?></span>
        </div>
        <div class="cfg-card-body p-0">
          <?php if (empty($tipos)): ?>
          <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            No hay tipos de tarea creados todavía.
          </div>
          <?php else: ?>
          <table class="table cfg-table mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tipos as $i => $t): ?>
              <tr>
                <td class="text-muted"><?= $i + 1 ?></td>
                <td>
                  <!-- Inline edit -->
                  <form method="POST" action="<?= RUTA_URL ?>/Configuracion/editTipo"
                        class="d-flex gap-2 align-items-center" id="formTipo<?= $t['id_tipo'] ?>">
                    <input type="hidden" name="id" value="<?= $t['id_tipo'] ?>">
                    <input type="text" name="nombre" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($t['nombre']) ?>" required maxlength="100"
                           id="inputTipo<?= $t['id_tipo'] ?>">
                  </form>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-1 justify-content-end">
                    <button type="submit" form="formTipo<?= $t['id_tipo'] ?>"
                            class="btn btn-outline-primary btn-sm" title="Guardar">
                      <i class="bi bi-save"></i>
                    </button>
                    <a href="<?= RUTA_URL ?>/Configuracion/deleteTipo/<?= $t['id_tipo'] ?>"
                       class="btn btn-outline-danger btn-sm" title="Eliminar"
                       onclick="return confirm('¿Eliminar el tipo «<?= htmlspecialchars($t['nombre']) ?>»?\nLas tareas asociadas perderán su tipo.')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
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

  <?php endif; ?>

</div>
</div>

<script>
// Toggle visibilidad contraseñas
document.querySelectorAll('.btn-toggle-pass').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.target);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'bi bi-eye-slash';
    } else {
      input.type = 'password';
      icon.className = 'bi bi-eye';
    }
  });
});

// Medidor de fortaleza de contraseña
const passNueva  = document.getElementById('passNueva');
const strengthBar = document.getElementById('strengthBar');
const strengthLabel = document.getElementById('strengthLabel');

if (passNueva) {
  passNueva.addEventListener('input', () => {
    const val = passNueva.value;
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
      { pct: 0,   cls: '',            label: '—' },
      { pct: 20,  cls: 'bg-danger',   label: 'Muy débil' },
      { pct: 40,  cls: 'bg-warning',  label: 'Débil' },
      { pct: 60,  cls: 'bg-info',     label: 'Aceptable' },
      { pct: 80,  cls: 'bg-primary',  label: 'Fuerte' },
      { pct: 100, cls: 'bg-success',  label: 'Muy fuerte' },
    ];
    const l = levels[Math.min(score, 5)];
    strengthBar.style.width = l.pct + '%';
    strengthBar.className = 'progress-bar ' + l.cls;
    strengthLabel.textContent = l.label;
    strengthLabel.className = l.cls.replace('bg-', 'text-') || 'text-muted';
  });
}
</script>

<style>
/* === TABS === */
.cfg-tabs{display:flex;gap:4px;border-bottom:2px solid #e5e7eb;padding-bottom:0;}
.cfg-tab{display:flex;align-items:center;padding:10px 18px;border-radius:8px 8px 0 0;font-weight:500;font-size:.88rem;color:#374151;text-decoration:none;border:1px solid transparent;border-bottom:none;transition:background .15s;}
.cfg-tab:hover{background:#f3f4f6;color:#111827;}
.cfg-tab.active{background:#fff;color:#2563eb;border-color:#e5e7eb;border-bottom-color:#fff;margin-bottom:-2px;font-weight:600;}

/* === CARDS === */
.cfg-card{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;height:100%;}
.cfg-card-header{padding:14px 20px;background:#f8fafc;border-bottom:1px solid #e5e7eb;font-weight:600;font-size:.88rem;color:#374151;}
.cfg-card-body{padding:20px;}

/* === TABLE === */
.cfg-table thead th{background:#f1f5f9;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;border-bottom:2px solid #e2e8f0;padding:10px 16px;}
.cfg-table tbody td{padding:10px 16px;vertical-align:middle;border-bottom:1px solid #f1f5f9;}
.cfg-table tbody tr:last-child td{border-bottom:none;}
.cfg-table tbody tr:hover{background:#f8fafc;}

/* === FORMULA === */
.cfg-formula{background:#f8fafc;border-radius:8px;padding:14px 16px;font-size:.88rem;}
.cfg-formula-row{display:flex;justify-content:space-between;margin-bottom:4px;}
.cfg-formula-row:last-child{margin-bottom:0;}

/* === COLOR === */
.text-indigo{color:#4f46e5;}
</style>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
