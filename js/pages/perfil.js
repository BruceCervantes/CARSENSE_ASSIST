// ══════════════════════════════════════════════
// CARSENSE — Perfil Page
// ══════════════════════════════════════════════
import { getUser } from '../auth.js';
import { navigate } from '../router.js';
import { icon } from '../icons.js';

const severityColor = { alta: '#e03030', media: '#f59e0b', baja: '#10b981' };

let vehicles = [
  { id: 1, brand: 'Toyota', model: 'Corolla', year: '2018', km: '67,000', plate: 'ABC-1234', color: 'Blanco', active: true, accentColor: '#e03030' },
  { id: 2, brand: 'Honda', model: 'CR-V', year: '2015', km: '92,000', plate: 'XYZ-5678', color: 'Gris', active: false, accentColor: '#3b82f6' },
];

const consultations = [
  { id: 1, title: 'Ruido al frenar', system: 'Frenos', severity: 'alta', date: '2 días', resultId: '1' },
  { id: 2, title: 'Vibración en volante', system: 'Suspensión', severity: 'media', date: '5 días', resultId: '2' },
  { id: 3, title: 'Consumo excesivo de combustible', system: 'Motor', severity: 'baja', date: '1 mes', resultId: '3' },
  { id: 4, title: 'Luz de aceite encendida', system: 'Motor', severity: 'baja', date: '2 meses', resultId: '4' },
];

const reminders = [
  { label: 'Cambio de pastillas de freno', due: 'En 3 días · 500 km', urgency: 'alta' },
  { label: 'Cambio de aceite', due: 'En 1 semana', urgency: 'media' },
  { label: 'Revisión de neumáticos', due: 'En 1 mes', urgency: 'baja' },
];

const activityData = [
  { month: 'Ene', count: 1 }, { month: 'Feb', count: 3 }, { month: 'Mar', count: 2 },
  { month: 'Abr', count: 0 }, { month: 'May', count: 4 }, { month: 'Jun', count: 6 },
];

const maxActivity = Math.max(...activityData.map(d => d.count));

let showAddModal = false;
let addForm = { brand: '', model: '', year: '', km: '', plate: '', color: '' };
const vehicleBrands = ['Toyota','Honda','Nissan','Chevrolet','Ford','Volkswagen','Hyundai','Kia','Mazda','Renault','Otro'];
const vehicleColors = ['Blanco','Negro','Gris','Rojo','Azul','Verde','Plateado','Otro'];

export function renderPerfil(container) {
  const user = getUser();
  if (!user) { navigate('/login'); return; }
  _render(container, user);
}

