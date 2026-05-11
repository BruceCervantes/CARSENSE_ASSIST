// ══════════════════════════════════════════════
// CARSENSE — Registro Page (2 steps)
// ══════════════════════════════════════════════
import { register } from '../auth.js';
import { navigate } from '../router.js';
import { icon } from '../icons.js';

const vehicleBrands = ['Toyota','Honda','Nissan','Chevrolet','Ford','Volkswagen','Hyundai','Kia','Mazda'];

const logoSVG = `<svg width="32" height="32" viewBox="0 0 40 40" fill="none">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
</svg>`;

let state = { step: 1, name: '', email: '', password: '', confirmPassword: '', vehicleBrand: '', vehicleModel: '', vehicleYear: '', vehicleKm: '', skipVehicle: false, loading: false, error: '' };

export function renderRegistro(container) {
  state = { step: 1, name: '', email: '', password: '', confirmPassword: '', vehicleBrand: '', vehicleModel: '', vehicleYear: '', vehicleKm: '', skipVehicle: false, loading: false, error: '' };
  _render(container);
}

function _render(container) {
  container.className = '';
  container.innerHTML = `
    <div class="auth-page">
      <!-- Left panel -->
      <div class="auth-left" style="width:42%">
        <div class="auth-left-glow" style="background:radial-gradient(circle at 30% 50%, rgba(224,48,48,0.16) 0%, transparent 65%)"></div>
        <div class="auth-left-grid"></div>
        <div class="auth-left-content">
          <div style="display:flex;align-items:center;gap:8px">
            ${logoSVG}
            <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
          </div>
          <div>
            <h2 style="color:#fff;font-size:1.8rem;line-height:1.2;margin-bottom:12px">
              Empieza a conocer<br><span style="color:var(--accent)">tu vehículo.</span>
            </h2>
            <p style="color:var(--muted);font-size:13px;line-height:1.7;margin-bottom:28px">
              Crea tu cuenta en segundos y accede a diagnósticos con IA, historial y recordatorios.
            </p>
            <div style="display:flex;flex-direction:column;gap:12px">
              ${[
                { n: 1, label: 'Crea tu cuenta' },
                { n: 2, label: 'Registra tu vehículo' },
                { n: 3, label: '¡Listo para diagnosticar!' },
              ].map(s => `
                <div style="display:flex;align-items:center;gap:12px">
                  <div style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;
                    ${state.step >= s.n ? 'background:var(--accent);color:#fff' : 'background:var(--bg3);border:1px solid var(--border);color:var(--muted3)'}">
                    ${s.n}
                  </div>
                  <span style="font-size:13px;color:${state.step >= s.n ? '#fff' : 'var(--muted3)'}">
                    ${s.label}
                  </span>
                </div>
              `).join('')}
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            ${[
              { value: '2,400+', label: 'Usuarios activos' },
              { value: '18k+', label: 'Diagnósticos realizados' },
            ].map(s => `
              <div class="card" style="text-align:center;padding:16px">
                <div style="color:var(--accent);font-size:1.2rem;font-weight:700;margin-bottom:4px">${s.value}</div>
                <div style="color:var(--muted2);font-size:11px">${s.label}</div>
              </div>
            `).join('')}
          </div>
        </div>
      </div>

      <!-- Right form -->
      <div class="auth-right">
        <div class="auth-form-container">
          <!-- Step indicator -->
          <div style="display:flex;gap:8px;margin-bottom:20px">
            ${[1,2].map(s => `
              <div style="height:4px;flex:1;border-radius:4px;background:${state.step >= s ? 'var(--accent)' : 'var(--bg3)'}"></div>
            `).join('')}
          </div>

          ${state.step === 1 ? _step1HTML() : _step2HTML()}

          ${state.error ? `<div class="error-box" style="margin-top:12px"><p>${state.error}</p></div>` : ''}
        </div>
      </div>
    </div>
  `;

  _attachEvents(container);
}

function _step1HTML() {
  const strength = state.password.length === 0 ? 0 : state.password.length < 6 ? 1 : state.password.length < 10 ? 2 : 3;
  const sc = ['#505070','#e03030','#f59e0b','#10b981'][strength];
  const sl = ['','Débil','Media','Fuerte'][strength];

  return `
    <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Crea tu cuenta</h1>
    <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
      ¿Ya tienes cuenta? <a href="#/login" style="color:var(--accent)">Inicia sesión</a>
    </p>
    <form id="reg-form-1" style="display:flex;flex-direction:column;gap:14px">
      <div>
        <label class="form-label">Nombre completo</label>
        <div class="input-wrap">
          ${icon('user', 14)}<input id="r-name" placeholder="Tu nombre" value="${state.name}"/>
        </div>
      </div>
      <div>
        <label class="form-label">Correo electrónico</label>
        <div class="input-wrap">
          ${icon('mail', 14)}<input type="email" id="r-email" placeholder="tucorreo@ejemplo.com" value="${state.email}"/>
        </div>
      </div>
      <div>
        <label class="form-label">Contraseña</label>
        <div class="input-wrap">
          ${icon('lock', 14)}
          <input type="password" id="r-pass" placeholder="Mínimo 6 caracteres" value="${state.password}"/>
          <button type="button" id="r-toggle-pass" style="color:var(--muted2);background:none;border:none;cursor:pointer">${icon('eye', 14)}</button>
        </div>
        ${state.password ? `
          <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
            <div class="pw-strength-bar">
              ${[1,2,3].map(i => `<div class="pw-strength-segment" style="background:${i<=strength?sc:'var(--bg3)'}"></div>`).join('')}
            </div>
            <span style="font-size:11px;color:${sc}">${sl}</span>
          </div>
        ` : ''}
      </div>
      <div>
        <label class="form-label">Confirmar contraseña</label>
        <div class="input-wrap">
          ${icon('lock', 14)}
          <input type="password" id="r-confirm" placeholder="Repite la contraseña" value="${state.confirmPassword}"/>
          ${state.confirmPassword && state.password === state.confirmPassword
            ? `<span style="color:var(--green)">${icon('check', 14)}</span>` : ''}
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        Continuar ${icon('arrowRight', 14)}
      </button>
    </form>
  `;
}

