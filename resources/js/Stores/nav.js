import { defineStore } from 'pinia'
import navigationItems from "@/navigation.js";

const page = usePage()

let _route = () => ''

const relativeUrl = (url) => {
  if (!url) return "";
  if (url == "undefined") return "";
  if (!url.match(/https?:\/\/.*/)) return url;
  return url.replace(/https?:\/\/[^\/]+/, "");
};

const mapRoute = (item, routeFn) => {
  const nitem = item.route
    ? { ...item, url: relativeUrl(routeFn(item.route)) }
    : item;
  if (nitem.url) nitem.url = relativeUrl(nitem.url);
  return nitem;
};

const mapItem = (item, routeFn) => {
  if (item.submenu)
    return {
      ...mapRoute(item, routeFn),
      hasItems: true,
      submenu: mapSubmenu(item.submenu, routeFn),
    };
  else
    return {
      ...mapRoute(item, routeFn),
      hasItems: false,
    };
};

const mapGroup = (group, routeFn) => ({
    ...group,
    items: group.items.map((item) =>
      item.route ? { ...item, url: relativeUrl(routeFn(item.route)) } : item
    ),
  });

const mapSubmenu = (submenu, routeFn) => {
    if(!submenu) return null
    const sections = []
    for(const section of submenu.sections) {
        if(typeof section.index !== 'undefined') continue
        sections.push({groups:[mapGroup(section, routeFn)]})
    }
    for(const section of submenu.sections) {
        if(typeof section.index !== 'undefined')
            sections[section.index].groups.push(mapGroup(section, routeFn))
    }
    return {sections}
}

