const state = reactive({
  soloTitulosLibros: false,
  archivosVista: "normal",
  vistaComunicados: "",
  mostrarPermisos: false,
  developerMode: false,
  emisoraRadio: null
});

export default function useSelectors() {
  return state;
}
