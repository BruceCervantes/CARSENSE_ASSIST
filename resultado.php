<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

require_once 'includes/icons.php';
require_once 'includes/images.php';
require_once 'api/config/database.php';
require_once 'api/models/DiagnosticResult.php';
require_once 'api/models/System.php';

$id     = trim($_GET['id'] ?? '');
$result = $id ? DiagnosticResult::getBySlug($id) : null;
$sys    = $result ? System::getBySlug($result['system_slug']) : null;

if (!$result || !$sys) {
    $pageTitle   = 'Resultado no encontrado — CarSense';
    $currentPage = 'diagnostico';
    $breadcrumbs = [['label'=>'Diagnóstico','url'=>'diagnostico.php'],['label'=>'Resultado','url'=>null]];
    require 'includes/header.php';
    ?>
    <div class="empty-state" style="min-height:60vh">
      <div class="empty-icon"><?= icon('search', 28) ?></div>
      <p class="empty-title">Diagnóstico no encontrado</p>
      <a href="diagnostico.php" style="color:var(--accent);font-size:13px">← Volver al diagnóstico</a>
    </div>
    <?php
    require 'includes/footer.php';
    exit;
}

// ── Build causes (port of buildCauses JS function) ──
function statusColor(string $s): string {
    return $s === 'critical' ? '#e03030' : ($s === 'warning' ? '#f59e0b' : '#10b981');
}

function buildCauses(array $components): array {
    $causes = array_map(function($c) {
        $wear   = (int)($c['wear'] ?? 0);
        $status = $c['wear_status'] ?? 'ok';
        if ($status === 'critical')     $prob = min(95, 65 + $wear * 0.3);
        elseif ($status === 'warning')  $prob = min(64, 28 + $wear * 0.52);
        else                            $prob = max(12, $wear * 0.35);
        return [
            'label' => $c['name'],
            'desc'  => $c['description'],
            'prob'  => (int)round($prob),
            'color' => statusColor($status),
            'slug'  => $c['slug'],
        ];
    }, $components);
    usort($causes, fn($a,$b) => $b['prob'] - $a['prob']);
    return array_slice($causes, 0, 3);
}

// ── Greeting ──
function renderTextPhp(string $raw): string {
    $html = preg_replace('/\*\*(.*?)\*\*/u', '<strong style="color:#fff">$1</strong>', $raw);
    return nl2br($html);
}

$priorityColor  = ['Alta'=>'#e03030','Media'=>'#f59e0b','Baja'=>'#10b981'];
$pColor         = $priorityColor[$result['priority']] ?? '#f59e0b';
$priorityTag    = $result['priority'] === 'Alta' ? 'URGENTE' : ($result['priority'] === 'Media' ? 'REVISAR' : 'LEVE');
$urgLabel       = $result['priority'] === 'Alta' ? 'urgente — actúa pronto' : ($result['priority'] === 'Media' ? 'moderada' : 'baja');

$greetingRaw = "Hola, revisé el diagnóstico. El problema detectado es **" . htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8') . "** en tu **" . htmlspecialchars($result['system_name'], ENT_QUOTES, 'UTF-8') . "**. La urgencia es **{$urgLabel}**.\n\n" . htmlspecialchars($result['description'], ENT_QUOTES, 'UTF-8') . "\n\nPuedo explicarte cualquier duda sobre esto en términos simples. ¿Qué quieres saber?";
$greetingHtml = renderTextPhp($greetingRaw);

$causes             = buildCauses($sys['components']);
$affectedComponents = $sys['components'];
$sysImg             = $sys['image_url'] ?: ($systemImages[$result['system_slug']] ?? '');
$sysColor           = htmlspecialchars($sys['color']);

