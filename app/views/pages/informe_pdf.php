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
// SAFE INIT (ROBUSTO)
// =========================
$filas = $filas ?? $datos ?? [];

$desde = $desde ?? '';
$hasta = $hasta ?? '';

$totalHoras = 0;
$totalExtras = 0;
?>

<!-- HEADER -->
<div class="header">
    <h2>Informe de Fichajes</h2>
</div>

<div class="info">
    <strong>Desde:</strong> <?= htmlspecialchars($desde) ?> |
    <strong>Hasta:</strong> <?= htmlspecialchars($hasta) ?> |
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
$horas = (float)($fila['horas_ordinarias'] ?? 0);
$extras = (float)($fila['horas_extra'] ?? 0);
$descanso = (float)($fila['horas_descanso'] ?? 0);

$totalHoras += $horas;
$totalExtras += $extras;
?>

<tr>
    <td><?= htmlspecialchars(($fila['nombre'] ?? '') . ' ' . ($fila['apellidos'] ?? '')) ?></td>
    <td><?= htmlspecialchars($fila['dni'] ?? '') ?></td>
    <td><?= htmlspecialchars($fila['fecha'] ?? '') ?></td>
    <td><?= htmlspecialchars($fila['hora_entrada'] ?? '') ?></td>
    <td><?= htmlspecialchars($fila['hora_salida'] ?? '') ?></td>

    <td><?= $horas ?>h</td>
    <td><?= $extras ?>h</td>
    <td><?= round($descanso, 2) ?>h</td>

    <td>
        <?php
        $estado = $fila['estado'] ?? '';

        if ($estado === 'cerrado') {
            echo '<span class="badge success">Cerrado</span>';
        } elseif ($estado === 'abierto') {
            echo '<span class="badge warning">Abierto</span>';
        } else {
            echo '<span class="badge danger">' . htmlspecialchars($estado) . '</span>';
        }
        ?>
    </td>

    <td>
        <?= !empty($fila['incidencia'])
            ? htmlspecialchars($fila['incidencia'])
            : '-' ?>
    </td>
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