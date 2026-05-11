// ══════════════════════════════════════════════
// CARSENSE — Diagnóstico Page
// ══════════════════════════════════════════════
import { navigate } from '../router.js';
import { icon } from '../icons.js';
import { diagnosticResults, filterZones, filterWhen, filterPriority, filterSensation, priorityColor, priorityLabel } from '../data.js';

let state = { query: '', inputValue: '', analyzing: false, selZones: [], selWhen: [], selPriority: [], selSensation: [], showMobileFilters: false };

export function renderDiagnostico(container) {
  state = { query: '', inputValue: '', analyzing: false, selZones: [], selWhen: [], selPriority: [], selSensation: [], showMobileFilters: false };
  _render(container);
}

function toggle(arr, val) {
  return arr.includes(val) ? arr.filter(x => x !== val) : [...arr, val];
}

function getFiltered() {
  return diagnosticResults.filter(r => {
    const q = state.query.toLowerCase();
    const matchQ = !q || r.title.toLowerCase().includes(q) || r.desc.toLowerCase().includes(q) || r.tags.some(t => t.toLowerCase().includes(q)) || r.system.toLowerCase().includes(q);
    const matchZone = !state.selZones.length || r.zones.some(z => state.selZones.includes(z));
    const matchWhen = !state.selWhen.length || r.when.some(w => state.selWhen.includes(w));
    const matchPriority = !state.selPriority.length || state.selPriority.includes(r.priority);
    const matchSensation = !state.selSensation.length || r.sensations.some(s => state.selSensation.includes(s));
    return matchQ && matchZone && matchWhen && matchPriority && matchSensation;
  });
}

function activeFilterCount() {
  return state.selZones.length + state.selWhen.length + state.selPriority.length + state.selSensation.length;
}