$questionsBySystem = [
    'frenos'       => ['¿Puedo seguir manejando con este problema?','¿Qué pasa si no lo reparo pronto?','¿Cuánto puede costar repararlo?','¿Cómo reviso las pastillas yo mismo?'],
    'motor'        => ['¿Qué tan urgente es llevarlo al taller?','¿Cuánto puede costar la reparación?','¿Cómo prevenir este problema?','¿Puedo manejar mientras tanto?'],
    'suspension'   => ['¿Es seguro manejar con este problema?','¿Cómo afecta esto a mis llantas?','¿Cuánto cuesta arreglarlo?','¿Cómo detectan esto en el taller?'],
    'electrico'    => ['¿Por qué se descarga la batería?','¿Cuándo debo reemplazar la batería?','¿Puedo arrancarlo con cables?','¿Cómo evito que vuelva a pasar?'],
    'enfriamiento' => ['¿Puedo manejar si el motor se calienta?','¿Qué hago si la temperatura sube?','¿Con qué frecuencia cambio el refrigerante?','¿Qué tan grave es el sobrecalentamiento?'],
    'escape'       => ['¿El olor es peligroso para mi salud?','¿Afecta al rendimiento del motor?','¿Es urgente repararlo?','¿Cuánto cuesta el catalizador?'],
];
$defaultQuestions = ['¿Qué tan urgente es llevarlo al taller?','¿Puedo seguir usando el auto?','¿Cuánto puede costar la reparación?','¿Cómo prevengo esto en el futuro?'];
$suggestedQs = $questionsBySystem[$result['system_slug']] ?? $defaultQuestions;

// Context object for grog.js (must match expected shape)
$chatCtx = [
    'result' => [
        'title'    => $result['title'],
        'desc'     => $result['description'],
        'system'   => $result['system_name'],
        'priority' => $result['priority'],
        'tags'     => $result['tags'],
        'zones'    => $result['zones'],
        'when'     => $result['when'],
    ],
    'system' => [
        'name'        => $sys['name'],
        'desc'        => $sys['description'],
        'criticality' => $sys['criticality'],
    ],
    'components' => array_map(fn($c) => [
        'name'       => $c['name'],
        'wearStatus' => $c['wear_status'],
        'wear'       => (int)$c['wear'],
        'desc'       => $c['description'],
    ], $sys['components']),
];

$pageTitle   = htmlspecialchars($result['title']) . ' — CarSense';
$currentPage = 'diagnostico';
$breadcrumbs = [
    ['label'=>'Diagnóstico', 'url'=>'diagnostico.php'],
    ['label'=>$result['title'], 'url'=>null],
];
require 'includes/header.php';
?>

