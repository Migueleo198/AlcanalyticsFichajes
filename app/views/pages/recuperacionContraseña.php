<?php require_once RUTA_APP . '/views/inc/header.php' ?>

<div class="login-container">
    <div class="login-box">
        <h2>Recuperar Contraseña</h2>

        <p class="description-text">
            Introduce tu correo electrónico o usuario y te enviaremos instrucciones para restablecer tu contraseña.
        </p>

        <form method="post">
            <div class="input-group">
                <input type="text" name="usuario_email" id="usuario_email" required="">
                <label for="usuario_email">Usuario o Correo Electrónico</label>
            </div>

            <button type="submit">Enviar Instrucciones</button>
        </form>

        <div class="help-text">
            <a href="<?= RUTA_URL ?>/login">
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureRecuperacionContraseña.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>