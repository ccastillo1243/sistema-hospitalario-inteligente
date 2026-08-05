<?php

declare(strict_types=1);

$pageTitle = 'Mi perfil';
$activeNav = 'profile';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Mi perfil</h2>
        <p class="section-hint">Edita tus datos personales y, si quieres, cambia tu contraseña.</p>

        <div class="panel" style="max-width:480px;">
            <div id="profileError" class="error-msg" style="display:none;"></div>
            <div id="profileSuccess" class="error-msg" style="display:none; background:var(--green-50); color:var(--green-700); border-color:#bbf7d0;"></div>
            <form id="profileForm">
                <div class="field-row">
                    <div class="field"><label>Nombre</label><input type="text" id="prf_nombre" required></div>
                    <div class="field"><label>Apellido</label><input type="text" id="prf_apellido" required></div>
                </div>
                <div class="field"><label>Email</label><input type="email" id="prf_email" required></div>
                <div class="field"><label>Rol(es)</label><input type="text" id="prf_roles" disabled style="background:var(--slate-50);"></div>

                <h3 style="margin-top:20px;">Cambiar contraseña (opcional)</h3>
                <div class="field"><label>Contraseña actual</label><input type="password" id="prf_password_actual"></div>
                <div class="field"><label>Nueva contraseña</label><input type="password" id="prf_password_nueva" placeholder="Mínimo 6 caracteres"></div>

                <div class="form-actions">
                    <button type="submit" class="btn">Guardar cambios</button>
                </div>
            </form>
        </div>
<?php
$pageScript = 'profile.js';
require __DIR__ . '/../src/views/partials/footer.php';
