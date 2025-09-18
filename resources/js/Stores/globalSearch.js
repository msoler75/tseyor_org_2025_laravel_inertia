import { nextTick } from 'vue'

const SEARCH_DELAY_MS = 350

let searchTimer = null
let queryChangeCallbacks = [] // Callbacks para notificar cambios de query

const state = reactive({
  opened: false,
  showSuggestions: true,
  query: "",
  lastQuery: "",
  results: null,
  restrictToCollections: null,
  includeDescription: false,
  searching: false,
  autoFocus: true,

  // Método para búsqueda inmediata (sin delay, cancela cualquier timer pendiente)
  searchNow() {
    this.cancelTimer()
    this.searching = true
    return doSearch()
  },

  // Método para búsqueda con delay (para cuando el usuario escribe)
  searchWithDelay(callback) {
    this.cancelTimer()
    if (this.query) {
      searchTimer = setTimeout(() => {
        if (callback && typeof callback === 'function') {
          callback()
        }
      }, SEARCH_DELAY_MS)
    }
  },

  // Método para configurar y ejecutar búsqueda inmediata (para componentes externos)
  configure(config) {
    this.cancelTimer()

    // Configurar propiedades SIN activar callbacks de query
    if (config.query !== undefined) {
      this.query = config.query
      // NO notificar cambio de query aquí porque configuramos todo de una vez
    }
    if (config.includeDescription !== undefined) this.includeDescription = config.includeDescription
    if (config.restrictToCollections !== undefined) this.restrictToCollections = config.restrictToCollections
    if (config.autoFocus !== undefined) this.autoFocus = config.autoFocus
    if (config.opened !== undefined) this.opened = config.opened

    // Ejecutar búsqueda inmediata si hay query
    if (this.query) {
      return this.searchNow()
    }
  },

  // Método para cambiar query y notificar (para input del usuario)
  setQuery(newQuery) {
    const oldQuery = this.query
    this.query = newQuery

    // Notificar a los callbacks si el query cambió
    if (oldQuery !== newQuery) {
      this.notifyQueryChange(newQuery, oldQuery)
    }
  },

  // Método para suscribirse a cambios de query
  onQueryChange(callback) {
    queryChangeCallbacks.push(callback)
  },

  // Método para notificar cambios de query
  notifyQueryChange(newQuery, oldQuery) {
    queryChangeCallbacks.forEach(callback => {
      try {
        callback(newQuery, oldQuery)
      } catch (error) {
        // Error silenciado para mantener la funcionalidad
      }
    })
  },

  cancelTimer() {
    if (searchTimer) {
      clearTimeout(searchTimer)
      searchTimer = null
    }
  },

  clear() {
    this.setQuery("");
  },

  reset() {
    this.setQuery("")
    this.cancelTimer()
    this.restrictToCollections = null
    this.results = null
    this.searching = false
    this.lastQuery = null
    this.autoFocus = true
  },

  open() {
    this.opened = true;
  },
});

function doSearch() {
    var currentQuery = state.query
    return axios.get(route('buscar') + '?query=' + state.query
            + (state.restrictToCollections ? '&collections=' + state.restrictToCollections : '')
            + (state.includeDescription ? '&description' : '')
        )
            .then(response => {
                console.log('response-data', response.data)
                state.results = response.data.listado.data // .sort((a, b) => b.__tntSearchScore__ - a.__tntSearchScore__);
                state.valid = response.data.busquedaValida
                state.searching = false
            })
            .catch(error => {
                state.searching = false
            })
                .finally(() => {
            state.lastQuery = currentQuery
        })

}

export default function useGlobalSearch() {
  return state;
}
