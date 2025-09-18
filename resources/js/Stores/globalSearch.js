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
    console.warn('[SEARCH DEBUG] searchNow() ejecutado')
    this.cancelTimer()
    this.searching = true
    return doSearch()
  },

  // Método para búsqueda con delay (para cuando el usuario escribe)
  searchWithDelay(callback) {
    console.warn('[SEARCH DEBUG] searchWithDelay() ejecutado')
    this.cancelTimer()
    if (this.query) {
      searchTimer = setTimeout(() => {
        console.warn('[SEARCH DEBUG] timer ejecutándose, llamando callback')
        if (callback && typeof callback === 'function') {
          callback()
        }
      }, SEARCH_DELAY_MS)
    }
  },

  // Método para configurar y ejecutar búsqueda inmediata (para componentes externos)
  configure(config) {
    console.warn('[SEARCH DEBUG] configure() llamado con config:', config)
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
      console.warn('[SEARCH DEBUG] configure() ejecutando búsqueda inmediata')
      return this.searchNow()
    }
  },

  // Método para cambiar query y notificar (para input del usuario)
  setQuery(newQuery) {
    console.warn('[SEARCH DEBUG] setQuery() llamado con:', newQuery)
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
    console.warn('[SEARCH DEBUG] onQueryChange() - callback registrado')
  },

  // Método para notificar cambios de query
  notifyQueryChange(newQuery, oldQuery) {
    console.warn('[SEARCH DEBUG] notifyQueryChange() - notificando cambio:', oldQuery, '->', newQuery)
    queryChangeCallbacks.forEach(callback => {
      try {
        callback(newQuery, oldQuery)
      } catch (error) {
        console.error('[SEARCH DEBUG] Error en callback de query change:', error)
      }
    })
  },

  cancelTimer() {
    if (searchTimer) {
      console.warn('[SEARCH DEBUG] cancelTimer() - timer cancelado')
      clearTimeout(searchTimer)
      searchTimer = null
    } else {
      console.warn('[SEARCH DEBUG] cancelTimer() - no había timer activo')
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
    console.warn('[SEARCH DEBUG] doSearch() iniciado con query:', state.query)
    var currentQuery = state.query
    return axios.get(route('buscar') + '?query=' + state.query
            + (state.restrictToCollections ? '&collections=' + state.restrictToCollections : '')
            + (state.includeDescription ? '&description' : '')
        )
            .then(response => {
                console.warn('[SEARCH DEBUG] doSearch() respuesta recibida para query:', currentQuery)
                console.log('response-data', response.data)
                state.results = response.data.listado.data // .sort((a, b) => b.__tntSearchScore__ - a.__tntSearchScore__);
                state.valid = response.data.busquedaValida
                state.searching = false
            })
            .catch(error => {
                console.warn('[SEARCH DEBUG] doSearch() error para query:', currentQuery, error)
                state.searching = false
            })
                .finally(() => {
            state.lastQuery = currentQuery
        })

}

export default function useGlobalSearch() {
  return state;
}
