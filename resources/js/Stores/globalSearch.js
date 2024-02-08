import { defineStore } from "pinia";

export const useGlobalSearch = defineStore("globalSearch", {
  state: () => ({
    opened: false,
    showSuggestions: true,
    query: "",
    lastQuery: "",
    results: null
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