<div class="resultado-page">
  <div class="resultado-inner">

    <!-- Header -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:16px">
      <div>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
          <div style="width:44px;height:44px;background:<?= $pColor ?>33;border:1px solid <?= $pColor ?>66;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="color:<?= $pColor ?>"><?= icon('alertTriangle', 18) ?></span>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <h1 style="font-size:1.25rem;color:#fff"><?= htmlspecialchars($result['title']) ?></h1>
              <span style="padding:2px 8px;background:<?= $pColor ?>33;border:1px solid <?= $pColor ?>66;color:<?= $pColor ?>;font-size:11px;border-radius:8px"><?= $priorityTag ?></span>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px">
              <?php foreach ($result['tags'] as $tag): ?>
                <span class="tag"><?= htmlspecialchars($tag) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <a href="sistema.php?id=<?= urlencode($result['system_slug']) ?>" class="btn btn-ghost btn-sm" style="display:inline-flex">
        Ver sistema <?= icon('chevronRight', 13) ?>
      </a>
    </div>

    <!-- Grid -->
    <div class="resultado-grid">

      <!-- Left column -->
      <div style="display:flex;flex-direction:column;gap:20px">

        <!-- Technical explanation -->
        <div class="card">
          <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px">Explicación Técnica</div>
          <p style="color:var(--text2);font-size:14px;line-height:1.7;margin-bottom:12px">
            El <span style="color:#fff;font-weight:500"><?= htmlspecialchars($sys['name']) ?></span> — <?= lcfirst(htmlspecialchars($sys['description'])) ?>
          </p>
          <p style="color:var(--text2);font-size:14px;line-height:1.7"><?= htmlspecialchars($result['description']) ?></p>
        </div>

        <!-- Probable causes -->
        <div class="card">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Posibles Causas</div>
            <span style="color:var(--muted2);font-size:11px">ordenado por probabilidad</span>
          </div>
          <?php foreach ($causes as $c): ?>
            <div style="margin-bottom:20px">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <div style="display:flex;align-items:center;gap:8px">
                  <div style="width:8px;height:8px;border-radius:50%;background:<?= $c['color'] ?>"></div>
                  <span style="color:#fff;font-size:14px"><?= htmlspecialchars($c['label']) ?></span>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                  <span style="color:<?= $c['color'] ?>;font-size:14px"><?= $c['prob'] ?>%</span>
                  <a href="componente.php?id=<?= urlencode($c['slug']) ?>&sistema=<?= urlencode($result['system_slug']) ?>" style="color:var(--muted2);display:flex"><?= icon('chevronRight', 12) ?></a>
                </div>
              </div>
              <p style="color:var(--muted2);font-size:12px;margin-bottom:8px;padding-left:16px"><?= htmlspecialchars($c['desc']) ?></p>
              <div class="causa-bar">
                <div class="causa-fill" style="width:<?= $c['prob'] ?>%;background:<?= $c['color'] ?>"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Visual system guide -->
        <div class="card" style="padding:0;overflow:hidden">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 20px 12px;flex-wrap:wrap;gap:8px">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em">Guía Visual del Sistema</div>
            <div style="display:flex;align-items:center;gap:6px;color:var(--muted2);font-size:11px">
              <?= icon('mapPin', 11) ?> Piezas afectadas resaltadas
            </div>
          </div>
          <div style="margin:0 20px 20px;border-radius:12px;overflow:hidden;border:1px solid <?= $sysColor ?>28;position:relative">
            <?php if ($sysImg): ?>
              <div style="height:240px;position:relative">
                <img src="<?= htmlspecialchars($sysImg) ?>" alt="<?= htmlspecialchars($sys['name']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy"/>
                <div style="position:absolute;inset:0;background:<?= $sysColor ?>;opacity:0.12"></div>
                <div style="position:absolute;inset:0;background:linear-gradient(to top,#131316 0%,transparent 50%)"></div>
                <div style="position:absolute;bottom:12px;left:12px;right:12px;display:flex;flex-wrap:wrap;gap:6px">
                  <?php foreach ($affectedComponents as $comp): ?>
                    <?php $sc = statusColor($comp['wear_status']); ?>
                    <a href="componente.php?id=<?= urlencode($comp['slug']) ?>&sistema=<?= urlencode($result['system_slug']) ?>"
                       style="display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:11px;background:<?= $sc ?>28;border:1px solid <?= $sc ?>50;color:<?= $sc ?>;text-decoration:none;backdrop-filter:blur(4px)">
                      <div style="width:6px;height:6px;border-radius:50%;background:<?= $sc ?>"></div>
                      <?= htmlspecialchars($comp['name']) ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php else: ?>
              <div style="height:192px;display:flex;align-items:center;justify-content:center;background:<?= $sysColor ?>0d">
                <div style="text-align:center">
                  <span style="color:var(--muted4)"><?= icon('wrench', 28) ?></span>
                  <div style="color:var(--muted4);font-size:11px;text-transform:uppercase;margin-top:8px">Diagrama <?= htmlspecialchars($sys['name']) ?></div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Maintenance -->
        <?php if (!empty($sys['maintenance'])): ?>
          <div class="card">
            <div style="color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px">Mantenimiento Sugerido</div>
            <div style="display:flex;flex-direction:column;gap:12px">
              <?php foreach ($sys['maintenance'] as $m): ?>
                <div style="display:flex;align-items:flex-start;gap:12px">
                  <span style="color:var(--green);flex-shrink:0;margin-top:2px"><?= icon('checkCircle', 15) ?></span>
                  <div>
                    <div style="color:#fff;font-size:14px"><?= htmlspecialchars($m['label']) ?></div>
                    <div style="color:var(--muted2);font-size:12px;margin-top:2px"><?= htmlspecialchars($m['interval_text']) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Warning -->
        <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);border-radius:var(--radius-xl);padding:16px;display:flex;align-items:flex-start;gap:12px">
          <span style="color:#f59e0b;flex-shrink:0;margin-top:2px"><?= icon('alertTriangle', 16) ?></span>
          <p style="color:rgba(192,192,160,1);font-size:12px;line-height:1.6">
            <span style="color:#f59e0b;font-weight:500">Aviso importante: </span>
            Esta orientación es educativa y no reemplaza la revisión de un mecánico certificado. Si el problema persiste o empeora, visita un taller lo antes posible.
          </p>
        </div>
      </div>

      <!-- AI Chat panel -->
      <div>
        <div class="chat-panel" id="chat-panel">

          <!-- System context -->
          <div style="padding:16px;border-bottom:1px solid var(--border);flex-shrink:0;background:<?= $sysColor ?>0f">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
              <span style="color:<?= $sysColor ?>bb;font-size:10px;text-transform:uppercase;letter-spacing:0.1em"><?= icon('cpu', 11) ?> Sistema afectado</span>
            </div>
            <a href="sistema.php?id=<?= urlencode($result['system_slug']) ?>" style="display:flex;align-items:center;gap:12px;margin-bottom:12px;text-decoration:none">
              <div style="width:56px;height:40px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1px solid <?= $sysColor ?>66">
                <?php if ($sysImg): ?>
                  <img src="<?= htmlspecialchars($sysImg) ?>" alt="" style="width:100%;height:100%;object-fit:cover" loading="lazy"/>
                <?php endif; ?>
              </div>
              <div>
                <div style="color:#fff;font-size:13px"><?= htmlspecialchars($sys['name']) ?></div>
                <div style="color:var(--muted2);font-size:11px;margin-top:2px;display:flex;align-items:center;gap:4px">Ver sistema completo <?= icon('chevronRight', 9) ?></div>
              </div>
            </a>
            <div style="display:flex;flex-wrap:wrap;gap:6px">
              <?php foreach ($affectedComponents as $comp): ?>
                <?php $sc = statusColor($comp['wear_status']); ?>
                <a href="componente.php?id=<?= urlencode($comp['slug']) ?>&sistema=<?= urlencode($result['system_slug']) ?>"
                   style="display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:8px;font-size:11px;background:<?= $sc ?>18;border:1px solid <?= $sc ?>40;color:<?= $sc ?>;text-decoration:none">
                  <div style="width:6px;height:6px;border-radius:50%;background:<?= $sc ?>"></div>
                  <?= htmlspecialchars($comp['name']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Assistant header -->
          <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);flex-shrink:0">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,var(--accent),#c02020);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 12px rgba(224,48,48,0.2)">
              <span style="color:#fff"><?= icon('bot', 14) ?></span>
            </div>
            <div style="flex:1">
              <div style="color:#fff;font-size:13px">Asistente IA</div>
              <div style="display:flex;align-items:center;gap:6px">
                <div style="width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse-dot 2s infinite"></div>
                <span style="color:var(--green);font-size:11px">Activo · Especialista en <?= htmlspecialchars(strtolower($sys['name'])) ?></span>
              </div>
            </div>
            <div style="background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:2px 8px;font-size:11px;color:var(--muted2)">Groq</div>
          </div>

          <!-- Messages -->
          <div class="chat-messages" id="chat-messages">
            <div class="chat-bubble-ai">
              <div class="chat-avatar" style="background:rgba(224,48,48,0.2);border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                <span style="color:var(--accent)"><?= icon('bot', 10) ?></span>
              </div>
              <div class="chat-text-ai" style="color:var(--text2);font-size:14px;line-height:1.7"><?= $greetingHtml ?></div>
            </div>
          </div>

          <!-- Suggested questions -->
          <div class="chat-suggestions">
            <div class="chat-suggestions-label">Preguntas frecuentes:</div>
            <div class="chat-suggestions-list">
              <?php foreach ($suggestedQs as $q): ?>
                <button class="chat-suggestion-btn" data-q="<?= htmlspecialchars($q) ?>"><?= htmlspecialchars($q) ?></button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Input -->
          <div class="chat-input-row">
            <div class="chat-input-wrap">
              <input class="chat-input" id="chat-input" placeholder="Pregunta sobre <?= htmlspecialchars(strtolower($sys['name'])) ?>..."/>
            </div>
            <button class="chat-send-btn" id="chat-send">
              <span style="color:#fff"><?= icon('send', 13) ?></span>
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

