<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="main-wrapper container-fluid">
<div class="content">

<!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">
    <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar jornadas...">

    <div class="d-flex align-items-center">
        <i class="bi bi-person-circle fs-5 profile"></i>
    </div>
</div>

<!-- CARD -->
<div class="card shadow-sm border-0 p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Gestión de Jornadas</h5>

        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus"></i> Nueva jornada
        </button>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Empleado</th>
                    <th>Horas/día</th>
                    <th>Horas/semana</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>

            <tbody id="listaJornadas">
                <!-- JS RENDER -->
            </tbody>

        </table>
    </div>

</div>
</div>
</div>
</div>

<!-- =========================
MODAL CREAR
========================= -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Nueva jornada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- ❌ NO ACTION, NO REDIRECT -->
            <form id="formAddJornada">

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="id_usuario" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($datos['usuarios'] as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>">
                                    <?= $u['nombre'] . ' ' . $u['apellidos'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Horas día</label>
                        <input type="number" step="0.1" name="horas_dia" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Horas semana</label>
                        <input type="number" step="0.1" name="horas_semana" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================
MODAL EDIT
========================= -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar jornada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- ❌ NO ACTION -->
            <form id="formEditJornada">

                <input type="hidden" name="id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Empleado</label>
                        <select name="id_usuario" class="form-select">
                            <?php foreach ($datos['usuarios'] as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>">
                                    <?= $u['nombre'] . ' ' . $u['apellidos'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Horas día</label>
                        <input type="number" step="0.1" name="horas_dia" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Horas semana</label>
                        <input type="number" step="0.1" name="horas_semana" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Actualizar</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================
MODAL DELETE
========================= -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Eliminar jornada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- ❌ NO ACTION -->
            <form id="formDeleteJornada">

                <input type="hidden" name="id">

                <div class="modal-body">
                    <p>¿Seguro que quieres eliminar esta jornada?</p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger" type="submit">Eliminar</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================
GLOBAL JS VARS
========================= -->
<script>
  const USER_ROL = "<?= $_SESSION['rol'] ?? '' ?>";
  const USER_ID = "<?= $_SESSION['id_usuario'] ?? '' ?>";
  const RUTA_URL = "<?= RUTA_URL ?>";
</script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>

<!-- =========================
JS (FIXED PATH IMPORTANT)
========================= -->
<script src="<?= RUTA_URL ?>/js/infrastructure/infrastructureAsignJornadas.js"></script>
