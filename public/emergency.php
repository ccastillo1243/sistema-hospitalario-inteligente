<?php

declare(strict_types=1);

$pageTitle = 'Emergencias';
$activeNav = 'emergency';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Casos de emergencia</h2>
        <p class="section-hint">Ordenados por prioridad de triage (más urgente primero), no por orden de llegada.</p>
        <div class="toolbar">
            <input type="search" id="casesSearch" placeholder="Buscar por paciente o motivo…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <div style="display:flex; gap:8px;">
                <a href="reports/emergency-cases.xlsx" class="btn btn-secondary"><?= Icons::svg('file') ?><span>Reporte Excel</span></a>
                <button id="newCaseBtn" class="btn"><?= Icons::svg('plus') ?><span>Nuevo caso</span></button>
            </div>
        </div>
        <div class="table-wrap">
            <table id="casesTable">
                <thead><tr><th>Paciente</th><th>Triage</th><th>Motivo</th><th>Llegada</th><th>Estado</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="caseModal">
            <div class="modal">
                <div class="modal-header"><h3>Nuevo caso</h3><button type="button" class="icon-btn" id="cancelCaseBtn">&times;</button></div>
                <form id="caseForm">
                    <div class="field"><label>Paciente</label><select id="cas_paciente_id" required></select></div>
                    <div class="field"><label>Nivel de triage (1=más urgente, 5=menos)</label><select id="cas_nivel_id" required></select></div>
                    <div class="field"><label>Motivo</label><input type="text" id="cas_motivo" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar caso</button>
                        <button type="button" class="btn btn-secondary" id="cancelCaseBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="attendModal">
            <div class="modal">
                <div class="modal-header"><h3>Atender caso <span id="attendCaseId"></span></h3><button type="button" class="icon-btn" id="cancelAttendBtn">&times;</button></div>
                <form id="attendForm">
                    <input type="hidden" id="atn_caso_id">
                    <div class="field"><label>Médico</label><select id="atn_medico_id" required></select></div>
                    <div class="field"><label>Notas</label><input type="text" id="atn_notas"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar atención</button>
                        <button type="button" class="btn btn-secondary" id="cancelAttendBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'emergency.js';
require __DIR__ . '/../src/views/partials/footer.php';
