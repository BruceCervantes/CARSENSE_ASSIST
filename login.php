<?php
session_start();
if (!empty($_SESSION['user'])) { header('Location: index.php'); exit; }

require_once 'includes/icons.php';
require_once 'api/config/database.php';
require_once 'api/models/User.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';
    if (!$email || !$password) {
        $error = 'Completa todos los campos.';
    } else {
        $row = User::findByEmail($email);
        if ($row && User::verifyPassword($password, $row['password_hash'])) {
            unset($row['password_hash']);
            $_SESSION['user'] = $row;
            header('Location: index.php');
            exit;
        }
        $error = 'Credenciales incorrectas. Intenta de nuevo.';
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Iniciar sesión — CarSense</title>
  <link rel="stylesheet" href="css/styles.css"/>
  <script>(function(){if(localStorage.getItem('theme')==='light')document.documentElement.classList.add('light');})();</script>
</head>
<body>
<div class="auth-page">
  <!-- Left panel -->
  <div class="auth-left" style="width:50%">
    <div class="auth-left-glow"></div>
    <div class="auth-left-grid"></div>
    <div class="auth-left-content">
      <div style="display:flex;align-items:center;gap:8px">
        <?= $logoSVG ?>
        <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
      </div>
      <div>
        <h2 style="color:#fff;font-size:1.8rem;line-height:1.2;margin-bottom:12px">
          Tu asistente automotriz<br><span style="color:var(--accent)">inteligente.</span>
        </h2>
        <p style="color:var(--muted);font-size:14px;line-height:1.7;margin-bottom:32px">
          Diagnósticos con IA, historial de vehículos, recordatorios de mantenimiento y guías técnicas en un solo lugar.
        </p>
        <div style="display:flex;flex-direction:column;gap:16px">
          <?php foreach ([
            ['icon'=>'zap',  'label'=>'Diagnóstico IA en segundos', 'desc'=>'Describe el síntoma con tus palabras'],
            ['icon'=>'car',  'label'=>'Múltiples vehículos',        'desc'=>'Gestiona toda tu flota personal'],
            ['icon'=>'lock', 'label'=>'Privado y seguro',           'desc'=>'Tu información protegida siempre'],
          ] as $f): ?>
            <div style="display:flex;align-items:flex-start;gap:12px">
              <div style="width:32px;height:32px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:var(--accent)"><?= icon($f['icon'], 13) ?></span>
              </div>
              <div>
                <div style="color:#fff;font-size:14px"><?= $f['label'] ?></div>
                <div style="color:var(--muted2);font-size:12px;margin-top:2px"><?= $f['desc'] ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card" style="padding:16px">
        <p style="color:var(--text2);font-size:13px;font-style:italic;line-height:1.6;margin-bottom:12px">
          "Me explicó exactamente qué estaba fallando en los frenos de mi carro antes de ir al taller. Ahorré dinero y tiempo."
        </p>
        <div style="display:flex;align-items:center;gap:8px">
          <div class="user-avatar" style="width:28px;height:28px;font-size:11px">M</div>
          <div>
            <div style="color:#fff;font-size:12px">Mario R.</div>
            <div style="color:var(--muted2);font-size:11px">Toyota Corolla 2019</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right form -->
  <div class="auth-right">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:40px" id="mobile-logo">
      <?= $logoSVG ?>
      <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
    </div>
    <div class="auth-form-container">
      <h1 style="font-size:1.5rem;color:#fff;margin-bottom:4px">Inicia sesión</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:28px">
        ¿No tienes cuenta? <a href="registro.php" style="color:var(--accent)">Regístrate gratis</a>
      </p>

      <button id="demo-btn" type="button" class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:20px;gap:8px">
        <span style="color:var(--accent)"><?= icon('car', 14) ?></span>
        Acceder con cuenta demo
      </button>

      <div class="divider">
        <div class="divider-line"></div>
        <span>o con tu email</span>
        <div class="divider-line"></div>
      </div>

      <?php if ($error): ?>
        <div class="error-box" style="margin-bottom:16px"><p><?= htmlspecialchars($error) ?></p></div>
      <?php endif; ?>

      <form method="post" action="login.php" style="display:flex;flex-direction:column;gap:16px">
        <div>
          <label class="form-label">Correo electrónico</label>
          <div class="input-wrap">
            <span class="input-icon"><?= icon('mail', 14) ?></span>
            <input type="email" name="email" placeholder="tucorreo@ejemplo.com" autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
          </div>
        </div>
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
            <label class="form-label" style="margin:0">Contraseña</label>
            <a href="olvide-contrasena.php" style="color:var(--accent);font-size:12px">¿Olvidaste tu contraseña?</a>
          </div>
          <div class="input-wrap">
            <span class="input-icon"><?= icon('lock', 14) ?></span>
            <input type="password" id="login-password" name="password" placeholder="••••••••" autocomplete="current-password"/>
            <button type="button" id="toggle-pass" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:flex">
              <?= icon('eye', 14) ?>
            </button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px">
          Iniciar sesión <?= icon('arrowRight', 14) ?>
        </button>
      </form>

      <p style="text-align:center;color:var(--muted3);font-size:11px;margin-top:28px;line-height:1.6">
        Al iniciar sesión aceptas nuestros
        <span style="color:var(--muted);cursor:pointer">Términos de Uso</span> y
        <span style="color:var(--muted);cursor:pointer">Política de Privacidad</span>.
      </p>
    </div>
  </div>
</div>

<script>
  (function(){
    if(localStorage.getItem('theme')==='light') document.body.classList.add('light');

    // Password toggle
    var toggleBtn = document.getElementById('toggle-pass');
    var passInput = document.getElementById('login-password');
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

    // Demo button: pre-fill and submit
    var demoBtn = document.getElementById('demo-btn');
    if (demoBtn) {
      demoBtn.addEventListener('click', function(){
        document.querySelector('input[name="email"]').value    = 'bruce@carsense.app';
        document.querySelector('input[name="password"]').value = 'demo1234';
        document.querySelector('form').submit();
      });
    }

    // Mobile logo visibility
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
