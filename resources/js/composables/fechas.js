// Helpers de fecha en español: parseo, validación, futuro y formateo localizado
export function aFecha(raw) {
  if (!raw) return null;
  const d = raw instanceof Date ? raw : new Date(raw);
  return isNaN(d) ? null : d;
}

export function esValida(raw) {
  return aFecha(raw) !== null;
}

export function esFechaFutura(raw, referencia = new Date()) {
  const d = aFecha(raw);
  if (!d) return false;
  return d >= referencia;
}

export const fechaFormatoEsp = function (fecha, options) {
  const fechaObj = new Date(fecha);
  const defaultOptions = { day: 'numeric', month: 'short', year: 'numeric' };
  const opciones = { ...defaultOptions, ...options };
  return fechaObj.toLocaleDateString('es-ES', opciones).replace(/\sde\s/g, '/');
}

export default { aFecha, esValida, esFechaFutura, fechaFormatoEsp };
