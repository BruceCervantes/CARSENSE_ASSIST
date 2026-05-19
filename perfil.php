<?php
require_once 'includes/auth_check.php';
require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/Vehicle.php';
require_once 'api/models/ConsultationHistory.php';

$userId = (int)$user['id'];

$rawVehicles      = Vehicle::getByUser($userId);
$rawConsultations = ConsultationHistory::getByUser($userId);

function dateLabel(string $createdAt): string {
    $date = new DateTime($createdAt);
    $now  = new DateTime();
    $days = (int)$date->diff($now)->days;
    return $days === 0 ? 'hoy' : ($days === 1 ? 'ayer' : "hace {$days} días");
}

$severityColor = ['alta' => '#e03030', 'media' => '#f59e0b', 'baja' => '#10b981'];
$accentColors  = ['#e03030','#3b82f6','#f59e0b','#10b981','#f97316','#06b6d4'];
$vehicleBrands = ['Toyota','Honda','Nissan','Chevrolet','Ford','Volkswagen','Hyundai','Kia','Mazda','Renault','Otro'];
$vehicleColors = ['Blanco','Negro','Gris','Rojo','Azul','Verde','Plateado','Otro'];

$activeVehicle = null;
foreach ($rawVehicles as $v) {
    if ((int)$v['is_active'] === 1) { $activeVehicle = $v; break; }
}
if (!$activeVehicle && !empty($rawVehicles)) {
    $activeVehicle = $rawVehicles[0];
}

$pageTitle   = 'Mi perfil — CarSense';
$currentPage = 'perfil';
$breadcrumbs = [['label'=>'Perfil','url'=>null]];
require 'includes/header.php';
?>