function _renderFilterPanel() {
  const afc = activeFilterCount();
  return `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <div class="filter-title">
        <span style="color:var(--accent)">${icon('filter', 12)}</span>
        <span>Filtros</span>
      </div>
      ${afc > 0 ? `<button id="clear-filters" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font)">Limpiar (${afc})</button>` : ''}
    </div>

    <!-- Zona -->
    <div class="filter-group">
      <div class="filter-group-label">Zona del vehículo</div>
      ${filterZones.map(z => _checkItem(z, state.selZones.includes(z), 'zone')).join('')}
    </div>

    <!-- Cuando -->
    <div class="filter-group">
      <div class="filter-group-label">Cuando ocurre</div>
      ${filterWhen.map(w => _checkItem(w, state.selWhen.includes(w), 'when')).join('')}
    </div>

    <!-- Prioridad -->
    <div class="filter-group">
      <div class="filter-group-label">Prioridad</div>
      ${filterPriority.map(p => _checkItemColor(p, state.selPriority.includes(p), priorityColor[p])).join('')}
    </div>

    <!-- Sensación -->
    <div class="filter-group">
      <div class="filter-group-label">Sensación</div>
      <div class="filter-chip-group">
        ${filterSensation.map(s => `
          <button class="filter-chip ${state.selSensation.includes(s)?'checked':''}" data-type="sensation" data-val="${s}">${s}</button>
        `).join('')}
      </div>
    </div>

    <button id="clear-filters-bottom" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;margin-top:8px">
      Limpiar filtros
    </button>
  `;
}

function _checkItem(label, checked, type) {
  return `
    <label class="filter-check" data-type="${type}" data-val="${label}">
      <div class="filter-check-box ${checked?'checked':''}">
        ${checked ? `<span style="color:#fff">${icon('check', 9)}</span>` : ''}
      </div>
      <span class="filter-check-label">${label}</span>
    </label>
  `;
}

function _checkItemColor(label, checked, color) {
  return `
    <label class="filter-check" data-type="priority" data-val="${label}">
      <div class="filter-check-box" style="${checked?`background:${color};border-color:${color}`:''}">
        ${checked ? `<span style="color:#fff">${icon('check', 9)}</span>` : ''}
      </div>
      <span class="filter-check-label">${label}</span>
      <span style="margin-left:auto;width:6px;height:6px;border-radius:50%;background:${color};display:inline-block"></span>
    </label>
  `;
}

function _renderResults() {
  const filtered = getFiltered();
  const afc = activeFilterCount();

  return `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px">
      <div>
        ${state.query
          ? `<div style="font-size:14px;color:#fff;margin-bottom:2px">Resultados para <span style="color:var(--accent)">"${state.query}"</span></div>
             <div style="font-size:12px;color:var(--muted2)">${filtered.length} coincidencia${filtered.length!==1?'s':''} encontrada${filtered.length!==1?'s':''}${afc>0?` · ${afc} filtro${afc!==1?'s':''} aplicado${afc!==1?'s':''}`:''}</div>`
          : `<div style="font-size:14px;color:#fff;margin-bottom:2px">Síntomas frecuentes${afc>0?` <span style="color:var(--accent)">· ${afc} filtro${afc!==1?'s':''}</span>`:''}</div>
             <div style="font-size:12px;color:var(--muted2)">${filtered.length} resultado${filtered.length!==1?'s':''} disponible${filtered.length!==1?'s':''}</div>`}
      </div>
      ${state.query ? `<button id="new-search" class="btn btn-ghost btn-sm">Nueva búsqueda</button>` : ''}
    </div>

    <div id="results-list">
      ${filtered.length === 0 ? `
        <div class="empty-state">
          <div class="empty-icon">${icon('search', 20)}</div>
          <p class="empty-title">Sin resultados para los filtros seleccionados</p>
          <button id="clear-empty" style="color:var(--accent);font-size:12px;background:none;border:none;cursor:pointer;font-family:var(--font)">Limpiar filtros</button>
        </div>
      ` : filtered.map((r, i) => `
        <div class="result-card ${i===0?'top':''}" style="cursor:pointer" data-result-id="${r.id}">
          <div class="result-icon" style="background:${priorityColor[r.priority]}18;border-color:${priorityColor[r.priority]}40">
            ${r.priority==='Baja'
              ? `<span style="color:${priorityColor[r.priority]}">${icon('checkCircle', 14)}</span>`
              : `<span style="color:${priorityColor[r.priority]}">${icon('alertTriangle', 14)}</span>`}
          </div>
          <div class="result-body">
            <div class="result-title">
              ${r.title}
              <span style="padding:2px 8px;border-radius:4px;font-size:10px;font-weight:500;background:${priorityColor[r.priority]}22;color:${priorityColor[r.priority]};border:1px solid ${priorityColor[r.priority]}40">
                ${priorityLabel[r.priority]}
              </span>
            </div>
            <p class="result-desc">${r.desc}</p>
            <div class="result-tags">
              ${r.tags.map(t => `<span class="tag">${t}</span>`).join('')}
            </div>
          </div>
          <div class="result-cta">
            <button class="btn ${i===0?'btn-primary btn-sm':'btn-ghost btn-sm'}" data-result-id="${r.id}">
              Ver análisis ${i===0?icon('chevronRight',12):''}
            </button>
          </div>
        </div>
      `).join('')}
    </div>

    <!-- AI hint -->
    <div style="margin-top:20px;display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg)">
      <div style="width:28px;height:28px;background:rgba(224,48,48,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <span style="color:var(--accent)">${icon('lightbulb', 12)}</span>
      </div>
      <span style="color:var(--muted);font-size:12px">
        Describe el síntoma con tus propias palabras y la IA lo analiza.
        <em style="color:rgba(255,255,255,0.5)">"ruido metálico al frenar en reversa"</em>
        o usa los filtros para encontrar causas rápido.
      </span>
    </div>
  `;
}

function _render(container) {
  container.innerHTML = `
    <div class="diag-page">
      <!-- Search header -->
      <div class="diag-search-bar">
        <div class="diag-search-inner">
          <div class="search-wrap">
            <div class="search-input-group">
              <span style="color:var(--muted2);flex-shrink:0">${icon('search', 15)}</span>
              <input id="diag-input" class="search-input" placeholder='Describe el síntoma: "mi carro hace ruido raro al frenar"...' value="${state.inputValue}" autofocus/>
              ${state.inputValue ? `
                <button id="clear-input" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:flex">${icon('x', 13)}</button>
              ` : ''}
              <button id="mic-btn" style="color:var(--muted2);background:none;border:none;cursor:pointer;display:flex;transition:color 0.15s" title="Micrófono (próximamente)">
                ${icon('mic', 13)}
              </button>
            </div>
            <button id="analyze-btn" class="btn btn-primary" style="flex-shrink:0" ${!state.inputValue.trim()||state.analyzing?'disabled':''}>
              ${state.analyzing ? `<span class="spinner"></span> Analizando...` : `Analizar ${icon('arrowRight', 14)}`}
            </button>
            <button id="mobile-filter-btn" class="btn btn-ghost btn-sm" style="flex-shrink:0;position:relative">
              ${icon('sliders', 13)}
              ${activeFilterCount()>0?`<span class="notif-badge" style="position:absolute;top:-4px;right:-4px">${activeFilterCount()}</span>`:''}
            </button>
          </div>
          ${state.analyzing ? `
            <div class="ai-progress" style="margin-top:12px">
              <span class="spinner" style="border-color:rgba(224,48,48,0.4);border-top-color:var(--accent)"></span>
              <span>La IA está analizando tu consulta...</span>
            </div>
          ` : ''}
        </div>
      </div>

      <!-- Main: sidebar + content -->
      <div class="diag-main">
        <!-- Desktop sidebar -->
        <aside class="diag-sidebar" id="diag-sidebar">
          ${_renderFilterPanel()}
        </aside>

        <!-- Mobile filters overlay -->
        ${state.showMobileFilters ? `
          <div id="mobile-filter-overlay" style="position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.6)"></div>
          <div style="position:fixed;left:0;top:0;bottom:0;width:260px;background:var(--bg2);border-right:1px solid var(--border2);padding:20px 16px;overflow-y:auto;z-index:101">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
              <span style="color:#fff;font-size:14px">Filtros</span>
              <button id="close-mobile-filters" style="background:none;border:none;cursor:pointer;color:var(--muted2)">${icon('x', 16)}</button>
            </div>
            ${_renderFilterPanel()}
          </div>
        ` : ''}

        <!-- Results area -->
        <div class="diag-content" id="diag-content">
          ${_renderResults()}
        </div>
      </div>
    </div>
  `;

  _attachEvents(container);
}

function _attachEvents(container) {
  // Search input
  const input = document.getElementById('diag-input');
  input.addEventListener('input', e => { state.inputValue = e.target.value; });
  input.addEventListener('keydown', e => { if (e.key === 'Enter') _doAnalyze(); });

  document.getElementById('analyze-btn')?.addEventListener('click', _doAnalyze);
  document.getElementById('clear-input')?.addEventListener('click', () => {
    state.inputValue = ''; state.query = ''; _render(container);
  });

  document.getElementById('new-search')?.addEventListener('click', () => {
    state.inputValue = ''; state.query = ''; state.selZones = []; state.selWhen = []; state.selPriority = []; state.selSensation = [];
    _render(container);
  });

  document.getElementById('mobile-filter-btn')?.addEventListener('click', () => {
    state.showMobileFilters = !state.showMobileFilters; _render(container);
  });

  document.getElementById('mobile-filter-overlay')?.addEventListener('click', () => {
    state.showMobileFilters = false; _render(container);
  });

  document.getElementById('close-mobile-filters')?.addEventListener('click', () => {
    state.showMobileFilters = false; _render(container);
  });

  // Filter checkboxes
  container.querySelectorAll('.filter-check').forEach(el => {
    el.addEventListener('click', () => {
      const type = el.dataset.type;
      const val = el.dataset.val;
      if (type === 'zone') state.selZones = toggle(state.selZones, val);
      else if (type === 'when') state.selWhen = toggle(state.selWhen, val);
      else if (type === 'priority') state.selPriority = toggle(state.selPriority, val);
      _render(container);
    });
  });

  container.querySelectorAll('.filter-chip').forEach(el => {
    el.addEventListener('click', () => {
      state.selSensation = toggle(state.selSensation, el.dataset.val);
      _render(container);
    });
  });

  document.getElementById('clear-filters')?.addEventListener('click', _clearFilters);
  document.getElementById('clear-filters-bottom')?.addEventListener('click', _clearFilters);
  document.getElementById('clear-empty')?.addEventListener('click', _clearFilters);

  // Result cards
  container.querySelectorAll('[data-result-id]').forEach(el => {
    el.addEventListener('click', () => navigate(`/resultado/${el.dataset.resultId}`));
  });

  function _clearFilters() {
    state.selZones = []; state.selWhen = []; state.selPriority = []; state.selSensation = [];
    _render(container);
  }
}

function _doAnalyze() {
  if (!state.inputValue.trim() || state.analyzing) return;
  state.analyzing = true;
  _render(document.getElementById('page-content'));
  setTimeout(() => {
    state.query = state.inputValue;
    state.analyzing = false;
    _render(document.getElementById('page-content'));
  }, 1400);
}
