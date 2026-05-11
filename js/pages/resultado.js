// ══════════════════════════════════════════════
// CARSENSE — Resultado Page (with AI Chat)
// ══════════════════════════════════════════════
import { icon } from '../icons.js';
import { systemImages, diagnosticResults, priorityColor } from '../data.js';

const causes = [
  { label: 'Pastillas de freno desgastadas', desc: 'Fricción directa metal-metal. Principal causa del chirrido metálico.', prob: 78, color: '#e03030', compId: 'pastillas-freno' },
  { label: 'Discos de freno irregulares', desc: 'Superficie rayada o deformada causa vibración en el pedal al frenar.', prob: 55, color: '#f59e0b', compId: 'disco-freno' },
  { label: 'Acumulación de polvo u óxido', desc: 'Normal después de lluvias o períodos sin uso. Desaparece tras pocos frenados.', prob: 28, color: '#3b82f6', compId: 'pinzas-freno' },
];

const affectedSystem = {
  id: 'frenos', name: 'Sistema de Frenos', color: '#10b981',
  components: [
    { id: 'pastillas-freno', name: 'Pastillas de Freno', status: 'critical' },
    { id: 'disco-freno', name: 'Disco de Freno', status: 'warning' },
    { id: 'pinzas-freno', name: 'Pinzas de Freno', status: 'ok' },
    { id: 'liquido-frenos', name: 'Líquido de Frenos', status: 'ok' },
  ],
};

const maintenance = [
  { label: 'Pastillas de Freno', interval: 'Cada 20,000 km o 1 año. Reemplazar cuando el grosor baje de 3mm.' },
  { label: 'Líquido de Frenos', interval: 'Cambiar cada 2 años o 40,000 km independientemente de su apariencia.' },
  { label: 'Discos de Freno', interval: 'Inspeccionar en cada cambio de pastillas. Reemplazar si hay ranuras profundas.' },
];

const suggestedQuestions = [
  '¿Cómo reviso las pastillas yo mismo?',
  '¿Qué pasa si no lo reparo pronto?',
  '¿Cuáles son las marcas de pastillas más confiables?',
  '¿Cuánto tiempo dura el cambio de pastillas?',
];

const aiResponses = {
  default: 'Basado en los síntomas descritos, te recomiendo visitar un mecánico certificado para una inspección visual. Esta orientación es educativa y no reemplaza el diagnóstico profesional presencial.',
  reviso: 'Para revisar las pastillas tú mismo: 1) Gira las ruedas y mira a través de los rayos, 2) El grosor mínimo seguro es **3mm**, 3) Si escuchas chirrido constante, probablemente ya están al límite. Usa una linterna para mejor visibilidad.',
  reparo: 'Si no repares las pastillas a tiempo: 1) Los **discos de freno** se dañarán (costo 3x mayor), 2) El tiempo de frenado aumentará, 3) En el peor caso, los frenos pueden fallar completamente. **Actúa en los próximos 3–5 días**.',
  marcas: 'Las marcas más confiables de pastillas son: **Brembo** (premium), **Akebono** (quietas y duraderas), **Bosch** (relación precio-calidad), **Wagner** (económicas pero efectivas). Para uso diario, Bosch o Wagner son excelentes opciones.',
  tiempo: 'El cambio de pastillas de freno tarda entre **30 minutos y 1.5 horas** dependiendo del vehículo. En un taller profesional suelen hacerlo en 45 minutos.',
};

function statusColor(s) { return s==='critical'?'#e03030':s==='warning'?'#f59e0b':'#10b981'; }
function statusLabel(s) { return s==='critical'?'Crítico':s==='warning'?'Revisar':'OK'; }

function getAIResponse(q) {
  const low = q.toLowerCase();
  if (low.includes('reviso') || low.includes('revisar') || low.includes('verificar')) return aiResponses.reviso;
  if (low.includes('reparo') || low.includes('pasa si')) return aiResponses.reparo;
  if (low.includes('marcas') || low.includes('confiables')) return aiResponses.marcas;
  if (low.includes('tiempo') || low.includes('cuánto')) return aiResponses.tiempo;
  return `Basado en tu consulta sobre "${q}", te recomiendo que un mecánico certificado revise los frenos en persona. Esta orientación es educativa.`;
}

function renderText(text) {
  return text.split('**').map((part, i) => i%2===1 ? `<strong style="color:#fff">${part}</strong>` : part).join('');
}

let messages = [];
let chatInput = '';
let isTyping = false;

