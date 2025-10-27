import { reactive, watch } from "vue";

// selectores/opciones del usuario que pueden persistir entre sesiones o entre cambios de página

const state = reactive({
  soloTitulosLibros: false,
  archivosVista: "normal",
  vistaComunicados: "",
  mostrarPermisos: false,
  developerMode: false,
  emisoraRadio: null,
  tamanyoFuente: 16,
});

// avoid registering multiple watchers when useSelectors() is called from many components
let _persistInitialized = false;

export default function useSelectors() {
  if (typeof window !== "undefined" && !_persistInitialized) {
    try {
      // Initialize from server props first, then localStorage
      const page = window.page || {};
      const initialFontSize = page.props?.initialFontSize || 16;
      const stored = localStorage.getItem("tseyor_tamanyoFuente");
      const parsed = stored ? parseInt(stored, 10) : initialFontSize;
      if (!Number.isNaN(parsed)) state.tamanyoFuente = parsed;

      // If localStorage has a different value than server, sync the cookie
      if (stored && parseInt(stored, 10) !== initialFontSize) {
        fetch('/update-font-size', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ fontSize: parseInt(stored, 10) }),
        }).catch(error => {
          console.warn('Error syncing font size to server:', error);
        });
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
          // Also update cookie for server-side persistence
          fetch('/update-font-size', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ fontSize: val }),
          }).catch(error => {
            console.warn('Error updating font size on server:', error);
          });
        } catch (e) {
          // ignore
        }
      }
    );

    _persistInitialized = true;
  }

  return state;
}
