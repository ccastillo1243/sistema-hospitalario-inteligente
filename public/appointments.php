<?php

declare(strict_types=1);

$pageTitle = 'Agenda médica';
$activeNav = 'appointments';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Citas</h2>
        <div class="toolbar">
            <span></span>
            <button id="newAppointmentBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva cita</span></button>
        </div>
        <div class="table-wrap">
            <table id="appointmentsTable">
                <thead>
                    <tr><th>Fecha/hora</th><th>Paciente</th><th>Médico</th><th>Tipo</th><th>Estado</th><th></th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
<?php
$pageScript = 'appointments.js';
require __DIR__ . '/../src/views/partials/footer.php';