<div class="perfil-page">
  <h1 style="font-size:1.25rem;color:#fff;margin-bottom:20px">Mi perfil</h1>

  <div class="perfil-grid">

    <!-- Left column -->
    <div style="display:flex;flex-direction:column;gap:16px">

      <!-- User card -->
      <div class="card" style="display:flex;align-items:center;gap:16px">
        <div class="user-avatar" style="width:56px;height:56px;font-size:1.2rem;font-weight:700;flex-shrink:0">
          <?= htmlspecialchars($user['initials']) ?>
        </div>
        <div style="flex:1">
          <div style="color:#fff;font-size:1rem;margin-bottom:2px"><?= htmlspecialchars($user['name']) ?></div>
          <div style="color:var(--muted);font-size:12px"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <button class="btn btn-ghost btn-xs"><?= icon('edit', 12) ?> Editar</button>
      </div>

      <!-- Vehicles -->
      <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Mis Vehículos</div>
          <button id="add-vehicle-btn" class="btn btn-primary btn-xs" style="display:inline-flex">
            <?= icon('plus', 12) ?> Agregar
          </button>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php if (empty($rawVehicles)): ?>
            <p style="color:var(--muted2);font-size:13px">No tienes vehículos registrados.</p>
          <?php endif; ?>
          <?php foreach ($rawVehicles as $v):
            $ac = htmlspecialchars($v['accent_color'] ?: '#e03030');
            $isActive = (int)$v['is_active'] === 1;
          ?>
            <div class="vehicle-card <?= $isActive ? 'active' : '' ?>" data-vid="<?= (int)$v['id'] ?>" style="cursor:pointer">
              <div style="width:36px;height:36px;border-radius:8px;background:<?= $ac ?>22;border:1px solid <?= $ac ?>40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:<?= $ac ?>"><?= icon('car', 16) ?></span>
              </div>
              <div style="flex:1;min-width:0">
                <div style="color:#fff;font-size:13px"><?= htmlspecialchars($v['brand'] . ' ' . $v['model'] . ' ' . $v['year']) ?></div>
                <div style="color:var(--muted2);font-size:11px;margin-top:2px"><?= htmlspecialchars($v['km']) ?> km · <?= htmlspecialchars($v['plate']) ?></div>
              </div>
              <?php if ($isActive): ?>
                <span style="font-size:10px;color:var(--green);background:rgba(16,185,129,0.15);padding:2px 8px;border-radius:9999px;border:1px solid rgba(16,185,129,0.3);white-space:nowrap">Activo</span>
              <?php endif; ?>
              <button class="icon-btn danger" data-delete-vid="<?= (int)$v['id'] ?>" style="display:flex">
                <?= icon('trash', 12) ?>
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- Right column -->
    <div style="display:flex;flex-direction:column;gap:16px">

      <!-- Active vehicle detail -->
      <?php if ($activeVehicle): ?>
        <?php $ac = htmlspecialchars($activeVehicle['accent_color'] ?: '#e03030'); ?>
        <div class="card" style="border-color:<?= $ac ?>40">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div style="width:44px;height:44px;border-radius:10px;background:<?= $ac ?>22;border:1px solid <?= $ac ?>40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <span style="color:<?= $ac ?>"><?= icon('car', 20) ?></span>
            </div>
            <div>
              <div style="color:#fff;font-size:1rem"><?= htmlspecialchars($activeVehicle['brand'] . ' ' . $activeVehicle['model']) ?></div>
              <div style="color:var(--muted);font-size:12px"><?= htmlspecialchars($activeVehicle['year'] . ' · ' . $activeVehicle['color']) ?></div>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
            <?php foreach ([
              ['label'=>'Kilometraje', 'value'=>$activeVehicle['km'].' km',            'icon'=>'activity'],
              ['label'=>'Placa',       'value'=>$activeVehicle['plate'],                'icon'=>'settings'],
              ['label'=>'Diagnósticos','value'=>count($rawConsultations).' realizados', 'icon'=>'search'],
            ] as $stat): ?>
              <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:12px;text-align:center">
                <span style="color:var(--muted2)"><?= icon($stat['icon'], 14) ?></span>
                <div style="color:#fff;font-size:13px;margin-top:6px;font-weight:600"><?= htmlspecialchars($stat['value']) ?></div>
                <div style="color:var(--muted2);font-size:10px;margin-top:2px"><?= htmlspecialchars($stat['label']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Consultation history -->
      <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Historial de consultas</div>
          <a href="diagnostico.php" style="color:var(--accent);font-size:11px">Nueva consulta</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php if (empty($rawConsultations)): ?>
            <p style="color:var(--muted2);font-size:13px">Sin consultas aún.</p>
          <?php endif; ?>
          <?php foreach ($rawConsultations as $c):
            $sc = $severityColor[$c['severity']] ?? '#888';
            $dl = dateLabel($c['created_at']);
          ?>
            <a href="resultado.php?id=<?= urlencode($c['result_slug']) ?>"
               style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--bg3);border:1px solid var(--border);border-radius:10px;text-decoration:none;color:inherit;transition:background 0.15s"
               onmouseover="this.style.background='#1a1a22'" onmouseout="this.style.background='var(--bg3)'">
              <div style="width:8px;height:8px;border-radius:50%;background:<?= $sc ?>;flex-shrink:0"></div>
              <div style="flex:1;min-width:0">
                <div style="color:#fff;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($c['title']) ?></div>
                <div style="color:var(--muted2);font-size:11px;margin-top:2px"><?= htmlspecialchars($c['system_name']) ?> · <?= $dl ?></div>
              </div>
              <span style="color:var(--muted2)"><?= icon('chevronRight', 13) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Add vehicle modal (hidden by default) -->
  <div class="modal-overlay" id="modal-overlay" style="display:none">
    <div class="modal">
      <div class="modal-header">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:36px;height:36px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center">
            <span style="color:var(--accent)"><?= icon('car', 16) ?></span>
          </div>
          <div>
            <h2 style="color:#fff;font-size:15px">Agregar vehículo</h2>
            <p style="color:var(--muted2);font-size:11px">Registra tu auto para diagnósticos personalizados</p>
          </div>
        </div>
        <button id="close-modal" class="icon-btn" style="display:flex"><?= icon('x', 15) ?></button>
      </div>
      <div class="modal-body">
        <form id="add-vehicle-form" style="display:flex;flex-direction:column;gap:14px">
          <div>
            <label class="form-label">Marca *</label>
            <select class="input-standalone" id="av-brand">
              <option value="">Selecciona una marca</option>
              <?php foreach ($vehicleBrands as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
              <label class="form-label">Modelo *</label>
              <input class="input-standalone" id="av-model" placeholder="Ej: Corolla"/>
            </div>
            <div>
              <label class="form-label">Año *</label>
              <input class="input-standalone" id="av-year" placeholder="Ej: 2018" maxlength="4"/>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
              <label class="form-label">Kilometraje</label>
              <input class="input-standalone" id="av-km" placeholder="Ej: 45000"/>
            </div>
            <div>
              <label class="form-label">Placa</label>
              <input class="input-standalone" id="av-plate" placeholder="Ej: ABC-123"/>
            </div>
          </div>
          <div>
            <label class="form-label">Color</label>
            <select class="input-standalone" id="av-color">
              <option value="">Selecciona color</option>
              <?php foreach ($vehicleColors as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div id="modal-error" class="error-box" style="display:none"><p></p></div>
          <div style="display:flex;gap:10px;margin-top:4px">
            <button type="button" id="cancel-modal" class="btn btn-ghost" style="flex:1;justify-content:center">Cancelar</button>
            <button type="submit" id="submit-vehicle" class="btn btn-primary" style="flex:1;justify-content:center">Agregar vehículo</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var userId     = <?= $userId ?>;
  var vehicleCount = <?= count($rawVehicles) ?>;
  var accentColors = ['#e03030','#3b82f6','#f59e0b','#10b981','#f97316','#06b6d4'];

  var overlay    = document.getElementById('modal-overlay');
  var addBtn     = document.getElementById('add-vehicle-btn');
  var closeBtn   = document.getElementById('close-modal');
  var cancelBtn  = document.getElementById('cancel-modal');
  var modalError = document.getElementById('modal-error');
  var submitBtn  = document.getElementById('submit-vehicle');

  function openModal(){  overlay.style.display = 'flex'; }
  function closeModal(){ overlay.style.display = 'none'; }

  addBtn.addEventListener('click', openModal);
  closeBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function(e){ if (e.target === overlay) closeModal(); });

  async function apiCall(url, method, body) {
    var opts = { method: method, headers: { 'Content-Type': 'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    var resp = await fetch(url, opts);
    var json = await resp.json();
    if (!json.success) throw new Error(json.error || 'API error');
    return json.data;
  }

  // Add vehicle
  document.getElementById('add-vehicle-form').addEventListener('submit', async function(e){
    e.preventDefault();
    var brand = document.getElementById('av-brand').value;
    var model = document.getElementById('av-model').value.trim();
    var year  = document.getElementById('av-year').value.trim();

    if (!brand || !model || !year) {
      modalError.style.display = 'block';
      modalError.querySelector('p').textContent = 'Marca, modelo y año son obligatorios.';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';

    try {
      await apiCall('api/endpoints/create.php', 'POST', {
        resource: 'vehicle',
        user_id: userId,
        brand: brand,
        model: model,
        year: year,
        km:    document.getElementById('av-km').value || '0',
        plate: document.getElementById('av-plate').value || '-',
        color: document.getElementById('av-color').value || 'Sin especificar',
        accent_color: accentColors[vehicleCount % accentColors.length],
      });
      window.location.reload();
    } catch (err) {
      modalError.style.display = 'block';
      modalError.querySelector('p').textContent = err.message || 'Error al guardar el vehículo.';
      submitBtn.disabled = false;
      submitBtn.textContent = 'Agregar vehículo';
    }
  });

  // Set active vehicle (click on card)
  document.querySelectorAll('.vehicle-card[data-vid]').forEach(function(card){
    card.addEventListener('click', async function(e){
      if (e.target.closest('[data-delete-vid]')) return;
      var vid = parseInt(card.dataset.vid);
      try {
        await apiCall('api/endpoints/update.php', 'PUT', { resource: 'vehicle_active', user_id: userId, vehicle_id: vid });
        window.location.reload();
      } catch(err) { console.error(err); }
    });
  });

  // Delete vehicle
  document.querySelectorAll('[data-delete-vid]').forEach(function(btn){
    btn.addEventListener('click', async function(e){
      e.stopPropagation();
      var vid = parseInt(btn.dataset.deleteVid);
      try {
        await apiCall('api/endpoints/delete.php?resource=vehicle&id=' + vid + '&user_id=' + userId, 'DELETE');
        window.location.reload();
      } catch(err) { console.error(err); }
    });
  });
})();
</script>

<?php require 'includes/footer.php'; ?>
