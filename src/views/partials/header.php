<?php
/**
 * Layout compartido (sidebar + topbar). Cada página define $pageTitle y
 * $activeNav antes de hacer require de este archivo, y llama a footer.php
 * al final pasando $pageScript (nombre del archivo en assets/js/modules/).
 */
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Icons.php';
require_once __DIR__ . '/../../core/Permissions.php';

Auth::startSession();

if (!Auth::check()) {
    header('Location: login.php');
    exit;
}

$nombre = htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$apellido = htmlspecialchars($_SESSION['apellido'] ?? '', ENT_QUOTES, 'UTF-8');
$rolesSesion = Auth::roles();
$rolesTexto = htmlspecialchars(implode(', ', $rolesSesion), ENT_QUOTES, 'UTF-8');
$inicial = strtoupper(mb_substr($nombre !== '' ? $nombre : 'U', 0, 1, 'UTF-8'));
$pageTitle = $pageTitle ?? 'Sistema Hospitalario';
$activeNav = $activeNav ?? '';

$navItemsAll = [
    ['href' => 'app.php', 'key' => 'inicio', 'label' => 'Inicio', 'icon' => 'home'],
    ['href' => 'dashboard.php', 'key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'chart'],
    ['href' => 'patients.php', 'key' => 'patients', 'label' => 'Pacientes', 'icon' => 'users'],
    ['href' => 'staff.php', 'key' => 'staff', 'label' => 'Personal', 'icon' => 'users'],
    ['href' => 'appointments.php', 'key' => 'appointments', 'label' => 'Agenda médica', 'icon' => 'calendar'],
    ['href' => 'hospitalization.php', 'key' => 'hospitalization', 'label' => 'Hospitalización', 'icon' => 'bed'],
    ['href' => 'laboratory.php', 'key' => 'laboratory', 'label' => 'Laboratorio', 'icon' => 'flask'],
    ['href' => 'pharmacy.php', 'key' => 'pharmacy', 'label' => 'Farmacia', 'icon' => 'pill'],
    ['href' => 'inventory.php', 'key' => 'inventory', 'label' => 'Inventario', 'icon' => 'box'],
    ['href' => 'radiology.php', 'key' => 'radiology', 'label' => 'Radiología', 'icon' => 'scan'],
    ['href' => 'billing.php', 'key' => 'billing', 'label' => 'Facturación', 'icon' => 'card'],
    ['href' => 'emergency.php', 'key' => 'emergency', 'label' => 'Emergencias', 'icon' => 'alert'],
    ['href' => 'users.php', 'key' => 'users', 'label' => 'Usuarios', 'icon' => 'lock'],
];

$navItems = array_values(array_filter(
    $navItemsAll,
    fn($item) => Permissions::canAccess($item['key'], $rolesSesion)
));

$accesoPermitido = Permissions::canAccess($activeNav, $rolesSesion);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · Sistema Hospitalario</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <span class="brand-mark"><?= Icons::svg('bed', 'icon') ?></span>
                <span class="brand-text">Hospital<strong>OS</strong></span>
            </div>
            <nav class="nav">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= $item['href'] ?>" class="nav-link<?= $activeNav === $item['key'] ? ' active' : '' ?>">
                        <?= Icons::svg($item['icon'], 'icon') ?>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="sidebar-footer">
                <div class="user-chip">
                    <span class="avatar"><?= $inicial ?></span>
                    <div class="user-chip-text">
                        <strong><?= $nombre ?> <?= $apellido ?></strong>
                        <small><?= $rolesTexto ?></small>
                    </div>
                </div>
                <button id="logoutBtn" class="btn btn-ghost btn-block" type="button">
                    <?= Icons::svg('logout', 'icon') ?><span>Cerrar sesión</span>
                </button>
            </div>
        </aside>

        <div class="content">
            <header class="topbar">
                <button id="sidebarToggle" class="icon-btn only-mobile" type="button" aria-label="Menú">
                    <?= Icons::svg('menu', 'icon') ?>
                </button>
                <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="topbar-spacer"></div>
                <span class="role-pill"><?= $rolesTexto ?></span>
            </header>
            <main class="app-main">
<?php if (!$accesoPermitido): ?>
                <div class="panel" style="max-width:520px; text-align:center; margin:60px auto;">
                    <span class="brand-mark" style="width:44px;height:44px;margin:0 auto 14px;"><?= Icons::svg('alert') ?></span>
                    <h3>No tienes acceso a este módulo</h3>
                    <p class="section-hint">Tu rol (<?= $rolesTexto ?>) no tiene permiso para ver esta sección. Si crees que es un error, contacta al administrador.</p>
                    <a href="app.php" class="btn">Volver al inicio</a>
                </div>
<?php
    require __DIR__ . '/footer.php';
    exit;
endif;
?>
