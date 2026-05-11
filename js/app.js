// ══════════════════════════════════════════════
// CARSENSE — Main App Entry
// Vanilla HTML/CSS/JS — sin frameworks
// ══════════════════════════════════════════════
import { initRouter, navigate } from './router.js';
import { renderLayout } from './layout.js';

// Pages
import { renderHome } from './pages/home.js';
import { renderLogin } from './pages/login.js';
import { renderRegistro } from './pages/registro.js';
import { renderOlvideContrasena } from './pages/olvide-contrasena.js';
import { renderDiagnostico } from './pages/diagnostico.js';
import { renderResultado } from './pages/resultado.js';
import { renderPerfil } from './pages/perfil.js';
import { renderSistemas } from './pages/sistemas.js';
import { renderSistemaDetalle } from './pages/sistema-detalle.js';
import { renderComponenteDetalle } from './pages/componente-detalle.js';

// ── Apply saved theme ────────────────────────
if (localStorage.getItem('theme') === 'light') {
  document.body.classList.add('light');
}

// ── Auth pages (no layout) ───────────────────
const authRoutes = ['/login', '/registro', '/olvide-contrasena'];

function isAuthRoute(path) {
  return authRoutes.some(r => path === r || path.startsWith(r + '/'));
}

// ── Render page ──────────────────────────────
function renderPage(path, params = {}) {
  const app = document.getElementById('app');

  if (isAuthRoute(path)) {
    // Auth pages: full screen without layout
    app.innerHTML = '<div id="auth-container"></div>';
    const authContainer = document.getElementById('auth-container');
    if (path === '/login') renderLogin(authContainer);
    else if (path === '/registro') renderRegistro(authContainer);
    else if (path === '/olvide-contrasena') renderOlvideContrasena(authContainer);
    return;
  }

  // App pages: with layout
  app.innerHTML = `
    <div class="app-layout" id="app-layout">
      <header id="app-header"></header>
      <div id="breadcrumbs-bar" class="breadcrumbs-bar"></div>
      <main id="page-content" class="page-content"></main>
      <nav id="mobile-nav" class="mobile-nav"></nav>
    </div>
  `;

  renderLayout(path);

  const content = document.getElementById('page-content');

  if (path === '/') renderHome(content);
  else if (path === '/diagnostico') renderDiagnostico(content);
  else if (path.startsWith('/resultado/')) renderResultado(content, params);
  else if (path === '/perfil') renderPerfil(content);
  else if (path === '/sistemas') renderSistemas(content);
  else if (path.match(/^\/sistemas\/[^/]+$/) && !path.match(/^\/sistemas\/[^/]+\/[^/]+$/)) renderSistemaDetalle(content, params);
  else if (path.match(/^\/sistemas\/[^/]+\/[^/]+$/)) renderComponenteDetalle(content, params);
  else renderHome(content); // fallback
}

// ── Router setup ─────────────────────────────
initRouter([
  { path: '/', handler: () => renderPage('/') },
  { path: '/login', handler: () => renderPage('/login') },
  { path: '/registro', handler: () => renderPage('/registro') },
  { path: '/olvide-contrasena', handler: () => renderPage('/olvide-contrasena') },
  { path: '/diagnostico', handler: () => renderPage('/diagnostico') },
  { path: '/resultado/:id', handler: (p) => renderPage('/resultado/' + p.id, p) },
  { path: '/perfil', handler: () => renderPage('/perfil') },
  { path: '/sistemas', handler: () => renderPage('/sistemas') },
  { path: '/sistemas/:systemId', handler: (p) => renderPage('/sistemas/' + p.systemId, p) },
  { path: '/sistemas/:systemId/:componentId', handler: (p) => renderPage('/sistemas/' + p.systemId + '/' + p.componentId, p) },
]);
