/**
 * Construye la lista de imágenes a mostrar en la página del evento.
 *
 * - Si `mostrarMiniatura` es true y existe `imagen`, la portada va primero
 *   (deduplicada contra `imagenes`).
 * - Si `mostrarMiniatura` es false o no hay portada, solo se muestran `imagenes`.
 * - Si no hay nada, se devuelve un array vacío.
 *
 * @param {{ imagen?: string|null, imagenes?: string[]|null, mostrar_miniatura?: boolean }} evento
 * @returns {string[]}
 */
export function buildDisplayImages(evento) {
    const imagenes = Array.isArray(evento?.imagenes) ? evento.imagenes : [];
    const imagen = evento?.imagen || null;
    const mostrarMiniatura = Boolean(evento?.mostrar_miniatura);

    if (mostrarMiniatura && imagen) {
        const result = [imagen];
        for (const img of imagenes) {
            if (img && !result.includes(img)) {
                result.push(img);
            }
        }
        return result;
    }

    return imagenes.filter(Boolean);
}

/**
 * Construye la lista completa de imágenes para el visor (lightbox):
 * la portada primero, seguida de las adicionales, deduplicadas.
 *
 * @param {{ imagen?: string|null, imagenes?: string[]|null }} evento
 * @returns {string[]}
 */
export function buildAllImages(evento) {
    const imagenes = Array.isArray(evento?.imagenes) ? evento.imagenes : [];
    const imagen = evento?.imagen || null;
    const result = [];

    if (imagen) {
        result.push(imagen);
    }
    for (const img of imagenes) {
        if (img && !result.includes(img)) {
            result.push(img);
        }
    }

    return result;
}
