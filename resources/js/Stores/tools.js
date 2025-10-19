import { reactive } from "vue";
import { nextTick } from "vue";
import {useNav} from "./nav";


const nav = useNav();

// selectores/opciones del usuario que pueden persistir entre sesiones o entre cambios de página

const tools = reactive({
  // variables de estado de la interfaz Tools
  mostrarBuscarTexto: false,
  mostrarHerramientas: "auto", // true, false, auto
  hayContenido: false,
  // variables computadas
  mostrarTools: computed(()=>{
    return !tools.mostrarBuscarTexto &&  (
        tools.mostrarHerramientas===true ||(tools.mostrarHerramientas==='auto'&& nav.scrollingUp)
     )
  }),
  // métodos
  detectContent() {
    setTimeout(()=>{
        tools.hayContenido = !!document.querySelector('.prose-text-container');
    }, 100)
  },
  closeTools() {
    console.log("CLOSING TOOLS");
    this.mostrarBuscarTexto = false;
    this.mostrarHerramientas = "auto";
    nav.scrollingUp = false;
  },
  toggleTools(event) {
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
    this.mostrarBuscarTexto = !this.mostrarBuscarTexto;
    this.mostrarHerramientas = "auto";
    nav.scrollingUp = false;
  },
  closeSearch() {
    this.mostrarBuscarTexto = false;
    this.mostrarHerramientas = "auto";
    nav.scrollingUp = false;
  },
});

export default function useTools() {
  return tools;
}
