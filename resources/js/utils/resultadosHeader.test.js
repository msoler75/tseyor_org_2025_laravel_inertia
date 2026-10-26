import { describe, it, expect } from "vitest";
import { buildResultsSummary, buildResultsSuffix } from "./resultadosHeader.js";

describe("buildResultsSuffix", () => {
  it("category + query", () => {
    expect(buildResultsSuffix({ category: "comunicados", query: "esferas" })).toBe(
      " para 'esferas' en categoría 'comunicados'"
    );
  });

  it("category only", () => {
    expect(buildResultsSuffix({ category: "Meditaciones" })).toBe(
      " en categoría 'Meditaciones'"
    );
  });

  it("query only", () => {
    expect(buildResultsSuffix({ query: "quimica" })).toBe(" para 'quimica'");
  });

  it("neither returns empty string", () => {
    expect(buildResultsSuffix({})).toBe("");
    expect(buildResultsSuffix()).toBe("");
  });
});

describe("buildResultsSummary", () => {
  it("plural resource + category", () => {
    expect(
      buildResultsSummary({ total: 70, resource: "libro", category: "comunicados" })
    ).toBe("70 libros en categoría 'comunicados'");
  });

  it("plural audio resource + capitalized category", () => {
    expect(
      buildResultsSummary({ total: 4, resource: "audio", category: "Meditaciones" })
    ).toBe("4 audios en categoría 'Meditaciones'");
  });

  it("singular resource + category", () => {
    expect(
      buildResultsSummary({ total: 1, resource: "libro", category: "comunicados" })
    ).toBe("1 libro en categoría 'comunicados'");
  });

  it("category without resource keeps default noun 'resultados'", () => {
    expect(buildResultsSummary({ total: 70, category: "comunicados" })).toBe(
      "70 resultados en categoría 'comunicados'"
    );
  });

  it("plural resource + query only", () => {
    expect(
      buildResultsSummary({ total: 70, resource: "libro", query: "quimica" })
    ).toBe("70 libros para 'quimica'");
  });

  it("plural resource + query + category", () => {
    expect(
      buildResultsSummary({
        total: 10,
        resource: "libro",
        query: "esferas",
        category: "comunicados",
      })
    ).toBe("10 libros para 'esferas' en categoría 'comunicados'");
  });

  it("no resource, no category, no query", () => {
    expect(buildResultsSummary({ total: 70 })).toBe("70 resultados");
  });

  it("singular resource + query only", () => {
    expect(buildResultsSummary({ total: 1, resource: "audio", query: "meditar" })).toBe(
      "1 audio para 'meditar'"
    );
  });

  it("singular default noun without filters", () => {
    expect(buildResultsSummary({ total: 1 })).toBe("1 resultado");
  });
});
