// ══════════════════════════════════════════════
// CARSENSE — Sistema Detalle Page
// ══════════════════════════════════════════════
import { navigate } from '../router.js';
import { icon } from '../icons.js';
import { systemsData, systemImages } from '../data.js';

export function renderSistemaDetalle(container, params) {
  const { systemId } = params;
  const sys = systemsData[systemId];

  if (!sys) {
    container.innerHTML = `
      <div class="empty-state" style="min-height:60vh">
        <div class="empty-icon">${icon('settings', 28)}</div>
        <p class="empty-title">Sistema no encontrado</p>
        <a href="#/sistemas" style="color:var(--accent);font-size:13px">← Volver a sistemas</a>
      </div>`;
    return;
  }

  const img = systemImages[systemId];

  container.innerHTML = `
    <div class="sysdetalle-page">
      <!-- Header -->
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:44px;height:44px;border-radius:12px;background:${sys.color}18;border:1px solid ${sys.color}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="color:${sys.color}">${icon('cpu', 20)}</span>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <h1 style="font-size:1.25rem;color:#fff">${sys.name}</h1>
              <span style="padding:2px 8px;background:${sys.color}22;border:1px solid ${sys.color}40;color:${sys.color};font-size:11px;border-radius:4px">
                Criticidad: ${sys.criticality}
              </span>
            </div>
            <p style="color:var(--muted);font-size:13px;margin-top:4px;max-width:540px">${sys.desc}</p>
          </div>
        </div>
        <a href="#/diagnostico" class="btn btn-primary btn-sm" style="display:inline-flex">
          ${icon('search', 13)} Diagnosticar
        </a>
      </div>

      <div class="sysdetalle-grid">
        <!-- Main: image + components -->
        <div style="display:flex;flex-direction:column;gap:20px">

          <!-- Image with hotpoints -->
          ${img ? `
            <div class="card" style="padding:0;overflow:hidden">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px 12px">
                <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Vista del sistema</div>
                <div style="font-size:11px;color:var(--muted2);display:flex;align-items:center;gap:6px">
                  ${icon('mapPin', 11)}
                  <span class="hotpoint-hint-desktop">Pasa el mouse sobre los puntos</span>
                  <span class="hotpoint-hint-mobile">Presiona los puntos</span>
                </div>
              </div>
              <div style="position:relative;height:240px;overflow:hidden">
                <img src="${img}" alt="${sys.name}" style="width:100%;height:100%;object-fit:cover"/>
                <div style="position:absolute;inset:0;background:${sys.color};opacity:0.1"></div>
                <div style="position:absolute;inset:0;background:linear-gradient(to top,var(--bg2) 0%,transparent 50%)"></div>
                ${sys.hotpoints.map(hp => `
                  <div class="hotpoint" style="left:${hp.x}%;top:${hp.y}%">
                    <div class="hotpoint-dot" style="background:${sys.color}"></div>
                    <div class="hotpoint-tooltip" style="border-color:${sys.color}60">
                      <div class="hotpoint-tooltip-name">${hp.name}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
          ` : ''}

          <!-- Components grid -->
          <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Componentes (${sys.components.length})</div>
            </div>
            <div class="component-grid">
              ${sys.components.map(comp => `
                <a href="#/sistemas/${systemId}/${comp.id}" class="component-card">
                  <div style="width:36px;height:36px;border-radius:8px;background:${sys.color}18;border:1px solid ${sys.color}30;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:${sys.color}">${icon('settings', 15)}</span>
                  </div>
                  <div style="flex:1;min-width:0">
                    <div style="color:#fff;font-size:13px;margin-bottom:2px">${comp.name}</div>
                    <div style="color:var(--muted2);font-size:11px;white-space:normal;line-height:1.4">${comp.desc}</div>
                  </div>
                  <span style="color:var(--muted3)">${icon('chevronRight', 13)}</span>
                </a>
              `).join('')}
            </div>
          </div>

          <!-- Symptoms -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Síntomas relacionados</div>
            <div style="display:flex;flex-direction:column;gap:8px">
              ${sys.symptoms.map(s => `
                <a href="#/resultado/${s.resultId}" style="display:flex;align-items:center;gap:10px;padding:10px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;text-decoration:none;transition:all 0.15s;color:inherit"
                   onmouseover="this.style.background='#1a1a22'" onmouseout="this.style.background='var(--bg3)'">
                  <span style="color:var(--accent)">${icon('activity', 13)}</span>
                  <span style="color:var(--text2);font-size:13px;flex:1">${s.label}</span>
                  <span style="color:var(--muted2)">${icon('arrowRight', 12)}</span>
                </a>
              `).join('')}
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div style="display:flex;flex-direction:column;gap:20px">
          <!-- Stats -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Estadísticas</div>
            <div style="display:flex;flex-direction:column;gap:12px">
              ${[
                { label: 'Componentes', value: sys.components.length, color: sys.color },
                { label: 'Síntomas documentados', value: sys.symptoms.length, color: '#f59e0b' },
                { label: 'Criticidad', value: sys.criticality, color: sys.criticality==='Alta'?'#e03030':sys.criticality==='Media'?'#f59e0b':'#10b981' },
              ].map(s => `
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:var(--bg3);border-radius:8px;border:1px solid var(--border)">
                  <span style="font-size:12px;color:var(--muted)">${s.label}</span>
                  <span style="font-size:13px;font-weight:600;color:${s.color}">${s.value}</span>
                </div>
              `).join('')}
            </div>
          </div>

          <!-- Maintenance -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Mantenimiento preventivo</div>
            <div style="display:flex;flex-direction:column;gap:12px">
              ${sys.maintenance.map(m => `
                <div style="display:flex;align-items:flex-start;gap:10px">
                  <span style="color:var(--green);flex-shrink:0;margin-top:2px">${icon('checkCircle', 14)}</span>
                  <div>
                    <div style="color:#fff;font-size:13px">${m.label}</div>
                    <div style="color:var(--muted2);font-size:11px;margin-top:2px;line-height:1.5">${m.interval}</div>
                  </div>
                </div>
              `).join('')}
            </div>
          </div>

          <!-- Quick access -->
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">Acciones rápidas</div>
            <div style="display:flex;flex-direction:column;gap:8px">
              <a href="#/diagnostico" class="btn btn-primary btn-sm" style="display:flex;justify-content:center">
                ${icon('search', 13)} Nuevo diagnóstico
              </a>
              <a href="#/sistemas" class="btn btn-ghost btn-sm" style="display:flex;justify-content:center">
                ${icon('arrowLeft', 13)} Todos los sistemas
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  // Hotpoint hover (desktop) + tap (mobile)
  const allTooltips = () => container.querySelectorAll('.hotpoint-tooltip');
  const hideAll = () => allTooltips().forEach(t => {
    t.style.opacity = '0'; t.style.transform = 'translateX(-50%) translateY(0)';
  });
  const show = t => { t.style.opacity = '1'; t.style.transform = 'translateX(-50%) translateY(-2px)'; };

  container.querySelectorAll('.hotpoint').forEach(hp => {
    const tooltip = hp.querySelector('.hotpoint-tooltip');

    // Desktop: hover
    hp.addEventListener('mouseenter', () => show(tooltip));
    hp.addEventListener('mouseleave', () => {
      tooltip.style.opacity = '0'; tooltip.style.transform = 'translateX(-50%) translateY(0)';
    });

    // Mobile: touchstart con preventDefault bloquea los eventos de mouse
    // sintetizados (mouseenter/click) que causaban el doble toque
    hp.addEventListener('touchstart', e => {
      e.preventDefault();
      e.stopPropagation();
      const isOpen = tooltip.style.opacity === '1';
      hideAll();
      if (!isOpen) show(tooltip);
    }, { passive: false });
  });

  // Cerrar al tocar o hacer clic fuera de cualquier hotpoint
  document.addEventListener('touchstart', hideAll);
  document.addEventListener('click', hideAll);
}
