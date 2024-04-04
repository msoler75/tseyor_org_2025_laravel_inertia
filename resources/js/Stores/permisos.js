const state = reactive({
  permisos: [],
  cargarPermisos() {
    fetch("/usuarios/_permisos")
      .then((response) => response.json())
      .then((data) => {
        console.log("permisos response", data);
        this.permisos = data;
      })
      .catch((error) => {
        console.error("Error al cargar los permisos:", error);
      });
  },
  tienePermiso(permiso) {
    return this.permisos.includes(permiso);
  },
});

// carga inicial
state.cargarPermisos();

export default function usePermisos() {
  return state;
}
