import { defineStore } from 'pinia'
import { nextTick } from "vue";
import useNav from './nav'

export const useToolsStore = defineStore('tools', {
  state: () => ({
    mostrarBuscarTexto: false,
    mostrarHerramientas: "auto", // true, false, auto
    hayContenido: false,
  }),

  getters: {
    mostrarTools() {
      const nav = useNav()
      return !this.mostrarBuscarTexto && (
        this.mostrarHerramientas === true || (this.mostrarHerramientas === 'auto' && nav.scrollingUp)
      )
    },
  },

  actions: {
    detectContent() {
      setTimeout(() => {
        this.hayContenido = !!document.querySelector('.prose-text-container');
      }, 100)
    },

    closeTools() {
      // console.log("CLOSING TOOLS");
      const nav = useNav()
      this.mostrarBuscarTexto = false;
      this.mostrarHerramientas = "auto";
      nav.scrollingUp = false;
    },

    toggleTools(event) {
      const nav = useNav()
      // hay que comprobar si estamos en una vista de contenido
      if (!document.querySelector(".prose-text-container")) {
        this.closeTools();
        return;
      }
      const tagNames = ["IMG", "A", "BUTTON", "INPUT", "TEXTAREA"];
      console.log("CLICKED ON", event.target.tagName);
      if (tagNames.includes(event.target.tagName)) {
        // si es una imagen u otro elemento al que usualmente se le hace click, ignoramos la llamada a tools
        this.closeTools();
        return;
      }
      // puede haber sido un click a un enlace, hay que comprobar si estamos navegando o no
      if (nav.navigating) return;
      nextTick(() => {
        if (nav.navigating) return;
        // parece que no es un click a un enlace

        if (this.mostrarHerramientas === "auto")
          this.mostrarHerramientas = !nav.scrollingUp;
        else this.mostrarHerramientas = !this.mostrarHerramientas;
      });
    },

    toggleSearch() {
      const nav = useNav()
      this.mostrarBuscarTexto = !this.mostrarBuscarTexto;
      this.mostrarHerramientas = "auto";
      nav.scrollingUp = false;
    },

    closeSearch() {
      const nav = useNav()
      this.mostrarBuscarTexto = false;
      this.mostrarHerramientas = "auto";
      nav.scrollingUp = false;
    },
  },
})

// Composable que mantiene la misma API que antes
export default function useTools() {
  const store = useToolsStore()

  // Retornar el store directamente para mantener la misma API
  // Esto permite usar tools.propiedad sin necesidad de .value
  return store
}
