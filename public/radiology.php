<?php

declare(strict_types=1);

$pageTitle = 'Radiología';
$activeNav = 'radiology';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Órdenes de radiología</h2>
        <div class="toolbar">
            <span></span>
            <button id="newOrderBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva orden</span></button>
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
                    <div class="field"><label>ID paciente</label><input type="number" id="ord_paciente_id" required></div>
                    <div class="field"><label>ID médico</label><input type="number" id="ord_medico_id" required></div>
                    <div class="field"><label>ID tipo de estudio</label><input type="number" id="ord_tipo_estudio_id" required></div>
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
                    <div class="field"><label>ID de personal que realiza</label><input type="number" id="est_realizado_por" required></div>
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
                    <div class="field"><label>ID radiólogo</label><input type="number" id="inf_radiologo_id" required></div>
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
