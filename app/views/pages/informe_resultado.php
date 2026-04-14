<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

<?php
// =========================
// FIX: accept BOTH formats (controller mismatch safe)
// =========================

// if controller sends raw array -> use it directly
// if controller sends ["datos"=>...] -> fallback
$filas = $datos['datos'] ?? $datos ?? [];

$desde = $datos['desde'] ?? ($desde ?? '');
$hasta = $datos['hasta'] ?? ($hasta ?? '');

$totalHoras = 0;
$totalExtras = 0;

// PDF URL SAFE
$pdfUrl = RUTA_URL . "/informes/generarPDF?desde=" . urlencode($desde) . "&hasta=" . urlencode($hasta);
?>

<!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">
  <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar un informea...">

  <div class="d-flex align-items-center">

    <div class="dropdown me-3 position-relative">
      <i class="bi bi-bell fs-5" id="notificacionesIcon" data-bs-toggle="dropdown" style="cursor:pointer;"></i>

      <span id="contadorNotificaciones"
            class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
        3
      </span>

      <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;" id="listaNotificaciones">
        <li class="fw-bold mb-2">Notificaciones</li>
        <li class="dropdown-item">Nuevo fichaje registrado</li>
        <li class="dropdown-item">Contrato por vencer</li>
        <li class="dropdown-item">Actualización completada</li>
      </ul>
    </div>

    <i class="bi bi-person-circle fs-5 profile"
       data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>

  </div>
</div>

<!-- HEADER -->
<h3>Informe de Fichajes</h3>

<p class="text-muted">
    Desde <strong><?= htmlspecialchars($desde) ?></strong>
    hasta <strong><?= htmlspecialchars($hasta) ?></strong>
</p>

<!-- CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 cardhov">
            <h6>Total registros</h6>
            <h3><?= count($filas) ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-3 cardhov">
            <h6>Horas ordinarias</h6>
            <h3><?= $totalHoras ?>h</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-3 cardhov">
            <h6>Horas extra</h6>
            <h3><?= $totalExtras ?>h</h3>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card card-custom p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Detalle de fichajes</h5>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i>
            </button>

            <a href="<?= $pdfUrl ?>"
               class="btn btn-danger btn-sm"
               target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Empleado</th>
                    <th>Fecha</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Horas</th>
                    <th>Extra</th>
                    <th>Descanso</th>
                    <th>Estado</th>
                    <th>Incidencia</th>
                </tr>
            </thead>

            <tbody>

            <?php if (empty($filas)): ?>
                <tr>
                    <td colspan="9" class="text-center">No hay datos</td>
                </tr>
            <?php else: ?>

                <?php foreach ($filas as $fila): ?>

                    <?php
                        $horas = $fila['horas_ordinarias'] ?? 0;
                        $extras = $fila['horas_extra'] ?? 0;

                        $totalHoras += $horas;
                        $totalExtras += $extras;
                    ?>

                    <tr>
                        <td><?= ($fila['nombre'] ?? '') . ' ' . ($fila['apellidos'] ?? '') ?></td>
                        <td><?= $fila['fecha'] ?? '' ?></td>
                        <td><?= $fila['hora_entrada'] ?? '' ?></td>
                        <td><?= $fila['hora_salida'] ?? '' ?></td>
                        <td><?= $horas ?> h</td>
                        <td><?= $extras ?> h</td>
                        <td><?= round($fila['horas_descanso'] ?? 0, 2) ?> h</td>

                        <td>
                            <?php if (($fila['estado'] ?? '') === 'cerrado'): ?>
                                <span class="badge bg-success">Cerrado</span>
                            <?php elseif (($fila['estado'] ?? '') === 'abierto'): ?>
                                <span class="badge bg-warning">Abierto</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $fila['estado'] ?? '' ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= !empty($fila['incidencia'])
                                ? $fila['incidencia']
                                : '<span class="text-muted">-</span>' ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- TOTALS -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card card-custom p-3">
            <h6>Total horas ordinarias</h6>
            <h4><?= $totalHoras ?> h</h4>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-custom p-3">
            <h6>Total horas extra</h6>
            <h4><?= $totalExtras ?> h</h4>
        </div>
    </div>
</div>

</div>
</div>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>