export const useNavStore = defineStore('nav', {
  state: () => ({
    items: [],
    timer: null,
    announce: false,
    announceClosed: false,
    sideBarShow: false,
    movingFast: true, // actualmente no se usa
    scrollY: 0,
    navigatingFrom: null,
    fadingOutPage: false,
    //fadingOutPage: false,
    preservePage: false,
    // dontScroll: false,
    navigating: false,
    hoverDeactivated: false,
    scrollingUp: false,
    tabHovering: null,
    enteringTimeout: null,
    activeTab: null,
    route: null,
    // Variables para scroll-to-top logic (ahora parte del estado)
    _heightToShow: 300,
    _wrapToShow: 120,
    _wrapToHide: 70,
    _prevY: -10000,
    _subiendo: false,
    _recorridoUp: 0,
    _recorridoDown: 0,
  }),

  getters: {
    fullPage: (state) => {
      // Auto-detectar basado en la ruta (funciona en SSR)
      return ['/', '/origenes-de-tseyor'].includes(page.url);
    }
  },

  actions: {
    init(_r) {
      _route = _r
      this.route = _r
      this.items = navigationItems.map(item => mapItem(item, _r));
      this._updateCurrent()
    },

    _in(tab, url) {
      // comprueba si la ruta está en alguno de los items del tab
      if (tab.url && url.indexOf(tab.url) >= 0) return true;
      if (!tab.hasItems) return false;
      return !!tab.submenu?.sections
        .find((section)=>section.groups.find((group) =>
            group.items.find((item) => {
            return url.indexOf(item.url) >= 0;
          })
        ))
    },

    _updateCurrent() {
      const url = relativeUrl(page.url)
      this.items.forEach(tab=>{
          tab.current = this._in(tab, url)
      })
    },

    _updateActive() {
      this.activeTab = this.items.find((tab) => tab.open);
    },

    setItems(items) {
      const tabs = items.map(item => mapItem(item, this.route))
      for (const tab of tabs) this.items.push(tab);
      this._updateCurrent()
    },

    activateTab(tab) {
      tab.activating = true;
      if(tab.open) return
      this.closeTabs()
      if(tab.hasItems) {
          tab.open = true;
      }
      this._updateActive()
    },

    toggleTab(tab) {
      if(!tab.hasItems) return
      tab.open = !tab.open
      if(!tab.open) this.closeTabs()
      this._updateActive()
    },

    closeTab(tab) {
      if (tab) tab.open = false;
      this._updateActive()
    },

    closeTabs() {
      for (const tab of this.items) {
        tab.open = false;
      }
      this._updateActive()
    },

    deactivateMenu() {
      // cerramos los submenús
      this.hoverDeactivated = true;
      this.closeTabs();
    },

    reactivateMenu() {
      this.hoverDeactivated = false;
      this.activateHoveredTab()
    },

    // cuando pasa el mouse por encima
    hoverTab(tab) {
      this.closeTabs()
      this.tabHovering = tab;
      if(this.hoverDeactivated) return
      if (tab.hasItems) this.activateTab(tab);
      else this.closeTabs();
    },

    // cuando el mouse deja el tab
    unhoverTab(tab) {
      if (this.tabHovering == tab) {
          this.tabHovering = null;
      }
    },

    // cuando se recupera la activación de hover
    activateHoveredTab() {
      if (this.tabHovering) this.hoverTab(this.tabHovering);
    },

    /*fadeoutPage() {
      this.fadingOutPage = true;
      this.dontScroll = true;
    },*/

    scrollToId(id, options) {
      const defaultOptions = { offset: 0, behavior: "smooth" };
      let { offset, behavior } = { ...defaultOptions, ...options };
      if (!offset) offset = 0;
      id = decodeURIComponent(id);
      // Obtén el elemento objetivo
      var objetivo = document.querySelector("#" + id + ",a[name='" + id + "']");
      if (!objetivo) {
          id = id.toLowerCase()
        // Obtener el elemento H1
        const elems = document.querySelectorAll("h1,h2");

        // Recorrer los elementos H1 y H2
        elems.forEach((heading) => {
          if (heading.textContent.toLowerCase() === id) objetivo = heading;
        });
      }
      if (objetivo) {
        const targetY =
          objetivo.offsetTop -
          document.querySelector("nav.sticky").offsetHeight +
          offset;

        // Ajusta la posición del elemento objetivo
        console.log('nav.scroll_to_id')
        window.scrollTo({
          top: targetY,
          behavior,
        });
      }
    },

    scrollToHereElem() {
      const el = document.querySelector("#scroll-to-here");
      return el;
    },

    doScrollToHere(behavior) {
        console.log('doScrollToHere called with behavior:', behavior);
      const el = document.querySelector("#scroll-to-here");
      if (!el) return false;


      if (!behavior || (typeof behavior != "string")) behavior = "smooth";

      console.log('scrolling to #scroll-to-here', behavior);

      // altura del menu top nav
      const navH = document.querySelector("nav").getBoundingClientRect().height;
      const posY0 = document.querySelector("body").getBoundingClientRect().top;
      //obtenemos la posición Y de elemento el del DOM dentro de la página. Ejemplo, si estuviera arriba de todo la posición sería 0, si estuviera a 100 pixeles del comienzo de la página, la posición sería 100. Independiente del scroll de la página
      var posY = el.getBoundingClientRect().top; // sale negativo muchas veces por el scroll de página, no queremos eso
      // hemos de restarle el scroll de la página (tiene que quedar un numero positivo. el valor de posY )
      posY = posY - posY0 - navH * 2.2; // dejamos un espacio extra para la row de SearchInput y otros filtros
      // nos movemos a la posición posY
    console.log('nav.doScrollToHere: scrolling to Y position', posY, 'with behavior', behavior);
      window.scrollTo({
        top: posY,
        behavior,
      });
      return true;
    },

    scrollToTopPage(behavior) {
      if (!behavior || typeof behaviour != "string") behavior = "smooth";
      console.log('nav.scroll_to_top_page')
      window.scrollTo({
        top: 0,
        behavior,
      });
    },

    // Scroll-to-top inteligente: busca contenedores .sections y hace scroll en ellos si existen
    scrollToTopSmart(behavior) {
      if (!behavior || typeof behavior != "string") behavior = "smooth";
      const div =
        document.querySelector("div.sections.snap-proximity") ||
        document.querySelector("div.sections.smooth-snap") ||
        document.querySelector("div.sections.scroll-region") ||
        document.querySelector("div.sections.scroll-smooth");
        console.log('nav.scroll_to_top_smart')
      if (div) div.scrollTo({ top: 0, behavior });
      else window.scrollTo({ top: 0, behavior });
    },

    // Método para actualizar el scroll Y y manejar la lógica de scroll-to-top
    updateScrollY(y) {
      if (this._prevY !== -10000) {
        const dy = y - this._prevY;
        if (y < this._heightToShow) this.scrollingUp = false;
        else if (dy > 0) {
          // bajando
          if (this._subiendo) {
            this._recorridoDown = dy;
          } else {
            this._recorridoDown += dy;
            if (this._recorridoDown > this._wrapToHide) this.scrollingUp = false;
          }
          this._subiendo = false;
        } else {
          // subiendo (dy <= 0)
          if (!this._subiendo) {
            this._recorridoUp = dy;
          } else {
            this._recorridoUp += dy;
            if (this._recorridoUp < -1 * this._wrapToShow) this.scrollingUp = true;
          }
          this._subiendo = true;
        }
      }
      this._prevY = y;
      this.scrollY = y;
    },
  },
})

// Composable que mantiene la misma API que antes
export default function useNav() {
  const store = useNavStore()

  // Watch para actualizar current cuando cambie la URL (solo en cliente)
  if (typeof window !== 'undefined') {
    watch(() => page.url, () => {
      store._updateCurrent()
    })

    // Watch para el scroll (solo en cliente)
    watch(() => store.scrollY, (y) => {
      store.updateScrollY(y)
    })
  }

  // Retornar el store directamente para mantener la misma API
  // Esto permite usar nav.propiedad sin necesidad de .value
  return store
}
