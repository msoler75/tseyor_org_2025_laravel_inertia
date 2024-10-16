const state = reactive({
  ruta: "",
  rutaBase: "",
  // estado de navegación
  navegando: null,
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
  // operaciones de copiar o mover
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
});

const computados = {
  // estamos en la raíz?
  enRaiz: computed(
    () =>
      state.items[1]?.tipo === "disco" || state.items[0].ruta == "mis_archivos"
  ),

  // ruta actual
  rutaActual: computed(() =>
    state.itemsShow.length ? state.itemsShow[0].ruta : ""
  ),

  // puede editar la carpeta actual?
  puedeEscribir: computed(
    () =>
      state.esAdministrador ||
      (state.itemsShow.length ? state.itemsShow[0].puedeEscribir : false)
  ),

  // puede leer la carpeta actual?
  puedeLeer: computed(
    () =>
      state.esAdministrador ||
      (state.itemsShow.length ? state.itemsShow[0].puedeLeer : false)
  ),

  // items seleccionados
  itemsSeleccionados: computed(() =>
    state.itemsShow.filter(
      (item) =>
        ![".", ".."].includes(item.nombre) &&
        item.seleccionado &&
        !item.eliminado
    )
  ),

  // puede mover aquí los items seleccionados?
  puedeMoverSeleccionados: computed(
    () =>
      state.esAdministrador ||
      computados.itemsSeleccionados.value.find((item) => item.puedeEscribir)
  ),

  // puede borrar los items seleccionados?
  puedeBorrarSeleccionados: computed(
    () => computados.puedeMoverSeleccionados.value
  ),

  // estamos en estado de buscando carpeta destino?
  buscandoCarpetaDestino: computed(
    () => state.isMovingFiles || state.isCopyingFiles
  ),

  // propietario
  tituloPropietario: computed(() => {
    if (!state.propietarioRef) return "";
    return "Propietario: " + state.propietarioRef.nombre;
  }),
};

const metodos = {
  // Métodos
  toggleSeleccionando() {
    state.seleccionando = !state.seleccionando;
  },

  // callbacks
  on(nombre, fn) {
    state.callbacks[nombre] = fn;
  },

  call(nombre, arg1, arg2) {
    if (!(nombre in state.callbacks)) {
      console.error("Callback", nombre, "no encontrado");
      return;
    }
    state.callbacks[nombre](arg1, arg2);
  },

  actualizar() {
    metodos.call("update");
  },

  // CLICK ITEMS

  // EMBED

  // SELECCION

  cancelarSeleccion() {
    state.seleccionando = false;
    state.itemsShow.forEach((item) => (item.seleccionado = false));
  },

  seleccionarTodos() {
    state.itemsShow.forEach((item) => (item.seleccionado = true));
  },

  // verifica que cuando no hay ningun item seleccionado, se termina el modo de selección
  verificarFinSeleccion() {
    if (!state.seleccionando) return;
    if (screen.width >= 1024) return;
    const alguno = state.itemsShow.find((item) => item.seleccionado);
    if (!alguno) state.seleccionando = false;
  },

  // CLICKS

  clickItem(item, event) {
    metodos.call("clickItem", item, event);
  },

  clickDisk(item, event) {
    metodos.call("clickDisk", item, event);
  },

  clickFolder(item, event) {
    metodos.call("clickFolder", item, event);
  },

  clickFile(item, event) {
    metodos.call("clickFile", item, event);
  },

  clickBreadcrumb(item, event) {
    metodos.call("clickBreadcrumb", item, event);
  },
};

// WATCHERS

// si hay algun cambio en los items
watch(() => state.itemsShow, metodos.verificarFinSeleccion, { deep: true });

watch(
  () => computados.itemsSeleccionados.value.length,
  (value) => {
    console.log("itemsSeleccionados.length=", value);
    if (!value) metodos.cancelarSeleccion();
  }
);

const storeProxy = new Proxy(
  { ...metodos, ...state, ...computados },
  {
    get(target, property) {
      if (property in computados) {
        return computados[property].value;
      }
      if (property in metodos) {
        return metodos[property];
      }
      return state[property];
    },
    set(target, property, value) {
      if (property in state) {
        state[property] = value;
      } else {
        target[property] = value;
      }
      return true;
    },
  }
);

export function useStore() {
  return storeProxy;
}

//export const usePlayer = createGlobalState(() => {
export default function useFolderExplorerStore() {
  return storeProxy;
}
