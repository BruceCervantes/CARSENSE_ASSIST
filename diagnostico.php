<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/DiagnosticResult.php';

$results = DiagnosticResult::getAll();

$filterZones     = ['Ruedas delanteras','Motor','Transmisión','Suspensión','Frenos','Eléctrico','Escape'];
$filterWhen      = ['Al frenar','Al arrancar','Al girar','En curvas','A alta velocidad','En ralentí'];
$filterPriority  = ['Alta','Media','Baja'];
$filterSensation = ['Fricción','Ruido','Vibración','Calor','Humo','Olor'];
$priorityColor   = ['Alta'=>'#e03030','Media'=>'#f59e0b','Baja'=>'#10b981'];
$priorityLabel   = ['Alta'=>'URGENCIA ALTA','Media'=>'URGENCIA MEDIA','Baja'=>'URGENCIA BAJA'];

$pageTitle   = 'Diagnóstico — CarSense';
$currentPage = 'diagnostico';
$breadcrumbs = [['label'=>'Diagnóstico','url'=>null]];
require 'includes/header.php';
?>

<div class="diag-page">

  <!-- Search bar -->
  <div class="diag-search-bar">
    <div class="diag-search-inner">
      <div class="search-wrap" style="flex-wrap:wrap">
        <div class="search-input-group">
          <span style="color:var(--muted2);flex-shrink:0"><?= icon('search', 15) ?></span>
          <input id="diag-input" class="search-input"
                 placeholder='Describe el síntoma: "mi carro hace ruido raro al frenar"...'/>
          <button id="clear-input" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:none;flex-shrink:0">
            <?= icon('x', 13) ?>
          </button>
        </div>
        <button id="analyze-btn" class="btn btn-primary" style="flex-shrink:0">
          <span id="analyze-label">Analizar <?= icon('arrowRight', 14) ?></span>
          <span id="analyze-spinner" style="display:none"><span class="spinner"></span> Analizando...</span>
        </button>
        <button id="mobile-filter-btn" class="btn btn-ghost btn-sm" style="flex-shrink:0;position:relative">
          <?= icon('sliders', 13) ?>
          <span id="filter-badge" class="notif-badge" style="position:absolute;top:-4px;right:-4px;display:none"></span>
        </button>
      </div>
    </div>
  </div>

  <!-- Main -->
  <div class="diag-main">

    <!-- Desktop sidebar -->
    <aside class="diag-sidebar" id="diag-sidebar">
      <?php require '_diag_filters.php'; ?>
    </aside>

    <!-- Mobile filter overlay -->
    <div id="mobile-filter-overlay" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.6)"></div>
    <div id="mobile-filter-panel" style="display:none;position:fixed;left:0;top:0;bottom:0;width:260px;background:var(--bg2);border-right:1px solid var(--border2);padding:20px 16px;overflow-y:auto;z-index:101">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <span style="color:#fff;font-size:14px">Filtros</span>
        <button id="close-mobile-filters" style="background:none;border:none;cursor:pointer;color:var(--muted2)"><?= icon('x', 16) ?></button>
      </div>
      <?php require '_diag_filters.php'; ?>
    </div>

    <!-- Results -->
    <div class="diag-content">

      <!-- Status row -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px">
        <div>
          <div id="result-heading" style="font-size:14px;color:#fff;margin-bottom:2px">
            Síntomas frecuentes
          </div>
          <div id="result-subheading" style="font-size:12px;color:var(--muted2)">
            <?= count($results) ?> resultado<?= count($results) !== 1 ? 's' : '' ?> disponible<?= count($results) !== 1 ? 's' : '' ?>
          </div>
        </div>
        <button id="new-search-btn" class="btn btn-ghost btn-sm" style="display:none">Nueva búsqueda</button>
      </div>

      <!-- Cards -->
      <div id="results-list">
        <?php foreach ($results as $i => $r):
          $pc  = $priorityColor[$r['priority']]  ?? '#888';
          $pl  = $priorityLabel[$r['priority']]  ?? $r['priority'];
          $zonesAttr = htmlspecialchars(json_encode($r['zones']),      ENT_QUOTES, 'UTF-8');
          $whenAttr  = htmlspecialchars(json_encode($r['when']),       ENT_QUOTES, 'UTF-8');
          $sensAttr  = htmlspecialchars(json_encode($r['sensations']), ENT_QUOTES, 'UTF-8');
          $tagsAttr  = htmlspecialchars(json_encode($r['tags']),       ENT_QUOTES, 'UTF-8');
        ?>
          <div class="result-card"
               data-result-id="<?= htmlspecialchars($r['slug']) ?>"
               data-priority="<?= htmlspecialchars($r['priority']) ?>"
               data-zones="<?= $zonesAttr ?>"
               data-when="<?= $whenAttr ?>"
               data-sensations="<?= $sensAttr ?>"
               data-tags="<?= $tagsAttr ?>"
               data-title="<?= htmlspecialchars(strtolower($r['title'])) ?>"
               data-desc="<?= htmlspecialchars(strtolower($r['description'])) ?>"
               data-system="<?= htmlspecialchars(strtolower($r['system_name'])) ?>"
               style="cursor:pointer">
            <div class="result-icon" style="background:<?= $pc ?>18;border-color:<?= $pc ?>40">
              <?php if ($r['priority'] === 'Baja'): ?>
                <span style="color:<?= $pc ?>"><?= icon('checkCircle', 14) ?></span>
              <?php else: ?>
                <span style="color:<?= $pc ?>"><?= icon('alertTriangle', 14) ?></span>
              <?php endif; ?>
            </div>
            <div class="result-body">
              <div class="result-title">
                <?= htmlspecialchars($r['title']) ?>
                <span style="padding:2px 8px;border-radius:4px;font-size:10px;font-weight:500;background:<?= $pc ?>22;color:<?= $pc ?>;border:1px solid <?= $pc ?>40">
                  <?= htmlspecialchars($pl) ?>
                </span>
              </div>
              <p class="result-desc"><?= htmlspecialchars($r['description']) ?></p>
              <div class="result-tags">
                <?php foreach ($r['tags'] as $tag): ?>
                  <span class="tag"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="result-cta">
              <button class="btn btn-ghost btn-sm result-btn"
                      data-result-id="<?= htmlspecialchars($r['slug']) ?>">
                Ver análisis
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Empty state -->
      <div id="empty-results" class="empty-state" style="display:none">
        <div class="empty-icon"><?= icon('search', 20) ?></div>
        <p class="empty-title">Sin resultados para los filtros seleccionados</p>
        <button id="clear-empty-btn" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font)">Limpiar filtros</button>
      </div>

      <!-- AI hint -->
      <div style="margin-top:20px;display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg)">
        <div style="width:28px;height:28px;background:rgba(224,48,48,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <span style="color:var(--accent)"><?= icon('lightbulb', 12) ?></span>
        </div>
        <span style="color:var(--muted);font-size:12px">
          Describe el síntoma con tus propias palabras y la IA lo analiza.
          <em style="color:rgba(255,255,255,0.5)">"ruido metálico al frenar en reversa"</em>
          o usa los filtros para encontrar causas rápido.
        </span>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var selZones     = [];
  var selWhen      = [];
  var selPriority  = [];
  var selSensation = [];
  var currentQuery = '';
  var analyzing    = false;

  var input         = document.getElementById('diag-input');
  var analyzeBtn    = document.getElementById('analyze-btn');
  var analyzeLabel  = document.getElementById('analyze-label');
  var analyzeSpinner= document.getElementById('analyze-spinner');
  var clearInput    = document.getElementById('clear-input');
  var newSearchBtn  = document.getElementById('new-search-btn');
  var filterBadge   = document.getElementById('filter-badge');
  var resultHeading = document.getElementById('result-heading');
  var resultSub     = document.getElementById('result-subheading');
  var emptyResults  = document.getElementById('empty-results');
  var resultsList   = document.getElementById('results-list');

  // ── Input ──
  input.addEventListener('input', function(){
    var v = input.value;
    clearInput.style.display = v ? 'flex' : 'none';
  });

  input.addEventListener('keydown', function(e){
    if (e.key === 'Enter') doAnalyze();
  });

  analyzeBtn.addEventListener('click', doAnalyze);

  clearInput.addEventListener('click', function(){
    input.value = '';
    currentQuery = '';
    clearInput.style.display = 'none';
    newSearchBtn.style.display = 'none';
    updateResults();
  });

  newSearchBtn.addEventListener('click', function(){
    input.value = '';
    currentQuery = '';
    selZones = []; selWhen = []; selPriority = []; selSensation = [];
    newSearchBtn.style.display = 'none';
    clearInput.style.display = 'none';
    clearAllCheckboxes();
    updateFilterBadge();
    updateResults();
  });

  function doAnalyze(){
    if (!input.value.trim() || analyzing) return;
    analyzing = true;
    analyzeLabel.style.display = 'none';
    analyzeSpinner.style.display = 'inline-flex';
    analyzeBtn.disabled = true;

    setTimeout(function(){
      currentQuery = input.value.trim();
      analyzing = false;
      analyzeLabel.style.display = 'inline-flex';
      analyzeSpinner.style.display = 'none';
      analyzeBtn.disabled = false;
      newSearchBtn.style.display = 'inline-flex';
      updateResults();
    }, 1400);
  }

  // ── Filters ──
  function toggle(arr, val){
    var idx = arr.indexOf(val);
    if (idx === -1) arr.push(val); else arr.splice(idx, 1);
    return arr;
  }

  function updateFilterBadge(){
    var count = selZones.length + selWhen.length + selPriority.length + selSensation.length;
    filterBadge.textContent = count;
    filterBadge.style.display = count > 0 ? 'flex' : 'none';
  }

  function clearAllCheckboxes(){
    document.querySelectorAll('.filter-check-box').forEach(function(box){
      box.classList.remove('checked');
      box.style.background = '';
      box.style.borderColor = '';
      var icon = box.querySelector('.check-icon');
      if (icon) icon.style.display = 'none';
    });
    document.querySelectorAll('.filter-chip').forEach(function(chip){
      chip.classList.remove('checked');
    });
    var clears = document.querySelectorAll('.clear-filters-btn');
    clears.forEach(function(b){ b.style.display = 'none'; });
  }

  document.querySelectorAll('.filter-check').forEach(function(label){
    label.addEventListener('click', function(){
      var type = label.dataset.type;
      var val  = label.dataset.val;
      var box  = label.querySelector('.filter-check-box');
      var iconEl = box ? box.querySelector('.check-icon') : null;
      var color  = label.dataset.color || '';

      var arr;
      if (type === 'zone')     arr = selZones;
      else if (type === 'when') arr = selWhen;
      else if (type === 'priority') arr = selPriority;
      else return;

      toggle(arr, val);
      var isChecked = arr.indexOf(val) !== -1;

      if (box) {
        box.classList.toggle('checked', isChecked);
        box.style.background   = isChecked && color ? color : '';
        box.style.borderColor  = isChecked && color ? color : '';
      }
      if (iconEl) iconEl.style.display = isChecked ? 'flex' : 'none';

      updateFilterBadge();
      updateClearBtn();
      updateResults();
    });
  });

  document.querySelectorAll('.filter-chip').forEach(function(chip){
    chip.addEventListener('click', function(){
      var val = chip.dataset.val;
      toggle(selSensation, val);
      chip.classList.toggle('checked', selSensation.indexOf(val) !== -1);
      updateFilterBadge();
      updateClearBtn();
      updateResults();
    });
  });

  document.querySelectorAll('.clear-filters-btn').forEach(function(btn){
    btn.addEventListener('click', clearFilters);
  });

  document.getElementById('clear-empty-btn').addEventListener('click', clearFilters);

  function clearFilters(){
    selZones = []; selWhen = []; selPriority = []; selSensation = [];
    clearAllCheckboxes();
    updateFilterBadge();
    updateResults();
  }

  function updateClearBtn(){
    var count = selZones.length + selWhen.length + selPriority.length + selSensation.length;
    document.querySelectorAll('.clear-filters-btn').forEach(function(b){
      b.style.display = count > 0 ? 'inline-block' : 'none';
      b.textContent = 'Limpiar (' + count + ')';
    });
  }

  // ── Mobile filters ──
  var mobileBtn     = document.getElementById('mobile-filter-btn');
  var mobileOverlay = document.getElementById('mobile-filter-overlay');
  var mobilePanel   = document.getElementById('mobile-filter-panel');
  var closeMobile   = document.getElementById('close-mobile-filters');

  function openMobile(){
    mobileOverlay.style.display = 'block';
    mobilePanel.style.display   = 'block';
  }
  function closeMobilePanel(){
    mobileOverlay.style.display = 'none';
    mobilePanel.style.display   = 'none';
  }

  mobileBtn.addEventListener('click', openMobile);
  mobileOverlay.addEventListener('click', closeMobilePanel);
  closeMobile.addEventListener('click', closeMobilePanel);

  // ── Results filtering ──
  function updateResults(){
    var cards = resultsList.querySelectorAll('.result-card');
    var q = currentQuery.toLowerCase();
    var visible = [];

    cards.forEach(function(card){
      var zones      = JSON.parse(card.dataset.zones      || '[]');
      var when       = JSON.parse(card.dataset.when       || '[]');
      var sensations = JSON.parse(card.dataset.sensations || '[]');
      var tags       = JSON.parse(card.dataset.tags       || '[]');

      var matchQ = !q ||
        card.dataset.title.includes(q) ||
        card.dataset.desc.includes(q)  ||
        card.dataset.system.includes(q)||
        tags.some(function(t){ return t.toLowerCase().includes(q); });

      var matchZone     = selZones.length === 0     || zones.some(function(z){ return selZones.includes(z); });
      var matchWhen     = selWhen.length === 0       || when.some(function(w){ return selWhen.includes(w); });
      var matchPriority = selPriority.length === 0   || selPriority.includes(card.dataset.priority);
      var matchSensation= selSensation.length === 0  || sensations.some(function(s){ return selSensation.includes(s); });

      var show = matchQ && matchZone && matchWhen && matchPriority && matchSensation;
      card.style.display = show ? '' : 'none';
      if (show) visible.push(card);
    });

    emptyResults.style.display = visible.length === 0 ? 'flex' : 'none';
    // Update status text
    var afc = selZones.length + selWhen.length + selPriority.length + selSensation.length;
    if (q) {
      resultHeading.innerHTML = 'Resultados para <span style="color:var(--accent)">\"' + escHtml(currentQuery) + '\"</span>';
      resultSub.textContent = visible.length + ' coincidencia' + (visible.length !== 1 ? 's' : '') + ' encontrada' + (visible.length !== 1 ? 's' : '') +
        (afc > 0 ? ' · ' + afc + ' filtro' + (afc !== 1 ? 's' : '') + ' aplicado' + (afc !== 1 ? 's' : '') : '');
    } else {
      resultHeading.innerHTML = 'Síntomas frecuentes' + (afc > 0 ? ' <span style="color:var(--accent)">· ' + afc + ' filtro' + (afc !== 1 ? 's' : '') + '</span>' : '');
      resultSub.textContent = visible.length + ' resultado' + (visible.length !== 1 ? 's' : '') + ' disponible' + (visible.length !== 1 ? 's' : '');
    }
  }

  function escHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  // ── Navigate on card/button click ──
  resultsList.addEventListener('click', function(e){
    var card = e.target.closest('[data-result-id]');
    if (card && card.dataset.resultId) {
      window.location.href = 'resultado.php?id=' + encodeURIComponent(card.dataset.resultId);
    }
  });

  updateResults();
})();
</script>

<?php require 'includes/footer.php'; ?>
