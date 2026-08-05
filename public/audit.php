<?php

declare(strict_types=1);

$pageTitle = 'Auditoría';
$activeNav = 'audit';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Historial de auditoría</h2>
        <p class="section-hint">Registro de todas las acciones (crear/editar/eliminar) realizadas en el sistema. Solo visible para administradores.</p>

        <div class="toolbar">
            <input type="search" id="auditSearch" placeholder="Buscar por usuario o ID de registro…" style="max-width:280px; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
            <div style="display:flex; gap:8px;">
                <select id="auditTabla" style="padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
                    <option value="">Todas las tablas</option>
                </select>
                <select id="auditAccion" style="padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">
                    <option value="">Todas las acciones</option>
                    <option value="create">Crear</option>
                    <option value="update">Editar</option>
                    <option value="delete">Eliminar</option>
                </select>
            </div>
        </div>

        <div class="table-wrap">
            <table id="auditTable">
                <thead><tr><th>Fecha</th><th>Usuario</th><th>Tabla</th><th>Registro</th><th>Acción</th><th>Detalle</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
<?php
$pageScript = 'audit.js';
require __DIR__ . '/../src/views/partials/footer.php';
