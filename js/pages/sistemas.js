// ══════════════════════════════════════════════
// CARSENSE — Sistemas Page
// ══════════════════════════════════════════════
import { icon } from '../icons.js';
import { systems, systemImages } from '../data.js';

export function renderSistemas(container) {
  let search = '';

  const _systemCard = s => {
    const img = systemImages[s.id];
    return `
      <a href="#/sistemas/${s.id}" class="sistema-card">
        <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:${s.color};border-radius:3px 0 0 3px"></div>
        ${img ? `
          <div style="height:120px;margin:-20px -20px 16px;overflow:hidden;border-radius:var(--radius-xl) var(--radius-xl) 0 0;position:relative">
            <img src="${img}" alt="${s.name}" style="width:100%;height:100%;object-fit:cover"/>
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,var(--bg2) 100%)"></div>
            <div style="position:absolute;top:12px;right:12px;padding:2px 8px;background:${s.color}22;border:1px solid ${s.color}44;border-radius:4px;font-size:10px;color:${s.color}">${s.components} componentes</div>
          </div>
        ` : `
          <div style="height:80px;margin:-20px -20px 16px;background:${s.color}10;border-radius:var(--radius-xl) var(--radius-xl) 0 0;display:flex;align-items:center;justify-content:center">
            <span style="color:${s.color};opacity:0.4">${icon('cpu', 32)}</span>
          </div>
        `}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
          <div style="width:8px;height:8px;border-radius:50%;background:${s.color}"></div>
          <h3 style="font-size:14px;color:var(--text)">${s.name}</h3>
        </div>
        <p style="font-size:12px;color:var(--muted2);line-height:1.5;margin-bottom:12px">${s.desc}</p>
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div style="display:flex;gap:12px">
            <span style="font-size:11px;color:${s.color}">${s.components} componentes</span>
            <span style="font-size:11px;color:var(--muted3)">${s.symptoms} síntomas</span>
          </div>
          <span style="color:${s.color}">${icon('arrowRight', 13)}</span>
        </div>
      </a>
    `;
  };

  const _renderGrid = () => {
    const grid = container.querySelector('.sistemas-grid');
    if (!grid) return;
    const filtered = systems.filter(s =>
      !search ||
      s.name.toLowerCase().includes(search.toLowerCase()) ||
      s.desc.toLowerCase().includes(search.toLowerCase())
    );
    grid.innerHTML = filtered.length === 0
      ? `<div class="empty-state" style="grid-column:1/-1">
           <div class="empty-icon">${icon('search', 20)}</div>
           <p class="empty-title">No se encontraron sistemas</p>
         </div>`
      : filtered.map(_systemCard).join('');
  };

  container.innerHTML = `
    <div class="sistemas-page">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:8px">
        <h1 style="font-size:1.25rem;color:var(--text)">Sistemas del vehículo</h1>
        <div class="search-input-group" style="max-width:280px;flex:1">
          <span style="color:var(--muted2)">${icon('search', 14)}</span>
          <input id="sys-search" class="search-input" placeholder="Buscar sistema..."/>
        </div>
      </div>

      <p style="color:var(--muted);font-size:13px;margin-bottom:20px">
        Explora los sistemas principales del vehículo y aprende sobre sus componentes.
      </p>

      <!-- Car overview SVG -->
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-xl);padding:20px;margin-bottom:20px;overflow:hidden">
        <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Vista general del vehículo</div>
        <div style="max-width:600px;margin:0 auto">
          <svg viewBox="0 0 600 220" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto">
            <defs>
              <radialGradient id="carGlow" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#e03030" stop-opacity="0.1"/>
                <stop offset="100%" stop-color="#e03030" stop-opacity="0"/>
              </radialGradient>
            </defs>
            <ellipse cx="300" cy="110" rx="220" ry="90" fill="url(#carGlow)"/>
            <rect x="100" y="120" width="400" height="60" rx="14" style="fill:var(--bg4)" stroke="var(--border2)" stroke-width="1"/>
            <path d="M175 120 Q195 75 240 68 L360 68 Q405 75 425 120Z" style="fill:var(--bg3)" stroke="var(--border2)" stroke-width="1"/>
            <path d="M192 118 Q205 80 243 73 L357 73 Q395 80 408 118Z" style="fill:var(--bg2)" fill-opacity="0.9"/>
            <circle cx="175" cy="183" r="32" style="fill:var(--bg3)" stroke="var(--border3)" stroke-width="1.5"/>
            <circle cx="175" cy="183" r="20" fill="none" stroke="var(--border2)" stroke-width="1.5"/>
            <circle cx="425" cy="183" r="32" style="fill:var(--bg3)" stroke="var(--border3)" stroke-width="1.5"/>
            <circle cx="425" cy="183" r="20" fill="none" stroke="var(--border2)" stroke-width="1.5"/>
            <rect x="102" y="128" width="24" height="16" rx="5" fill="#e03030" fill-opacity="0.8"/>
            <rect x="474" y="128" width="24" height="16" rx="5" fill="#f59e0b" fill-opacity="0.6"/>
            ${systems.map((s, i) => {
              const positions = [{x:300,y:95},{x:175,y:150},{x:420,y:155},{x:300,y:145},{x:175,y:183},{x:425,y:183}];
              const p = positions[i] || {x:300,y:110};
              return `
                <a href="#/sistemas/${s.id}">
                  <circle cx="${p.x}" cy="${p.y}" r="8" fill="${s.color}" fill-opacity="0.9" style="cursor:pointer"/>
                  <circle cx="${p.x}" cy="${p.y}" r="14" fill="${s.color}" fill-opacity="0.15"/>
                </a>
              `;
            }).join('')}
          </svg>
        </div>
      </div>

      <div class="sistemas-grid">
        ${systems.map(_systemCard).join('')}
      </div>
    </div>
  `;

  document.getElementById('sys-search').addEventListener('input', e => {
    search = e.target.value;
    _renderGrid();
  });
}
