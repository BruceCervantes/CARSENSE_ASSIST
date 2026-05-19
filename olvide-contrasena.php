<?php
session_start();
if (!empty($_SESSION['user'])) { header('Location: index.php'); exit; }
require_once 'includes/icons.php';

$logoSVG = '<svg width="32" height="32" viewBox="0 0 40 40" fill="none">
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
  <title>Recuperar contraseña — CarSense</title>
  <link rel="stylesheet" href="css/styles.css"/>
  <script>(function(){if(localStorage.getItem('theme')==='light')document.documentElement.classList.add('light');})();</script>
</head>
<body>
<div style="min-height:100vh;background:var(--bg);display:flex;align-items:center;justify-content:center;padding:32px;font-family:var(--font)">
  <div style="width:100%;max-width:380px">

    <a href="login.php" style="display:flex;align-items:center;gap:8px;margin-bottom:40px;text-decoration:none">
      <?= $logoSVG ?>
      <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
    </a>

    <div id="error-box" class="error-box" style="display:none;margin-bottom:16px"><p id="error-text"></p></div>

    <!-- Step: email -->
    <div id="step-email">
      <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">¿Olvidaste tu contraseña?</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:28px">
        Ingresa tu correo y te enviaremos un código de verificación.
      </p>
      <form id="fp-form">
        <label class="form-label">Correo electrónico</label>
        <div class="input-wrap" style="margin-bottom:16px">
          <span class="input-icon"><?= icon('mail', 14) ?></span>
          <input type="email" id="fp-email" placeholder="tucorreo@ejemplo.com" autocomplete="email"/>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <span id="send-label">Enviar código <?= icon('arrowRight', 14) ?></span>
          <span id="send-spinner" style="display:none"><span class="spinner"></span> Enviando...</span>
        </button>
      </form>
      <div style="margin-top:20px;text-align:center">
        <a href="login.php" style="color:var(--muted2);font-size:12px;display:inline-flex;align-items:center;gap:4px">
          <?= icon('arrowLeft', 12) ?> Volver al inicio de sesión
        </a>
      </div>
    </div>

    <!-- Step: code -->
    <div id="step-code" style="display:none">
      <div style="width:48px;height:48px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
        <span style="color:var(--accent)"><?= icon('shieldCheck', 20) ?></span>
      </div>
      <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Verifica tu código</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
        Enviamos un código de 6 dígitos a <strong id="code-email-display" style="color:var(--text2)"></strong>.<br>
        <span style="color:var(--muted3);font-size:11px">(Usa 123456 para demo)</span>
      </p>
      <form id="fp-code-form">
        <div class="otp-inputs" style="margin-bottom:20px">
          <?php for ($i = 0; $i < 6; $i++): ?>
            <input class="otp-input" id="otp-<?= $i ?>" type="text" maxlength="1" inputmode="numeric"/>
          <?php endfor; ?>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <span id="verify-label">Verificar código <?= icon('arrowRight', 14) ?></span>
          <span id="verify-spinner" style="display:none"><span class="spinner"></span> Verificando...</span>
        </button>
      </form>
      <div style="margin-top:16px;text-align:center">
        <button id="resend-btn" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font)">
          ¿No recibiste el código? Reenviar
        </button>
      </div>
    </div>

    <!-- Step: reset -->
    <div id="step-reset" style="display:none">
      <div style="width:48px;height:48px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
        <span style="color:var(--accent)"><?= icon('keyRound', 20) ?></span>
      </div>
      <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Nueva contraseña</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:24px">Elige una contraseña segura de al menos 6 caracteres.</p>
      <form id="fp-reset-form" style="display:flex;flex-direction:column;gap:14px">
        <div>
          <label class="form-label">Nueva contraseña</label>
          <div class="input-wrap">
            <span class="input-icon"><?= icon('lock', 14) ?></span>
            <input type="password" id="fp-new-pass" placeholder="Mínimo 6 caracteres"/>
          </div>
        </div>
        <div>
          <label class="form-label">Confirmar contraseña</label>
          <div class="input-wrap">
            <span class="input-icon"><?= icon('lock', 14) ?></span>
            <input type="password" id="fp-confirm-pass" placeholder="Repite la contraseña"/>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
          <span id="save-label">Guardar contraseña <?= icon('arrowRight', 14) ?></span>
          <span id="save-spinner" style="display:none"><span class="spinner"></span> Guardando...</span>
        </button>
      </form>
    </div>

    <!-- Step: success -->
    <div id="step-success" style="display:none;text-align:center;padding:20px 0">
      <div style="width:64px;height:64px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <span style="color:var(--green)"><?= icon('checkCircle', 28) ?></span>
      </div>
      <h1 style="font-size:1.4rem;color:#fff;margin-bottom:8px">¡Contraseña actualizada!</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:28px">Tu contraseña ha sido cambiada exitosamente.</p>
      <a href="login.php" class="btn btn-primary" style="display:inline-flex;justify-content:center">
        Ir al inicio de sesión <?= icon('arrowRight', 14) ?>
      </a>
    </div>

  </div>
