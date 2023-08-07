import navigationItems from "../navigation.js";
import { createGlobalState } from "@vueuse/core";

const mapItem = (item) => {
  if (item.route) {
    return { ...item, url: route(item.route) };
  } else {
    return {
      ...item,
      submenu: mapSubmenu(item.submenu),
    };
  }
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

export const useNav = createGlobalState(() => {
  // Aquí puedes inicializar el estado y definir tus getters y actions

  const items = navigationItems.map(mapItem);
  const ghostTab = ref(null);
  var timer = null;
  const announce = ref(false);
  const defaultClass = ref("");
  //const _class = ref("")
  // position: 'sticky',
  const fullPage = ref(false);
  const scrollY = ref(0);

  const activeTab = computed(() => items.find((tab) => tab.open));

  const routeIn = (tab, ruta) => {
    // comprueba si la ruta está en alguno de los items del tab
    return !!tab.submenu.sections.find((section) =>
      section.items.find((item) => item.url == ruta)
    );
  };

  function activateTab(tab) {
    tab.activating = true;
    // console.log("activateTab", tab.title);
    if (!tab.open || !tab.submenu) closeTabs();
    setTimeout(() => {
      tab.open = true;
      activeTabChange(tab);
    }, 1);
  }

  function toggleTab(tab) {
    // console.log("toggleTab", tab, tab.title, tab.open);
    let oldState = !!tab.open;
    if (!oldState || !tab.submenu) closeTabs();
    tab.open = !oldState;
    if (tab.open) activeTabChange(tab);
    // console.log("tab is now", tab.open);
    return false;
  }

  function activeTabChange(newTab) {
    clearTimeout(timer);
    if (newTab) ghostTab.value = newTab;
    else
      timer = setTimeout(() => {
        ghostTab.value = activeTab.value;
      }, 75);
  }

  function closeTab(tab) {
    // console.log("close tab");
    if (tab) tab.open = false;
  }

  function closeTabs() {
    for (const tab of items) {
      tab.open = false;
    }
  }

  return {
    items,
    ghostTab,
    announce,
    defaultClass,
    fullPage,
    scrollY,
    activeTab,
    routeIn,
    activateTab,
    toggleTab,
    activeTabChange,
    closeTab,
    closeTabs,
  };
});
