import { defineStore } from 'pinia'

const SEARCH_DELAY_MS = 350

export const useGlobalSearchStore = defineStore('globalSearch', {
  state: () => ({
    opened: false,
    showSuggestions: true,
    query: "",
    lastQuery: "",
    results: null,
    restrictToCollections: null,
    includeDescription: false,
    searching: false,
    autoFocus: true,
    // Timer y callbacks se manejan por instancia para SSR-safety
    _searchTimer: null,
    _queryChangeCallbacks: [],
  }),

  actions: {
    // Método para búsqueda inmediata (sin delay, cancela cualquier timer pendiente)
    searchNow() {
      this.cancelTimer()
      this.searching = true
      return this._doSearch()
    },

    // Método para búsqueda con delay (para cuando el usuario escribe)
    searchWithDelay(callback) {
      this.cancelTimer()
      if (this.query) {
        this._searchTimer = setTimeout(() => {
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
      this._queryChangeCallbacks.push(callback)
    },

    // Método para notificar cambios de query
    notifyQueryChange(newQuery, oldQuery) {
      this._queryChangeCallbacks.forEach(callback => {
        try {
          callback(newQuery, oldQuery)
        } catch (error) {
          // Error silenciado para mantener la funcionalidad
        }
      })
    },

    cancelTimer() {
      if (this._searchTimer) {
        clearTimeout(this._searchTimer)
        this._searchTimer = null
      }
    },

    clear() {
      this.setQuery("")
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
      this.opened = true
    },

    // Método privado para ejecutar búsqueda
    _doSearch() {
      const currentQuery = this.query
      return axios.get(route('buscar') + '?query=' + this.query
              + (this.restrictToCollections ? '&collections=' + this.restrictToCollections : '')
              + (this.includeDescription ? '&description' : '')
          )
              .then(response => {
                  console.log('response-data', response.data)
                  this.results = response.data.listado.data // .sort((a, b) => b.__tntSearchScore__ - a.__tntSearchScore__);
                  this.valid = response.data.busquedaValida
                  this.searching = false
              })
              .catch(error => {
                  this.searching = false
              })
              .finally(() => {
                  this.lastQuery = currentQuery
              })
    },
  },
})

// Composable que mantiene la misma API que antes
export default function useGlobalSearch() {
  const store = useGlobalSearchStore()

  // Retornar el store directamente para mantener la misma API
  // Esto permite usar search.propiedad sin necesidad de .value
  return store
}
