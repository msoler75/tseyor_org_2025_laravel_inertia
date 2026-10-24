/**
 * Texto del mensaje "no hay resultados" según la querystring de la página.
 *
 * - Solo palabra buscada: "No hay resultados para 'x'"
 * - Solo filtros (categoria/ano/orden/...): "No hay resultados para los filtros seleccionados"
 * - Palabra y filtros: "No hay resultados para 'x' con los filtros seleccionados"
 * - Sin nada: "No hay resultados"
 *
 * Ignora `page` y el marcador de "todas" `categoria=_`.
 *
 * @param {string} queryString
 * @param {string} keyword nombre del parámetro de la palabra buscada (p. ej. "buscar")
 * @returns {string}
 */
export function buildNoResultsMessage(queryString, keyword = "buscar") {
    const params = new URLSearchParams(queryString || "");
    const query = params.get(keyword);
    params.delete(keyword);
    params.delete("page");

    let hasFilters = false;
    for (const [, value] of params.entries()) {
        if (value && value !== "_") {
            hasFilters = true;
            break;
        }
    }

    if (query && hasFilters) {
        return `No hay resultados para '${query}' con los filtros seleccionados`;
    }
    if (query) {
        return `No hay resultados para '${query}'`;
    }
    if (hasFilters) {
        return "No hay resultados para los filtros seleccionados";
    }
    return "No hay resultados";
}
