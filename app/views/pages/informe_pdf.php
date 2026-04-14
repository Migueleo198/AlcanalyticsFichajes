<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
body {
    font-family: Arial, sans-serif;
    font-size: 11px;
}

.header {
    text-align: center;
    margin-bottom: 10px;
}

.header h2 {
    margin: 0;
}

.info {
    margin-bottom: 10px;
    font-size: 12px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #000;
    padding: 4px;
    text-align: center;
}

th {
    background-color: #eee;
}

.badge {
    padding: 2px 6px;
    font-size: 10px;
}

.success { background: #28a745; color: #fff; }
.warning { background: #ffc107; }
.danger { background: #dc3545; color: #fff; }

.footer {
    margin-top: 10px;
    font-size: 11px;
}
</style>

</head>
<body>

<?php
// =========================
// SAFE INIT
// =========================
$filas = $filas ?? [];

$totalHoras = 0;
$totalExtras = 0;
?>

<!-- HEADER -->
<div class="header">
    <h2>Informe de Fichajes</h2>
</div>

<div class="info">
    <strong>Desde:</strong> <?= $desde ?? '' ?> |
    <strong>Hasta:</strong> <?= $hasta ?? '' ?> |
    <strong>Generado:</strong> <?= date('d/m/Y H:i') ?>
</div>

<!-- TABLE -->
<table>
<thead>
<tr>
    <th>Empleado</th>
    <th>DNI</th>
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
    <td colspan="10">No hay datos</td>
</tr>
<?php else: ?>

<?php foreach ($filas as $fila): ?>

<?php
$horas = $fila['horas_ordinarias'] ?? 0;
$extras = $fila['horas_extra'] ?? 0;
$descanso = $fila['horas_descanso'] ?? 0;

$totalHoras += $horas;
$totalExtras += $extras;
?>

<tr>
    <td><?= ($fila['nombre'] ?? '') . ' ' . ($fila['apellidos'] ?? '') ?></td>
    <td><?= $fila['dni'] ?? '' ?></td>
    <td><?= $fila['fecha'] ?? '' ?></td>
    <td><?= $fila['hora_entrada'] ?? '' ?></td>
    <td><?= $fila['hora_salida'] ?? '' ?></td>

    <td><?= $horas ?>h</td>
    <td><?= $extras ?>h</td>

    <td><?= round((float)$descanso, 2) ?>h</td>

    <td>
        <?php if (($fila['estado'] ?? '') == 'cerrado'): ?>
            <span class="badge success">Cerrado</span>
        <?php elseif (($fila['estado'] ?? '') == 'abierto'): ?>
            <span class="badge warning">Abierto</span>
        <?php else: ?>
            <span class="badge danger"><?= $fila['estado'] ?? '' ?></span>
        <?php endif; ?>
    </td>

    <td><?= !empty($fila['incidencia']) ? $fila['incidencia'] : '-' ?></td>
</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>
</table>

<!-- FOOTER -->
<div class="footer">
    <strong>Total horas:</strong> <?= $totalHoras ?> h |
    <strong>Total extra:</strong> <?= $totalExtras ?> h
</div>

</body>
</html>