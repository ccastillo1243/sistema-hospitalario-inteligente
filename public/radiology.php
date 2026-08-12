<?php

declare(strict_types=1);

$pageTitle = 'Radiología';
$activeNav = 'radiology';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Órdenes de radiología</h2>
        <div class="toolbar">
            <input type="search" id="ordersSearch" placeholder="Buscar por paciente o médico…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <div style="display:flex; gap:8px;">
                <a href="reports/radiology-orders.pdf" class="btn btn-secondary"><?= Icons::svg('file') ?><span>Reporte PDF</span></a>
                <button id="newOrderBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva orden</span></button>
            </div>
        </div>
        <div class="table-wrap">
            <table id="ordersTable">
                <thead><tr><th>ID</th><th>Paciente</th><th>Médico</th><th>Estudio</th><th>Estado</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="orderModal">
            <div class="modal">
                <div class="modal-header"><h3>Nueva orden</h3><button type="button" class="icon-btn" id="cancelOrderBtn">&times;</button></div>
                <form id="orderForm">
                    <div class="field"><label>Paciente</label><select id="ord_paciente_id" required></select></div>
                    <div class="field"><label>Médico</label><select id="ord_medico_id" required></select></div>
                    <div class="field"><label>Tipo de estudio</label><select id="ord_tipo_estudio_id" required></select></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Crear orden</button>
                        <button type="button" class="btn btn-secondary" id="cancelOrderBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="studyModal">
            <div class="modal">
                <div class="modal-header"><h3>Registrar estudio — Orden <span id="studyOrderId"></span></h3><button type="button" class="icon-btn" id="cancelStudyBtn">&times;</button></div>
                <form id="studyForm">
                    <input type="hidden" id="est_orden_id">
                    <div class="field"><label>Personal que realiza</label><select id="est_realizado_por" required></select></div>
                    <div class="field"><label>Observaciones</label><input type="text" id="est_observaciones"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar estudio</button>
                        <button type="button" class="btn btn-secondary" id="cancelStudyBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="reportModal">
            <div class="modal">
                <div class="modal-header"><h3>Registrar informe — Estudio <span id="reportStudyId"></span></h3><button type="button" class="icon-btn" id="cancelReportBtn">&times;</button></div>
                <form id="reportForm">
                    <input type="hidden" id="inf_estudio_id">
                    <div class="field"><label>Radiólogo</label><select id="inf_radiologo_id" required></select></div>
                    <div class="field"><label>Contenido</label><input type="text" id="inf_contenido" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar informe</button>
                        <button type="button" class="btn btn-secondary" id="cancelReportBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'radiology.js';
require __DIR__ . '/../src/views/partials/footer.php';
