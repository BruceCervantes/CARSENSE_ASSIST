// ══════════════════════════════════════════════
// CARSENSE — Olvide Contraseña (3 steps)
// ══════════════════════════════════════════════
import { navigate } from '../router.js';
import { icon } from '../icons.js';

const logoSVG = `<svg width="32" height="32" viewBox="0 0 40 40" fill="none">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
</svg>`;

let state = { step: 'email', email: '', code: ['','','','','',''], newPassword: '', confirmPassword: '', loading: false, error: '' };

export function renderOlvideContrasena(container) {
  state = { step: 'email', email: '', code: ['','','','','',''], newPassword: '', confirmPassword: '', loading: false, error: '' };
  _render(container);
}

function _render(container) {
  container.className = '';
  container.innerHTML = `
    <div style="min-height:100vh;background:var(--bg);display:flex;align-items:center;justify-content:center;padding:32px;font-family:var(--font)">
      <div style="width:100%;max-width:380px">
        <!-- Logo -->
        <a href="#/" style="display:flex;align-items:center;gap:8px;margin-bottom:40px;text-decoration:none">
          ${logoSVG}
          <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
        </a>

        ${_stepHTML()}

        ${state.error ? `<div class="error-box" style="margin-top:12px"><p>${state.error}</p></div>` : ''}
      </div>
    </div>
  `;
  _attachEvents(container);
}

function _stepHTML() {
  if (state.step === 'email') return `
    <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">¿Olvidaste tu contraseña?</h1>
    <p style="color:var(--muted);font-size:13px;margin-bottom:28px">
      Ingresa tu correo y te enviaremos un código de verificación.
    </p>
    <form id="fp-form">
      <label class="form-label">Correo electrónico</label>
      <div class="input-wrap" style="margin-bottom:16px">
        ${icon('mail', 14)}
        <input type="email" id="fp-email" placeholder="tucorreo@ejemplo.com" value="${state.email}" autocomplete="email"/>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center" ${state.loading?'disabled':''}>
        ${state.loading ? `<span class="spinner"></span> Enviando...` : `Enviar código ${icon('arrowRight', 14)}`}
      </button>
    </form>
    <div style="margin-top:20px;text-align:center">
      <a href="#/login" style="color:var(--muted2);font-size:12px;display:inline-flex;align-items:center;gap:4px">
        ${icon('arrowLeft', 12)} Volver al inicio de sesión
      </a>
    </div>
  `;

  if (state.step === 'code') return `
    <div style="width:48px;height:48px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
      <span style="color:var(--accent)">${icon('shieldCheck', 20)}</span>
    </div>
    <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Verifica tu código</h1>
    <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
      Ingresamos un código de 6 dígitos a <strong style="color:var(--text2)">${state.email}</strong>.<br>
      <span style="color:var(--muted3);font-size:11px">(Usa 123456 para demo)</span>
    </p>
    <form id="fp-code-form">
      <div class="otp-inputs" style="margin-bottom:20px">
        ${state.code.map((v, i) => `
          <input class="otp-input" id="otp-${i}" type="text" maxlength="1" value="${v}" inputmode="numeric"/>
        `).join('')}
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center" ${state.loading?'disabled':''}>
        ${state.loading ? `<span class="spinner"></span> Verificando...` : `Verificar código ${icon('arrowRight', 14)}`}
      </button>
    </form>
    <div style="margin-top:16px;text-align:center">
      <button id="resend-btn" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font)">
        ¿No recibiste el código? Reenviar
      </button>
    </div>
  `;

  if (state.step === 'reset') return `
    <div style="width:48px;height:48px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
      <span style="color:var(--accent)">${icon('keyRound', 20)}</span>
    </div>
    <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Nueva contraseña</h1>
    <p style="color:var(--muted);font-size:13px;margin-bottom:24px">Elige una contraseña segura de al menos 6 caracteres.</p>
    <form id="fp-reset-form" style="display:flex;flex-direction:column;gap:14px">
      <div>
        <label class="form-label">Nueva contraseña</label>
        <div class="input-wrap">
          ${icon('lock', 14)}
          <input type="password" id="fp-new-pass" placeholder="Mínimo 6 caracteres"/>
        </div>
      </div>
      <div>
        <label class="form-label">Confirmar contraseña</label>
        <div class="input-wrap">
          ${icon('lock', 14)}
          <input type="password" id="fp-confirm-pass" placeholder="Repite la contraseña"/>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center" ${state.loading?'disabled':''}>
        ${state.loading ? `<span class="spinner"></span> Guardando...` : `Guardar contraseña ${icon('arrowRight', 14)}`}
      </button>
    </form>
  `;

  if (state.step === 'success') return `
    <div style="text-align:center;padding:20px 0">
      <div style="width:64px;height:64px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <span style="color:var(--green)">${icon('checkCircle', 28)}</span>
      </div>
      <h1 style="font-size:1.4rem;color:#fff;margin-bottom:8px">¡Contraseña actualizada!</h1>
      <p style="color:var(--muted);font-size:13px;margin-bottom:28px">Tu contraseña ha sido cambiada exitosamente.</p>
      <a href="#/login" class="btn btn-primary" style="display:inline-flex;justify-content:center">
        Ir al inicio de sesión ${icon('arrowRight', 14)}
      </a>
    </div>
  `;
}

