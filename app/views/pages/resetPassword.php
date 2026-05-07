<?php require_once RUTA_APP . '/views/inc/header.php'; ?>

<div class="login-container">

    <div class="login-box">

        <h2>Nueva contraseña</h2>

        <p class="description-text">
            Introduce tu nueva contraseña para completar el restablecimiento.
        </p>

        <form id="formResetPassword">

            <!-- TOKEN -->
            <input
                type="hidden"
                name="token"
                value="<?= htmlspecialchars($datos['token']) ?>"
            >

            <!-- USER ID -->
            <input
                type="hidden"
                name="usuarioId"
                value="<?= (int)$datos['usuarioId'] ?>"
            >

            <!-- NEW PASSWORD -->
            <div class="input-group">

                <input
                    type="password"
                    name="nueva_password"
                    id="nueva_password"
                    required
                    minlength="4"
                >

                <label for="nueva_password">
                    Nueva contraseña
                </label>

            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="input-group">

                <input
                    type="password"
                    name="confirma_password"
                    id="confirma_password"
                    required
                    minlength="4"
                >

                <label for="confirma_password">
                    Confirmar contraseña
                </label>

            </div>

            <button type="submit">
                Actualizar contraseña
            </button>

        </form>

        <div class="help-text">

            <a href="<?= RUTA_URL ?>/login">
                Volver al login
            </a>

        </div>

    </div>

</div>

<script
    type="module"
    src="<?= RUTA_URL ?>/js/infrastructure/infrastructureResetPassword.js"
></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>