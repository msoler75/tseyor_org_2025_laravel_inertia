import navigationItems from "../navigation.js";

var route = null;

const relativeUrl = (url) => {
  // console.log('relativeUrl', url)
  if (!url) return "";
  if (url == "undefined") return "";
  if (!url.match(/https?:\/\/.*/)) return url;
  return url.replace(/https?:\/\/[^\/]+/, "");
};

const mapRoute = (item) => {
  const nitem = item.route
    ? { ...item, url: relativeUrl(route(item.route)) }
    : item;
  if (nitem.url) nitem.url = relativeUrl(nitem.url);
  return nitem;
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
    item.route ? { ...item, url: relativeUrl(route(item.route)) } : item
  ),
});

const mapSubmenu = (submenu) => ({
  ...submenu,
  sections: submenu.sections.map(mapSection),
});

const state = reactive({
  //items: [],
  items: [],
  ghostTab: null, //?
  timer: null,
  announce: false,
  announceClosed: false,
  defaultClass: "",
  class: "",
  sideBarShow: false,
  // position: 'sticky',
  fullPage: false,
  scrollY: 0,
  fadingOutPage: false,
  dontFadeout: false,
  navigating: false,
  dontScroll: false,
  hoverDeactivated: false, // para evitar que se active el hover en la reentrada del mouse a la ventana
  tabHovering: null, // tab en el que el mouse se encuentra durante la desactivación del hover
  init(_routeFunc) {
    route = _routeFunc;
    this.items = navigationItems.map(mapItem);
  },
  in(tab, url) {
    // comprueba si la ruta está en alguno de los items del tab
    const rutaRelativa = relativeUrl(url);
    if (tab.url && rutaRelativa.indexOf(tab.url) >= 0) return true;
    if (!tab.hasItems) return false;
    return !!tab.submenu?.sections?.find((section) =>
      section.items.find((item) => {
        return rutaRelativa.indexOf(item.url) >= 0;
      })
    );
  },
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
        this.ghostTab = activeTab.value;
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
  // cuando pasa el mouse por encima
  hoverTab(tab) {
    if (this.hoverDeactivated) {
      this.tabHovering = tab;
      return;
    }
    if (tab.hasItems) this.activateTab(tab);
    else this.closeTabs();
  },
  // cuando el mouse deja el tab
  unhoverTab(tab) {
    if (this.tabHovering == tab) this.tabHovering = null;
  },
  // cuando se recupera la activación de hover
  activateHoveredTab() {
    if (this.tabHovering) this.hoverTab(this.tabHovering);
  },
  fadeoutPage() {
    console.log("nav.fadeoutPage()");
    this.fadingOutPage = true;
    this.dontScroll = true;
  },

  scrollToId(id, options) {
    console.log("scrollToId", id, options);
    const defaultOptions = { offset: 0, behavior: "smooth" };
    let { offset, behavior } = { ...defaultOptions, ...options };
    if (!offset) offset = 0;
    id = decodeURIComponent(id);
    console.log("scrolling to", id);
    // Obtén el elemento objetivo
    var objetivo = document.querySelector("#" + id + ",a[name='" + id + "']");
    if (!objetivo) {
        id = id.toLowerCase()
      console.log("trying to look titles", id);
      // Obtener el elemento H1
      const elems = document.querySelectorAll("h1,h2");

      // Recorrer los elementos H1 y H2
      elems.forEach((heading) => {
        if (heading.textContent.toLowerCase() === id) objetivo = heading;
      });
    }
    if (objetivo) {
      console.log("scrolling to elem", objetivo);

      const targetY =
        objetivo.offsetTop -
        document.querySelector("nav.sticky").offsetHeight +
        offset;

      // Ajusta la posición del elemento objetivo
      window.scrollTo({
        top: targetY,
        behavior,
      });
    }
  },

  scrollToContent(behavior) {
    console.log('scrollto_content')
    const el = document.querySelector("#content-main");
    if (!el) return false;

    if (!behavior || typeof behaviour != "string") behavior = "smooth";

    // altura del menu top nav
    const navH = document.querySelector("nav").getBoundingClientRect().height;
    const posY0 = document.querySelector("body").getBoundingClientRect().top;
    //obtenemos la posición Y de elemento el del DOM dentro de la página. Ejemplo, si estuviera arriba de todo la posición sería 0, si estuviera a 100 pixeles del comienzo de la página, la posición sería 100. Independiente del scroll de la página
    var posY = el.getBoundingClientRect().top; // sale negativo muchas veces por el scroll de página, no queremos eso
    // hemos de restarle el scroll de la página (tiene que quedar un numero positivo. el valor de posY )
    posY = posY - posY0 - navH * 2.1;
    console.log("posY", posY);
    // nos movemos a la posición posY
    window.scrollTo({
      top: posY,
      behavior,
    });
    return true;
  },
  scrollToTopPage(behavior) {
    if (!behavior || typeof behaviour != "string") behavior = "smooth";
    window.scrollTo({
      top: 0,
      behavior,
    });
  },
});

state.activeTab = computed(() => {
  return state.items.find((tab) => tab.open);
});

export function useNav() {
  return state;
}
