import { defineStore } from "pinia";

export const useGlobalSearch = defineStore("globalSearch", {
  state: () => ({
    opened: false,
    showSuggestions: true,
    query: "",
    lastQuery: "",
    results: null,
    restrictToCollections: null
  }),
  actions: {
    clear () {
      this.query = "";
    },
    open () {
      this.opened = true;
    },
  },
});
