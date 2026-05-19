<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'includes/images.php';
require_once 'api/config/database.php';
require_once 'api/models/Component.php';

$slug      = trim($_GET['id']      ?? '');
$sysSlug   = trim($_GET['sistema'] ?? '');
$comp      = $slug ? Component::getBySlug($slug) : null;

if (!$comp) {
    $pageTitle   = 'Componente no encontrado — CarSense';
    $currentPage = 'sistemas';
    $breadcrumbs = [
        ['label'=>'Sistemas',   'url'=>'sistemas.php'],
        ['label'=>'Componente', 'url'=>null],
    ];
    require 'includes/header.php';
    ?>
    <div class="empty-state" style="min-height:60vh">
      <div class="empty-icon"><?= icon('settings', 28) ?></div>
      <p class="empty-title">Componente no encontrado</p>
      <a href="<?= $sysSlug ? 'sistema.php?id='.urlencode($sysSlug) : 'sistemas.php' ?>" style="color:var(--accent);font-size:13px">← Volver al sistema</a>
    </div>
    <?php
    require 'includes/footer.php';
    exit;
}

$sysSlug     = $comp['system_slug'];
$sysColor    = htmlspecialchars($comp['system_color']);
$wearStatus  = $comp['wear_status'];
$wearColor   = $wearStatus === 'critical' ? '#e03030' : ($wearStatus === 'warning' ? '#f59e0b' : '#10b981');
$wearLabel   = $wearStatus === 'critical' ? 'Requiere atención urgente' : ($wearStatus === 'warning' ? 'Revisar próximamente' : 'En buen estado');
$img         = $comp['image_url'] ?: ($componentImages[$slug] ?? ($componentImages[$sysSlug] ?? ''));

$pageTitle   = htmlspecialchars($comp['name']) . ' — CarSense';
$currentPage = 'sistemas';
$breadcrumbs = [
    ['label'=>'Sistemas',                    'url'=>'sistemas.php'],
    ['label'=>$comp['system_name'],          'url'=>'sistema.php?id='.urlencode($sysSlug)],
    ['label'=>$comp['name'],                 'url'=>null],
];
require 'includes/header.php';
?>

