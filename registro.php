<?php
session_start();
if (!empty($_SESSION['user'])) { header('Location: index.php'); exit; }

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/User.php';
require_once 'api/models/Vehicle.php';

$error     = '';
$errorStep = 1;
$postName  = '';
$postEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']          ?? '');
    $email       = trim($_POST['email']         ?? '');
    $password    =      $_POST['password']      ?? '';
    $skipVehicle = !empty($_POST['skip_vehicle']);
    $postName    = $name;
    $postEmail   = $email;

    if (!$name || !$email || !$password) {
        $error = 'Completa todos los campos.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            if (User::findByEmail($email)) {
                $error = 'El correo ya está registrado. <a href="login.php" style="color:var(--accent)">Inicia sesión</a>.';
            } else {
                $newUser = User::create($name, $email, $password);
                if (!$skipVehicle) {
                    $brand = trim($_POST['vehicle_brand'] ?? '');
                    $model = trim($_POST['vehicle_model'] ?? '');
                    $year  = trim($_POST['vehicle_year']  ?? '');
                    $km    = trim($_POST['vehicle_km']    ?? '0');
                    if ($brand && $model) {
                        Vehicle::create([
                            'user_id' => $newUser['id'],
                            'brand'   => $brand,
                            'model'   => $model,
                            'year'    => $year,
                            'km'      => $km ?: '0',
                        ]);
                    }
                }
                $_SESSION['user'] = $newUser;
                header('Location: index.php');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Error al crear la cuenta. Intenta de nuevo.';
        }
    }
}

$logoSVG = '<svg width="32" height="32" viewBox="0 0 40 40" fill="none">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <path d="M14.5 21 L15 18.5 L25 18.5 L25.5 21 Z" fill="#0a0a0e" fill-opacity="0.6"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
  <path d="M10 27 L14 27 L15.5 24.5 L17 29 L20 25 L22 27 L30 27" stroke="#e03030" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" stroke-opacity="0.85"/>
</svg>';

