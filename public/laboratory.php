<?php

declare(strict_types=1);

$pageTitle = 'Laboratorio';
$activeNav = 'laboratory';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Órdenes de laboratorio</h2>
        <div class="toolbar">
            <input type="search" id="ordersSearch" placeholder="Buscar por paciente o médico…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <div style="display:flex; gap:8px;">
                <a href="reports/lab-orders.pdf" class="btn btn-secondary"><?= Icons::svg('file') ?><span>Reporte PDF</span></a>
                <button id="newOrderBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva orden</span></button>
            </div>
        </div>
        <div class="table-wrap">
            <table id="ordersTable">
                <thead><tr><th>ID</th><th>Paciente</th><th>Médico</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="orderModal">
            <div class="modal">
                <div class="modal-header"><h3>Nueva orden</h3><button type="button" class="icon-btn" id="cancelOrderBtn">&times;</button></div>
                <form id="orderForm">
                    <div class="field"><label>ID paciente</label><input type="number" id="ord_paciente_id" required></div>
                    <div class="field"><label>ID médico</label><input type="number" id="ord_medico_id" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Crear orden</button>
                        <button type="button" class="btn btn-secondary" id="cancelOrderBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel" id="samplesPanel" style="display:none;">
            <h3>Muestras — Orden <span id="sampleOrderId"></span></h3>
            <form id="sampleForm" class="field-row" style="align-items:flex-end;">
                <input type="hidden" id="sam_orden_id">
                <div class="field"><label>ID tipo de examen</label><input type="number" id="sam_tipo_examen_id" required></div>
                <div class="field"><label>Tipo de muestra</label><input type="text" id="sam_tipo_muestra" required></div>
                <div class="field" style="display:flex; gap:8px;">
                    <button type="submit" class="btn">Registrar muestra</button>
                    <button type="button" class="btn btn-secondary" id="closeSamplesBtn">Cerrar</button>
                </div>
            </form>
            <div class="table-wrap">
                <table id="samplesTable">
                    <thead><tr><th>Código</th><th>Tipo muestra</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="modal-overlay" id="resultModal">
            <div class="modal">
                <div class="modal-header"><h3>Registrar resultado — Muestra <span id="resultSampleId"></span></h3><button type="button" class="icon-btn" id="cancelResultBtn">&times;</button></div>
                <form id="resultForm">
                    <input type="hidden" id="res_muestra_id">
                    <div class="field"><label>ID parámetro</label><input type="number" id="res_parametro_id" required></div>
                    <div class="field"><label>Valor</label><input type="text" id="res_valor" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar resultado</button>
                        <button type="button" class="btn btn-secondary" id="cancelResultBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'laboratory.js';
require __DIR__ . '/../src/views/partials/footer.php';
