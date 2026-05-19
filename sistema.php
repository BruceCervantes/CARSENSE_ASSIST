<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/System.php';

$slug = trim($_GET['id'] ?? '');
$sys  = $slug ? System::getBySlug($slug) : null;

if (!$sys) {
    $pageTitle   = 'Sistema no encontrado — CarSense';
    $currentPage = 'sistemas';
    $breadcrumbs = [['label'=>'Sistemas','url'=>'sistemas.php'],['label'=>'No encontrado','url'=>null]];
    require 'includes/header.php';
    ?>
    <div class="empty-state" style="min-height:60vh">
      <div class="empty-icon"><?= icon('settings', 28) ?></div>
      <p class="empty-title">Sistema no encontrado</p>
      <a href="sistemas.php" style="color:var(--accent);font-size:13px">← Volver a sistemas</a>
    </div>
    <?php
    require 'includes/footer.php';
    exit;
}

$color       = htmlspecialchars($sys['color']);
$pageTitle   = htmlspecialchars($sys['name']) . ' — CarSense';
$currentPage = 'sistemas';
$breadcrumbs = [
    ['label' => 'Sistemas', 'url' => 'sistemas.php'],
    ['label' => $sys['name'],  'url' => null],
];
require 'includes/header.php';

$critColor = match(strtolower($sys['criticality'] ?? '')) {
    'alta'  => '#e03030',
    'media' => '#f59e0b',
    default => '#10b981',
};
?>

