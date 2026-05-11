<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="main-wrapper container-fluid">
<div class="content">




<script>
const fichajeActivo = <?php echo json_encode($datos['fichajeActivo'] ?? null); ?>;
</script>

<script type='module' src="<?= RUTA_URL ?>/js/application/homeLoadCounterUseCases.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureHome.js"></script>
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/filtros.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>