function _attachEvents(container) {
  const delay = ms => new Promise(r => setTimeout(r, ms));

  if (state.step === 'email') {
    document.getElementById('fp-form').addEventListener('submit', async e => {
      e.preventDefault();
      state.email = document.getElementById('fp-email').value;
      if (!state.email) { state.error = 'Ingresa tu correo electrónico.'; _render(container); return; }
      if (!/\S+@\S+\.\S+/.test(state.email)) { state.error = 'Correo no válido.'; _render(container); return; }
      state.loading = true; state.error = ''; _render(container);
      await delay(1200);
      state.loading = false; state.step = 'code'; _render(container);
    });
  }

  if (state.step === 'code') {
    // OTP inputs
    state.code.forEach((_, i) => {
      const input = document.getElementById(`otp-${i}`);
      input.addEventListener('input', e => {
        const val = e.target.value.replace(/\D/g,'');
        state.code[i] = val.slice(-1);
        input.value = state.code[i];
        if (val && i < 5) document.getElementById(`otp-${i+1}`)?.focus();
      });
      input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !state.code[i] && i > 0) document.getElementById(`otp-${i-1}`)?.focus();
      });
    });

    document.getElementById('fp-code-form').addEventListener('submit', async e => {
      e.preventDefault();
      const fullCode = state.code.join('');
      if (fullCode.length < 6) { state.error = 'Ingresa los 6 dígitos del código.'; _render(container); return; }
      state.loading = true; state.error = ''; _render(container);
      await delay(900);
      state.loading = false;
      if (fullCode !== '123456') { state.error = 'Código incorrecto. Intenta de nuevo.'; _render(container); return; }
      state.step = 'reset'; _render(container);
    });

    document.getElementById('resend-btn').addEventListener('click', async () => {
      state.step = 'email'; _render(container);
    });
  }

  if (state.step === 'reset') {
    document.getElementById('fp-reset-form').addEventListener('submit', async e => {
      e.preventDefault();
      const np = document.getElementById('fp-new-pass').value;
      const cp = document.getElementById('fp-confirm-pass').value;
      if (!np || !cp) { state.error = 'Completa todos los campos.'; _render(container); return; }
      if (np !== cp) { state.error = 'Las contraseñas no coinciden.'; _render(container); return; }
      if (np.length < 6) { state.error = 'La contraseña debe tener al menos 6 caracteres.'; _render(container); return; }
      state.loading = true; state.error = ''; _render(container);
      await delay(1200);
      state.loading = false; state.step = 'success'; _render(container);
    });
  }
}
