import { defineStore } from "pinia";

export const useSelectors = defineStore("selectors", {
  state: () => ({
    soloTitulosLibros: false,
    archivosVista: 'normal'
  }),
  getters: {},
  actions: {},
});
