import { createGlobalState, useStorage } from "@vueuse/core";

// ui selectors
export const useSelectors = createGlobalState(() =>
  useStorage("vue-use-locale-storage", {
    soloTitulosLibros: false,
    archivosVista: "normal",
  })
);
