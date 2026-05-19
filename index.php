<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/System.php';

$systems = System::getAll();

$symptoms = [
    ['id'=>1,'icon'=>'settings',   'title'=>'Ruido al Frenar',        'desc'=>'Chirridos, rechinidos o golpes al aplicar los frenos.',             'system'=>'Sistema de Frenos',      'priority'=>'alta', 'highlight'=>false],
    ['id'=>2,'icon'=>'activity',   'title'=>'Vibración en Dirección',  'desc'=>'El volante tiembla o vibra a ciertas velocidades al frenar.',       'system'=>'Suspensión / Frenos',    'priority'=>'media','highlight'=>true ],
    ['id'=>3,'icon'=>'thermometer','title'=>'Motor Recalentado',       'desc'=>'Indicador de temperatura sube o hay vapor bajo el cofre.',          'system'=>'Sistema de Enfriamiento','priority'=>'alta', 'highlight'=>false],
    ['id'=>4,'icon'=>'settings',   'title'=>'Pérdida de Potencia',     'desc'=>'El auto siente lento, no sube bien o falla al subir pendientes.',   'system'=>'Motor / Combustible',    'priority'=>'media','highlight'=>false],
    ['id'=>5,'icon'=>'zap',        'title'=>'Luz de Check Engine',     'desc'=>'Se encendió el indicador de error o falla en el tablero.',          'system'=>'Sistema Eléctrico',      'priority'=>'baja', 'highlight'=>false],
    ['id'=>6,'icon'=>'wind',       'title'=>'Olor a Quemado',          'desc'=>'Olor inusual a quemado, aceite o combustible mientras conduce.',    'system'=>'Motor / Frenos',         'priority'=>'alta', 'highlight'=>false],
];

$priorityColor = ['alta' => '#e03030', 'media' => '#f59e0b', 'baja' => '#10b981'];
$pageTitle     = 'CarSense — Tu asistente automotriz';
$currentPage   = 'home';
$breadcrumbs   = [];
require 'includes/header.php';
?>

<!-- Hero -->
<section class="hero">
  <div class="hero-glow"></div>
  <div class="hero-grid"></div>
  <div class="hero-inner">

    <!-- Text -->
    <div style="flex:1;max-width:480px">
      <h1 class="hero-h1 animate-up delay-1">Entiende tu auto,</h1>
      <h1 class="hero-h1 hero-h1-accent animate-up delay-2">toma mejores decisiones.</h1>
      <p class="hero-desc animate-up delay-3">
        Describe el síntoma con tus propias palabras. La inteligencia artificial analiza el problema y te orienta con explicaciones claras, sin tecnicismos.
      </p>
      <div class="hero-ctas animate-up delay-4">
        <a href="diagnostico.php" class="btn btn-primary">
          Diagnosticar ahora <?= icon('arrowRight', 14) ?>
        </a>
        <a href="sistemas.php" class="btn btn-outline">
          Ver sistemas del auto
        </a>
      </div>
      <div class="hero-systems animate-up delay-5">
        <?php foreach ($systems as $s): ?>
          <a href="sistema.php?id=<?= urlencode($s['slug']) ?>" class="system-pill"
             style="background:<?= htmlspecialchars($s['color']) ?>22;border:1px solid <?= htmlspecialchars($s['color']) ?>44;color:<?= htmlspecialchars($s['color']) ?>">
            <span class="system-pill-dot" style="background:<?= htmlspecialchars($s['color']) ?>"></span>
            <?= htmlspecialchars($s['name']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Car diagram -->
    <div class="hero-diagram animate-up delay-3">
      <div class="diagram-panel" style="padding:0;background:linear-gradient(160deg,var(--bg3) 0%,var(--bg4) 100%)">
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.022) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.022) 1px,transparent 1px);background-size:32px 32px;pointer-events:none"></div>
        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:65%;height:56px;background:radial-gradient(ellipse,rgba(224,48,48,0.22) 0%,transparent 70%);pointer-events:none"></div>
        <img class="theme-img"
             data-dark="SistemaNegro.png" data-light="SistemaBlanco.png"
             alt="Sistemas del vehículo"
             style="width:100%;height:auto;display:block;position:relative;z-index:1">
      </div>
    </div>

  </div>
</section>

<!-- Stats bar -->
<section class="stats-bar">
  <div class="stats-inner">
    <?php foreach ([
      ['Diagnósticos realizados', '18k+'],
      ['Usuarios activos', '2,400+'],
      ['Sistemas cubiertos', (string)count($systems)],
      ['Precisión IA', '94%'],
    ] as [$label, $value]): ?>
      <div class="stat-item">
        <span class="stat-value"><?= $value ?></span>
        <span class="stat-label"><?= $label ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Frequent symptoms -->
<section class="symptoms-section">
  <div class="symptoms-inner">
    <div class="symptoms-header">
      <h2 class="section-title">
        <span class="section-title-bar"></span>
        SÍNTOMAS FRECUENTES
      </h2>
      <a href="diagnostico.php" style="color:var(--accent);font-size:12px;display:flex;align-items:center;gap:4px">
        Ver Todos <?= icon('chevronRight', 13) ?>
      </a>
    </div>

    <div class="symptoms-grid">
      <?php foreach ($symptoms as $s): ?>
        <?php $color = $priorityColor[$s['priority']] ?? '#888'; ?>
        <a href="resultado.php?id=<?= $s['id'] ?>" class="symptom-card">
          <div class="symptom-icon" style="background:<?= $color ?>22">
            <span style="color:<?= $color ?>"><?= icon($s['icon'], 15) ?></span>
          </div>
          <div style="flex:1;min-width:0">
            <div class="symptom-title"><?= htmlspecialchars($s['title']) ?></div>
            <p class="symptom-desc"><?= htmlspecialchars($s['desc']) ?></p>
            <div class="symptom-footer">
              <span class="tag"><?= htmlspecialchars($s['system']) ?></span>
              <div style="display:flex;align-items:center;gap:4px">
                <span class="severity-dot" style="background:<?= $color ?>"></span>
                <span style="font-size:12px;color:<?= $color ?>"><?= ucfirst($s['priority']) ?></span>
              </div>
            </div>
          </div>
          <div class="symptom-arrow"><?= icon('arrowRight', 14) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