</div>

<script>
(function(){
  if(localStorage.getItem('theme')==='light') document.body.classList.add('light');

  var currentStep = 'email';
  var userEmail = '';
  var otpCode = ['','','','','',''];

  var steps = {
    email:   document.getElementById('step-email'),
    code:    document.getElementById('step-code'),
    reset:   document.getElementById('step-reset'),
    success: document.getElementById('step-success'),
  };
  var errorBox  = document.getElementById('error-box');
  var errorText = document.getElementById('error-text');

  function showStep(name) {
    currentStep = name;
    Object.keys(steps).forEach(function(k){ steps[k].style.display = k === name ? 'block' : 'none'; });
    errorBox.style.display = 'none';
  }

  function showError(msg) {
    errorText.textContent = msg;
    errorBox.style.display = 'block';
  }

  function delay(ms) { return new Promise(function(r){ setTimeout(r, ms); }); }

  // ── Email step ──
  document.getElementById('fp-form').addEventListener('submit', async function(e){
    e.preventDefault();
    userEmail = document.getElementById('fp-email').value.trim();
    if (!userEmail)                         { showError('Ingresa tu correo electrónico.'); return; }
    if (!/\S+@\S+\.\S+/.test(userEmail))   { showError('Correo no válido.'); return; }

    document.getElementById('send-label').style.display   = 'none';
    document.getElementById('send-spinner').style.display = 'inline-flex';

    await delay(1200);

    document.getElementById('send-label').style.display   = 'inline-flex';
    document.getElementById('send-spinner').style.display = 'none';
    document.getElementById('code-email-display').textContent = userEmail;
    otpCode = ['','','','','',''];
    showStep('code');
  });

  // ── OTP inputs ──
  for (var i = 0; i < 6; i++) {
    (function(idx){
      var input = document.getElementById('otp-' + idx);
      input.addEventListener('input', function(e){
        var val = e.target.value.replace(/\D/g,'');
        otpCode[idx] = val.slice(-1);
        input.value  = otpCode[idx];
        if (val && idx < 5) document.getElementById('otp-' + (idx + 1)).focus();
      });
      input.addEventListener('keydown', function(e){
        if (e.key === 'Backspace' && !otpCode[idx] && idx > 0) document.getElementById('otp-' + (idx - 1)).focus();
      });
    })(i);
  }

  document.getElementById('fp-code-form').addEventListener('submit', async function(e){
    e.preventDefault();
    var fullCode = otpCode.join('');
    if (fullCode.length < 6) { showError('Ingresa los 6 dígitos del código.'); return; }

    document.getElementById('verify-label').style.display   = 'none';
    document.getElementById('verify-spinner').style.display = 'inline-flex';

    await delay(900);

    document.getElementById('verify-label').style.display   = 'inline-flex';
    document.getElementById('verify-spinner').style.display = 'none';

    if (fullCode !== '123456') { showError('Código incorrecto. Intenta de nuevo.'); return; }
    showStep('reset');
  });

  document.getElementById('resend-btn').addEventListener('click', function(){
    showStep('email');
  });

  // ── Reset step ──
  document.getElementById('fp-reset-form').addEventListener('submit', async function(e){
    e.preventDefault();
    var np = document.getElementById('fp-new-pass').value;
    var cp = document.getElementById('fp-confirm-pass').value;
    if (!np || !cp)    { showError('Completa todos los campos.'); return; }
    if (np !== cp)     { showError('Las contraseñas no coinciden.'); return; }
    if (np.length < 6) { showError('La contraseña debe tener al menos 6 caracteres.'); return; }

    document.getElementById('save-label').style.display   = 'none';
    document.getElementById('save-spinner').style.display = 'inline-flex';

    await delay(1200);

    document.getElementById('save-label').style.display   = 'inline-flex';
    document.getElementById('save-spinner').style.display = 'none';
    showStep('success');
  });
})();
</script>
</body>
</html>
