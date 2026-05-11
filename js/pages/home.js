// ══════════════════════════════════════════════
// CARSENSE — Home Page
// ══════════════════════════════════════════════
import { icon } from '../icons.js';

const systems = [
  { id: 'motor', label: 'Motor y Combustible', color: '#e03030' },
  { id: 'suspension', label: 'Suspensión', color: '#f59e0b' },
  { id: 'electrico', label: 'Sistema Eléctrico', color: '#3b82f6' },
  { id: 'enfriamiento', label: 'Enfriamiento', color: '#06b6d4' },
  { id: 'frenos', label: 'Sistema de Frenos', color: '#10b981' },
  { id: 'escape', label: 'Sistema de Escape', color: '#f97316' },
];

const symptoms = [
  { id: 1, iconName: 'settings', title: 'Ruido al Frenar', desc: 'Chirridos, rechinidos o golpes al aplicar los frenos.', system: 'Sistema de Frenos', severity: 'alta', highlight: false },
  { id: 2, iconName: 'activity', title: 'Vibración en Dirección', desc: 'El volante tiembla o vibra a ciertas velocidades al frenar.', system: 'Suspensión / Frenos', severity: 'media', highlight: true },
  { id: 3, iconName: 'thermometer', title: 'Motor Recalentado', desc: 'Indicador de temperatura sube o hay vapor bajo el cofre.', system: 'Sistema de Enfriamiento', severity: 'alta', highlight: false },
  { id: 4, iconName: 'settings', title: 'Pérdida de Potencia', desc: 'El auto siente lento, no sube bien o falla al subir pendientes.', system: 'Motor / Combustible', severity: 'media', highlight: false },
  { id: 5, iconName: 'zap', title: 'Luz de Check Engine', desc: 'Se encendió el indicador de error o falla en el tablero.', system: 'Sistema Eléctrico', severity: 'baja', highlight: false },
  { id: 6, iconName: 'wind', title: 'Olor a Quemado', desc: 'Olor inusual a quemado, aceite o combustible mientras conduce.', system: 'Motor / Frenos', severity: 'alta', highlight: false },
];

const severityColor = { alta: '#e03030', media: '#f59e0b', baja: '#10b981' };

const carSVG = `
<svg viewBox="0 0 480 220" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto">
  <defs>
    <radialGradient id="hglow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#e03030" stop-opacity="0.15"/>
      <stop offset="100%" stop-color="#e03030" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <ellipse cx="240" cy="110" rx="180" ry="80" fill="url(#hglow)"/>
  <!-- Car body -->
  <rect x="80" y="120" width="320" height="55" rx="12" style="fill:var(--bg4)" stroke="#e03030" stroke-width="1.2" stroke-opacity="0.5"/>
  <!-- Roof -->
  <path d="M140 120 Q160 75 200 68 L280 68 Q320 75 340 120Z" style="fill:var(--bg3)" stroke="#e03030" stroke-width="1" stroke-opacity="0.4"/>
  <!-- Windshield -->
  <path d="M165 118 Q178 80 205 74 L275 74 Q302 80 315 118Z" style="fill:var(--bg2)" fill-opacity="0.8"/>
  <!-- Windows -->
  <path d="M168 115 L178 83 L205 76 L275 76 L302 83 L312 115Z" fill="none" stroke="#3b82f6" stroke-width="0.8" stroke-opacity="0.4"/>
  <!-- Wheels -->
  <circle cx="145" cy="175" r="28" style="fill:var(--bg3)" stroke="#e03030" stroke-width="2" stroke-opacity="0.7"/>
  <circle cx="145" cy="175" r="16" fill="none" stroke="#e03030" stroke-width="1.5" stroke-opacity="0.5"/>
  <circle cx="335" cy="175" r="28" style="fill:var(--bg3)" stroke="#e03030" stroke-width="2" stroke-opacity="0.7"/>
  <circle cx="335" cy="175" r="16" fill="none" stroke="#e03030" stroke-width="1.5" stroke-opacity="0.5"/>
  <!-- Headlights -->
  <rect x="82" y="128" width="22" height="14" rx="4" fill="#e03030" fill-opacity="0.8"/>
  <rect x="376" y="128" width="22" height="14" rx="4" fill="#f59e0b" fill-opacity="0.6"/>
  <!-- Pulse line diagnostic -->
  <polyline points="70,200 100,200 115,185 130,215 145,195 165,200 200,200" stroke="#e03030" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.7"/>
  <!-- System indicators -->
  <circle cx="145" cy="145" r="5" fill="#10b981" fill-opacity="0.9"/>
  <circle cx="200" cy="100" r="5" fill="#e03030" fill-opacity="0.9"/>
  <circle cx="280" cy="100" r="5" fill="#f59e0b" fill-opacity="0.9"/>
  <circle cx="335" cy="145" r="5" fill="#3b82f6" fill-opacity="0.9"/>
  <circle cx="240" cy="85" r="5" fill="#06b6d4" fill-opacity="0.9"/>
  <circle cx="240" cy="150" r="5" fill="#f97316" fill-opacity="0.9"/>
  <!-- Connecting lines -->
  <line x1="145" y1="145" x2="200" y2="100" stroke="#e03030" stroke-width="0.5" stroke-opacity="0.3" stroke-dasharray="4,3"/>
  <line x1="335" y1="145" x2="280" y2="100" stroke="#3b82f6" stroke-width="0.5" stroke-opacity="0.3" stroke-dasharray="4,3"/>
  <line x1="240" y1="85" x2="240" y2="150" stroke="#06b6d4" stroke-width="0.5" stroke-opacity="0.3" stroke-dasharray="4,3"/>
</svg>`;

