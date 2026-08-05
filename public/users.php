<?php

declare(strict_types=1);

$pageTitle = 'Usuarios';
$activeNav = 'users';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Usuarios del sistema</h2>
        <p class="section-hint">Cuentas disponibles para iniciar sesión y probar cada rol. Solo el administrador puede ver y gestionar esta sección.</p>

        <div class="toolbar">
            <span></span>
            <button id="newUserBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo usuario</span></button>
        </div>

        <div class="table-wrap">
            <table id="usersTable">
                <thead>
                    <tr><th>Nombre</th><th>Email</th><th>Roles</th><th>Estado</th><th>Último acceso</th><th></th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="userModal">
            <div class="modal">
                <div class="modal-header">
                    <h3 id="userModalTitle">Nuevo usuario</h3>
                    <button type="button" class="icon-btn" id="cancelUserBtn">&times;</button>
                </div>
                <div id="userModalError" class="error-msg" style="display:none;"></div>
                <form id="userForm">
                    <input type="hidden" id="usr_id">
                    <div class="field-row">
                        <div class="field"><label>Nombre</label><input type="text" id="usr_nombre" required></div>
                        <div class="field"><label>Apellido</label><input type="text" id="usr_apellido" required></div>
                    </div>
                    <div class="field"><label>Email</label><input type="email" id="usr_email" required></div>
                    <div class="field">
                        <label id="usr_password_label">Contraseña</label>
                        <input type="password" id="usr_password" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="field">
                        <label>Roles</label>
                        <div id="usr_roles" class="field-row" style="gap:8px;"></div>
                    </div>
                    <div class="field" id="usr_activo_wrapper" style="display:none;">
                        <label><input type="checkbox" id="usr_activo" style="width:auto; margin-right:6px;">Cuenta activa</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Guardar</button>
                        <button type="button" class="btn btn-secondary" id="cancelUserBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'users.js';
require __DIR__ . '/../src/views/partials/footer.php';
