import { reactive, watch } from "vue";

const state = reactive({
  soloTitulosLibros: false,
  archivosVista: "normal",
  vistaComunicados: "",
  mostrarPermisos: false,
  developerMode: false,
  emisoraRadio: null,
  tamanyoFuente: 16
});

// avoid registering multiple watchers when useSelectors() is called from many components
let _persistInitialized = false;

export default function useSelectors() {
  if (typeof window !== "undefined" && !_persistInitialized) {
    try {
      const stored = localStorage.getItem("tseyor_tamanyoFuente");
      if (stored !== null) {
        const parsed = parseInt(stored, 10);
        if (!Number.isNaN(parsed)) state.tamanyoFuente = parsed;
      }
    } catch (e) {
      // ignore localStorage errors (privacy mode, etc.)
    }

    // persist changes
    watch(
      () => state.tamanyoFuente,
      (val) => {
        try {
          localStorage.setItem("tseyor_tamanyoFuente", String(val));
        } catch (e) {
          // ignore
        }
      }
    );

    _persistInitialized = true;
  }

  return state;
}
