<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?> 

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

<!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar bajas...">

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
  <h5>Gestión de Bajas</h5>

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
      No hay bajas registradas.
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
          <td><?= $baja['usuario'] ?></td>
          <td><?= $baja['motivo'] ?></td>
          <td><?= $baja['fecha_inicio'] ?></td>
          <td><?= $baja['fecha_fin'] ?? '-' ?></td>

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

          <td>
            <?php if ($baja['estado'] == 'pendiente'): ?>
    
              <a href="<?= RUTA_URL ?>/Bajas/cambiarEstado/<?= $baja['id'] ?>/aprobada"
               class="btn btn-success btn-sm">
               Aprobar
             </a>

           <a href="<?= RUTA_URL ?>/Bajas/cambiarEstado/<?= $baja['id'] ?>/rechazada"
            class="btn btn-danger btn-sm">
            Rechazar
             </a>

            <?php else: ?>
              <span class="text-muted">Sin acciones</span>
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


          <script>
document.addEventListener('DOMContentLoaded', () => {
    document.dispatchEvent(new Event('tableReady'));
});
</script>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtroBajas.js"></script>


<?php require_once RUTA_APP . '/views/inc/footer.php' ?>