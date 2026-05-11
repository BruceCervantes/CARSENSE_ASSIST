// ══════════════════════════════════════════════
// CARSENSE — Login Page
// ══════════════════════════════════════════════
import { login } from '../auth.js';
import { navigate } from '../router.js';
import { icon } from '../icons.js';

const logoSVG = `
<svg width="32" height="32" viewBox="0 0 40 40" fill="none">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <path d="M14.5 21 L15 18.5 L25 18.5 L25.5 21 Z" fill="#0a0a0e" fill-opacity="0.6"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
  <path d="M10 27 L14 27 L15.5 24.5 L17 29 L20 25 L22 27 L30 27" stroke="#e03030" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" stroke-opacity="0.85"/>
</svg>`;

export function renderLogin(container) {
  container.className = '';
  container.innerHTML = `
    <div class="auth-page">
      <!-- Left decorative panel -->
      <div class="auth-left" style="width:50%">
        <div class="auth-left-glow"></div>
        <div class="auth-left-grid"></div>
        <div class="auth-left-content">
          <!-- Logo -->
          <div style="display:flex;align-items:center;gap:8px">
            ${logoSVG}
            <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
          </div>

          <!-- Features -->
          <div>
            <h2 style="color:#fff;font-size:1.8rem;line-height:1.2;margin-bottom:12px">
              Tu asistente automotriz<br>
              <span style="color:var(--accent)">inteligente.</span>
            </h2>
            <p style="color:var(--muted);font-size:14px;line-height:1.7;margin-bottom:32px">
              Diagnósticos con IA, historial de vehículos, recordatorios de mantenimiento y guías técnicas en un solo lugar.
            </p>
            <div style="display:flex;flex-direction:column;gap:16px">
              ${[
                { iconName: 'zap', label: 'Diagnóstico IA en segundos', desc: 'Describe el síntoma con tus palabras' },
                { iconName: 'car', label: 'Múltiples vehículos', desc: 'Gestiona toda tu flota personal' },
                { iconName: 'lock', label: 'Privado y seguro', desc: 'Tu información protegida siempre' },
              ].map(f => `
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <div style="width:32px;height:32px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:var(--accent)">${icon(f.iconName, 13)}</span>
                  </div>
                  <div>
                    <div style="color:#fff;font-size:14px">${f.label}</div>
                    <div style="color:var(--muted2);font-size:12px;margin-top:2px">${f.desc}</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>

          <!-- Testimonial -->
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

      <!-- Right form panel -->
      <div class="auth-right">
        <!-- Mobile logo -->
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:40px" class="mobile-logo-auth">
          ${logoSVG}
          <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
        </div>

        <div class="auth-form-container">
          <h1 style="font-size:1.5rem;color:#fff;margin-bottom:4px">Inicia sesión</h1>
          <p style="color:var(--muted);font-size:13px;margin-bottom:28px">
            ¿No tienes cuenta? <a href="#/registro" style="color:var(--accent)">Regístrate gratis</a>
          </p>

          <!-- Demo button -->
          <button id="demo-btn" class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:20px;gap:8px">
            <span style="color:var(--accent)">${icon('car', 14)}</span>
            Acceder con cuenta demo
          </button>

          <div class="divider">
            <div class="divider-line"></div>
            <span>o con tu email</span>
            <div class="divider-line"></div>
          </div>

          <form id="login-form" style="display:flex;flex-direction:column;gap:16px">
            <!-- Email -->
            <div>
              <label class="form-label">Correo electrónico</label>
              <div class="input-wrap">
                <span class="input-icon">${icon('mail', 14)}</span>
                <input type="email" id="login-email" placeholder="tucorreo@ejemplo.com" autocomplete="email"/>
              </div>
            </div>

            <!-- Password -->
            <div>
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <label class="form-label" style="margin:0">Contraseña</label>
                <a href="#/olvide-contrasena" style="color:var(--accent);font-size:12px">¿Olvidaste tu contraseña?</a>
              </div>
              <div class="input-wrap">
                <span class="input-icon">${icon('lock', 14)}</span>
                <input type="password" id="login-password" placeholder="••••••••" autocomplete="current-password"/>
                <button type="button" id="toggle-pass" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:flex">
                  ${icon('eye', 14)}
                </button>
              </div>
            </div>

            <div id="login-error" class="error-box hidden"><p></p></div>

            <button type="submit" id="login-submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px">
              Iniciar sesión ${icon('arrowRight', 14)}
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
  `;

  // Hide mobile logo on large screens via CSS
  const mobileEl = container.querySelector('.mobile-logo-auth');
  const mq = window.matchMedia('(min-width:1024px)');
  mobileEl.style.display = mq.matches ? 'none' : 'flex';
  mq.addEventListener('change', e => { mobileEl.style.display = e.matches ? 'none' : 'flex'; });

  // Toggle password
  let showPass = false;
  document.getElementById('toggle-pass').addEventListener('click', () => {
    showPass = !showPass;
    document.getElementById('login-password').type = showPass ? 'text' : 'password';
    document.getElementById('toggle-pass').innerHTML = icon(showPass ? 'eyeOff' : 'eye', 14);
  });

  // Demo login
  document.getElementById('demo-btn').addEventListener('click', async () => {
    const btn = document.getElementById('demo-btn');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner"></span> Cargando...`;
    await login('bruce@carsense.app', 'demo1234');
    navigate('/');
  });

  // Form submit
  document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const errorBox = document.getElementById('login-error');
    const submitBtn = document.getElementById('login-submit');

    if (!email || !password) {
      errorBox.classList.remove('hidden');
      errorBox.querySelector('p').textContent = 'Completa todos los campos.';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner"></span> Verificando...`;
    errorBox.classList.add('hidden');

    const ok = await login(email, password);
    if (ok) {
      navigate('/');
    } else {
      submitBtn.disabled = false;
      submitBtn.innerHTML = `Iniciar sesión ${icon('arrowRight', 14)}`;
      errorBox.classList.remove('hidden');
      errorBox.querySelector('p').textContent = 'Credenciales incorrectas. Intenta de nuevo.';
    }
  });
}
