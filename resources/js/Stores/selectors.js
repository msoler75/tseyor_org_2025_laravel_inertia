import { defineStore } from 'pinia';

// selectores/opciones del usuario que pueden persistir entre sesiones o entre cambios de página

export const useSelectorsStore = defineStore('selectors', {
  state: () => ({
    soloTitulosLibros: false,
    archivosVista: "normal",
    vistaComunicados: "",
    mostrarPermisos: false,
    developerMode: false,
    emisoraRadio: null,
  }),

  actions: {
    // Inicialización del store para SSR y persistencia
    initializeStore() {
      // Reserved for future persisted selectors; intentionally left blank
    },
  },
});

// Composable que mantiene la misma API que antes
export default function useSelectors() {
  const store = useSelectorsStore();

  // Inicializar el store la primera vez que se usa
  store.initializeStore();

  // Retornar el store directamente para mantener la misma API
  // Esto permite usar store.propiedad sin necesidad de .value
  return store;
}
