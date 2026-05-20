<?php
// Expects: $pageTitle (string), $currentPage (string), $user (array|null), $breadcrumbs (array)
$user        = $user        ?? null;
$breadcrumbs = $breadcrumbs ?? [];
$currentPage = $currentPage ?? '';
$pageTitle   = $pageTitle   ?? 'CarSense';

$navItems = [
    ['id' => 'home',       'path' => 'index.php',      'label' => 'Inicio',      'icon' => 'home'],
    ['id' => 'diagnostico','path' => 'diagnostico.php', 'label' => 'Diagnóstico','icon' => 'search'],
    ['id' => 'sistemas',   'path' => 'sistemas.php',    'label' => 'Sistemas',    'icon' => 'cpu'],
];

$logoSVG = '<svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <path d="M14.5 21 L15 18.5 L25 18.5 L25.5 21 Z" fill="#0a0a0e" fill-opacity="0.6"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
  <path d="M10 27 L14 27 L15.5 24.5 L17 29 L20 25 L22 27 L30 27" stroke="#e03030" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" stroke-opacity="0.85"/>
</svg>';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="css/styles.css"/>
  <script>
    (function(){if(localStorage.getItem('theme')==='light')document.documentElement.classList.add('light');})();
  </script>
</head>
<body>
<div id="app-wrapper">

  <header id="app-header">
    <a href="index.php" class="header-logo">
      <?= $logoSVG ?>
      <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
    </a>

    <nav class="header-nav">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= $item['path'] ?>" class="header-nav-link <?= $currentPage === $item['id'] ? 'active' : '' ?>">
          <?= icon($item['icon'], 13) ?>
          <span><?= $item['label'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="header-spacer"></div>

    <div class="header-actions">
      <button class="theme-toggle" id="theme-toggle-btn" title="Cambiar tema">
        <?= icon('sun', 14) ?>
      </button>

      <?php if ($user): ?>
        <a href="perfil.php" class="user-avatar"><?= htmlspecialchars($user['initials']) ?></a>

        <a href="logout.php" class="icon-btn danger" title="Cerrar sesión" style="display:flex">
          <?= icon('logout', 13) ?>
        </a>
      <?php else: ?>
        <a href="login.php" class="header-nav-link" style="font-size:12px;gap:6px">
          <?= icon('login', 13) ?>
          <span>Iniciar sesión</span>
        </a>
        <a href="registro.php" class="btn btn-primary btn-sm">Registrarse</a>
      <?php endif; ?>
    </div>
  </header>

  <div id="breadcrumbs-bar" class="breadcrumbs-bar<?= empty($breadcrumbs) ? ' hidden' : '' ?>">
    <?php
    $last = count($breadcrumbs) - 1;
    foreach ($breadcrumbs as $i => $crumb):
    ?>
      <?php if ($i > 0): ?>
        <span class="breadcrumb-sep">/</span>
      <?php endif; ?>
      <?php if ($crumb['url'] !== null && $i < $last): ?>
        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
      <?php else: ?>
        <span class="breadcrumb-current"><?= htmlspecialchars($crumb['label']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <main id="page-content" class="page-content">
