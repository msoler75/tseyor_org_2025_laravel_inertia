import { defineStore } from 'pinia'

export const useFolderExplorerStore = defineStore('folderExplorer', {
  state: () => ({
    ruta: "",
    rutaBase: "",
    // estado de navegación
    navegando: null,
    // modo embed
    modoInsertar: false,
    // items
    items: [], // items raw
    itemsShow: [], // items mostrados
    // callbacks
    callbacks: {},
    // propiedades y estados
    embed: false, // el navegador de archivos está embebido
    esAdministrador: false, // el usuario actual es administrador?
    infoCargada: false, // la información adicional de cada item (permisos, numero de archivos y carpetas) está cargada?
    seleccionando: false, // estamos en modo de selección de items?
    seleccionAbierta: false, // un estado para permitir la selección desde cero
    // operaciones de copiar o mover
    sourcePath: null,
    isMovingFiles: false, // estamos en modo de moviendo items?
    isCopyingFiles: false, // estamos en modo de copiando items?
    filesToMove: [], // items a mover
    filesToCopy: [], // items a copiar
    // propiedades y permisos
    permisosModificados: false,
    propietarioRef: null,
    // busquedas
    textoBuscar: "",
    mostrandoResultadosBusqueda: false,
    resultadosBusqueda: [],
    //visor de imágenes
    v3ImgPreviewFn: null,
    images: [], // lista de imagenes para previsualizar
    // ordenación
    ordenarPor: "normal",
    // admin
    mostrarRutas: false,
  }),

  getters: {
    // estamos en la raíz?
    enRaiz() {
      return this.items[1]?.tipo === "disco" || this.items[0]?.ruta == "mis_archivos"
    },

    // ruta actual
    rutaActual() {
      return this.itemsShow.length ? this.itemsShow[0].ruta : ""
    },

    // puede editar la carpeta actual?
    puedeEscribir() {
      return this.esAdministrador ||
        (this.itemsShow.length ? this.itemsShow[0].puedeEscribir : false)
    },

    // puede leer la carpeta actual?
    puedeLeer() {
      return this.esAdministrador ||
        (this.itemsShow.length ? this.itemsShow[0].puedeLeer : false)
    },

    // items seleccionados
    itemsSeleccionados() {
      return this.itemsShow.filter(
        (item) =>
          ![".", ".."].includes(item.nombre) &&
          item.seleccionado &&
          !item.eliminado
      )
    },

    // EMBED
    imagenesSeleccionadas() {
      return this.itemsSeleccionados.filter(item => this.isImage(item.nombre))
    },

    // puede mover aquí los items seleccionados?
    puedeMoverSeleccionados() {
      return this.esAdministrador ||
        this.itemsSeleccionados.find((item) => item.puedeEscribir)
    },

    // puede borrar los items seleccionados?
    puedeBorrarSeleccionados() {
      return this.puedeMoverSeleccionados
    },

    // estamos en estado de buscando carpeta destino?
    buscandoCarpetaDestino() {
      return this.isMovingFiles || this.isCopyingFiles
    },

    // propietario
    tituloPropietario() {
      if (!this.propietarioRef) return "";
      return "Propietario: " + this.propietarioRef.nombre;
    },
  },

  actions: {
    // callbacks
    on(nombre, fn) {
      this.callbacks[nombre] = fn;
    },

    call(nombre, arg1, arg2) {
      if (!(nombre in this.callbacks)) {
        console.error("Callback", nombre, "no encontrado");
        return;
      }
      this.callbacks[nombre](arg1, arg2);
    },

    actualizar() {
      this.call("update");
    },

    // SELECCION

    cancelarSeleccion() {
      this.seleccionando = false;
      this.itemsShow.forEach((item) => (item.seleccionado = false));
    },

    seleccionarTodos() {
      this.itemsShow.forEach((item) => (item.seleccionado = true));
    },

    // verifica que cuando no hay ningun item seleccionado, se termina el modo de selección
    verificarFinSeleccion() {
      if (!this.seleccionando) return;
      if (typeof window !== 'undefined' && screen.width >= 1024) return;
      const alguno = this.itemsShow.find((item) => item.seleccionado);
      if (!alguno && !this.seleccionAbierta) this.seleccionando = false;
    },

    // CLICKS

    clickItem(item, event) {
      this.call("clickItem", item, event);
    },

    clickDisk(item, event) {
      this.call("clickDisk", item, event);
    },

    clickFolder(item, event) {
      this.call("clickFolder", item, event);
    },

    clickFile(item, event) {
      this.call("clickFile", item, event);
    },

    clickBreadcrumb(item, event) {
      this.call("clickBreadcrumb", item, event);
    },

    touchStart(item, event) {
      this.call("touchStart", item, event);
    },

    touchEnd(item, event) {
      this.call("touchEnd", item, event);
    },

    touchMove(item, event) {
      this.call("touchMove", item, event);
    },

    // HELPERS

    toggleItem(item) {
      if (!item.touching) item.seleccionado = !item.seleccionado;
      item.touching = false;
    },

    nombreItem(item) {
      if (item.actual)
        return `<span class='text-neutral opacity-70'>&lt;${item.nombre}&gt;</span>`;
      if (item.padre)
        return `<span class='text-neutral opacity-70'>&lt;arriba&gt;</span>`;
      return item.nombre;
    },

    isImage(fileName) {
      if (!fileName || typeof fileName != "string") return false;
      const ext = fileName.split(".").pop().toLowerCase();

      switch (ext) {
        case "gif":
        case "pcx":
        case "bmp":
        case "svg":
        case "jpg":
        case "jpeg":
        case "jfif":
        case "webp":
        case "png":
          return true;
      }
      return false;
    },

    esPantallaTactil() {
      if (typeof window === 'undefined') return false;
      return "ontouchstart" in window || navigator.maxTouchPoints > 0;
    },
  }
})

export function useStore() {
  const store = useFolderExplorerStore()
  return store
}

export default function useFolderExplorer() {
  const store = useFolderExplorerStore()

  // WATCHERS (solo en cliente)
  if (typeof window !== 'undefined') {
    // si hay algun cambio en los items
    watch(() => store.itemsShow, () => store.verificarFinSeleccion(), { deep: true })

    watch(
      () => store.itemsSeleccionados.length,
      (value) => {
        if(value)
          store.seleccionAbierta = false
        else
          store.verificarFinSeleccion()
      }
    )
  }

  return store
}
