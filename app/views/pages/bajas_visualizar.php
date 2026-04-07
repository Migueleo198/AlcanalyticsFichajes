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
      <i class="bi bi-person-circle fs-5 profile"></i>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div class="container mt-4">
    <h3><?= $datos['title'] ?></h3>

    <?php if (empty($datos['bajas'])): ?>

      <div class="alert alert-info mt-3">
        No tienes bajas registradas.
      </div>

    <?php else: ?>

      <div class="card card-custom p-3">

        <div class="d-flex justify-content-between mb-3">
          <h5>Listado de Bajas</h5>
          <button class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-funnel"></i> Filtrar
          </button> 
        </div>

        <table class="table align-middle">
          <thead>
            <tr>
              <th>Motivo</th>
              <th>Fecha inicio</th>
              <th>Fecha fin</th>
              <th>Estado</th>
              <th>Solicitada</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach ($datos['bajas'] as $baja): ?>

            <tr>
              <td><?= $baja['motivo'] ?></td>
              <td><?= $baja['fecha_inicio'] ?></td>
              <td><?= $baja['fecha_fin'] ? $baja['fecha_fin'] : '-' ?></td>

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

              <td><?= $baja['fecha_solicitud'] ?></td>
            </tr>

          <?php endforeach; ?>

          </tbody>
        </table>

      </div>

    <?php endif; ?>

  </div>

</div>
</div>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>