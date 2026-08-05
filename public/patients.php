<?php

declare(strict_types=1);

$pageTitle = 'Pacientes';
$activeNav = 'patients';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Pacientes</h2>
        <p class="section-hint">Registro y expedientes básicos de pacientes del hospital.</p>

        <div class="toolbar">
            <span></span>
            <button id="newPatientBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo paciente</span></button>
        </div>

        <div class="table-wrap">
            <table id="patientsTable">
                <thead>
                    <tr>
                        <th>Expediente</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Documento</th>
                        <th>Género</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
<?php
$pageScript = 'patients.js';
require __DIR__ . '/../src/views/partials/footer.php';
