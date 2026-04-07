<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

 <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar jornadas...">

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

 

  <!-- TABLA -->
  <div class="card card-custom p-3">
    <h5>Listado de Jornadas</h5>

    <div class="table-responsive">
      <table class="table table-bordered table-striped mt-3">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre Usuario</th>
            <th>Horas/día</th>
            <th>Horas/semana</th>
            <th>Fecha inicio</th>
            <th>Fecha fin</th>
          </tr>
        </thead>
        <tbody>

        <?php if (empty($datos['jornadas'])): ?>
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No hay jornadas registradas
                </td>
            </tr>
        <?php else: ?>

            <?php foreach ($datos['jornadas'] as $index => $j): ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $j['nombre_usuario'] ?></td>
                    <td><?= $j['horas_dia'] ?> h</td>
                    <td><?= $j['horas_semana'] ?> h</td>
                    <td><?= date('d/m/Y', strtotime($j['fecha_inicio'])) ?></td>
                    <td>
                        <?= $j['fecha_fin'] 
                            ? date('d/m/Y', strtotime($j['fecha_fin'])) 
                            : '<span class="text-muted">-</span>' ?>
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

<script>
const RUTA_URL = "<?= RUTA_URL ?>";
</script>


<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureJornadas.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>