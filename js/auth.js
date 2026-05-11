// ══════════════════════════════════════════════
// CARSENSE — Auth State Module
// ══════════════════════════════════════════════

let _user = null;

export function getUser() { return _user; }
export function isLoggedIn() { return _user !== null; }

export async function login(email, _password) {
  await delay(1200);
  const name = email.split('@')[0];
  const initials = name.slice(0, 2).toUpperCase();
  _user = { name, email, initials };
  return true;
}

export async function register(name, email, _password) {
  await delay(1400);
  const initials = name.slice(0, 2).toUpperCase();
  _user = { name, email, initials };
  return true;
}

export function logout() { _user = null; }

function delay(ms) { return new Promise(r => setTimeout(r, ms)); }