function initMessages() {
  messages = [
  //  { role: 'ai', text: 'Hola, analicé tus síntomas. El **ruido metálico al frenar** a baja velocidad con vibración leve apunta principalmente a **desgaste de pastillas**. Te explico qué significa esto y qué opciones tienes.' },
  //  { role: 'question', text: '¿Puedo seguir manejando con este problema?' },
  //  { role: 'ai', text: 'Puedes en el corto plazo, pero **no es recomendable**. Las pastillas pueden fallar de forma repentina. Idealmente visita un taller en los próximos **2–3 días** para evitar daños mayores al disco.' },
  //  { role: 'user', text: '¿Cuánto me costaría la reparación aproximadamente?' },
  //  { role: 'ai', text: 'El costo varía según el vehículo y la región. En promedio: **Pastillas de freno**: $25–$80 USD. **Mano de obra**: $40–$120 USD. Total estimado: **$65–$300 USD** por eje delantero.' },
  ];
}

function renderMessages() {
  return messages.map(msg => {
    if (msg.role === 'question') return `
      <div class="chat-question"><p>💬 ${msg.text}</p></div>
    `;
    if (msg.role === 'user') return `
      <div class="chat-bubble-user">
        <div class="chat-text-user">${msg.text}</div>
        <div class="chat-avatar" style="background:var(--bg4);width:24px;height:24px;border-radius:50%;flex-shrink:0">
          <span style="color:var(--muted)">${icon('user', 10)}</span>
        </div>
      </div>
    `;
    return `
      <div class="chat-bubble-ai">
        <div class="chat-avatar" style="background:rgba(224,48,48,0.2);border-radius:50%;flex-shrink:0">
          <span style="color:var(--accent)">${icon('bot', 10)}</span>
        </div>
        <div class="chat-text-ai">${renderText(msg.text)}</div>
      </div>
    `;
  }).join('') + (isTyping ? `
    <div class="chat-typing">
      <div class="chat-avatar" style="background:rgba(224,48,48,0.2);border-radius:50%;flex-shrink:0">
        <span style="color:var(--accent)">${icon('bot', 10)}</span>
      </div>
      <div class="typing-dots">
        <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
      </div>
    </div>
  ` : '');
}

function scrollChat() {
  const el = document.getElementById('chat-messages');
  if (el) el.scrollTop = el.scrollHeight;
}