$vehicleBrands = ['Toyota','Honda','Nissan','Chevrolet','Ford','Volkswagen','Hyundai','Kia','Mazda'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Crear cuenta — CarSense</title>
  <link rel="stylesheet" href="css/styles.css"/>
  <script>(function(){if(localStorage.getItem('theme')==='light')document.documentElement.classList.add('light');})();</script>
</head>
<body>
<div class="auth-page">
  <!-- Left panel -->
  <div class="auth-left" style="width:42%">
    <div class="auth-left-glow" style="background:radial-gradient(circle at 30% 50%, rgba(224,48,48,0.16) 0%, transparent 65%)"></div>
    <div class="auth-left-grid"></div>
    <div class="auth-left-content">
      <a href="index.php" style="display:flex;align-items:center;gap:8px;text-decoration:none">
        <?= $logoSVG ?>
        <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
      </a>
      <div>
        <h2 style="color:#fff;font-size:1.8rem;line-height:1.2;margin-bottom:12px">
          Empieza a conocer<br><span style="color:var(--accent)">tu vehículo.</span>
        </h2>
        <p style="color:var(--muted);font-size:13px;line-height:1.7;margin-bottom:28px">
          Crea tu cuenta en segundos y accede a diagnósticos con IA, historial y recordatorios.
        </p>
        <div style="display:flex;flex-direction:column;gap:12px">
          <?php foreach ([
            [1, 'Crea tu cuenta'],
            [2, 'Registra tu vehículo'],
            [3, '¡Listo para diagnosticar!'],
          ] as [$n, $label]): ?>
            <div class="step-indicator" data-step="<?= $n ?>" style="display:flex;align-items:center;gap:12px">
              <div class="step-dot" style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">
                <?= $n ?>
              </div>
              <span class="step-label" style="font-size:13px"><?= $label ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <?php foreach ([['2,400+','Usuarios activos'],['18k+','Diagnósticos realizados']] as [$v, $l]): ?>
          <div class="card" style="text-align:center;padding:16px">
            <div style="color:var(--accent);font-size:1.2rem;font-weight:700;margin-bottom:4px"><?= $v ?></div>
            <div style="color:var(--muted2);font-size:11px"><?= $l ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Right form -->
  <div class="auth-right">
    <a href="index.php" style="display:flex;align-items:center;gap:8px;margin-bottom:40px;text-decoration:none" id="mobile-logo">
      <?= $logoSVG ?>
      <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
    </a>
    <div class="auth-form-container">
      <!-- Step progress bars -->
      <div style="display:flex;gap:8px;margin-bottom:20px">
        <div class="step-bar" style="height:4px;flex:1;border-radius:4px"></div>
        <div class="step-bar" style="height:4px;flex:1;border-radius:4px"></div>
      </div>

      <?php if ($error): ?>
        <div class="error-box" id="error-box" style="margin-bottom:16px"><p><?= $error ?></p></div>
      <?php else: ?>
        <div class="error-box" id="error-box" style="display:none;margin-bottom:16px"><p id="error-text"></p></div>
      <?php endif; ?>

      <form method="post" action="registro.php" id="reg-form">
        <!-- Hidden vehicle skip flag -->
        <input type="hidden" name="skip_vehicle" id="skip-vehicle-input" value=""/>

        <!-- ── STEP 1 ── -->
        <div id="step-1-wrapper">
          <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Crea tu cuenta</h1>
          <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
            ¿Ya tienes cuenta? <a href="login.php" style="color:var(--accent)">Inicia sesión</a>
          </p>
          <div style="display:flex;flex-direction:column;gap:14px">
            <div>
              <label class="form-label">Nombre completo</label>
              <div class="input-wrap">
                <span class="input-icon"><?= icon('user', 14) ?></span>
                <input type="text" name="name" id="r-name" placeholder="Tu nombre" value="<?= htmlspecialchars($postName) ?>"/>
              </div>
            </div>
            <div>
              <label class="form-label">Correo electrónico</label>
              <div class="input-wrap">
                <span class="input-icon"><?= icon('mail', 14) ?></span>
                <input type="email" name="email" id="r-email" placeholder="tucorreo@ejemplo.com" value="<?= htmlspecialchars($postEmail) ?>" autocomplete="email"/>
              </div>
            </div>
            <div>
              <label class="form-label">Contraseña</label>
              <div class="input-wrap">
                <span class="input-icon"><?= icon('lock', 14) ?></span>
                <input type="password" name="password" id="r-pass" placeholder="Mínimo 6 caracteres"/>
                <button type="button" id="r-toggle-pass" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:flex">
                  <?= icon('eye', 14) ?>
                </button>
              </div>
              <div id="pw-strength"></div>
            </div>
            <div>
              <label class="form-label">Confirmar contraseña</label>
              <div class="input-wrap">
                <span class="input-icon"><?= icon('lock', 14) ?></span>
                <input type="password" id="r-confirm" placeholder="Repite la contraseña"/>
                <span id="confirm-check"></span>
              </div>
            </div>
            <button type="button" id="next-step-btn" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px">
              Continuar <?= icon('arrowRight', 14) ?>
            </button>
          </div>
        </div>

        <!-- ── STEP 2 ── -->
        <div id="step-2-wrapper" style="display:none">
          <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Tu vehículo</h1>
          <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
            Registra tu auto para diagnósticos personalizados. Puedes omitir este paso.
          </p>
          <div id="vehicle-fields" style="display:flex;flex-direction:column;gap:14px">
            <div>
              <label class="form-label">Marca</label>
              <input type="hidden" name="vehicle_brand" id="vehicle-brand-input" value=""/>
              <div class="brand-grid">
                <?php foreach ($vehicleBrands as $brand): ?>
                  <button type="button" class="brand-btn" data-brand="<?= $brand ?>"><?= $brand ?></button>
                <?php endforeach; ?>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div>
                <label class="form-label">Modelo</label>
                <input type="text" class="input-standalone" name="vehicle_model" id="r-model" placeholder="Ej: Corolla..."/>
              </div>
              <div>
                <label class="form-label">Año</label>
                <input type="text" class="input-standalone" name="vehicle_year" id="r-year" placeholder="Ej: 2018" maxlength="4"/>
              </div>
            </div>
            <div>
              <label class="form-label">Kilometraje actual</label>
              <input type="text" class="input-standalone" name="vehicle_km" id="r-km" placeholder="Ej: 67000"/>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;margin-top:16px">
            <button type="submit" id="reg-submit-btn" class="btn btn-primary" style="width:100%;justify-content:center">
              <span id="submit-label">Crear cuenta y guardar vehículo</span>
              <?= icon('arrowRight', 14) ?>
            </button>
            <button type="button" id="skip-vehicle-btn" style="width:100%;text-align:center;color:var(--muted2);font-size:12px;padding:4px;background:none;border:none;cursor:pointer;font-family:var(--font)">
              Omitir este paso →
            </button>
            <button type="button" id="back-step-btn" style="width:100%;text-align:center;color:var(--muted3);font-size:12px;padding:4px;background:none;border:none;cursor:pointer;font-family:var(--font)">
              ← Volver al paso anterior
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  if(localStorage.getItem('theme')==='light') document.body.classList.add('light');

  var currentStep = 1;
  var skipVehicle = false;

  var step1El = document.getElementById('step-1-wrapper');
  var step2El = document.getElementById('step-2-wrapper');
  var errorBox = document.getElementById('error-box');
  var errorText = document.getElementById('error-text');

  function showError(msg) {
    if (errorText) errorText.textContent = msg;
    if (errorBox) { errorBox.style.display = 'block'; errorBox.querySelector('p').textContent = msg; }
  }
  function clearError() {
    if (errorBox) errorBox.style.display = 'none';
  }

  function setStep(n) {
    currentStep = n;
    step1El.style.display = n === 1 ? 'block' : 'none';
    step2El.style.display = n === 2 ? 'block' : 'none';

    // Progress bars
    document.querySelectorAll('.step-bar').forEach(function(bar, i) {
      bar.style.background = (i + 1) <= n ? 'var(--accent)' : 'var(--bg3)';
    });

    // Left panel indicators
    document.querySelectorAll('.step-indicator').forEach(function(el) {
      var sn = parseInt(el.dataset.step);
      var dot = el.querySelector('.step-dot');
      var label = el.querySelector('.step-label');
      var active = sn <= n;
      dot.style.background   = active ? 'var(--accent)' : 'var(--bg3)';
      dot.style.border       = active ? 'none' : '1px solid var(--border)';
      dot.style.color        = active ? '#fff' : 'var(--muted3)';
      label.style.color      = active ? '#fff' : 'var(--muted3)';
    });
  }

  setStep(1);

  // Password toggle
  var toggleBtn = document.getElementById('r-toggle-pass');
  var passInput = document.getElementById('r-pass');
  var showPass  = false;
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function(){
      showPass = !showPass;
      passInput.type = showPass ? 'text' : 'password';
      toggleBtn.innerHTML = showPass
        ? '<svg width="14" height="14" style="display:inline-block;vertical-align:middle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
        : '<svg width="14" height="14" style="display:inline-block;vertical-align:middle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
  }

  // Password strength
  passInput.addEventListener('input', function(){
    var pw = passInput.value;
    var strength = pw.length === 0 ? 0 : pw.length < 6 ? 1 : pw.length < 10 ? 2 : 3;
    var sc = ['#505070','#e03030','#f59e0b','#10b981'][strength];
    var sl = ['','Débil','Media','Fuerte'][strength];
    var el = document.getElementById('pw-strength');
    if (!el) return;
    if (!pw) { el.innerHTML = ''; return; }
    var segs = [1,2,3].map(function(i){
      return '<div class="pw-strength-segment" style="background:' + (i <= strength ? sc : 'var(--bg3)') + '"></div>';
    }).join('');
    el.innerHTML = '<div style="display:flex;align-items:center;gap:8px;margin-top:6px">'
      + '<div class="pw-strength-bar">' + segs + '</div>'
      + '<span style="font-size:11px;color:' + sc + '">' + sl + '</span></div>';

    _updateConfirmCheck();
  });

  // Confirm password check
  var confirmInput = document.getElementById('r-confirm');
  confirmInput.addEventListener('input', _updateConfirmCheck);

  function _updateConfirmCheck() {
    var el = document.getElementById('confirm-check');
    if (!el) return;
    var match = confirmInput.value && passInput.value === confirmInput.value;
    el.innerHTML = match
      ? '<svg width="14" height="14" style="display:inline-block;vertical-align:middle;color:var(--green)" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
      : '';
  }

  // Step 1 → Step 2
  document.getElementById('next-step-btn').addEventListener('click', function(){
    clearError();
    var name    = document.getElementById('r-name').value.trim();
    var email   = document.getElementById('r-email').value.trim();
    var pass    = document.getElementById('r-pass').value;
    var confirm = document.getElementById('r-confirm').value;

    if (!name || !email || !pass) { showError('Completa todos los campos.'); return; }
    if (pass !== confirm)         { showError('Las contraseñas no coinciden.'); return; }
    if (pass.length < 6)          { showError('La contraseña debe tener al menos 6 caracteres.'); return; }

    setStep(2);
  });

  // Brand selection
  var selectedBrand = '';
  document.querySelectorAll('.brand-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      selectedBrand = btn.dataset.brand;
      document.getElementById('vehicle-brand-input').value = selectedBrand;
      document.querySelectorAll('.brand-btn').forEach(function(b){
        b.classList.toggle('selected', b.dataset.brand === selectedBrand);
      });
    });
  });

  // Skip vehicle toggle
  document.getElementById('skip-vehicle-btn').addEventListener('click', function(){
    skipVehicle = !skipVehicle;
    document.getElementById('skip-vehicle-input').value = skipVehicle ? '1' : '';
    document.getElementById('vehicle-fields').style.display = skipVehicle ? 'none' : 'flex';
    document.getElementById('submit-label').textContent = skipVehicle ? 'Crear cuenta' : 'Crear cuenta y guardar vehículo';
    this.textContent = skipVehicle ? '← Agregar mi vehículo' : 'Omitir este paso →';
  });

  // Back to step 1
  document.getElementById('back-step-btn').addEventListener('click', function(){
    clearError();
    setStep(1);
  });

  // Mobile logo
  var mobileLogo = document.getElementById('mobile-logo');
  if (mobileLogo) {
    var mq = window.matchMedia('(min-width:1024px)');
    mobileLogo.style.display = mq.matches ? 'none' : 'flex';
    mq.addEventListener('change', function(e){ mobileLogo.style.display = e.matches ? 'none' : 'flex'; });
  }
})();
</script>
</body>
</html>
