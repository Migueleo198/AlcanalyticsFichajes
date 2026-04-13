<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

<h3 class="mb-4">Detalle de Fichajes</h3>

<input id="buscadorTabla" class="form-control mb-3" placeholder="Buscar fichajes...">

<div class="card p-3">

<table class="table table-striped">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Estado</th>
            <th>Descansos</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($fichajes as $f): ?>
            <tr>
                <td><?= $f['fecha'] ?></td>
                <td>
                    <?= $f['nombre'] ?? '' ?> <?= $f['apellidos'] ?? '' ?>
                </td>
                <td><?= $f['hora_entrada'] ?></td>
                <td><?= $f['hora_salida'] ?? 'En curso' ?></td>
                <td><?= $f['estado'] ?></td>
                <td>
                    <?php if (!empty($f['descansos'])): ?>
                        <?php foreach ($f['descansos'] as $d): ?>
                            <div>
                                <?= $d['hora_inicio'] ?> → <?= $d['hora_fin'] ?? 'En curso' ?>
                                (<?= $d['motivo'] ?>)
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        Sin pausas
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>

</div>

</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.dispatchEvent(new Event('tableReady'));
});
</script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructurePaginacion.js"></script>
<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>