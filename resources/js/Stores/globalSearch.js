const state = reactive({
  opened: false,
  showSuggestions: true,
  query: "",
  lastQuery: "",
  results: null,
  restrictToCollections: null,
  clear() {
    this.query = "";
  },
  open() {
    this.opened = true;
  },
});

export default function useGlobalSearch() {
  return state;
}
