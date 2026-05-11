// ══════════════════════════════════════════════
// CARSENSE — Componente Detalle Page
// ══════════════════════════════════════════════
import { icon } from '../icons.js';
import { componentsData, componentImages, systemImages } from '../data.js';

export function renderComponenteDetalle(container, params) {
  const { systemId, componentId } = params;
  const comp = componentsData[componentId];

  if (!comp) {
    container.innerHTML = `
      <div class="empty-state" style="min-height:60vh">
        <div class="empty-icon">${icon('settings', 28)}</div>
        <p class="empty-title">Componente no encontrado</p>
        <a href="#/sistemas/${systemId}" style="color:var(--accent);font-size:13px">← Volver al sistema</a>
      </div>`;
    return;
  }

  const img = componentImages[componentId] || systemImages[systemId];
  const wearColor = comp.wearStatus==='critical' ? '#e03030' : comp.wearStatus==='warning' ? '#f59e0b' : '#10b981';
  const wearLabel = comp.wearStatus==='critical' ? 'Requiere atención urgente' : comp.wearStatus==='warning' ? 'Revisar próximamente' : 'En buen estado';

  let activeTab = 0;
  const tabs = ['Descripción', 'Especificaciones', 'Síntomas', 'Consejos'];

  const _render = () => {
    container.innerHTML = `
      <div class="compdetalle-page">
        <!-- Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
          <div style="display:flex;align-items:center;gap:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:${comp.systemColor}18;border:1px solid ${comp.systemColor}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <span style="color:${comp.systemColor}">${icon('settings', 20)}</span>
            </div>
            <div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.15rem;color:#fff">${comp.name}</h1>
                <a href="#/sistemas/${comp.systemId}" style="padding:2px 8px;background:${comp.systemColor}22;border:1px solid ${comp.systemColor}40;color:${comp.systemColor};font-size:10px;border-radius:4px;text-decoration:none;text-transform:uppercase">
                  ${comp.system}
                </a>
              </div>
              <p style="color:var(--muted);font-size:13px">${comp.desc}</p>
            </div>
          </div>
          <div style="display:flex;gap:8px">
            <button class="btn btn-ghost btn-sm">Guardar ficha</button>
            <a href="#/resultado/1" class="btn btn-primary btn-sm" style="display:inline-flex">
              Diagnosticar ${icon('arrowRight', 13)}
            </a>
          </div>
        </div>

        <!-- Wear status strip -->
        <div class="card" style="margin-bottom:20px;padding:16px">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
            <div style="flex:1;min-width:200px">
              <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;margin-bottom:6px">
                <span style="color:var(--muted)">Desgaste estimado</span>
                <span style="color:${wearColor};font-weight:500">${wearLabel}</span>
              </div>
              <div style="height:10px;background:var(--bg3);border-radius:9999px;overflow:hidden">
                <div style="height:100%;width:${comp.wear}%;background:linear-gradient(90deg,${wearColor}88,${wearColor});border-radius:9999px;transition:width 0.5s"></div>
              </div>
              <p style="color:var(--muted2);font-size:11px;margin-top:6px">${comp.wearLabel}</p>
            </div>
          </div>
        </div>

        <div class="compdetalle-grid">
          <!-- Main content -->
          <div>
            <!-- Image -->
            ${img ? `
              <div class="card" style="padding:0;overflow:hidden;margin-bottom:20px">
                <div style="position:relative;height:220px">
                  <img src="${img}" alt="${comp.name}" style="width:100%;height:100%;object-fit:cover"/>
                  <div style="position:absolute;inset:0;background:${comp.systemColor};opacity:0.1"></div>
                  <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--bg2) 0%,transparent 60%)"></div>
                  <div style="position:absolute;bottom:12px;left:12px">
                    <span style="padding:4px 10px;background:${comp.systemColor}28;border:1px solid ${comp.systemColor}50;border-radius:6px;font-size:11px;color:${comp.systemColor}">
                      ${comp.name}
                    </span>
                  </div>
                </div>
              </div>
            ` : ''}

            <!-- Tabs -->
            <div class="card">
              <div class="tabs-row">
                ${tabs.map((tab, i) => `
                  <button class="tab-btn ${i===activeTab?'active':''}" data-tab="${i}">${tab}</button>
                `).join('')}
              </div>

              <div id="tab-content">
                ${_renderTabContent()}
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div style="display:flex;flex-direction:column;gap:16px">
            <!-- System nav -->
            <div class="card">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Sistema</div>
              <a href="#/sistemas/${comp.systemId}" style="display:flex;align-items:center;gap:10px;padding:10px;background:${comp.systemColor}10;border:1px solid ${comp.systemColor}30;border-radius:10px;text-decoration:none;margin-bottom:8px">
                <span style="color:${comp.systemColor}">${icon('cpu', 16)}</span>
                <div>
                  <div style="color:#fff;font-size:13px">${comp.system}</div>
                  <div style="color:var(--muted2);font-size:11px;display:flex;align-items:center;gap:4px">Ver sistema completo ${icon('chevronRight', 9)}</div>
                </div>
              </a>
            </div>

            <!-- Quick actions -->
            <div class="card">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Acciones</div>
              <div style="display:flex;flex-direction:column;gap:8px">
                <a href="#/resultado/1" class="btn btn-primary btn-sm" style="display:flex;justify-content:center">
                  ${icon('search', 13)} Iniciar diagnóstico
                </a>
                <a href="#/sistemas/${comp.systemId}" class="btn btn-ghost btn-sm" style="display:flex;justify-content:center">
                  ${icon('arrowLeft', 13)} Volver al sistema
                </a>
              </div>
            </div>

            <!-- Status card -->
            <div class="card" style="border-color:${wearColor}40">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:36px;height:36px;border-radius:8px;background:${wearColor}18;border:1px solid ${wearColor}40;display:flex;align-items:center;justify-content:center">
                  <span style="color:${wearColor}">${comp.wearStatus==='ok' ? icon('checkCircle',16) : icon('alertTriangle',16)}</span>
                </div>
                <div>
                  <div style="color:#fff;font-size:13px">${wearLabel}</div>
                  <div style="color:${wearColor};font-size:11px">${comp.wear}% de desgaste</div>
                </div>
              </div>
              <div style="height:6px;background:var(--bg3);border-radius:9999px;overflow:hidden">
                <div style="height:100%;width:${comp.wear}%;background:${wearColor};border-radius:9999px"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    // Tab events
    container.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        activeTab = parseInt(btn.dataset.tab);
        _render();
      });
    });
  };

  function _renderTabContent() {
    if (activeTab === 0) return `
      <p style="color:var(--text2);font-size:14px;line-height:1.7">${comp.desc}</p>
      <p style="color:var(--muted);font-size:13px;margin-top:12px;line-height:1.6">
        Este componente es parte del <strong style="color:var(--text2)">${comp.system}</strong> y cumple un papel fundamental en el funcionamiento seguro del vehículo.
      </p>
    `;

    if (activeTab === 1) return `
      <div style="display:flex;flex-direction:column;gap:0">
        ${comp.specs.map((s, i) => `
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;${i>0?'border-top:1px solid var(--border)':''}">
            <span style="color:var(--muted);font-size:13px">${s.label}</span>
            <span style="color:#fff;font-size:13px;font-weight:500">${s.value}</span>
          </div>
        `).join('')}
      </div>
    `;

    if (activeTab === 2) return `
      <div style="display:flex;flex-direction:column;gap:8px">
        ${comp.symptoms.map(s => `
          <div style="display:flex;align-items:flex-start;gap:10px;padding:10px;background:rgba(224,48,48,0.06);border:1px solid rgba(224,48,48,0.15);border-radius:8px">
            <span style="color:var(--accent);flex-shrink:0;margin-top:2px">${icon('alertTriangle', 13)}</span>
            <span style="color:var(--text2);font-size:13px">${s}</span>
          </div>
        `).join('')}
      </div>
    `;

    if (activeTab === 3) return `
      <div style="display:flex;flex-direction:column;gap:10px">
        ${comp.tips.map(t => `
          <div style="display:flex;align-items:flex-start;gap:10px">
            <span style="color:var(--green);flex-shrink:0;margin-top:2px">${icon('lightbulb', 13)}</span>
            <span style="color:var(--text2);font-size:13px;line-height:1.6">${t}</span>
          </div>
        `).join('')}
      </div>
    `;

    return '';
  }

  _render();
}
