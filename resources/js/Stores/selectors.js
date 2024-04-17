const state = reactive({
  soloTitulosLibros: false,
  archivosVista: "normal",
  vistaComunicados: "",
  mostrarPermisos: false,
  developerMode: false
});

export default function useSelectors() {
  return state;
}
