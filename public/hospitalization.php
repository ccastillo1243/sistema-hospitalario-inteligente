<?php

declare(strict_types=1);

$pageTitle = 'Hospitalización';
$activeNav = 'hospitalization';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Camas</h2>
        <div class="table-wrap" style="margin-bottom:28px;">
            <table id="bedsTable">
                <thead><tr><th>ID</th><th>Habitación</th><th>Código</th><th>Estado</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <h2>Ingresos activos</h2>
        <div class="toolbar">
            <span></span>
            <button id="newAdmissionBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo ingreso</span></button>
        </div>
        <div class="table-wrap">
            <table id="admissionsTable">
                <thead>
                    <tr><th>Paciente</th><th>Cama</th><th>Habitación</th><th>Médico</th><th>Motivo</th><th>Ingresado</th><th>Alta</th><th></th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="admissionModal">
            <div class="modal">
                <div class="modal-header">
                    <h3>Nuevo ingreso</h3>
                    <button type="button" class="icon-btn" id="cancelAdmissionBtn">&times;</button>
                </div>
                <form id="admissionForm">
                    <div class="field"><label>ID paciente</label><input type="number" id="adm_paciente_id" required></div>
                    <div class="field"><label>ID cama (debe estar libre)</label><input type="number" id="adm_cama_id" required></div>
                    <div class="field"><label>ID médico</label><input type="number" id="adm_medico_id" required></div>
                    <div class="field"><label>Motivo</label><input type="text" id="adm_motivo" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar ingreso</button>
                        <button type="button" class="btn btn-secondary" id="cancelAdmissionBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="dischargeModal">
            <div class="modal">
                <div class="modal-header">
                    <h3>Registrar alta</h3>
                    <button type="button" class="icon-btn" id="cancelDischargeBtn">&times;</button>
                </div>
                <form id="dischargeForm">
                    <input type="hidden" id="dis_ingreso_id">
                    <div class="field"><label>Resumen</label><input type="text" id="dis_resumen" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar alta</button>
                        <button type="button" class="btn btn-secondary" id="cancelDischargeBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'hospitalization.js';
require __DIR__ . '/../src/views/partials/footer.php';