<div class="sysdetalle-page">

  <!-- Header -->
  <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:12px">
      <div style="width:44px;height:44px;border-radius:12px;background:<?= $color ?>18;border:1px solid <?= $color ?>40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <span style="color:<?= $color ?>"><?= icon('cpu', 20) ?></span>
      </div>
      <div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <h1 style="font-size:1.25rem;color:#fff"><?= htmlspecialchars($sys['name']) ?></h1>
          <span style="padding:2px 8px;background:<?= $color ?>22;border:1px solid <?= $color ?>40;color:<?= $color ?>;font-size:11px;border-radius:4px">
            Criticidad: <?= htmlspecialchars($sys['criticality']) ?>
          </span>
        </div>
        <p style="color:var(--muted);font-size:13px;margin-top:4px;max-width:540px"><?= htmlspecialchars($sys['description']) ?></p>
      </div>
    </div>
    <a href="diagnostico.php" class="btn btn-primary btn-sm" style="display:inline-flex">
      <?= icon('search', 13) ?> Diagnosticar
    </a>
  </div>

  <div class="sysdetalle-grid">
    <!-- Main column -->
    <div style="display:flex;flex-direction:column;gap:20px">

      <!-- Image with hotpoints -->
      <?php if (!empty($sys['image_url'])): ?>
        <div class="card" style="padding:0;overflow:hidden">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px 12px">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Vista del sistema</div>
            <div style="font-size:11px;color:var(--muted2);display:flex;align-items:center;gap:6px">
              <?= icon('mapPin', 11) ?>
              <span class="hotpoint-hint-desktop">Pasa el mouse sobre los puntos</span>
              <span class="hotpoint-hint-mobile">Presiona los puntos</span>
            </div>
          </div>
          <div style="position:relative;height:240px;overflow:hidden">
            <img src="<?= htmlspecialchars($sys['image_url']) ?>" alt="<?= htmlspecialchars($sys['name']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy"/>
            <div style="position:absolute;inset:0;background:<?= $color ?>;opacity:0.1"></div>
            <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--bg2) 0%,transparent 50%)"></div>
            <?php foreach ($sys['hotpoints'] as $hp): ?>
              <div class="hotpoint" style="left:<?= (float)$hp['x_pos'] ?>%;top:<?= (float)$hp['y_pos'] ?>%">
                <div class="hotpoint-dot" style="background:<?= $color ?>"></div>
                <div class="hotpoint-tooltip" style="border-color:<?= $color ?>60">
                  <div class="hotpoint-tooltip-name"><?= htmlspecialchars($hp['name']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Components -->
      <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">
            Componentes (<?= count($sys['components']) ?>)
          </div>
        </div>
        <div class="component-grid">
          <?php foreach ($sys['components'] as $comp): ?>
            <a href="componente.php?id=<?= urlencode($comp['slug']) ?>&sistema=<?= urlencode($sys['slug']) ?>" class="component-card">
              <div style="width:36px;height:36px;border-radius:8px;background:<?= $color ?>18;border:1px solid <?= $color ?>30;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:<?= $color ?>"><?= icon('settings', 15) ?></span>
              </div>
              <div style="flex:1;min-width:0">
                <div style="color:#fff;font-size:13px;margin-bottom:2px"><?= htmlspecialchars($comp['name']) ?></div>
                <div style="color:var(--muted2);font-size:11px;white-space:normal;line-height:1.4"><?= htmlspecialchars($comp['description']) ?></div>
              </div>
              <span style="color:var(--muted3)"><?= icon('chevronRight', 13) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Symptoms -->
      <?php if (!empty($sys['symptoms'])): ?>
        <div class="card">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Síntomas relacionados</div>
          <div style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($sys['symptoms'] as $s): ?>
              <a href="resultado.php?id=<?= urlencode($s['result_slug']) ?>"
                 style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;text-decoration:none;color:inherit;transition:background 0.15s"
                 onmouseover="this.style.background='#1a1a22'" onmouseout="this.style.background='var(--bg3)'">
                <span style="color:var(--accent)"><?= icon('activity', 13) ?></span>
                <span style="color:var(--text2);font-size:13px;flex:1"><?= htmlspecialchars($s['label']) ?></span>
                <span style="color:var(--muted2)"><?= icon('arrowRight', 12) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div style="display:flex;flex-direction:column;gap:20px">

      <!-- Stats -->
      <div class="card">
        <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Estadísticas</div>
        <div style="display:flex;flex-direction:column;gap:12px">
          <?php foreach ([
            ['Componentes',          count($sys['components']), $color],
            ['Síntomas documentados', count($sys['symptoms']),  '#f59e0b'],
            ['Criticidad',           $sys['criticality'],       $critColor],
          ] as [$label, $value, $c]): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:var(--bg3);border-radius:8px;border:1px solid var(--border)">
              <span style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($label) ?></span>
              <span style="font-size:13px;font-weight:600;color:<?= $c ?>"><?= htmlspecialchars((string)$value) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Maintenance -->
      <?php if (!empty($sys['maintenance'])): ?>
        <div class="card">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Mantenimiento preventivo</div>
          <div style="display:flex;flex-direction:column;gap:12px">
            <?php foreach ($sys['maintenance'] as $m): ?>
              <div style="display:flex;align-items:flex-start;gap:10px">
                <span style="color:var(--green);flex-shrink:0;margin-top:2px"><?= icon('checkCircle', 14) ?></span>
                <div>
                  <div style="color:#fff;font-size:13px"><?= htmlspecialchars($m['label']) ?></div>
                  <div style="color:var(--muted2);font-size:11px;margin-top:2px;line-height:1.5"><?= htmlspecialchars($m['interval_text']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Quick access -->
      <div class="card">
        <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Acciones rápidas</div>
        <div style="display:flex;flex-direction:column;gap:8px">
          <a href="diagnostico.php" class="btn btn-primary btn-sm" style="display:flex;justify-content:center">
            <?= icon('search', 13) ?> Nuevo diagnóstico
          </a>
          <a href="sistemas.php" class="btn btn-ghost btn-sm" style="display:flex;justify-content:center">
            <?= icon('arrowLeft', 13) ?> Todos los sistemas
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var allTooltips = function(){ return document.querySelectorAll('.hotpoint-tooltip'); };
  var hideAll = function(){
    allTooltips().forEach(function(t){
      t.style.opacity = '0';
      t.style.transform = 'translateX(-50%) translateY(0)';
    });
  };
  var show = function(t){
    t.style.opacity = '1';
    t.style.transform = 'translateX(-50%) translateY(-2px)';
  };

  document.querySelectorAll('.hotpoint').forEach(function(hp){
    var tooltip = hp.querySelector('.hotpoint-tooltip');
    hp.addEventListener('mouseenter', function(){ show(tooltip); });
    hp.addEventListener('mouseleave', function(){
      tooltip.style.opacity = '0';
      tooltip.style.transform = 'translateX(-50%) translateY(0)';
    });
    hp.addEventListener('touchstart', function(e){
      e.preventDefault(); e.stopPropagation();
      var isOpen = tooltip.style.opacity === '1';
      hideAll();
      if (!isOpen) show(tooltip);
    }, { passive: false });
  });

  document.addEventListener('touchstart', hideAll);
  document.addEventListener('click', hideAll);
})();
</script>

<?php require 'includes/footer.php'; ?>
