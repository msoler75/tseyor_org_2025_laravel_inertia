import { defineStore } from "pinia";

//export const usePlayer = createGlobalState(() => {
export const useSelectors = defineStore("selectors", {
  state: () => ({
    soloTitulosLibros: false,
    archivosVista: "normal",
    vistaComunicados: "tarjetas",
    mostrarPermisos: false // muestra permisos en el listado de archivos. útil para administradores
  }),
});
