// ══════════════════════════════════════════════
// CARSENSE — Groq AI Service (via server proxy)
// ══════════════════════════════════════════════

const PROXY_URL = '/api/endpoints/grog.php';

function buildSystemPrompt(ctx) {
  const { result, system, components } = ctx;

  const compList = components
    .map(c => {
      const estado = c.wearStatus === 'critical' ? 'CRÍTICO' : c.wearStatus === 'warning' ? 'REVISAR' : 'OK';
      return `  - ${c.name} [${estado}, desgaste ${c.wear}%]: ${c.desc}`;
    })
    .join('\n');

  return `Eres CarSense AI, el asistente automotriz inteligente de la plataforma CarSense.
Tu misión es ayudar a los usuarios a entender problemas de su vehículo de forma simple, clara y amigable — como un mecánico de confianza, no como un manual técnico.

═══ DIAGNÓSTICO ACTUAL DEL USUARIO ═══
Problema detectado: ${result.title}
Descripción: ${result.desc}
Sistema afectado: ${result.system}
Nivel de urgencia: ${result.priority}
Síntomas reportados: ${result.tags.join(', ')}
Zona del vehículo: ${result.zones.join(', ')}
Cuándo ocurre: ${result.when.join(', ')}

═══ INFORMACIÓN DEL SISTEMA (${system.name}) ═══
${system.desc}
Criticidad del sistema: ${system.criticality}

Componentes y estado actual:
${compList}

═══ INSTRUCCIONES DE COMPORTAMIENTO ═══
1. Responde SIEMPRE en español, de forma conversacional y empática
2. Usa lenguaje simple: explica como si el usuario no supiera nada de mecánica
3. Sé conciso: máximo 3 párrafos breves. Prefiere párrafos naturales sobre listas largas
4. Usa **negritas** con doble asterisco para resaltar datos clave (precios estimados, medidas, plazos urgentes)
5. Para urgencia ALTA, insiste en visitar un mecánico pronto. Para Media o Baja, da más autonomía
6. Cuando menciones precios o costos, SIEMPRE expréesalos en **pesos mexicanos (MXN)**. Nunca uses dólares u otras monedas. Si no tienes el dato exacto para México, da un rango orientativo en MXN y aclara que puede variar según taller y región
7. Si no tienes el dato exacto, orienta de forma general y dilo con honestidad
7. Si preguntan algo fuera del ámbito automotriz, redirígelo amablemente al diagnóstico
8. Nunca digas que eres un modelo de lenguaje o IA — eres el asistente de CarSense`;
}

export async function chatWithGemini(userMessage, conversationHistory, diagnosticCtx) {
  const messages = [
    { role: 'system', content: buildSystemPrompt(diagnosticCtx) },
    ...conversationHistory,
    { role: 'user', content: userMessage },
  ];

  const response = await fetch(PROXY_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      messages,
      max_tokens: 500,
      temperature: 0.65,
      top_p: 0.9,
    }),
  });

  if (!response.ok) {
    const err = await response.json().catch(() => ({}));
    throw new Error(err.error || `Error ${response.status} en el servidor`);
  }

  const data = await response.json();
  const text = data.choices?.[0]?.message?.content;

  if (!text) throw new Error('Respuesta vacía del servidor');

  return text;
}
