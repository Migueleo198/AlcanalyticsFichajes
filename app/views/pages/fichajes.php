<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

  <div class="content text-center">

    <a href="<?= RUTA_URL ?>/home/index" class="btn btn-outline-black text-light bg-primary mb-3">
      ← Volver
    </a>

    <div class="card card-custom p-4">

      <h4>Control de Jornada</h4>

      <span id="estado" class="badge bg-secondary mb-3">...</span>

      <h1 id="reloj">00:00:00</h1>

      <div class="mt-4 d-flex justify-content-center gap-3">

        <button id="btnIniciar" class="btn btn-success">Iniciar</button>

        <!-- BOTÓN ÚNICO PAUSAR / REANUDAR -->
        <button id="btnPausar" class="btn btn-warning">Pausar</button>

        <button id="btnFinalizar" class="btn btn-danger">Finalizar</button>

      </div>

    </div>

  </div>

</div>

<div class="modal fade" id="modalPausa" tabindex="-1" aria-labelledby="modalPausaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalPausaLabel">Motivo de la pausa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <label class="form-label">Selecciona el motivo</label>

        <select id="motivoPausa" class="form-select">
          <option value="">-- Selecciona un motivo --</option>
          <option value="Descanso">Descanso</option>
          <option value="Comida">Comida</option>
          <option value="Pausa personal">Pausa personal</option>
          <option value="Fumar">Fumar</option>
          <option value="Gestión laboral">Gestión laboral</option>
          <option value="Otro">Otro</option>
        </select>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="confirmarPausa" class="btn btn-warning">Confirmar</button>
      </div>

    </div>
  </div>
</div>

<script>
const RUTA_URL = "<?= RUTA_URL ?>";

window.fichajeActivo = <?= json_encode($datos['fichaje'] ?? null); ?>;
window.descansos = <?= json_encode($datos['descansos'] ?? []); ?>;
window.enDescanso = <?= json_encode($datos['enDescanso'] ?? false); ?>;
</script>

<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureFichajes.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>