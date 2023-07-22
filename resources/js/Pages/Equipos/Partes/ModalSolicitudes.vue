<template>
    <Modal :show="modalSolicitudes" @close="modalSolicitudes = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 select-none">
            <h3>Solicitudes de ingreso</h3>

            <tabs :options="{ useUrlFragment: false }">
                <tab :name="`Solicitudes pendientes (${numPendientes})`">

                    <div v-if="equipo.solicitudesPendientes.length"
                        class="overflow-y-auto bg-base-100 shadow max-h-[calc(100vh-470px)]">
                        <table class="table w-full">
                            <tbody class="divide-y">
                                <tr v-for="solicitud of equipo.solicitudesPendientes" :key="solicitud.id"
                                    class="cursor-pointer">
                                    <td>
                                        <Avatar v-if="solicitud.usuario" :user="solicitud.usuario" openTab />
                                    </td>
                                    <td>
                                        <a :href="route('usuario', solicitud.usuario.slug || solicitud.usuario.id)"
                                            target="_blank">
                                            {{ solicitud.usuario.name }}
                                            &lt;{{ solicitud.usuario.email }}&gt;
                                        </a>
                                    </td>
                                    <td>
                                        <div v-if="solicitud.fecha_aceptacion" class="py-3 uppercase text-success">
                                            solicitud aceptada
                                        </div>
                                        <div v-else-if="solicitud.fecha_denegacion" class="py-3 uppercase text-error">
                                            solicitud denegada
                                        </div>
                                        <div v-else class="flex gap-3 mr-5 flex-shrink-0">
                                            <button class="btn" @click="aceptar(solicitud)">
                                                <Icon icon="ph:thumbs-up-duotone" />
                                                <span class="hidden sm:inline"> aceptar</span>
                                            </button>
                                            <button class="btn" @click="denegar(solicitud)">
                                                <Icon icon="ph:thumbs-down-duotone" />
                                                <span class="hidden sm:inline">denegar</span>
                                            </button>
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
                        <table class="table">
                            <thead>
                                <th>Fecha de solicitud</th>
                                <th>Usuario</th>
                                <th>Respuesta</th>
                                <th></th>
                                <th>Por</th>
                            </thead>
                            <tbody>
                                <tr v-for="solicitud of solicitudesHistorial" :key="solicitud.id">
                                    <td>
                                        <TimeAgo :date="solicitud.created_at" />
                                    </td>
                                    <td>
                                        <User :user="solicitud.usuario" />
                                    </td>
                                    <td><span v-if="solicitud.fecha_aceptacion" class="text-success">Aceptada</span>
                                        <span v-else-if="solicitud.fecha_denegacion" class="text-error">Denegada</span>
                                    </td>
                                    <td>
                                        <TimeAgo v-if="solicitud.fecha_aceptacion || solicitud.fecha_denegacion"
                                            :date="solicitud.fecha_aceptacion || solicitud.fecha_denegacion" />
                                    </td>
                                    <td>
                                        <User v-if="solicitud.coordinador" :user="solicitud.coordinador" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

const numPendientes = computed(() => {
    return props.equipo.solicitudesPendientes.filter(s => !s.fecha_aceptacion && !s.fecha_denegacion).length
})

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

function aceptar(solicitud) {
    solicitud.fecha_aceptacion = new Date().toISOString()
    solicitud.coordinador = usePage().props.auth.user
    axios.get(route('solicitud.aceptar', solicitud.id))
        .then(response => {
            solicitudesHistorial.value.unshift(solicitud)
            // emit('')
        })
        .catch(error => {
            alert("Hubo algún error")
        })
}

function denegar(solicitud) {
    solicitud.fecha_denegacion = new Date().toISOString()
    solicitud.coordinador = usePage().props.auth.user
    axios.get(route('solicitud.denegar', solicitud.id))
        .then(response => {
            solicitudesHistorial.value.unshift(solicitud)
        })
        .catch(error => {
            alert("Hubo algún error")
        })
}
</script>
