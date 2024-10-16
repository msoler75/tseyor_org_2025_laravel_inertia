const state = reactive({
  embed: false, // el navegador de archivos está embebido
  esAdministrador: false, // el usuario actual es administrador?
  infoCargada: false, // la información adicional de cada item (permisos, numero de archivos y carpetas) está cargada?
  seleccionando: false, // estamos en modo de selección de items?
  // operaciones de copiar o mover
  isMovingFiles: false, // estamos en modo de moviendo items?
  isCopyingFiles: false, // estamos en modo de copiando items?
  filesToMove: [], // items a mover
  filesToCopy: [], // items a copiar
  // renombrar
  nuevoNombre: "",
  itemRenombrando: null,
  modalRenombrarItem: false,
  renombrandoItem: false
});



const store = {
    ...state,
    buscandoCarpetaDestino: computed(() => state.isMovingFiles || state.isCopyingFiles),

    // Aquí puedes agregar métodos para modificar el estado
    toggleSeleccionando() {
      state.seleccionando = !state.seleccionando;
    },

    // operaciones de renombrar
    abrirModalRenombrar(item) {
        // item.seleccionado = false // para el caso de renombrar un item seleccionado
        state.renombrandoItem = false
        state.itemRenombrando = item
        state.nuevoNombre = item.nombre
        state.modalRenombrarItem = true
        // establecemos focus en el input
        setTimeout(() => {
            if (state.modalRenombrarItem)
            {
                const elem = document.querySelector('#nuevoNombre')
                if(elem)
                    elem.focus()
            }
        }, 500)
    },

    renombrarItem() {
        state.itemRenombrando.seleccionado = false
        state.renombrandoItem = true
        axios.post('/files/rename', {
            folder: state.itemRenombrando.carpeta,
            oldName: state.itemRenombrando.nombre,
            newName: state.nuevoNombre,
        })
            .then(response => {
                console.log({ response })
                state.modalRenombrarItem = false
                const item = state.itemRenombrando // itemsComplete.find(it => it.nombre == itemRenombrando.value.nombre)
                // console.log('renombrar item', item)
                item.ruta = item.carpeta + '/' + state.nuevoNombre
                const parts = item.url.split('/')
                parts[parts.length - 1] = parts[parts.length - 1].replace(item.nombre, state.nuevoNombre)
                item.url = parts.join('/')
                item.nombre = state.nuevoNombre
                if (!state.embed)
                    if (item.actual) {
                        // reemplazar la URL actual en el historial del navegador
                        router.replace(item.url);

                        // reemplazar el título de la página
                        document.title = item.ruta
                    }
                // else
                // reloadPage()
            })
            .catch(err => {
                const errorMessage = err.response.data.error || 'Ocurrió un error al renombrar el elemento'
                alert(errorMessage)
                state.renombrandoItem = false
            })
    }
  };

//export const usePlayer = createGlobalState(() => {
export default function useFolderExplorerStore() {
    return store;
}
