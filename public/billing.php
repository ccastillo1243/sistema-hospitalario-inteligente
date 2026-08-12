<?php

declare(strict_types=1);

$pageTitle = 'Facturación';
$activeNav = 'billing';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Facturas</h2>
        <div class="toolbar">
            <input type="search" id="invoicesSearch" placeholder="Buscar por paciente…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <button id="newInvoiceBtn" class="btn"><?= Icons::svg('plus') ?><span>Nueva factura</span></button>
        </div>
        <div class="table-wrap">
            <table id="invoicesTable">
                <thead><tr><th>ID</th><th>Paciente</th><th>Subtotal</th><th>Impuestos</th><th>Total</th><th>Pagado</th><th>Estado</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="invoiceModal">
            <div class="modal">
                <div class="modal-header"><h3>Nueva factura</h3><button type="button" class="icon-btn" id="cancelInvoiceBtn">&times;</button></div>
                <form id="invoiceForm">
                    <div class="field"><label>Paciente</label><select id="inv_paciente_id" required></select></div>
                    <div class="field-row">
                        <div class="field"><label>Servicio 1</label><select id="inv_servicio1_id" required></select></div>
                        <div class="field"><label>Cantidad</label><input type="number" id="inv_servicio1_cant" value="1" required></div>
                    </div>
                    <div class="field-row">
                        <div class="field"><label>Servicio 2 (opcional)</label><select id="inv_servicio2_id"></select></div>
                        <div class="field"><label>Cantidad</label><input type="number" id="inv_servicio2_cant" value="1"></div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Crear factura</button>
                        <button type="button" class="btn btn-secondary" id="cancelInvoiceBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="paymentModal">
            <div class="modal">
                <div class="modal-header"><h3>Registrar pago — Factura <span id="payInvoiceId"></span></h3><button type="button" class="icon-btn" id="cancelPaymentBtn">&times;</button></div>
                <form id="paymentForm">
                    <input type="hidden" id="pay_factura_id">
                    <div class="field"><label>Método de pago</label><select id="pay_metodo_id" required></select></div>
                    <div class="field"><label>Monto</label><input type="number" step="0.01" id="pay_monto" required></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar pago</button>
                        <button type="button" class="btn btn-secondary" id="cancelPaymentBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'billing.js';
require __DIR__ . '/../src/views/partials/footer.php';