<script type="module">
import { chatWithGemini } from './js/grog.js';
import { icon }           from './js/icons.js';

var geminiHistory = [];
var isTyping      = false;
var chatCtx       = <?= json_encode($chatCtx) ?>;

var chatMsgs = document.getElementById('chat-messages');
var chatInput= document.getElementById('chat-input');
var sendBtn  = document.getElementById('chat-send');

function renderText(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong style="color:#fff">$1</strong>')
    .replace(/\n\n/g, '<br><br>')
    .replace(/\n/g,   '<br>');
}

function appendBubble(role, text) {
  var div = document.createElement('div');
  if (role === 'user') {
    div.className = 'chat-bubble-user';
    div.innerHTML =
      '<div class="chat-text-user">' + text + '</div>' +
      '<div class="chat-avatar" style="background:var(--bg4);width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center">' +
        '<span style="color:var(--muted)">' + icon('user', 10) + '</span>' +
      '</div>';
  } else {
    div.className = 'chat-bubble-ai';
    var style = role === 'error'
      ? 'color:#f59e0b;font-size:13px;line-height:1.6'
      : 'color:var(--text2);font-size:14px;line-height:1.7';
    div.innerHTML =
      '<div class="chat-avatar" style="background:rgba(224,48,48,0.2);border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center">' +
        '<span style="color:var(--accent)">' + icon('bot', 10) + '</span>' +
      '</div>' +
      '<div class="chat-text-ai" style="' + style + '">' + renderText(text) + '</div>';
  }
  chatMsgs.appendChild(div);
  chatMsgs.scrollTop = chatMsgs.scrollHeight;
}

