<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/System.php';

$systems     = System::getAll();
$pageTitle   = 'Sistemas — CarSense';
$currentPage = 'sistemas';
$breadcrumbs = [['label' => 'Sistemas', 'url' => null]];
require 'includes/header.php';
?>

<div class="sistemas-page">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:8px">
    <h1 style="font-size:1.25rem;color:var(--text)">Sistemas del vehículo</h1>
    <div class="search-input-group" style="max-width:280px;flex:1">
      <span style="color:var(--muted2)"><?= icon('search', 14) ?></span>
      <input id="sys-search" class="search-input" placeholder="Buscar sistema..."/>
    </div>
  </div>

  <p style="color:var(--muted);font-size:13px;margin-bottom:20px">
    Explora los sistemas principales del vehículo y aprende sobre sus componentes.
  </p>

  <!-- Car overview -->
  <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-xl);padding:20px;margin-bottom:20px;overflow:hidden">
    <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Vista general del vehículo</div>
    <div style="max-width:600px;margin:0 auto;position:relative;border-radius:var(--radius-xl);overflow:hidden;background:linear-gradient(160deg,var(--bg3) 0%,var(--bg4) 100%);border:1px solid var(--border2);box-shadow:var(--shadow)">
      <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.022) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.022) 1px,transparent 1px);background-size:32px 32px;pointer-events:none"></div>
      <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:65%;height:56px;background:radial-gradient(ellipse,rgba(224,48,48,0.18) 0%,transparent 70%);pointer-events:none"></div>
      <img class="theme-img"
           data-dark="SistemaNegro.png" data-light="SistemaBlanco.png"
           alt="Vista general de sistemas del vehículo"
           style="width:100%;height:auto;display:block;position:relative;z-index:1">
    </div>
  </div>

  <!-- Systems grid -->
  <div class="sistemas-grid" id="sistemas-grid">
    <?php foreach ($systems as $s):
      $img   = $s['image_url'] ?? '';
      $color = htmlspecialchars($s['color']);
      $slug  = urlencode($s['slug']);
    ?>
      <a href="sistema.php?id=<?= $slug ?>" class="sistema-card"
         data-name="<?= htmlspecialchars(strtolower($s['name'])) ?>"
         data-desc="<?= htmlspecialchars(strtolower($s['description'])) ?>">
        <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:<?= $color ?>;border-radius:3px 0 0 3px"></div>
        <?php if ($img): ?>
          <div style="height:120px;margin:-20px -20px 16px;overflow:hidden;border-radius:var(--radius-xl) var(--radius-xl) 0 0;position:relative">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($s['name']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy"/>
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,var(--bg2) 100%)"></div>
            <div style="position:absolute;top:12px;right:12px;padding:2px 8px;background:<?= $color ?>22;border:1px solid <?= $color ?>44;border-radius:4px;font-size:10px;color:<?= $color ?>"><?= (int)$s['component_count'] ?> componentes</div>
          </div>
        <?php else: ?>
          <div style="height:80px;margin:-20px -20px 16px;background:<?= $color ?>10;border-radius:var(--radius-xl) var(--radius-xl) 0 0;display:flex;align-items:center;justify-content:center">
            <span style="color:<?= $color ?>;opacity:0.4"><?= icon('cpu', 32) ?></span>
          </div>
        <?php endif; ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
          <div style="width:8px;height:8px;border-radius:50%;background:<?= $color ?>"></div>
          <h3 style="font-size:14px;color:var(--text)"><?= htmlspecialchars($s['name']) ?></h3>
        </div>
        <p style="font-size:12px;color:var(--muted2);line-height:1.5;margin-bottom:12px"><?= htmlspecialchars($s['description']) ?></p>
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div style="display:flex;gap:12px">
            <span style="font-size:11px;color:<?= $color ?>"><?= (int)$s['component_count'] ?> componentes</span>
            <span style="font-size:11px;color:var(--muted3)"><?= (int)$s['symptom_count'] ?> síntomas</span>
          </div>
          <span style="color:<?= $color ?>"><?= icon('arrowRight', 13) ?></span>
        </div>
      </a>
    <?php endforeach; ?>

    <div id="empty-search" class="empty-state" style="grid-column:1/-1;display:none">
      <div class="empty-icon"><?= icon('search', 20) ?></div>
      <p class="empty-title">No se encontraron sistemas</p>
    </div>
  </div>
</div>

<script>
(function(){
  var input   = document.getElementById('sys-search');
  var cards   = document.querySelectorAll('#sistemas-grid .sistema-card');
  var emptyEl = document.getElementById('empty-search');

  input.addEventListener('input', function(){
    var q = input.value.trim().toLowerCase();
    var visible = 0;
    cards.forEach(function(card){
      var match = !q || card.dataset.name.includes(q) || card.dataset.desc.includes(q);
      card.style.display = match ? '' : 'none';
      if (match) visible++;
    });
    emptyEl.style.display = visible === 0 ? 'flex' : 'none';
  });
})();
</script>

<?php require 'includes/footer.php'; ?>