<div class="compdetalle-page">

  <!-- Header -->
  <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:16px">
      <div style="width:48px;height:48px;border-radius:12px;background:<?= $sysColor ?>18;border:1px solid <?= $sysColor ?>40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <span style="color:<?= $sysColor ?>"><?= icon('settings', 20) ?></span>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
          <h1 style="font-size:1.15rem;color:#fff"><?= htmlspecialchars($comp['name']) ?></h1>
          <a href="sistema.php?id=<?= urlencode($sysSlug) ?>"
             style="padding:2px 8px;background:<?= $sysColor ?>22;border:1px solid <?= $sysColor ?>40;color:<?= $sysColor ?>;font-size:10px;border-radius:4px;text-decoration:none;text-transform:uppercase">
            <?= htmlspecialchars($comp['system_name']) ?>
          </a>
        </div>
        <p style="color:var(--muted);font-size:13px"><?= htmlspecialchars($comp['description']) ?></p>
      </div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="diagnostico.php" class="btn btn-primary btn-sm" style="display:inline-flex">
        Diagnosticar <?= icon('arrowRight', 13) ?>
      </a>
    </div>
  </div>

  <div class="compdetalle-grid">
    <!-- Main content -->
    <div>

      <!-- Image -->
      <?php if ($img): ?>
        <div class="card" style="padding:0;overflow:hidden;margin-bottom:20px">
          <div style="position:relative;height:220px">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($comp['name']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy"/>
            <div style="position:absolute;inset:0;background:<?= $sysColor ?>;opacity:0.1"></div>
            <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--bg2) 0%,transparent 60%)"></div>
            <div style="position:absolute;bottom:12px;left:12px">
              <span style="padding:4px 10px;background:<?= $sysColor ?>28;border:1px solid <?= $sysColor ?>50;border-radius:6px;font-size:11px;color:<?= $sysColor ?>">
                <?= htmlspecialchars($comp['name']) ?>
              </span>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Tabs -->
      <div class="card">
        <div class="tabs-row">
          <?php foreach (['Descripción','Especificaciones','Síntomas','Consejos'] as $i => $tab): ?>
            <button class="tab-btn <?= $i === 0 ? 'active' : '' ?>" data-tab="<?= $i ?>"><?= $tab ?></button>
          <?php endforeach; ?>
        </div>

        <!-- Tab: Descripción -->
        <div class="tab-content" data-tab-panel="0">
          <p style="color:var(--text2);font-size:14px;line-height:1.7"><?= htmlspecialchars($comp['description']) ?></p>
          <p style="color:var(--muted);font-size:13px;margin-top:12px;line-height:1.6">
            Este componente es parte del <strong style="color:var(--text2)"><?= htmlspecialchars($comp['system_name']) ?></strong> y cumple un papel fundamental en el funcionamiento seguro del vehículo.
          </p>
        </div>

        <!-- Tab: Especificaciones -->
        <div class="tab-content" data-tab-panel="1" style="display:none">
          <div style="display:flex;flex-direction:column;gap:0">
            <?php foreach ($comp['specs'] as $i => $s): ?>
              <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;<?= $i > 0 ? 'border-top:1px solid var(--border)' : '' ?>">
                <span style="color:var(--muted);font-size:13px"><?= htmlspecialchars($s['spec_label']) ?></span>
                <span style="color:#fff;font-size:13px;font-weight:500"><?= htmlspecialchars($s['spec_value']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tab: Síntomas -->
        <div class="tab-content" data-tab-panel="2" style="display:none">
          <div style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($comp['symptoms'] as $s): ?>
              <div style="display:flex;align-items:flex-start;gap:10px;padding:10px;background:rgba(224,48,48,0.06);border:1px solid rgba(224,48,48,0.15);border-radius:8px">
                <span style="color:var(--accent);flex-shrink:0;margin-top:2px"><?= icon('alertTriangle', 13) ?></span>
                <span style="color:var(--text2);font-size:13px"><?= htmlspecialchars($s) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tab: Consejos -->
        <div class="tab-content" data-tab-panel="3" style="display:none">
          <div style="display:flex;flex-direction:column;gap:10px">
            <?php foreach ($comp['tips'] as $t): ?>
              <div style="display:flex;align-items:flex-start;gap:10px">
                <span style="color:var(--green);flex-shrink:0;margin-top:2px"><?= icon('lightbulb', 13) ?></span>
                <span style="color:var(--text2);font-size:13px;line-height:1.6"><?= htmlspecialchars($t) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div style="display:flex;flex-direction:column;gap:16px">

      <!-- System nav -->
      <div class="card">
        <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Sistema</div>
        <a href="sistema.php?id=<?= urlencode($sysSlug) ?>"
           style="display:flex;align-items:center;gap:10px;padding:10px;background:<?= $sysColor ?>10;border:1px solid <?= $sysColor ?>30;border-radius:10px;text-decoration:none;margin-bottom:8px">
          <span style="color:<?= $sysColor ?>"><?= icon('cpu', 16) ?></span>
          <div>
            <div style="color:#fff;font-size:13px"><?= htmlspecialchars($comp['system_name']) ?></div>
            <div style="color:var(--muted2);font-size:11px;display:flex;align-items:center;gap:4px">Ver sistema completo <?= icon('chevronRight', 9) ?></div>
          </div>
        </a>
      </div>

      <!-- Quick actions -->
      <div class="card">
        <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Acciones</div>
        <div style="display:flex;flex-direction:column;gap:8px">
          <a href="diagnostico.php" class="btn btn-primary btn-sm" style="display:flex;justify-content:center">
            <?= icon('search', 13) ?> Iniciar diagnóstico
          </a>
          <a href="sistema.php?id=<?= urlencode($sysSlug) ?>" class="btn btn-ghost btn-sm" style="display:flex;justify-content:center">
            <?= icon('arrowLeft', 13) ?> Volver al sistema
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(function(){
  var btns   = document.querySelectorAll('.tab-btn');
  var panels = document.querySelectorAll('.tab-content');

  btns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var tab = btn.dataset.tab;
      btns.forEach(function(b){ b.classList.toggle('active', b.dataset.tab === tab); });
      panels.forEach(function(p){ p.style.display = p.dataset.tabPanel === tab ? 'block' : 'none'; });
    });
  });
})();
</script>

<?php require 'includes/footer.php'; ?>
