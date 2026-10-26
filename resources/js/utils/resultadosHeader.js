export function buildResultsSuffix({ category, query } = {}) {
  if (category && query) return ` para '${query}' en categoría '${category}'`;
  if (category) return ` en categoría '${category}'`;
  if (query) return ` para '${query}'`;
  return '';
}

export function buildResultsSummary({ total, resource, category, query }) {
  const noun = resource || 'resultado';
  const nounForm = total == 1 ? noun : noun + 's';
  const base = `${total} ${nounForm}`;

  return base + buildResultsSuffix({ category, query });
}
