// Pequeños helpers de fecha usados por componentes de eventos
export function toDate(raw) {
  if (!raw) return null;
  const d = raw instanceof Date ? raw : new Date(raw);
  return isNaN(d) ? null : d;
}

export function isValidDate(raw) {
  return toDate(raw) !== null;
}

export function isFuture(raw, ref = new Date()) {
  const d = toDate(raw);
  if (!d) return false;
  return d >= ref;
}

export default { toDate, isValidDate, isFuture };
