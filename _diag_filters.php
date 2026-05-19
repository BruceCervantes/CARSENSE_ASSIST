<?php
// Filter panel partial — included twice (desktop sidebar + mobile panel)
// Vars from parent: $filterZones, $filterWhen, $filterPriority, $filterSensation, $priorityColor
?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <div class="filter-title">
    <span style="color:var(--accent)"><?= icon('filter', 12) ?></span>
    <span>Filtros</span>
  </div>
  <button class="clear-filters-btn" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font);display:none">
    Limpiar (0)
  </button>
</div>

<!-- Zona del vehículo -->
<div class="filter-group">
  <div class="filter-group-label">Zona del vehículo</div>
  <?php foreach ($filterZones as $z): ?>
    <label class="filter-check" data-type="zone" data-val="<?= htmlspecialchars($z) ?>">
      <div class="filter-check-box">
        <span class="check-icon" style="display:none;color:#fff;align-items:center;justify-content:center"><?= icon('check', 9) ?></span>
      </div>
      <span class="filter-check-label"><?= htmlspecialchars($z) ?></span>
    </label>
  <?php endforeach; ?>
</div>

<!-- Cuando ocurre -->
<div class="filter-group">
  <div class="filter-group-label">Cuando ocurre</div>
  <?php foreach ($filterWhen as $w): ?>
    <label class="filter-check" data-type="when" data-val="<?= htmlspecialchars($w) ?>">
      <div class="filter-check-box">
        <span class="check-icon" style="display:none;color:#fff;align-items:center;justify-content:center"><?= icon('check', 9) ?></span>
      </div>
      <span class="filter-check-label"><?= htmlspecialchars($w) ?></span>
    </label>
  <?php endforeach; ?>
</div>

<!-- Prioridad -->
<div class="filter-group">
  <div class="filter-group-label">Prioridad</div>
  <?php foreach ($filterPriority as $p):
    $pc = $priorityColor[$p] ?? '#888';
  ?>
    <label class="filter-check" data-type="priority" data-val="<?= htmlspecialchars($p) ?>" data-color="<?= $pc ?>">
      <div class="filter-check-box">
        <span class="check-icon" style="display:none;color:#fff;align-items:center;justify-content:center"><?= icon('check', 9) ?></span>
      </div>
      <span class="filter-check-label"><?= htmlspecialchars($p) ?></span>
      <span style="margin-left:auto;width:6px;height:6px;border-radius:50%;background:<?= $pc ?>;display:inline-block;flex-shrink:0"></span>
    </label>
  <?php endforeach; ?>
</div>

<!-- Sensación -->
<div class="filter-group">
  <div class="filter-group-label">Sensación</div>
  <div class="filter-chip-group">
    <?php foreach ($filterSensation as $s): ?>
      <button class="filter-chip" data-type="sensation" data-val="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></button>
    <?php endforeach; ?>
  </div>
</div>

<button class="clear-filters-btn btn btn-ghost btn-sm" style="width:100%;justify-content:center;margin-top:8px;display:none">
  Limpiar filtros
</button>
