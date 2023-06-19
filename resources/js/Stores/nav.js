import { defineStore } from "pinia";
import navigationItems from "../navigation.js";

export const useNav = defineStore("nav", {
  state: () => ({
    items: navigationItems,
    ghostTab: null,
    timer: null,
  }),
  getters: {
    activeTab: (state) => state.items.find((tab) => tab.open),
  },
  actions: {
    activateTab(tab) {
      tab.activating = true;
      console.log("activateTab", tab.title);
      if (!tab.open || !tab.submenu) this.closeTabs();
      setTimeout(() => {
        tab.open = true;
        this.activeTabChange(tab);
      }, 1);
    },
    toggleTab(tab) {
      console.log("toggleTab", tab, tab.title, tab.open);
      let oldState = !!tab.open;
      if (!oldState || !tab.submenu) this.closeTabs();
      tab.open = !oldState;
      if (tab.open) this.activeTabChange(tab);
      console.log("tab is now", tab.open);
      return false
    },
    activeTabChange(newTab) {
      clearTimeout(this.timer);
      if (newTab) this.ghostTab = newTab;
      else
        this.timer = setTimeout(() => {
          this.ghostTab = this.activeTab;
        }, 75);
    },
    closeTab(tab) {
      console.log("close tab");
      if (tab) tab.open = false;
    },
    closeTabs() {
      for (const tab of this.items) {
        tab.open = false;
      }
    },
  },
});
