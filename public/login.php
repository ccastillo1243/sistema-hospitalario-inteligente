<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/core/Icons.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Sistema Hospitalario</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-card" id="loginCard">
        <div class="auth-brand">
            <span class="brand-mark" style="width:38px;height:38px;"><?= Icons::svg('bed') ?></span>
            <div>
                <div class="auth-title">Sistema Hospitalario</div>
                <div class="auth-subtitle">Ingresa con tu cuenta institucional</div>
            </div>
        </div>
        <div id="error" class="error-msg" style="display:none;"></div>
        <form id="loginForm">
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block">Ingresar</button>
        </form>
        <p class="auth-footer-link"><a href="#" id="forgotLink">¿Olvidaste tu contraseña?</a></p>
    </div>

    <div class="auth-card" id="forgotCard" style="display:none;">
        <div class="auth-brand">
            <span class="brand-mark" style="width:38px;height:38px;"><?= Icons::svg('bed') ?></span>
            <div>
                <div class="auth-title">Recuperar contraseña</div>
                <div class="auth-subtitle">Te enviaremos un enlace de recuperación</div>
            </div>
        </div>
        <div id="forgotStep1">
            <div class="field">
                <label for="forgotEmail">Correo electrónico</label>
                <input type="email" id="forgotEmail" required>
            </div>
            <button id="requestResetBtn" type="button" class="btn btn-block">Enviar enlace</button>
        </div>
        <div id="forgotStep2" style="display:none; margin-top:16px;">
            <p class="section-hint" style="margin-top:0;">
                Demo: en producción el token llegaría por correo. Aquí se muestra directamente.
            </p>
            <div class="field"><label>Token</label><input type="text" id="resetToken"></div>
            <div class="field"><label>Nueva contraseña</label><input type="password" id="resetPassword"></div>
            <button id="confirmResetBtn" type="button" class="btn btn-block">Cambiar contraseña</button>
        </div>
        <p id="forgotMsg" class="section-hint"></p>
        <p class="auth-footer-link"><a href="#" id="backToLoginLink">Volver al inicio de sesión</a></p>
    </div>

    <script src="assets/js/vendor/jquery.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>
