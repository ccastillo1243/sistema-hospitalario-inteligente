<?php

declare(strict_types=1);

$pageTitle = 'Personal';
$activeNav = 'staff';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Médicos</h2>
        <div class="toolbar">
            <span></span>
            <button id="newDoctorBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo médico</span></button>
        </div>
        <div class="table-wrap">
            <table id="doctorsTable">
                <thead><tr><th>Nombre</th><th>Apellido</th><th>Cédula</th><th>Especialidades</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <h2>Enfermería</h2>
        <div class="toolbar">
            <span></span>
            <button id="newNurseBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo enfermero/a</span></button>
        </div>
        <div class="table-wrap">
            <table id="nursesTable">
                <thead><tr><th>Nombre</th><th>Apellido</th><th>Cédula</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
<?php
$pageScript = 'staff.js';
require __DIR__ . '/../src/views/partials/footer.php';
