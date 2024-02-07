import { defineStore } from "pinia";

export const useGlobalSearch = defineStore("globalSearch", {
  state: () => ({
    opened: false,
    query: "",
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
