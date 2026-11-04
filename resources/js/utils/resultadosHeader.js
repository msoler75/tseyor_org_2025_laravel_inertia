export function buildResultsSuffix({ category, query } = {}) {
  // '_' es el marcador de "todos" que envían los controllers; no es una categoría real.
  const activeCategory = category && category !== "_" ? category : "";

  if (activeCategory && query) return ` para '${query}' en categoría '${activeCategory}'`;
  if (activeCategory) return ` en categoría '${activeCategory}'`;
  if (query) return ` para '${query}'`;
  return '';
}

export function buildResultsSummary({ total, resource, category, query }) {
  const noun = resource || 'resultado';
  const nounForm = total == 1 ? noun : noun + 's';
  const base = `${total} ${nounForm}`;

  return base + buildResultsSuffix({ category, query });
}
