<?php require_once RUTA_APP . '/views/inc/header.php' ?>

<div class="login-container">
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <form method="post">
            <div class="input-group">
                <input type="text" name="usuario" id="usuario" required="">
                <label for="usuario">Usuario</label>
            </div>
            <div class="input-group">
                <input type="password" name="contrasenya" id="contrasenya" required="">
                <label for="contrasenya">Contraseña</label>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <div class="help-text">
            <a href="<?= RUTA_URL ?>/Login/recuperacion">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</div>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureLogin.js"></script>
<?php require_once RUTA_APP . '/views/inc/footer.php' ?>