var typingEl = null;
function showTyping() {
  typingEl = document.createElement('div');
  typingEl.className = 'chat-typing';
  typingEl.innerHTML =
    '<div class="chat-avatar" style="background:rgba(224,48,48,0.2);border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center">' +
      '<span style="color:var(--accent)">' + icon('bot', 10) + '</span>' +
    '</div>' +
    '<div class="typing-dots">' +
      '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>' +
    '</div>';
  chatMsgs.appendChild(typingEl);
  chatMsgs.scrollTop = chatMsgs.scrollHeight;
}

function hideTyping() {
  if (typingEl) { typingEl.remove(); typingEl = null; }
}

async function sendMsg(text) {
  var msg = (text || chatInput.value || '').trim();
  if (!msg || isTyping) return;

  chatInput.value = '';
  isTyping = true;
  sendBtn.disabled = true;

  appendBubble('user', msg);
  showTyping();

  try {
    var aiText = await chatWithGemini(msg, geminiHistory, chatCtx);
    geminiHistory.push({ role: 'user', content: msg });
    geminiHistory.push({ role: 'assistant', content: aiText });
    hideTyping();
    appendBubble('ai', aiText);
  } catch (err) {
    hideTyping();
    var isKeyMissing = err.message === 'API_KEY_NOT_CONFIGURED';
    var errText = isKeyMissing
      ? '⚠️ La API de Groq no está configurada. Agrega tu clave en **js/config.js** para activar el asistente.'
      : 'Ocurrió un error al contactar al asistente: ' + err.message + '. Por favor intenta de nuevo.';
    appendBubble('error', errText);
  } finally {
    isTyping = false;
    sendBtn.disabled = false;
  }
}

sendBtn.addEventListener('click', function(){ sendMsg(); });
chatInput.addEventListener('keydown', function(e){ if (e.key === 'Enter') sendMsg(); });

document.querySelectorAll('.chat-suggestion-btn').forEach(function(btn){
  btn.addEventListener('click', function(){ sendMsg(btn.dataset.q); });
});

chatMsgs.scrollTop = chatMsgs.scrollHeight;
</script>

<?php require 'includes/footer.php'; ?>
