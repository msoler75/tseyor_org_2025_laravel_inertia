/**
 * Intercepta respuestas no-Inertia (404, 502, etc.) que el router de Inertia
 * mostraría en su modal de error con un iframe, y decide a dónde navegar en
 * su lugar: una navegación normal hace que el servidor renderice su página
 * de error con la URL solicitada en la barra de direcciones.
 */

/**
 * URL a la que navegar ante una respuesta inválida.
 * - GET same-origin: la URL de la propia visita.
 * - POST u otros métodos: la página actual (nunca re-enviar el formulario).
 * - Cross-origin o datos inválidos: la página actual (por seguridad).
 */
export function resolveInvalidTarget(response, currentHref) {
  const params = response?.requestParams?.all?.();
  const url = params?.url;
  const method = String(params?.method ?? "get").toLowerCase();

  if (method !== "get" || !url) {
    return currentHref;
  }

  try {
    const target = new URL(url, currentHref);
    if (target.origin !== new URL(currentHref).origin) {
      return currentHref;
    }
    return target.href;
  } catch {
    return currentHref;
  }
}

/**
 * Handler para `router.on('invalid', ...)`:
 * cancela el modal de Inertia y navega como un link normal.
 * Nunca lanza excepciones (si falla, al menos el modal quedó cancelado).
 */
export function handleInvalidResponse(
  event,
  { href = typeof window !== "undefined" ? window.location.href : "", assign } = {}
) {
  // El evento `inertia:invalid` es cancelable: preventDefault() suprime el modal.
  try {
    event?.preventDefault?.();
  } catch {
    /* el modal ya queda cancelado igualmente */
  }

  // Un prefetch no debe navegar por sí solo; el clic del usuario hará
  // la visita real, que volverá a evaluar esta misma ruta.
  try {
    if (event?.detail?.response?.requestParams?.isPrefetch?.()) {
      return;
    }
  } catch {
    /* ignore */
  }

  const destino = resolveInvalidTarget(event?.detail?.response, href);
  const navegar =
    assign ??
    ((to) => {
      window.location.href = to;
    });
  navegar(destino);
}
