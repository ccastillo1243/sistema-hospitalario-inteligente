<?php

declare(strict_types=1);

$pageTitle = 'Inicio';
$activeNav = 'inicio';
require __DIR__ . '/../src/views/partials/header.php';

$modulos = [
    ['href' => 'patients.php', 'icon' => 'users', 'label' => 'Pacientes', 'desc' => 'Expedientes y datos de contacto'],
    ['href' => 'staff.php', 'icon' => 'users', 'label' => 'Personal', 'desc' => 'Médicos y enfermería'],
    ['href' => 'appointments.php', 'icon' => 'calendar', 'label' => 'Agenda médica', 'desc' => 'Citas y disponibilidad'],
    ['href' => 'hospitalization.php', 'icon' => 'bed', 'label' => 'Hospitalización', 'desc' => 'Camas, ingresos y altas'],
    ['href' => 'laboratory.php', 'icon' => 'flask', 'label' => 'Laboratorio', 'desc' => 'Órdenes y resultados'],
    ['href' => 'pharmacy.php', 'icon' => 'pill', 'label' => 'Farmacia', 'desc' => 'Recetas y dispensación'],
    ['href' => 'inventory.php', 'icon' => 'box', 'label' => 'Inventario', 'desc' => 'Almacenes y existencias'],
    ['href' => 'radiology.php', 'icon' => 'scan', 'label' => 'Radiología', 'desc' => 'Estudios e informes'],
    ['href' => 'billing.php', 'icon' => 'card', 'label' => 'Facturación', 'desc' => 'Facturas y pagos'],
    ['href' => 'emergency.php', 'icon' => 'alert', 'label' => 'Emergencias', 'desc' => 'Triage y atención'],
    ['href' => 'dashboard.php', 'icon' => 'chart', 'label' => 'Dashboard', 'desc' => 'Indicadores y reportes'],
];
?>
        <h2>Bienvenido<?= $nombre ? ', ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : '' ?></h2>
        <p class="section-hint">Selecciona un módulo para comenzar.</p>

        <div class="kpi-grid">
            <?php foreach ($modulos as $m): ?>
                <a href="<?= $m['href'] ?>" class="panel" style="margin-bottom:0; display:flex; gap:12px; align-items:flex-start; text-decoration:none;">
                    <span class="brand-mark" style="flex-shrink:0;"><?= Icons::svg($m['icon']) ?></span>
                    <span>
                        <strong style="display:block; color:var(--slate-900); font-size:13.5px;"><?= $m['label'] ?></strong>
                        <small style="color:var(--slate-500); font-size:12px;"><?= $m['desc'] ?></small>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
<?php
require __DIR__ . '/../src/views/partials/footer.php';