export function renderHome(container) {
  container.innerHTML = `
    <div class="min-h-full">
      <!-- Hero -->
      <section class="hero">
        <div class="hero-glow"></div>
        <div class="hero-grid"></div>
        <div class="hero-inner">
          <!-- Text left -->
          <div style="flex:1;max-width:480px">
            <div class="hero-badge animate-up">
              <span class="badge-ia">IA ACTIVA</span>
              <span style="color:var(--muted);font-size:12px">Asistente Automotriz</span>
            </div>
            <h1 class="hero-h1 animate-up delay-1">Entiende tu auto,</h1>
            <h1 class="hero-h1 hero-h1-accent animate-up delay-2">toma mejores decisiones.</h1>
            <p class="hero-desc animate-up delay-3">
              Describe el síntoma con tus propias palabras. La inteligencia artificial analiza el problema y te orienta con explicaciones claras, sin tecnicismos.
            </p>
            <div class="hero-ctas animate-up delay-4">
              <a href="#/diagnostico" class="btn btn-primary">
                Diagnosticar ahora ${icon('arrowRight', 14)}
              </a>
              <a href="#/sistemas" class="btn btn-outline">
                Ver sistemas del auto
              </a>
            </div>
            <div class="hero-systems animate-up delay-5">
              ${systems.map(s => `
                <a href="#/sistemas/${s.id}" class="system-pill"
                   style="background:${s.color}22;border:1px solid ${s.color}44;color:${s.color}">
                  <span class="system-pill-dot" style="background:${s.color}"></span>
                  ${s.label}
                </a>
              `).join('')}
            </div>
          </div>

          <!-- Diagram right -->
          <div class="hero-diagram animate-up delay-3">
            <div class="diagram-panel">
              <div class="diagram-panel-header">
                <span>Sistemas detectados</span>
                <span style="color:var(--accent)">${systems.length} sistemas activos</span>
              </div>
              ${carSVG}
              <div class="diagram-legend">
                ${systems.map(s => `
                  <a href="#/sistemas/${s.id}" class="diagram-legend-item">
                    <span class="legend-dot" style="background:${s.color}"></span>
                    <span style="color:var(--text3);font-size:12px">${s.label.split(' ')[0]}</span>
                  </a>
                `).join('')}
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats bar -->
      <section class="stats-bar">
        <div class="stats-inner">
          ${[
            { label: 'Diagnósticos realizados', value: '18k+' },
            { label: 'Usuarios activos', value: '2,400+' },
            { label: 'Sistemas cubiertos', value: '6' },
            { label: 'Precisión IA', value: '94%' },
          ].map(s => `
            <div class="stat-item">
              <span class="stat-value">${s.value}</span>
              <span class="stat-label">${s.label}</span>
            </div>
          `).join('')}
        </div>
      </section>

      <!-- Symptoms -->
      <section class="symptoms-section">
        <div class="symptoms-inner">
          <div class="symptoms-header">
            <h2 class="section-title">
              <span class="section-title-bar"></span>
              SÍNTOMAS FRECUENTES
            </h2>
            <a href="#/diagnostico" style="color:var(--accent);font-size:12px;display:flex;align-items:center;gap:4px;transition:gap 0.15s">
              Ver Todos ${icon('chevronRight', 13)}
            </a>
          </div>

          <div class="symptoms-grid">
            ${symptoms.map(s => `
              <a href="#/resultado/${s.id}" class="symptom-card ${s.highlight ? 'highlight' : ''}">
                <div class="symptom-icon" style="background:${severityColor[s.severity]}22">
                  <span style="color:${severityColor[s.severity]}">${icon(s.iconName, 15)}</span>
                </div>
                <div style="flex:1;min-width:0">
                  <div class="symptom-title">${s.title}</div>
                  <p class="symptom-desc">${s.desc}</p>
                  <div class="symptom-footer">
                    <span class="tag">${s.system}</span>
                    <div style="display:flex;align-items:center;gap:4px">
                      <span class="severity-dot" style="background:${severityColor[s.severity]}"></span>
                      <span style="font-size:12px;color:${severityColor[s.severity]}">
                        ${s.severity.charAt(0).toUpperCase() + s.severity.slice(1)}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="symptom-arrow">${icon('arrowRight', 14)}</div>
              </a>
            `).join('')}
          </div>
        </div>
      </section>
    </div>
  `;
}
