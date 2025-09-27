// Helpers de fecha en español: parseo, validación, futuro y formateo localizado
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localizedFormat from 'dayjs/plugin/localizedFormat'

dayjs.extend(utc)
dayjs.extend(customParseFormat)
dayjs.extend(localizedFormat)

// aFecha: devuelve objeto Date si puede parsear, o null
export function aFecha(raw) {
  if (!raw) return null
  // Si es Date ya
  if (raw instanceof Date) return isNaN(raw) ? null : raw
  // dayjs acepta muchos formatos; intentar parse con UTC y formatos comunes
  const s = String(raw)
  // Intentar formatos comunes: 'YYYY-MM-DD', 'YYYY-MM-DD HH:mm:ss', ISO
  const candidate = dayjs(s, ['YYYY-MM-DD', 'YYYY-MM-DD HH:mm:ss', dayjs.ISO_8601], true)
  if (candidate.isValid()) return candidate.toDate()
  // fallback: intentar parse lax
  const lax = dayjs(s)
  return lax.isValid() ? lax.toDate() : null
}

export function esValida(raw) {
  return aFecha(raw) !== null
}

export function esFechaFutura(raw, referencia = new Date()) {
  const d = aFecha(raw)
  if (!d) return false
  return dayjs(d).isAfter(dayjs(referencia)) || dayjs(d).isSame(dayjs(referencia))
}

export const fechaFormatoEsp = function (fecha, options) {
  const d = aFecha(fecha)
  if (!d) return ''
  const defaultOptions = { day: 'numeric', month: 'short', year: 'numeric' }
  const opts = { ...defaultOptions, ...(options || {}) }
  return d.toLocaleDateString('es-ES', opts).replace(/\sde\s/g, '/')
}

// Formatea fecha solo (all-day) como YYYYMMDD.
export function formatDateOnly(dateInput, addDays = 0) {
  const d = aFecha(dateInput);
  if (!d) return '';
  const dt = new Date(d.getFullYear(), d.getMonth(), d.getDate());
  if (addDays) dt.setDate(dt.getDate() + addDays);
  const pad = (n) => String(n).padStart(2, '0');
  return `${dt.getFullYear()}${pad(dt.getMonth() + 1)}${pad(dt.getDate())}`;
}

// Formatea fecha+hora local como instancia UTC para Google Calendar: YYYYMMDDTHHMMSSZ
// dateInput admite Date, string ISO o 'YYYY-MM-DD' / 'YYYY-MM-DD HH:MM:SS'.
// timeInput puede ser 'HH:MM' o 'HH:MM:SS'. Si timeInput está vacío, intentará usar la hora presente en dateInput.
export function formatDateTimeLocalToUTC(dateInput, timeInput) {
  if (!dateInput && !timeInput) return ''

  // Construir un dayjs en zona local usando formatos conocidos
  let d
  if (timeInput) {
    // Preferir parse con formato concreto
    const combined = `${String(dateInput || '').trim()} ${String(timeInput || '').trim()}`.trim()
    d = dayjs(combined, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD[T]HH:mm:ss', dayjs.ISO_8601], true)
  } else {
    d = dayjs(String(dateInput || '').trim(), ['YYYY-MM-DD', 'YYYY-MM-DD HH:mm:ss', dayjs.ISO_8601], true)
  }

  if (!d.isValid()) {
    // fallback lax
    d = timeInput ? dayjs(`${dateInput} ${timeInput}`) : dayjs(dateInput)
  }

  if (!d.isValid()) return ''
  // Convertir a UTC y formatear para Google Calendar
  return d.utc().format('YYYYMMDDTHHmmss') + 'Z'
}

// Parse dateInput + timeInput into a local Date object (not UTC).
// Devuelve null si no es posible.
export function parseLocalDateTime(dateInput, timeInput) {
  if (!dateInput && !timeInput) return null
  // Usar dayjs para parseo local (no UTC)
  let d
  if (timeInput) {
    d = dayjs(`${String(dateInput || '').trim()} ${String(timeInput || '').trim()}`, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD HH:mm', dayjs.ISO_8601], true)
  } else {
    d = dayjs(String(dateInput || '').trim(), ['YYYY-MM-DD', 'YYYY-MM-DD HH:mm:ss', dayjs.ISO_8601], true)
  }
  if (!d.isValid()) d = timeInput ? dayjs(`${dateInput} ${timeInput}`) : dayjs(dateInput)
  return d.isValid() ? d.toDate() : null
}

// Construye start/end para Google Calendar dada la estructura de evento.
// Devuelve { start, end } donde cada campo es string con formato esperado por Google
// o '' si no está disponible. Centraliza la lógica usada por la UI.
export function buildGoogleCalendarDates(evento) {
  if (!evento) return { start: '', end: '' };

  const hasStartTime = !!evento.hora_inicio;
  const hasEndTime = !!evento.hora_fin;
  const multiDay = !!evento.fecha_fin && evento.fecha_fin !== evento.fecha_inicio;

  let start = '';
  let end = '';

  if (hasStartTime) {
    start = formatDateTimeLocalToUTC(evento.fecha_inicio, evento.hora_inicio);

    if (hasEndTime) {
      end = formatDateTimeLocalToUTC(evento.fecha_fin || evento.fecha_inicio, evento.hora_fin);
    } else {
      // No hay hora de fin: asumir 1 hora de duración a partir de fecha+hora_inicio
      const parsed = parseLocalDateTime(evento.fecha_inicio, evento.hora_inicio);
      if (parsed) {
        end = dayjs(parsed).add(1, 'hour').utc().format('YYYYMMDDTHHmmss') + 'Z'
      } else {
        // Intentar fallback usando formatDateTimeLocalToUTC (si puede parsear)
        const isoStart = formatDateTimeLocalToUTC(evento.fecha_inicio, evento.hora_inicio);
        if (isoStart) {
          const std = isoStart.replace(/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z$/, '$1-$2-$3T$4:$5:$6Z');
          const parsed2 = dayjs(std)
          if (parsed2.isValid()) {
            end = parsed2.add(1, 'hour').utc().format('YYYYMMDDTHHmmss') + 'Z'
          }
        }
      }
    }
  } else {
    // Evento todo el día
    start = formatDateOnly(evento.fecha_inicio, 0);
    if (multiDay) {
      end = formatDateOnly(evento.fecha_fin, 1);
    } else {
      end = formatDateOnly(evento.fecha_inicio, 1);
    }
  }

  return { start, end };
}

export default { aFecha, esValida, esFechaFutura, fechaFormatoEsp, formatDateOnly, formatDateTimeLocalToUTC, parseLocalDateTime, buildGoogleCalendarDates };
