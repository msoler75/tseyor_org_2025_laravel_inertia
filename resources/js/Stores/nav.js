import { defineStore } from "pinia";
import navigationItems from "../navigation.js";

const mapRoute = (item) => {
  if (item.route) return { ...item, url: route(item.route) };
  else return item;
};

const mapItem = (item) => {
  if (item.submenu)
    return {
      ...mapRoute(item),
      hasItems: true,
      submenu: mapSubmenu(item.submenu),
    };
  else
    return {
      ...mapRoute(item),
      hasItems: false,
    };
};

const mapSection = (section) => ({
  ...section,
  items: section.items.map((item) =>
    item.route ? { ...item, url: route(item.route) } : item
  ),
});

const mapSubmenu = (submenu) => ({
  ...submenu,
  sections: submenu.sections.map(mapSection),
});

export const useNav = defineStore("nav", {
  state: () => ({
    //items: [],
    items: navigationItems.map(mapItem),
    ghostTab: null, //?
    timer: null,
    announce: false,
    defaultClass: "",
    class: "",
    // position: 'sticky',
    fullPage: false,
    scrollY: 0,
  }),
  getters: {
    activeTab: (state) => state.items.find((tab) => tab.open),
    in: (state) => (tab, ruta) => {
      // comprueba si la ruta está en alguno de los items del tab
      return !!tab.submenu.sections.find((section) =>
        section.items.find((item) => item.url == ruta)
      );
    },
  },
  actions: {
    setItems(items) {
      const tabs = items.map(mapItem);
      for (const tab of tabs) this.items.push(tab);
    },
    activateTab(tab) {
      tab.activating = true;
      // console.log("activateTab", tab.title);
      if (!tab.open || !tab.hasItems) this.closeTabs();
      setTimeout(() => {
        tab.open = true;
        this.activeTabChange(tab);
      }, 1);
    },
    toggleTab(tab) {
      // console.log("toggleTab", tab, tab.title, tab.open);
      let oldState = !!tab.open;
      if (!oldState || !tab.hasItems) this.closeTabs();
      tab.open = !oldState;
      if (tab.open) this.activeTabChange(tab);
      // console.log("tab is now", tab.open);
      return false;
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
      // console.log("close tab");
      if (tab) tab.open = false;
    },
    closeTabs() {
      for (const tab of this.items) {
        tab.open = false;
      }
    },
  },
});
