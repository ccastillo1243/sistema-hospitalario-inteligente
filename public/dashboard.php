<?php

declare(strict_types=1);

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../src/views/partials/header.php';
?>
        <h2>Panel general</h2>
        <div class="kpi-grid" id="kpiGrid"></div>

        <div class="panel reports-bar">
            <strong style="margin-right:4px;">Reportes:</strong>
            <a href="reports/patients.pdf" class="btn btn-secondary btn-sm"><?= Icons::svg('file') ?><span>Pacientes (PDF)</span></a>
            <?php if (array_intersect(['admin', 'facturacion'], $rolesSesion)): ?>
                <a href="reports/invoices.xlsx" class="btn btn-secondary btn-sm"><?= Icons::svg('file') ?><span>Facturas (Excel)</span></a>
            <?php endif; ?>
            <?php if (array_intersect(['admin', 'farmacia'], $rolesSesion)): ?>
                <a href="reports/low-stock.pdf" class="btn btn-secondary btn-sm"><?= Icons::svg('file') ?><span>Stock bajo (PDF)</span></a>
            <?php endif; ?>
        </div>

        <div class="charts-grid">
            <div class="chart-card"><h3>Ocupación de camas por piso</h3><canvas id="bedChart"></canvas></div>
            <div class="chart-card"><h3>Medicamentos con stock bajo</h3><canvas id="stockChart"></canvas></div>
            <div class="chart-card"><h3>Casos de emergencia por prioridad</h3><canvas id="emergencyChart"></canvas></div>
        </div>
<?php
$extraScripts = ['chart.umd.min.js'];
$pageScript = 'dashboard.js';
require __DIR__ . '/../src/views/partials/footer.php';
