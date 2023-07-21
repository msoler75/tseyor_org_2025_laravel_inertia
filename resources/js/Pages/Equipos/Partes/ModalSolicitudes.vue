<template>
    <Modal :show="modalSolicitudes" maxWidth="sm" @close="modalSolicitudes = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 max-h-[90vh] select-none">
            <h3>Solicitudes de ingreso</h3>

            <input type="search" class="input shadow flex-shrink-0" placeholder="Buscar..." v-model="miembroBuscar">

            <div class="overflow-y-auto bg-base-100 shadow">
                <table v-if="equipo.solicitudesPendientes.length " class="table w-full">
                    <tbody class="divide-y">
                        <tr v-for="solicitud of equipo.solicitudesPendientes" :key="user.id" class="cursor-pointer"
                            :class="user.pivot.rol == 'coordinador' ? 'bg-blue-50' : ''">
                            <td><Avatar :user="solicitud.usuario"/></td>
                            <td>{{ solicitud.nombre }}</td>
                            <td>{{ solicitud.email }}</td>
                            <td>
                                <button class="btn">aceptar</button>
                                <button class="btn">denegar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="py-2">
                    No hay solicitudes pendientes.
                </div>
            </div>


            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="modalSolicitudes = false" type="button" class="btn btn-neutral">
                    cerrar
                </button>
            </div>
        </div>
    </Modal>
</template>


<script setup>

defineExpose({
    mostrar
});

const solicitudesHistorial = ref([])

const props = defineProps({ equipo: { type: Object, required: true } })

// Diálogo de ADMINISTRAR solicitudes de incorporación al equipo

const modalSolicitudes = ref(false)

// mostrar modal
function mostrar() {
    modalSolicitudes.value = true
}

onMounted(() => {
    axios.get(route('equipo.solicitudes', props.equipo.id))
        .then(response => {
            solicitudesHistorial.value = response.data
        })
        .catch(error => {
            console.log(error)
        })
})

</script>
