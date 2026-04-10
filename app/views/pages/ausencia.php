<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

    <h3 class="mb-3">Gestión de Ausencias</h3>

    <!-- FORMULARIO -->
    <div class="card p-3 mb-4">

        <form id="formAusencia">

            <div class="row">

                <div class="col-md-3">
                    <label>Usuario</label>
                   <select name="id_usuario" class="form-control" required>
                    <option value="">Selecciona usuario</option>
    
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
                        <?php foreach ($datos['motivos'] as $m): ?>
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

            <button class="btn btn-primary mt-3">Crear ausencia</button>

        </form>

    </div>

    <!-- LISTADO -->
    <div class="card p-3">

        <table class="table table-striped">
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

</div>

</div>

<script>
const RUTA_URL = "<?= RUTA_URL ?>";

// CREAR
document.getElementById("formAusencia").onsubmit = function(e) {
    e.preventDefault();

    fetch(RUTA_URL + "/Ausencias/crear", {
        method: "POST",
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(() => location.reload());
};

// ELIMINAR
document.querySelectorAll(".btnEliminar").forEach(btn => {
    btn.onclick = function() {

        if (!confirm("¿Eliminar ausencia?")) return;

        fetch(RUTA_URL + "/Ausencias/eliminar", {
            method: "POST",
            body: new URLSearchParams({ id: this.dataset.id })
        })
        .then(r => r.json())
        .then(() => location.reload());
    };
});
</script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>