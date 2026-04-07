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
        
       
        <button id="btnPausar" class="btn btn-warning">Pausar</button>
        
        <button id="btnFinalizar" class="btn btn-danger">Finalizar</button>

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