import { defineStore } from "pinia";

//export const usePlayer = createGlobalState(() => {
export const useSelectors = defineStore("selectors", {
  state: () => ({
    soloTitulosLibros: false,
    archivosVista: "normal",
    vistaComunicados: "tarjetas",
  }),
});
