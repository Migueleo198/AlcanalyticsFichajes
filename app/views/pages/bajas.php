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

  <div class="container mt-4">
    <h3><?= $datos['title'] ?></h3>

    <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success">
        Ausencia solicitada correctamente
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= RUTA_URL ?>/Bajas/solicitar">

        <div class="mb-3">
            <label class="form-label">Motivo de la ausencia</label>

            <select name="id_motivo" class="form-select" required>
                <option value="">-- Selecciona un motivo --</option>

                <?php foreach ($datos['motivos'] as $motivo): ?>
                    <option value="<?= $motivo['id_motivo'] ?>">
                        <?= $motivo['nombre'] ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">
            Solicitar ausencia
        </button>

    </form>
</div>
</div>
</div>


<script>
var RUTA_URL = "<?= RUTA_URL ?>";
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>