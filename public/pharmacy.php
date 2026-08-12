<?php

declare(strict_types=1);

$pageTitle = 'Farmacia';
$activeNav = 'pharmacy';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Stock de medicamentos</h2>
        <div class="table-wrap" style="margin-bottom:28px;">
            <table id="stockTable">
                <thead><tr><th>Medicamento</th><th>Stock vigente</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <h2>Recetas</h2>
        <div class="toolbar">
            <input type="search" id="prescriptionsSearch" placeholder="Buscar por paciente o médico…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <button id="newPrescriptionBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva receta</span></button>
        </div>
        <div class="table-wrap">
            <table id="prescriptionsTable">
                <thead><tr><th>ID</th><th>Paciente</th><th>Médico</th><th>Fecha</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="prescriptionModal">
            <div class="modal">
                <div class="modal-header"><h3>Nueva receta</h3><button type="button" class="icon-btn" id="cancelPrescriptionBtn">&times;</button></div>
                <form id="prescriptionForm">
                    <div class="field"><label>Paciente</label><select id="rec_paciente_id" required></select></div>
                    <div class="field"><label>Médico</label><select id="rec_medico_id" required></select></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Crear receta</button>
                        <button type="button" class="btn btn-secondary" id="cancelPrescriptionBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel" id="itemsPanel" style="display:none;">
            <h3>Ítems de la receta <span id="itemsRecetaId"></span></h3>
            <form id="itemForm" class="field-row" style="align-items:flex-end;">
                <input type="hidden" id="item_receta_id">
                <div class="field"><label>Medicamento</label><select id="item_medicamento_id" required></select></div>
                <div class="field"><label>Cantidad</label><input type="number" id="item_cantidad" required></div>
                <div class="field"><label>Indicaciones</label><input type="text" id="item_indicaciones"></div>
                <div class="field" style="display:flex; gap:8px;">
                    <button type="submit" class="btn">Agregar</button>
                    <button type="button" class="btn btn-secondary" id="closeItemsBtn">Cerrar</button>
                </div>
            </form>
            <div class="table-wrap">
                <table id="itemsTable">
                    <thead><tr><th>Medicamento</th><th>Cantidad</th><th>Indicaciones</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
<?php
$pageScript = 'pharmacy.js';
require __DIR__ . '/../src/views/partials/footer.php';