export function renderResultado(container, params) {
  initMessages();
  const sysImg = systemImages[affectedSystem.id];

  container.innerHTML = `
    <div class="resultado-page">
      <div class="resultado-inner">
        <!-- Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px">
          <div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
              <div style="width:44px;height:44px;background:rgba(224,48,48,0.2);border:1px solid rgba(224,48,48,0.4);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:var(--accent)">${icon('alertTriangle', 18)}</span>
              </div>
              <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                  <h1 style="font-size:1.25rem;color:#fff">Pastillas de Freno Desgastadas</h1>
                  <span style="padding:2px 8px;background:rgba(224,48,48,0.2);border:1px solid rgba(224,48,48,0.4);color:var(--accent);font-size:11px;border-radius:8px">URGENTE</span>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px">
                  ${['Ruido','Frenos','Baja velocidad','Metal'].map(t=>`<span class="tag">${t}</span>`).join('')}
                </div>
              </div>
            </div>
          </div>
          <a href="#/sistemas/frenos" class="btn btn-ghost btn-sm" style="display:inline-flex">
            Ver sistema ${icon('chevronRight', 13)}
          </a>
        </div>

        <!-- Two column grid -->
        <div class="resultado-grid">
          <!-- Main column -->
          <div style="display:flex;flex-direction:column;gap:20px">

            <!-- Explanation -->
            <div class="card">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Explicación Técnica</div>
              <p style="color:var(--text2);font-size:14px;line-height:1.7;margin-bottom:12px">
                El <span style="color:#fff;font-weight:500">sistema de frenos</span> reduce la velocidad del vehículo mediante la fricción entre las
                <span style="color:var(--accent);font-weight:500">pastillas</span> y el <span style="color:var(--accent);font-weight:500">disco de freno</span>.
                Cuando las pastillas alcanzan su límite de desgaste, un indicador metálico toca el disco y produce el chirrido característico.
              </p>
              <p style="color:var(--text2);font-size:14px;line-height:1.7">
                Los síntomas descritos —ruido al frenar a baja velocidad con ligera vibración en el pedal— indican desgaste avanzado. Ignorarlo puede dañar los discos y elevar significativamente el costo de reparación.
              </p>
            </div>

            <!-- Causes -->
            <div class="card">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Posibles Causas</div>
                <span style="color:var(--muted2);font-size:11px">ordenado por probabilidad</span>
              </div>
              ${causes.map(c => `
                <div style="margin-bottom:20px">
                  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                    <div style="display:flex;align-items:center;gap:8px">
                      <div style="width:8px;height:8px;border-radius:50%;background:${c.color}"></div>
                      <span style="color:#fff;font-size:14px">${c.label}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px">
                      <span style="color:${c.color};font-size:14px">${c.prob}%</span>
                      <a href="#/sistemas/frenos/${c.compId}" style="color:var(--muted2);display:flex">${icon('chevronRight', 12)}</a>
                    </div>
                  </div>
                  <p style="color:var(--muted2);font-size:12px;margin-bottom:8px;padding-left:16px">${c.desc}</p>
                  <div class="causa-bar">
                    <div class="causa-fill" style="width:${c.prob}%;background:${c.color}"></div>
                  </div>
                </div>
              `).join('')}
            </div>

            <!-- Visual diagram -->
            <div class="card" style="padding:0;overflow:hidden">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 20px 12px;flex-wrap:wrap;gap:8px">
                <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Guía Visual del Sistema</div>
                <div style="display:flex;align-items:center;gap:6px;color:var(--muted2);font-size:11px">
                  ${icon('mapPin', 11)} Piezas afectadas resaltadas
                </div>
              </div>
              <div style="margin:0 20px 20px;border-radius:12px;overflow:hidden;border:1px solid rgba(16,185,129,0.17);position:relative">
                ${sysImg ? `
                  <div style="height:240px;position:relative">
                    <img src="${sysImg}" alt="Sistema de Frenos" style="width:100%;height:100%;object-fit:cover"/>
                    <div style="position:absolute;inset:0;background:#10b981;opacity:0.12"></div>
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,#131316 0%,transparent 50%)"></div>
                    <!-- Component chips -->
                    <div style="position:absolute;bottom:12px;left:12px;right:12px;display:flex;flex-wrap:wrap;gap:6px">
                      ${affectedSystem.components.map(comp => {
                        const sc = statusColor(comp.status);
                        return `
                          <a href="#/sistemas/${affectedSystem.id}/${comp.id}" style="display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:11px;background:${sc}28;border:1px solid ${sc}50;color:${sc};text-decoration:none;transition:transform 0.15s;backdrop-filter:blur(4px)">
                            <div style="width:6px;height:6px;border-radius:50%;background:${sc}"></div>
                            ${comp.name}
                          </a>
                        `;
                      }).join('')}
                    </div>
                  </div>
                ` : `
                  <div style="height:192px;display:flex;align-items:center;justify-content:center;background:rgba(16,185,129,0.05)">
                    <div style="text-align:center">
                      <span style="color:var(--muted4)">${icon('wrench', 28)}</span>
                      <div style="color:var(--muted4);font-size:11px;text-transform:uppercase;margin-top:8px">Diagrama Sistema de Frenos</div>
                    </div>
                  </div>
                `}
              </div>
            </div>

            <!-- Maintenance -->
            <div class="card">
              <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px">Mantenimiento Sugerido</div>
              <div style="display:flex;flex-direction:column;gap:12px">
                ${maintenance.map(m => `
                  <div style="display:flex;align-items:flex-start;gap:12px">
                    <span style="color:var(--green);flex-shrink:0;margin-top:2px">${icon('checkCircle', 15)}</span>
                    <div>
                      <div style="color:#fff;font-size:14px">${m.label}</div>
                      <div style="color:var(--muted2);font-size:12px;margin-top:2px">${m.interval}</div>
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>

            <!-- Warning -->
            <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:var(--radius-xl);padding:16px;display:flex;align-items:flex-start;gap:12px">
              <span style="color:#f59e0b;flex-shrink:0;margin-top:2px">${icon('alertTriangle', 16)}</span>
              <p style="color:rgba(192,192,160,1);font-size:12px;line-height:1.6">
                <span style="color:#f59e0b;font-weight:500">Aviso importante: </span>
                Esta orientación es educativa y no reemplaza la revisión de un mecánico certificado. Si el ruido persiste o aumenta, visita un taller lo antes posible.
              </p>
            </div>
          </div>

          <!-- AI Chat panel -->
          <div>
            <div class="chat-panel" id="chat-panel">
              <!-- System context -->
              <div style="padding:16px;border-bottom:1px solid var(--border);flex-shrink:0;background:rgba(16,185,129,0.06)">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                  <span style="color:rgba(16,185,129,0.7);font-size:10px;text-transform:uppercase;letter-spacing:0.1em">${icon('cpu', 11, 'color:#10b98177')} Sistema afectado</span>
                </div>
                <a href="#/sistemas/${affectedSystem.id}" style="display:flex;align-items:center;gap:12px;margin-bottom:12px;text-decoration:none">
                  <div style="width:56px;height:40px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1px solid rgba(16,185,129,0.4)">
                    ${sysImg ? `<img src="${sysImg}" alt="" style="width:100%;height:100%;object-fit:cover"/>` : ''}
                  </div>
                  <div>
                    <div style="color:#fff;font-size:13px">${affectedSystem.name}</div>
                    <div style="color:var(--muted2);font-size:11px;margin-top:2px;display:flex;align-items:center;gap:4px">Ver sistema completo ${icon('chevronRight', 9)}</div>
                  </div>
                </a>
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                  ${affectedSystem.components.map(comp => {
                    const sc = statusColor(comp.status);
                    return `<a href="#/sistemas/${affectedSystem.id}/${comp.id}" style="display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:8px;font-size:11px;background:${sc}18;border:1px solid ${sc}40;color:${sc};text-decoration:none">
                      <div style="width:6px;height:6px;border-radius:50%;background:${sc}"></div>${comp.name}
                    </a>`;
                  }).join('')}
                </div>
              </div>

              <!-- AI header -->
              <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);flex-shrink:0">
                <div style="width:32px;height:32px;background:linear-gradient(135deg,var(--accent),#c02020);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 12px rgba(224,48,48,0.2)">
                  <span style="color:#fff">${icon('bot', 14)}</span>
                </div>
                <div style="flex:1">
                  <div style="color:#fff;font-size:13px">Asistente IA</div>
                  <div style="display:flex;align-items:center;gap:6px">
                    <div style="width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse-dot 2s infinite"></div>
                    <span style="color:var(--green);font-size:11px">Activo · Especialista en frenos</span>
                  </div>
                </div>
                <div style="background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:2px 8px;font-size:11px;color:var(--muted2)">IA</div>
              </div>

              <!-- Messages -->
              <div class="chat-messages" id="chat-messages">
                ${renderMessages()}
              </div>

              <!-- Suggested questions -->
              <div class="chat-suggestions">
                <div class="chat-suggestions-label">Preguntas frecuentes:</div>
                <div class="chat-suggestions-list">
                  ${suggestedQuestions.map(q => `
                    <button class="chat-suggestion-btn" data-q="${q}">${q}</button>
                  `).join('')}
                </div>
              </div>

              <!-- Input -->
              <div class="chat-input-row">
                <div class="chat-input-wrap">
                  <input class="chat-input" id="chat-input" placeholder="Pregunta sobre los frenos..." value="${chatInput}"/>
                </div>
                <button class="chat-send-btn" id="chat-send" ${isTyping?'disabled':''}>
                  <span style="color:#fff">${icon('send', 13)}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
      @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.4} }
    </style>
  `;

  scrollChat();
  _attachChatEvents(container);
}

function _attachChatEvents(container) {
  const sendMsg = (text) => {
    const msg = text || document.getElementById('chat-input').value;
    if (!msg.trim() || isTyping) return;
    messages.push({ role: 'user', text: msg });
    chatInput = '';
    isTyping = true;
    _updateChat();
    setTimeout(() => {
      isTyping = false;
      messages.push({ role: 'ai', text: getAIResponse(msg) });
      _updateChat();
    }, 1200);
  };

  const _updateChat = () => {
    const chatMsgs = document.getElementById('chat-messages');
    const chatInputEl = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send');
    if (chatMsgs) { chatMsgs.innerHTML = renderMessages(); scrollChat(); }
    if (chatInputEl) chatInputEl.value = '';
    if (sendBtn) sendBtn.disabled = isTyping;
  };

  document.getElementById('chat-send')?.addEventListener('click', () => sendMsg());
  document.getElementById('chat-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') sendMsg();
  });
  container.querySelectorAll('.chat-suggestion-btn').forEach(btn => {
    btn.addEventListener('click', () => sendMsg(btn.dataset.q));
  });
}
