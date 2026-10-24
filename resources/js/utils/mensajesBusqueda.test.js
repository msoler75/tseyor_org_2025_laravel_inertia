import { describe, it, expect } from "vitest";
import { buildNoResultsMessage } from "./mensajesBusqueda.js";

describe("buildNoResultsMessage", () => {
  it("solo palabra buscada", () => {
    expect(buildNoResultsMessage("?buscar=facilismo")).toBe(
      "No hay resultados para 'facilismo'"
    );
  });

  it("solo filtros (año y categoría)", () => {
    expect(buildNoResultsMessage("?ano=2004&categoria=2")).toBe(
      "No hay resultados para los filtros seleccionados"
    );
  });

  it("palabra buscada y filtros a la vez", () => {
    expect(buildNoResultsMessage("?buscar=facilismo&ano=2004&orden=recientes")).toBe(
      "No hay resultados para 'facilismo' con los filtros seleccionados"
    );
  });

  it("ignora page y el marcador de todas (categoria=_)", () => {
    expect(buildNoResultsMessage("?buscar=facilismo&page=3&categoria=_")).toBe(
      "No hay resultados para 'facilismo'"
    );
    expect(buildNoResultsMessage("?categoria=_&page=2")).toBe("No hay resultados");
  });

  it("sin querystring", () => {
    expect(buildNoResultsMessage("")).toBe("No hay resultados");
  });
});
