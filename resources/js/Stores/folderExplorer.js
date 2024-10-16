const state = reactive({
  // items
  items: [], // items raw
  itemsShow: [], // items mostrados

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
  // renombrar item
  nuevoNombre: "",
  itemRenombrando: null,
  modalRenombrarItem: false,
  renombrandoItem: false,
  // eliminar item
  itemAEliminar: null,
  modalEliminarItem: false,
});



const computados = {

    // estamos en la raíz?
  enRaiz: computed(
    () =>
      state.items[1]?.tipo === "disco" || state.items[0].ruta == "mis_archivos"
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
  puedeBorrarSeleccionados: computed(() => computados.puedeMoverSeleccionados.value),

  // estamos en estado de buscando carpeta destino?
  buscandoCarpetaDestino: computed(
    () => state.isMovingFiles || state.isCopyingFiles
  ),

}


const metodos = {


  // Métodos
  toggleSeleccionando() {
    state.seleccionando = !state.seleccionando;
  },

  // operaciones de renombrar
  abrirModalRenombrar(item) {
    // item.seleccionado = false // para el caso de renombrar un item seleccionado
    state.renombrandoItem = false;
    state.itemRenombrando = item;
    state.nuevoNombre = item.nombre;
    state.modalRenombrarItem = true;
    // establecemos focus en el input
    setTimeout(() => {
      if (state.modalRenombrarItem) {
        const elem = document.querySelector("#nuevoNombre");
        if (elem) elem.focus();
      }
    }, 500);
  },

  renombrarItem() {
    state.itemRenombrando.seleccionado = false;
    state.renombrandoItem = true;
    axios
      .post("/files/rename", {
        folder: state.itemRenombrando.carpeta,
        oldName: state.itemRenombrando.nombre,
        newName: state.nuevoNombre,
      })
      .then((response) => {
        console.log({ response });
        state.modalRenombrarItem = false;
        const item = state.itemRenombrando; // itemsComplete.find(it => it.nombre == itemRenombrando.value.nombre)
        // console.log('renombrar item', item)
        item.ruta = item.carpeta + "/" + state.nuevoNombre;
        const parts = item.url.split("/");
        parts[parts.length - 1] = parts[parts.length - 1].replace(
          item.nombre,
          state.nuevoNombre
        );
        item.url = parts.join("/");
        item.nombre = state.nuevoNombre;
        if (!state.embed)
          if (item.actual) {
            // reemplazar la URL actual en el historial del navegador
            router.replace(item.url);

            // reemplazar el título de la página
            document.title = item.ruta;
          }
        // else
        // reloadPage()
      })
      .catch((err) => {
        const errorMessage =
          err.response.data.error ||
          "Ocurrió un error al renombrar el elemento";
        alert(errorMessage);
        state.renombrandoItem = false;
      });
  },

  // eliminar item
  abrirEliminarModal(item) {
    state.itemAEliminar = item;
    state.modalEliminarItem = true;
  },

  eliminarArchivos() {
    console.log("eliminarArchivos");
    if (state.itemAEliminar) eliminarArchivo(state.itemAEliminar);
    else {
      for (var item of computados.itemsSeleccionados.value) eliminarArchivo(item);
    }
    state.modalEliminarItem = false;
  },

  eliminarArchivo(item) {
    console.log("eloiminar¡", item);
    const url =
      "/files" + ("/" + item.ruta).replace(/\/\//g, "/").replace(/%2F/g, "/");
    console.log({ url });
    return axios
      .delete(url)
      .then((response) => {
        item.eliminado = true;
      })
      .catch((err) => {
        const errorMessage =
          err.response.data.error ||
          "Ocurrió un error al eliminar el archivo " + item.nombre;
        alert(errorMessage);
      });
  },

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