function _step2HTML() {
  return `
    <h1 style="font-size:1.4rem;color:#fff;margin-bottom:4px">Tu vehículo</h1>
    <p style="color:var(--muted);font-size:13px;margin-bottom:24px">
      Registra tu auto para diagnósticos personalizados. Puedes omitir este paso.
    </p>
    <form id="reg-form-2" style="display:flex;flex-direction:column;gap:14px">
      ${!state.skipVehicle ? `
        <div>
          <label class="form-label">Marca</label>
          <div class="brand-grid">
            ${vehicleBrands.map(b => `
              <button type="button" class="brand-btn ${state.vehicleBrand===b?'selected':''}" data-brand="${b}">${b}</button>
            `).join('')}
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div>
            <label class="form-label">Modelo</label>
            <input class="input-standalone" id="r-model" placeholder="Ej: Corolla..." value="${state.vehicleModel}"/>
          </div>
          <div>
            <label class="form-label">Año</label>
            <input class="input-standalone" id="r-year" placeholder="Ej: 2018" maxlength="4" value="${state.vehicleYear}"/>
          </div>
        </div>
        <div>
          <label class="form-label">Kilometraje actual</label>
          <input class="input-standalone" id="r-km" placeholder="Ej: 67000" value="${state.vehicleKm}"/>
        </div>
      ` : ''}
      <button type="submit" id="reg-submit-2" class="btn btn-primary" style="width:100%;justify-content:center" ${state.loading?'disabled':''}>
        ${state.loading
          ? `<span class="spinner"></span> Creando cuenta...`
          : `${state.skipVehicle?'Crear cuenta':'Crear cuenta y guardar vehículo'} ${icon('arrowRight', 14)}`}
      </button>
      <button type="button" id="skip-vehicle-btn" style="width:100%;text-align:center;color:var(--muted2);font-size:12px;padding:4px;background:none;border:none;cursor:pointer;font-family:var(--font)">
        ${state.skipVehicle ? '← Agregar mi vehículo' : 'Omitir este paso →'}
      </button>
      <button type="button" id="back-step-btn" style="width:100%;text-align:center;color:var(--muted3);font-size:12px;padding:4px;background:none;border:none;cursor:pointer;font-family:var(--font)">
        ← Volver al paso anterior
      </button>
    </form>
  `;
}

function _attachEvents(container) {
  const errorBox = container.querySelector('.error-box');

  const showError = msg => {
    state.error = msg;
    if (errorBox) { errorBox.querySelector('p').textContent = msg; errorBox.style.display = 'block'; }
  };

  if (state.step === 1) {
    // Live update state on input
    const nameEl = document.getElementById('r-name');
    const emailEl = document.getElementById('r-email');
    const passEl = document.getElementById('r-pass');
    const confirmEl = document.getElementById('r-confirm');

    passEl.addEventListener('input', () => { state.password = passEl.value; _render(container); });
    confirmEl.addEventListener('input', () => { state.confirmPassword = confirmEl.value; _render(container); });

    let showPass = false;
    document.getElementById('r-toggle-pass').addEventListener('click', () => {
      showPass = !showPass;
      document.getElementById('r-pass').type = showPass ? 'text' : 'password';
      document.getElementById('r-toggle-pass').innerHTML = icon(showPass ? 'eyeOff' : 'eye', 14);
    });

    document.getElementById('reg-form-1').addEventListener('submit', (e) => {
      e.preventDefault();
      state.name = document.getElementById('r-name').value;
      state.email = document.getElementById('r-email').value;
      state.password = document.getElementById('r-pass').value;
      state.confirmPassword = document.getElementById('r-confirm').value;
      state.error = '';

      if (!state.name || !state.email || !state.password) { showError('Completa todos los campos.'); return; }
      if (state.password !== state.confirmPassword) { showError('Las contraseñas no coinciden.'); return; }
      if (state.password.length < 6) { showError('La contraseña debe tener al menos 6 caracteres.'); return; }

      state.step = 2;
      _render(container);
    });
  } else {
    // Brand selection
    container.querySelectorAll('.brand-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        state.vehicleBrand = btn.dataset.brand;
        container.querySelectorAll('.brand-btn').forEach(b => b.classList.toggle('selected', b.dataset.brand === state.vehicleBrand));
      });
    });

    document.getElementById('skip-vehicle-btn').addEventListener('click', () => {
      state.skipVehicle = !state.skipVehicle;
      _render(container);
    });

    document.getElementById('back-step-btn').addEventListener('click', () => {
      state.step = 1;
      _render(container);
    });

    document.getElementById('reg-form-2').addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!state.skipVehicle) {
        state.vehicleModel = document.getElementById('r-model')?.value || '';
        state.vehicleYear = document.getElementById('r-year')?.value || '';
        state.vehicleKm = document.getElementById('r-km')?.value || '';
      }
      state.loading = true;
      _render(container);
      await register(state.name, state.email, state.password);
      navigate('/');
    });
  }
}