function _render(container, user) {
  const activeVehicle = vehicles.find(v => v.active) || vehicles[0];

  container.innerHTML = `
    <div class="perfil-page">
      <h1 style="font-size:1.25rem;color:#fff;margin-bottom:20px">Mi perfil</h1>

      <div class="perfil-grid">
        <!-- Left column: user + vehicles -->
        <div style="display:flex;flex-direction:column;gap:16px">

          <!-- User card -->
          <div class="card" style="display:flex;align-items:center;gap:16px">
            <div style="width:56px;height:56px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;font-weight:700;flex-shrink:0">
              ${user.initials}
            </div>
            <div style="flex:1">
              <div style="color:#fff;font-size:1rem;margin-bottom:2px">${user.name}</div>
              <div style="color:var(--muted);font-size:12px">${user.email}</div>
            </div>
            <button class="btn btn-ghost btn-xs">${icon('edit', 12)} Editar</button>
          </div>

          <!-- Vehicles -->
          <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Mis Vehículos</div>
              <button id="add-vehicle-btn" class="btn btn-primary btn-xs" style="display:inline-flex">
                ${icon('plus', 12)} Agregar
              </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              ${vehicles.map(v => `
                <div class="vehicle-card ${v.active?'active':''}" data-vid="${v.id}">
                  <div style="width:36px;height:36px;border-radius:8px;background:${v.accentColor}22;border:1px solid ${v.accentColor}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:${v.accentColor}">${icon('car', 16)}</span>
                  </div>
                  <div style="flex:1;min-width:0">
                    <div style="color:#fff;font-size:13px">${v.brand} ${v.model} ${v.year}</div>
                    <div style="color:var(--muted2);font-size:11px;margin-top:2px">${v.km} km · ${v.plate}</div>
                  </div>
                  ${v.active ? `<span style="font-size:10px;color:var(--green);background:rgba(16,185,129,0.15);padding:2px 8px;border-radius:9999px;border:1px solid rgba(16,185,129,0.3)">Activo</span>` : ''}
                  <button class="icon-btn danger" data-delete-vid="${v.id}" style="display:flex">${icon('trash', 12)}</button>
                </div>
              `).join('')}
            </div>
          </div>

          <!-- Reminders -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">
              ${icon('bell', 12, 'color:var(--accent);margin-right:4px')} Recordatorios
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              ${reminders.map(r => `
                <div style="display:flex;align-items:flex-start;gap:10px">
                  <span style="color:${severityColor[r.urgency]};flex-shrink:0;margin-top:2px">
                    ${r.urgency==='alta' ? icon('alertTriangle',13) : r.urgency==='media' ? icon('clock',13) : icon('checkCircle',13)}
                  </span>
                  <div>
                    <div style="color:#fff;font-size:13px">${r.label}</div>
                    <div style="color:var(--muted2);font-size:11px;margin-top:2px">${r.due}</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>
        </div>

        <!-- Right column: history + stats -->
        <div style="display:flex;flex-direction:column;gap:16px">

          <!-- Active vehicle detail -->
          ${activeVehicle ? `
            <div class="card" style="border-color:${activeVehicle.accentColor}40">
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div style="width:44px;height:44px;border-radius:10px;background:${activeVehicle.accentColor}22;border:1px solid ${activeVehicle.accentColor}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <span style="color:${activeVehicle.accentColor}">${icon('car', 20)}</span>
                </div>
                <div>
                  <div style="color:#fff;font-size:1rem">${activeVehicle.brand} ${activeVehicle.model}</div>
                  <div style="color:var(--muted);font-size:12px">${activeVehicle.year} · ${activeVehicle.color}</div>
                </div>
              </div>
              <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                ${[
                  { label: 'Kilometraje', value: activeVehicle.km + ' km', iconName: 'activity' },
                  { label: 'Placa', value: activeVehicle.plate, iconName: 'settings' },
                  { label: 'Diagnósticos', value: '4 realizados', iconName: 'search' },
                ].map(s => `
                  <div style="background:var(--bg3);border:1px solid var(--border);border-radius:10px;padding:12px;text-align:center">
                    <span style="color:var(--muted2)">${icon(s.iconName, 14)}</span>
                    <div style="color:#fff;font-size:13px;margin-top:6px;font-weight:600">${s.value}</div>
                    <div style="color:var(--muted2);font-size:10px;margin-top:2px">${s.label}</div>
                  </div>
                `).join('')}
              </div>
            </div>
          ` : ''}

          <!-- Activity chart (simple bars) -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px">
              ${icon('barChart', 12, 'color:var(--accent);margin-right:4px')} Actividad de diagnósticos
            </div>
            <div style="display:flex;align-items:flex-end;gap:8px;height:80px">
              ${activityData.map(d => `
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                  <div style="width:100%;border-radius:4px 4px 0 0;background:${d.count>0?'var(--accent)':'var(--bg3)'};min-height:4px;height:${maxActivity>0?(d.count/maxActivity)*60+4:4}px;opacity:${d.count>0?0.7+d.count/maxActivity*0.3:1};transition:all 0.3s"></div>
                  <span style="font-size:9px;color:var(--muted2)">${d.month}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <!-- Consultation history -->
          <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Historial de consultas</div>
              <a href="#/diagnostico" style="color:var(--accent);font-size:11px">Nueva consulta</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              ${consultations.map(c => `
                <a href="#/resultado/${c.resultId}" style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--bg3);border:1px solid var(--border);border-radius:10px;text-decoration:none;transition:all 0.15s" 
                   onmouseover="this.style.background='#1a1a22'" onmouseout="this.style.background='var(--bg3)'">
                  <div style="width:8px;height:8px;border-radius:50%;background:${severityColor[c.severity]};flex-shrink:0"></div>
                  <div style="flex:1;min-width:0">
                    <div style="color:#fff;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.title}</div>
                    <div style="color:var(--muted2);font-size:11px;margin-top:2px">${c.system} · hace ${c.date}</div>
                  </div>
                  <span style="color:var(--muted2)">${icon('chevronRight', 13)}</span>
                </a>
              `).join('')}
            </div>
          </div>
        </div>
      </div>

      <!-- Add vehicle modal -->
      ${showAddModal ? `
        <div class="modal-overlay" id="modal-overlay">
          <div class="modal">
            <div class="modal-header">
              <div style="display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;background:rgba(224,48,48,0.15);border:1px solid rgba(224,48,48,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center">
                  <span style="color:var(--accent)">${icon('car', 16)}</span>
                </div>
                <div>
                  <h2 style="color:#fff;font-size:15px">Agregar vehículo</h2>
                  <p style="color:var(--muted2);font-size:11px">Registra tu auto para diagnósticos personalizados</p>
                </div>
              </div>
              <button id="close-modal" class="icon-btn" style="display:flex">${icon('x', 15)}</button>
            </div>
            <div class="modal-body">
              <form id="add-vehicle-form" style="display:flex;flex-direction:column;gap:14px">
                <div>
                  <label class="form-label">Marca *</label>
                  <select class="input-standalone" id="av-brand">
                    <option value="">Selecciona una marca</option>
                    ${vehicleBrands.map(b => `<option value="${b}" ${addForm.brand===b?'selected':''}>${b}</option>`).join('')}
                  </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                  <div>
                    <label class="form-label">Modelo *</label>
                    <input class="input-standalone" id="av-model" placeholder="Ej: Corolla" value="${addForm.model}"/>
                  </div>
                  <div>
                    <label class="form-label">Año *</label>
                    <input class="input-standalone" id="av-year" placeholder="Ej: 2018" maxlength="4" value="${addForm.year}"/>
                  </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                  <div>
                    <label class="form-label">Kilometraje</label>
                    <input class="input-standalone" id="av-km" placeholder="Ej: 45000" value="${addForm.km}"/>
                  </div>
                  <div>
                    <label class="form-label">Placa</label>
                    <input class="input-standalone" id="av-plate" placeholder="Ej: ABC-123" value="${addForm.plate}"/>
                  </div>
                </div>
                <div>
                  <label class="form-label">Color</label>
                  <select class="input-standalone" id="av-color">
                    <option value="">Selecciona color</option>
                    ${vehicleColors.map(c => `<option value="${c}" ${addForm.color===c?'selected':''}>${c}</option>`).join('')}
                  </select>
                </div>
                <div id="modal-error" class="error-box hidden"><p></p></div>
                <div style="display:flex;gap:10px;margin-top:4px">
                  <button type="button" id="cancel-modal" class="btn btn-ghost" style="flex:1;justify-content:center">Cancelar</button>
                  <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Agregar vehículo</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      ` : ''}
    </div>
  `;

  _attachEvents(container, user);
}

function _attachEvents(container, user) {
  document.getElementById('add-vehicle-btn')?.addEventListener('click', () => {
    showAddModal = true; addForm = { brand:'', model:'', year:'', km:'', plate:'', color:'' };
    _render(container, user);
  });

  document.getElementById('close-modal')?.addEventListener('click', () => { showAddModal = false; _render(container, user); });
  document.getElementById('cancel-modal')?.addEventListener('click', () => { showAddModal = false; _render(container, user); });
  document.getElementById('modal-overlay')?.addEventListener('click', e => {
    if (e.target.id === 'modal-overlay') { showAddModal = false; _render(container, user); }
  });

  document.getElementById('add-vehicle-form')?.addEventListener('submit', e => {
    e.preventDefault();
    const brand = document.getElementById('av-brand').value;
    const model = document.getElementById('av-model').value;
    const year = document.getElementById('av-year').value;
    const errEl = document.getElementById('modal-error');
    if (!brand || !model || !year) {
      errEl.classList.remove('hidden'); errEl.querySelector('p').textContent = 'Marca, modelo y año son obligatorios.';
      return;
    }
    const colors = ['#e03030','#3b82f6','#f59e0b','#10b981','#f97316','#06b6d4'];
    vehicles.push({
      id: Date.now(), brand, model, year,
      km: document.getElementById('av-km').value || '0',
      plate: document.getElementById('av-plate').value || '-',
      color: document.getElementById('av-color').value || 'Sin especificar',
      active: false, accentColor: colors[vehicles.length % colors.length],
    });
    showAddModal = false; _render(container, user);
  });

  // Set active vehicle
  container.querySelectorAll('[data-vid]').forEach(el => {
    el.addEventListener('click', e => {
      if (e.target.closest('[data-delete-vid]')) return;
      const vid = parseInt(el.dataset.vid);
      vehicles.forEach(v => v.active = v.id === vid);
      _render(container, user);
    });
  });

  // Delete vehicle
  container.querySelectorAll('[data-delete-vid]').forEach(el => {
    el.addEventListener('click', e => {
      e.stopPropagation();
      const vid = parseInt(el.dataset.deleteVid);
      vehicles = vehicles.filter(v => v.id !== vid);
      if (!vehicles.some(v => v.active) && vehicles.length > 0) vehicles[0].active = true;
      _render(container, user);
    });
  });
}
