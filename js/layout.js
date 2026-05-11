// ══════════════════════════════════════════════
// CARSENSE — Layout (Header + Nav + Breadcrumbs)
// ══════════════════════════════════════════════
import { getUser, logout } from './auth.js';
import { navigate } from './router.js';
import { icon } from './icons.js';
import { notifications, notifTypeColor, labelMap } from './data.js';

const logoSVG = `
<svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M20 2L36 11V29L20 38L4 29V11L20 2Z" fill="#e03030" fill-opacity="0.15" stroke="#e03030" stroke-width="1.5"/>
  <path d="M13 22 L13.5 19 Q14.5 16.5 17 16 L23 16 Q25.5 16.5 26.5 19 L27 22 Z" fill="#e03030" fill-opacity="0.9"/>
  <path d="M14.5 21 L15 18.5 L25 18.5 L25.5 21 Z" fill="#0a0a0e" fill-opacity="0.6"/>
  <circle cx="15" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <circle cx="25" cy="23.5" r="2.5" fill="#e03030" fill-opacity="0.7"/>
  <line x1="13" y1="22" x2="27" y2="22" stroke="#e03030" stroke-width="1.5" stroke-linecap="round"/>
  <path d="M10 27 L14 27 L15.5 24.5 L17 29 L20 25 L22 27 L30 27" stroke="#e03030" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" stroke-opacity="0.85"/>
</svg>`;

const navItems = [
  { path: '/', label: 'Inicio', iconName: 'home' },
  { path: '/diagnostico', label: 'Diagnóstico', iconName: 'search' },
  { path: '/sistemas', label: 'Sistemas', iconName: 'cpu' },
];

export function renderLayout(currentPath) {
  renderHeader(currentPath);
  renderBreadcrumbs(currentPath);
  renderMobileNav(currentPath);
}

function isActive(navPath, currentPath) {
  if (navPath === '/') return currentPath === '/';
  return currentPath.startsWith(navPath);
}

function renderHeader(currentPath) {
  const header = document.getElementById('app-header');
  const user = getUser();

  header.innerHTML = `
    <!-- Logo -->
    <a href="#/" class="header-logo">
      ${logoSVG}
      <span class="logo-text"><span class="white">CAR</span><span class="red">SENSE</span></span>
    </a>

    <!-- Nav links (desktop) -->
    <nav class="header-nav">
      ${navItems.map(item => `
        <a href="#${item.path}" class="header-nav-link ${isActive(item.path, currentPath) ? 'active' : ''}">
          ${icon(item.iconName, 13)}
          <span>${item.label}</span>
        </a>
      `).join('')}
    </nav>

    <div class="header-spacer"></div>

    <!-- Right actions -->
    <div class="header-actions">
      <!-- Theme toggle -->
      <button class="theme-toggle" id="theme-toggle-btn" title="${document.body.classList.contains('light') ? 'Modo oscuro' : 'Modo claro'}">
        ${document.body.classList.contains('light') ? icon('moon', 14) : icon('sun', 14)}
      </button>

      ${user ? `
        <!-- Bell -->
        <div style="position:relative">
          <button class="icon-btn" id="notif-btn" title="Notificaciones" style="display:flex">
            ${icon('bell', 13)}
            <span class="notif-badge">${notifications.length}</span>
          </button>
          <div id="notif-dropdown" class="notif-dropdown hidden"></div>
        </div>

        <!-- Avatar -->
        <a href="#/perfil" class="user-avatar">${user.initials}</a>

        <!-- Logout -->
        <button class="icon-btn danger" id="logout-btn" title="Cerrar sesión" style="display:flex">
          ${icon('logout', 13)}
        </button>
      ` : `
        <a href="#/login" class="header-nav-link" style="font-size:12px;gap:6px">
          ${icon('login', 13)}
          <span>Iniciar sesión</span>
        </a>
        <a href="#/registro" class="btn btn-primary btn-sm">Registrarse</a>
      `}
    </div>
  `;

  // Theme toggle
  document.getElementById('theme-toggle-btn').addEventListener('click', () => {
    const isLight = document.body.classList.toggle('light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    const btn = document.getElementById('theme-toggle-btn');
    btn.title = isLight ? 'Modo oscuro' : 'Modo claro';
    btn.innerHTML = isLight ? icon('moon', 14) : icon('sun', 14);
  });

  // Events
  if (user) {
    document.getElementById('logout-btn').addEventListener('click', () => {
      logout();
      navigate('/');
    });

    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle('hidden');
      if (!notifDropdown.classList.contains('hidden')) {
        renderNotifDropdown();
      }
    });
    document.addEventListener('click', () => notifDropdown.classList.add('hidden'), { once: false });
  }
}

function renderNotifDropdown() {
  const dd = document.getElementById('notif-dropdown');
  dd.innerHTML = `
    <div class="notif-header">
      <div style="display:flex;align-items:center;gap:8px">
        ${icon('bell', 13, 'color:var(--muted2)')}
        <span>Notificaciones</span>
      </div>
      <span class="notif-badge-count">${notifications.length} nuevas</span>
    </div>
    ${notifications.map(n => `
      <div class="notif-item">
        <div class="notif-dot" style="background:${notifTypeColor[n.type]}"></div>
        <div style="flex:1;min-width:0">
          <div class="notif-title">${n.title}</div>
          <div class="notif-desc">${n.desc}</div>
          <div class="notif-time">${n.time}</div>
        </div>
      </div>
    `).join('')}
    <div class="notif-footer">
      <button>Ver todas las notificaciones</button>
    </div>
  `;
  dd.classList.remove('hidden');
}

function renderBreadcrumbs(currentPath) {
  const bar = document.getElementById('breadcrumbs-bar');
  if (currentPath === '/') { bar.classList.add('hidden'); return; }
  bar.classList.remove('hidden');

  const parts = currentPath.split('/').filter(Boolean);
  const crumbs = [{ label: 'Inicio', path: '/' }];
  let acc = '';
  for (const part of parts) {
    acc += '/' + part;
    crumbs.push({ label: labelMap[part] || part, path: acc });
  }

  bar.innerHTML = crumbs.map((crumb, i) => `
    ${i > 0 ? `<span class="breadcrumb-sep">${icon('chevronRight', 10)}</span>` : ''}
    ${i === crumbs.length - 1
      ? `<span class="breadcrumb-current">${crumb.label}</span>`
      : `<a href="#${crumb.path}">${crumb.label}</a>`
    }
  `).join('');
}

function renderMobileNav(currentPath) {
  const nav = document.getElementById('mobile-nav');
  const user = getUser();
  nav.innerHTML = `
    ${navItems.map(item => `
      <a href="#${item.path}" class="mobile-nav-link ${isActive(item.path, currentPath) ? 'active' : ''}">
        ${icon(item.iconName, 18)}
        <span>${item.label}</span>
      </a>
    `).join('')}
    ${user
      ? `<a href="#/perfil" class="mobile-nav-link ${isActive('/perfil', currentPath) ? 'active' : ''}">
          ${icon('user', 18)}
          <span>Perfil</span>
        </a>`
      : `<a href="#/login" class="mobile-nav-link">
          ${icon('login', 18)}
          <span>Entrar</span>
        </a>`
    }
  `;
}
