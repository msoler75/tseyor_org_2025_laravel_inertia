const state = reactive({
  soloTitulosLibros: false,
  archivosVista: "normal",
  vistaComunicados: "",
  mostrarPermisos: false,
});

export default function useSelectors() {
  return state;
}
