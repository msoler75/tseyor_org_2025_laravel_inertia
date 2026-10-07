import { describe, it, expect } from "vitest";
import { buildDisplayImages, buildAllImages } from "./eventoImagenes.js";

describe("buildDisplayImages", () => {
  it("miniatura primero cuando mostrar_miniatura está activado", () => {
    const evento = {
      imagen: "portada.jpg",
      imagenes: ["extra1.jpg", "extra2.jpg"],
      mostrar_miniatura: true,
    };
    expect(buildDisplayImages(evento)).toEqual([
      "portada.jpg",
      "extra1.jpg",
      "extra2.jpg",
    ]);
  });

  it("solo imagenes cuando mostrar_miniatura está desactivado", () => {
    const evento = {
      imagen: "portada.jpg",
      imagenes: ["extra1.jpg", "extra2.jpg"],
      mostrar_miniatura: false,
    };
    expect(buildDisplayImages(evento)).toEqual(["extra1.jpg", "extra2.jpg"]);
  });

  it("deduplica la miniatura si también aparece en imagenes", () => {
    const evento = {
      imagen: "portada.jpg",
      imagenes: ["portada.jpg", "extra1.jpg"],
      mostrar_miniatura: true,
    };
    expect(buildDisplayImages(evento)).toEqual(["portada.jpg", "extra1.jpg"]);
  });

  it("miniatura sola cuando mostrar_miniatura está activado y no hay imagenes", () => {
    const evento = { imagen: "portada.jpg", imagenes: [], mostrar_miniatura: true };
    expect(buildDisplayImages(evento)).toEqual(["portada.jpg"]);
  });

  it("array vacío cuando mostrar_miniatura está desactivado y solo hay miniatura", () => {
    const evento = { imagen: "portada.jpg", imagenes: [], mostrar_miniatura: false };
    expect(buildDisplayImages(evento)).toEqual([]);
  });

  it("array vacío cuando no hay nada", () => {
    expect(
      buildDisplayImages({ imagen: null, imagenes: [], mostrar_miniatura: true })
    ).toEqual([]);
    expect(buildDisplayImages({})).toEqual([]);
  });
});

describe("buildAllImages", () => {
  it("miniatura primero, deduplicada, con imagenes adicionales", () => {
    const evento = {
      imagen: "portada.jpg",
      imagenes: ["portada.jpg", "extra1.jpg"],
    };
    expect(buildAllImages(evento)).toEqual(["portada.jpg", "extra1.jpg"]);
  });

  it("array vacío cuando no hay imágenes", () => {
    expect(buildAllImages({ imagen: null, imagenes: [] })).toEqual([]);
  });
});
