<?php

declare(strict_types=1);

$pageTitle = 'Inventario';
$activeNav = 'inventory';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Artículos de inventario</h2>
        <div class="toolbar">
            <input type="search" id="itemsSearch" placeholder="Buscar por nombre o categoría…" style="max-width:320px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <span></span>
        </div>
        <div class="table-wrap">
            <table id="itemsTable">
                <thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Almacén</th><th>Existencia</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="modal-overlay" id="movementModal">
            <div class="modal">
                <div class="modal-header"><h3>Registrar movimiento — <span id="movArticuloId"></span></h3><button type="button" class="icon-btn" id="cancelMovementBtn">&times;</button></div>
                <form id="movementForm">
                    <input type="hidden" id="mov_articulo_id">
                    <div class="field">
                        <label>Tipo</label>
                        <select id="mov_tipo">
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>
                    <div class="field"><label>Cantidad</label><input type="number" id="mov_cantidad" required></div>
                    <div class="field"><label>Motivo</label><input type="text" id="mov_motivo"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Registrar</button>
                        <button type="button" class="btn btn-secondary" id="cancelMovementBtn2">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
<?php
$pageScript = 'inventory.js';
require __DIR__ . '/../src/views/partials/footer.php';
