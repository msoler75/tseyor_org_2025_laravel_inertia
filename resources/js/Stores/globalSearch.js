const state = reactive({
  opened: false,
  showSuggestions: true,
  query: "",
  lastQuery: "",
  results: null,
  restrictToCollections: null,
  includeDescription: false,
  searching: false,
  async call() {
    return doSearch();
  },
  clear() {
    this.query = "";
  },
  reset() {
    this.clear()
    this.restrictToCollections = null
    this.results = null
    this.searching = true
    this.lastQuery = null
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
