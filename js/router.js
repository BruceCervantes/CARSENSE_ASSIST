// ══════════════════════════════════════════════
// CARSENSE — Hash Router
// ══════════════════════════════════════════════
// Routes: #/  #/login  #/registro  #/olvide-contrasena
//         #/diagnostico  #/resultado/:id  #/perfil
//         #/sistemas  #/sistemas/:id  #/sistemas/:id/:comp

let _routes = [];
let _currentRoute = null;

export function getRoute() { return _currentRoute; }

export function navigate(path) {
  window.location.hash = '#' + path;
}

export function initRouter(routes) {
  _routes = routes;
  window.addEventListener('hashchange', _resolve);
  _resolve();
}

function _resolve() {
  const hash = window.location.hash || '#/';
  const path = hash.replace(/^#/, '') || '/';

  for (const route of _routes) {
    const match = _matchRoute(route.path, path);
    if (match) {
      _currentRoute = { path, params: match.params, routePath: route.path };
      route.handler(match.params);
      return;
    }
  }

  // 404 fallback — redirect home
  navigate('/');
}

function _matchRoute(pattern, path) {
  const patParts = pattern.split('/').filter(Boolean);
  const pathParts = path.split('/').filter(Boolean);

  if (patParts.length !== pathParts.length) return null;

  const params = {};
  for (let i = 0; i < patParts.length; i++) {
    if (patParts[i].startsWith(':')) {
      params[patParts[i].slice(1)] = decodeURIComponent(pathParts[i]);
    } else if (patParts[i] !== pathParts[i]) {
      return null;
    }
  }
  return { params };
}
