<template>
    <Modal :show="modalSolicitudes" @close="modalSolicitudes = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 select-none">
            <h3>Solicitudes de ingreso</h3>

            <tabs>
                <tab :name="`Solicitudes pendientes (${equipo.solicitudesPendientes.length})`">

                    <div v-if="equipo.solicitudesPendientes.length"
                        class="overflow-y-auto bg-base-100 shadow max-h-[calc(100vh-470px)]">
                        <table class="table w-full">
                            <tbody class="divide-y">
                                <tr v-for="solicitud of equipo.solicitudesPendientes" :key="solicitud.id"
                                    class="cursor-pointer">
                                    <td>
                                        <Avatar :user="solicitud.usuario" openTab />
                                    </td>
                                    <td>
                                        <a :href="route('usuario', solicitud.usuario.slug || solicitud.usuario.id)"
                                            target="_blank">
                                            {{ solicitud.usuario.name }}
                                            &lt;{{ solicitud.usuario.email }}&gt;
                                        </a>
                                    </td>
                                    <td>
                                        <div class="flex gap-3">
                                            <button class="btn">aceptar</button>
                                            <button class="btn">denegar</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="py-2">
                        No hay solicitudes pendientes.
                    </div>
                </tab>

                <tab name="Historial">
                    <div v-if="solicitudesHistorial.length"
                        class="overflow-y-auto bg-base-100 shadow max-h-[calc(100vh-470px)]">
                        <ul>
                            <li v-for="solicitud of solicitudesHistorial" :key="solicitud.id">{{ solicitud }}</li>
                        </ul>
                    </div>
                </tab>
            </tabs>



            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="modalSolicitudes = false" type="button" class="btn btn-neutral">
                    cerrar
                </button>
            </div>
        </div>
    </Modal>
</template>


<script setup>
import { Tabs, Tab } from 'vue3-tabs-component';

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
