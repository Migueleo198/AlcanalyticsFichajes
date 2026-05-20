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
          <select name="id_motivo" class="form-select" required>
            <option value="">Selecciona motivo</option>

            <!-- ✅ CAMBIADO array -->
            <?php foreach ($datos['motivos_remunerados'] as $motivo): ?>
              <option value="<?= $motivo['id_motivo'] ?>">
                <?= $motivo['nombre'] ?>
              </option>
            <?php endforeach; ?>

          </select>
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
          <select name="id_motivo" class="form-control" required>
            <option value="">Selecciona</option>

            <?php foreach ($datos['motivos_no_remunerados'] as $m): ?>
              <option value="<?= $m['id_motivo'] ?>">
                <?= $m['nombre'] ?>
              </option>
            <?php endforeach; ?>

          </select>
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
            <td><?= $a['usuario'] ?></td>
            <td><?= $a['motivo'] ?></td>
            <td><?= $a['fecha_inicio'] ?></td>
            <td><?= $a['fecha_fin'] ?></td>
            <td>
              <button class="btn btn-danger btn-sm btnEliminar"
                data-id="<?= $a['id'] ?>">
                Borrar
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>


<script>
var RUTA_URL = "<?= RUTA_URL ?>";

// ✅ CREAR NO REMUNERADA (endpoint nuevo)
document.getElementById("formAusencia").onsubmit = function(e) {
  e.preventDefault();

  fetch(RUTA_URL + "/Ausencias/crearNoRemunerada", {
    method: "POST",
    body: new FormData(this)
  })
  .then(r => r.json())
  .then(() => location.reload());
};

// ✅ ELIMINAR (endpoint nuevo)
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
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>