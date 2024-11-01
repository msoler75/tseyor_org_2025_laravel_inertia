const state = reactive({
  permisos: [],
  // cargar los permisos
  cargarPermisos() {
    fetch("/usuario/_permisos")
      .then((response) => response.json())
      .then((data) => {
        console.log("permisos response", data);
        this.permisos = data;
      })
      .catch((error) => {
        console.error("Error al cargar los permisos:", error);
      });
  },
  // comprueba si el usuario dispone de un permiso
  tienePermiso(permiso) {
    return this.permisos.includes(permiso);
  },
  // eliminar los permisos
  borrarPermisos() {
    this.permisos.splice(0, this.permisos.length);
  },
  // muular electrónico
  saldo: "",
  saldoError: "",
  // cargar saldo
  cargarSaldo() {
    this.saldoError = "";
    fetch("/usuario/_saldo_muulares")
      .then((response) => response.json())
      .then((data) => {
        console.log("saldo response", data);
        if (data.error) {
          console.warn("Error al cargar el saldo:", data.error);
          this.saldoError = data.error;
          this.saldo = "Error";
          return;
        }
        this.saldo = data.saldo;
      })
      .catch((error) => {
        console.warn("Error al cargar el saldo:", error);
        this.saldoError = error;
        this.saldo = "Error";
      });
  },
});

// carga inicial
// state.cargarPermisos();

export default function useUserStore() {
  return state;
}
