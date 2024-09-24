<template>

    <!-- MODAL DE INVITACIONES -->
    <Modal :show="mostrarInvitar" @close="mostrarInvitar = false" maxWidth="xl">
        <div class="p-5 bg-base-200 max-h-full">
            <h3>Invitar al equipo</h3>
            <form @submit.prevent="invitar" class="flex flex-col gap-3 select-none">

                <tabs :options="{ disableScrollBehavior: true, useUrlFragment: false }">


                    <tab name="Buscar usuarios">

                        <div class="min-h-[calc(100vh_-_420px)] md:min-h-[calc(100vh_-_340px)]">
                            <div class="">
                                <input type="search"
                                class="input shadow flex-shrink-0 rounded-none border-b border-gray-500"
                                placeholder="Buscar usuario..." v-model="usuarioBuscar">
                            </div>

                            <div class="overflow-y-auto max-h-[calc(100vh_-_470px)] shadow"
                                :class="usuariosParaInvitar.length ? 'min-h-[160px] md:min-h-[120px]' : ''">
                                <table v-if="usuariosParaInvitar.length" class="table w-full bg-base-100  rounded-none">
                                    <tbody class="divide-y">
                                        <tr v-for="user of usuariosParaInvitar" :key="user.id">
                                            <td>{{ user.nombre }}</td>
                                            <td>
                                                <div v-if="user.miembro" class="uppercase p-1">Ya es miembro
                                                </div>
                                                <div v-else-if="user.agregado"
                                                    class="btn bg-base-100 border-none pointer-events-none">
                                                    <Icon icon="ph:check-circle-duotone" /> Agregado
                                                </div>
                                                <div v-else-if="user.invitacion"
                                                    class="btn bg-base-100 border-none pointer-events-none">
                                                    <Icon icon="ph:check-circle-info" /> Ya invitado
                                                </div>
                                                <div v-else class="btn" @click="agregarInvitado(user)">
                                                    Agregar
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-else-if="usuarioBuscar" class="py-12 px-4 bg-base-100">
                                    No hay resultados
                                </div>
                            </div>
                        </div>
                    </tab>


                    <tab :name="`Invitados ${usuariosInvitados.length ? '(' + usuariosInvitados.length + ')' : ''}`">
                        <div class="min-h-[calc(100vh_-_420px)] md:min-h-[calc(100vh_-_340px)]">

                            <div class="overflow-y-auto">

                                <table v-if="usuariosInvitados.length" class="table w-full bg-base-100 shadow">
                                    <tbody class="divide-y">
                                        <tr v-for="user of usuariosInvitados" :key="user.id">
                                            <td>{{ user.nombre }}</td>
                                            <td>
                                                <div v-if="user.invitacion" class="btn" @click="removerInvitado(user)">
                                                    Remover
                                                </div>
                                                <div v-else class="btn" @click="agregarInvitado(user)">
                                                    Agregar
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-else class="p-1">
                                    Ningún usuario agregado a la invitación.
                                </div>

                            </div>
                        </div>

                    </tab>

                    <tab :name="`Correos ${correosInvitados.length ? '(' + correosInvitados.length + ')' : ''}`">
                        <div class="min-h-[calc(100vh_-_420px)] md:min-h-[calc(100vh_-_340px)]">

                            <div class="mb-3">A las personas que no disponen de cuenta en tseyor.org puedes invitarlas
                                por correo.</div>

                            <textarea class="w-full" v-model="correos"
                                placeholder="correo1@gmail.com, correo2@yahoo.es, ..."></textarea>
                            <small>Escribe las direcciones de correo separadas por comas, por espacios, o en
                                cada
                                línea.</small>
                        </div>
                    </tab>


                    <tab
                        :name="`Invitaciones pendientes ${invitaciones.length ? '(' + invitaciones.length + ')' : ''}`">
                        <div class="min-h-[calc(100vh_-_420px)] md:min-h-[calc(100vh_-_340px)]">
                            <div class="flex justify-between items-center text-xs mb-4 gap-3">
                                <div v-for="stat of estadisticas" :key="stat.estado"
                                    :title="stat.total + ' ' + stat.label + (stat.label.endsWith('da') ? 's' : '')">
                                    {{ stat.emoji }}
                                    {{ stat.total }}
                                </div>
                                <div @click="cargarInvitaciones()"
                                    class="ml-auto flex items-center btn btn-xs btn-primary gap-1 flex-nowrap cursor-pointer">
                                    <Icon icon="ph:arrows-counter-clockwise-duotone" /> Actualizar
                                </div>
                            </div>
                            <div v-if="!invitaciones.length" class="py-4">
                                No hay invitaciones pendientes</div>
                            <div v-else class="max-h-[calc(100vh_-_470px)] md:max-h-[calc(100vh_-_380px)] overflow-y-auto">
                                <table class="table w-full bg-base-100  shadow">
                                    <thead>
                                        <tr>
                                            <th>Enviada</th>
                                            <th>Usuario/correo</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="inv of invitaciones.filter(inv => inv.estado != 'eliminada')"
                                            :key="inv.id">
                                            <td>
                                                <TimeAgo :date="new Date(inv.sent_at || inv.created_at)"
                                                    class="text-xs" />
                                            </td>
                                            <td>
                                                <div class="break-all text-sm select-text">
                                                    {{ inv.user ? inv.user.name : inv.email }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2 whitespace-nowrap"
                                                    :title="statusMap[inv.estado].title || statusMap[inv.estado].label"
                                                    :class="statusMap[inv.estado].class ?? ''">
                                                    <span class="text-xs uppercase">{{ statusMap[inv.estado].label
                                                        }}</span>
                                                    <Icon v-if="statusMap[inv.estado].icon"
                                                        :icon="statusMap[inv.estado].icon" />
                                                    <span v-else-if="statusMap[inv.estado].emoji">{{
                                                        statusMap[inv.estado].emoji
                                                    }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <Dropdown align="right" width="32">
                                                    <template #trigger>
                                                        <span class="my-1 bg-base-100 px-0.5 cursor-pointer">
                                                            <Icon icon="mdi:dots-vertical" class="text-xs" />
                                                        </span>
                                                    </template>

                                                    <template #content>
                                                        <div class="select-none">

                                                            <div v-if="['fallida'].includes(inv.estado)"
                                                                class="px-4 py-2 cursor-pointer hover:bg-base-100"
                                                                @click="reenviarInvitacion(inv)">
                                                                Reintentar</div>
                                                            <!-- tienen que haber pasado al menos dos horas para reenviar -->
                                                            <div v-else-if="inv.estado == 'cancelada' || (['enviada', 'declinada', 'caducada'].includes(inv.estado) && (new Date() - new Date(inv.sent_at || inv.created_at)) > 6291021)"
                                                                class="px-4 py-2 cursor-pointer hover:bg-base-100"
                                                                @click="reenviarInvitacion(inv)">
                                                                Reenviar
                                                            </div>
                                                            <div v-else-if="inv.estado == 'registro' && (new Date() - new Date(inv.sent_at || inv.created_at)) > 6291021"
                                                                class="px-4 py-2 cursor-pointer hover:bg-base-100"
                                                                @click="reenviarInvitacion(inv)">
                                                                Reenviar
                                                            </div>
                                                            <div v-if="inv.estado != 'cancelada'"
                                                                class="px-4 py-2 cursor-pointer hover:bg-base-100"
                                                                @click="cancelarInvitacion(inv)">Cancelar</div>
                                                            <div v-else="inv.estado=='cancelada'"
                                                                class="px-4 py-2 cursor-pointer hover:bg-base-100"
                                                                @click="eliminarInvitacion(inv)">Eliminar</div>

                                                        </div>
                                                    </template>
                                                </Dropdown>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </tab>

                </tabs>

                <div class="py-3 flex justify-end gap-5">
                    <button type="submit" class="btn btn-primary" :disabled="invitando || !numeroInvitados">
                        <Spinner v-show="invitando" class="mr-3" />
                        Invitar <span v-if="numeroInvitados">({{ numeroInvitados }})</span>
                    </button>

                    <button @click.prevent="mostrarInvitar = false" type="button" class="btn btn-neutral">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </Modal>

</template>



<script setup>
import { Tabs, Tab } from 'vue3-tabs-component';
import { useDebounce } from '@vueuse/core';
import { ucFirst } from '@/composables/textutils'


defineExpose({
    mostrar
});

const props = defineProps({ equipo: { type: Object, required: true } })

// Diálogo de INVITACIONES A FORMAR PARTE DEL EQUIPO
const mostrarInvitar = ref(false)
const correos = ref('');
const usuariosEncontrados = ref([]);
const usuariosInvitados = ref([])
const usuarioBuscar = ref("")
const debouncedBuscar = useDebounce(usuarioBuscar, 800);

watch(debouncedBuscar, buscarUsuarios)

const correosInvitados = computed(() => correos.value.split(/[\s,\n]+/m).map(c => c.trim()).filter(c => !!c).filter(c => c.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)))
const numeroInvitados = computed(() => usuariosInvitados.value.length + correosInvitados.value.length)
const invitando = ref(false)


const statusMap = {
    fallida: { label: 'Fallida', emoji: '❌' },
    pendiente: { label: 'En cola', emoji: '🕰️', title: 'El servidor ha encolado el envío' },
    enviada: { label: 'Entregada', emoji: '📬', },
    registro: { label: 'Registrándose', emoji: '✍️', class: 'text-green-500', title: 'El usuario aceptó y ahora está creando su cuenta' },
    caducada: { label: 'Caducada', emoji: '💀', class: 'opacity-75' },
    aceptada: { label: 'Aceptada', emoji: '✅', class: 'text-green-500' },
    declinada: { label: 'Rechazada', emoji: '🙅‍♂️', class: 'text-red-500' },
    cancelada: { label: 'Cancelada', emoji: '🗑️', class: 'opacity-50' },
    reintentando: { label: 'Reintentando', emoji: '📨' },
    reenviando: { label: 'Reenviando', emoji: '📨' },
}


// abre el diálogo modal
function mostrar() {
    usuarioBuscar.value = ''
    mostrarInvitar.value = true
}



function buscarUsuarios() {
    const query = debouncedBuscar.value.trim();

    if (query.length >= 3) {
        axios
            .get(route('usuarios.buscar', query))
            .then(response => {
                console.log('response', response.data)
                usuariosEncontrados.value = response.data
            })
            .catch(error => {
                console.error(error);
            });
    }
    else usuariosEncontrados.value = []
}

const usuariosParaInvitar = computed(() => {
    // añade el atributo 'miembro' a true si es miembro del equipo
    // le quitamos los usuarios que ya están invitados
    return usuariosEncontrados.value
        .map(u => ({
            ...u,
            agregado: !!usuariosInvitados.value.find(ui => ui.id == u.id),
            miembro: props.equipo.miembros.find(eu => eu.id == u.id) ? 1 : 0,
            invitacion: invitaciones.value.find(ui => ui.user_id == u.id && !ui.accepted_at && !ui.declined_at),
        }))
        .sort((a, b) => (a.miembro - b.miembro))

})

const invitaciones = ref([])
const estadisticas = computed(() => {
    const r = []
    Object.entries(statusMap).forEach(([k, v]) => {
        if (!['aceptada', 'reintentando', 'reenviando'].includes(k)) {
            const total = invitaciones.value.filter(i => i.estado == k).length
            if (total)
                r.push({ estado: k, total, label: statusMap[k].label, emoji: statusMap[k].emoji })
        }
    })
    return r
})

function cargarInvitaciones() {
    axios.get(route('equipo.invitaciones', props.equipo.id))
        .then(
            response => {
                invitaciones.value = response.data.invitaciones
                console.log('invitaciones:', response.data)
            }
        )
}

cargarInvitaciones()


function agregarInvitado(user) {
    usuariosInvitados.value.push({ ...user, invitacion: true })
}

function removerInvitado(user) {
    // lo quitamos de invitados
    const idx = usuariosInvitados.value.findIndex(u => u.id == user.id)
    if (idx > -1)
        usuariosInvitados.value.splice(idx, 1)
}


function mandado(num) {
    return num == 1 ? 'Se ha procesado' : 'Se han procesado'
}

function invitacionesPlural(num) {
    return num == 1 ? 'invitación' : 'invitaciones'
}

function usuariosPlural(num) {
    return num == 1 ? 'usuario' : 'usuarios'
}

function yason(num) {
    return num == 1 ? 'ya es miembro' : 'ya son miembros'
}

function yatiene(num) {
    return num == 1 ? 'ya tiene' : 'ya tienen'
}

function existe(num) {
    return num == 1 ? 'existe' : 'existen'
}

function invitar() {
    invitando.value = true
    axios
        .post(route('invitar', { idEquipo: props.equipo.id }), {
            correos: correosInvitados.value,
            usuarios: usuariosInvitados.value.map(u => u.id)
        })
        .then(response => {
            // Procesar la respuesta del controlador si es necesario
            const data = response.data
            alert((data.invitados.length ? `${mandado(data.invitados.length)} ${data.invitados.length} ${invitacionesPlural(data.invitados.length)}` : `No se han mandado las invitaciones`) +
                `\n` +
                (data.yaSonMiembros.length ? `${data.yaSonMiembros.length} ${usuariosPlural(data.yaSonMiembros.length)} ${yason(data.yaSonMiembros.length)} del equipo\n` : '') +
                (data.invitacionReciente.length ? `${data.invitacionReciente.length} ${usuariosPlural(data.invitacionReciente.length)} ${yatiene(data.invitacionReciente.length)} una invitación reciente\n` : '') +
                (data.noEncontrados.length ? `${data.noEncontrados.length} ${usuariosPlural(data.noEncontrados.length)} no ${existe(data.noEncontrados.length)}\n` : ''));

            // borramos lista de invitados
            correos.value = ""
            correosInvitados.value = []
            usuariosInvitados.value = []

            cargarInvitaciones()
            invitando.value = false
            console.log(response.data);
        })
        .catch(error => {
            console.error(error)
            cargarInvitaciones()
            // Manejar cualquier error de la solicitud
            switch (error.response?.status) {
                case 403: alert("No estás autorizado"); break;
                case 404:
                case 410: alert("Error interno. El equipo no existe"); break;
                default: alert("Hubo un error, no se pudo mandar las invitaciones.")
            }
            // console.error(response);
            invitando.value = false
        });
};

function reenviarInvitacion(invitacion) {
    const estadoPrevio = invitacion.estado
    invitacion.estado = invitacion.estado == 'fallida' ? 'reintentando' : 'reenviando'
    axios.get(route('invitacion.reenviar', invitacion.id))
        .then(() => {
            invitacion.estado = estadoPrevio == 'registro' ? 'registro' : 'pendiente'
            invitacion.sent_at = Date()
        })
        .catch(error => {
            console.error(error)
            // Manejar cualquier error de la solicitud
            switch (error.response?.status) {
                case 403: alert("No estás autorizado"); break;
                case 404:
                case 410: alert("Error interno. El equipo no existe"); break;
                default: alert("Hubo un error, no se pudo reenviar la invitación.")
            }
            // console.error(response);
            invitacion.estado = 'fallida'
        });
}

function cancelarInvitacion(invitacion) {
    if (confirm("Esto cancelará la invitación. ¿Deseas continuar?")) {
        axios.get(route('invitacion.cancelar', invitacion.id))
            .then(() => {
                invitacion.estado = 'cancelada'
            })
            .catch(error => {
                console.error(error)
                // Manejar cualquier error de la solicitud
                switch (error.response?.status) {
                    case 403: alert("No estás autorizado"); break;
                    case 404:
                    case 410: alert("Error interno. El equipo no existe"); break;
                    default: alert("Hubo un error, no se pudo cancelar la invitación.")
                }
                // console.error(response);
                invitacion.estado = 'fallida'
            });
    }
}


function eliminarInvitacion(invitacion) {
    if (confirm("Esto eliminará la invitación. ¿Deseas continuar?")) {
        axios.delete(route('invitacion.eliminar', invitacion.id))
            .then(() => {
                invitacion.estado = 'eliminada'
            })
            .catch(error => {
                console.error(error)
                // Manejar cualquier error de la solicitud
                switch (error.response?.status) {
                    case 403: alert("No estás autorizado"); break;
                    case 404:
                    case 410: alert("Error interno. El equipo no existe"); break;
                    default: alert("Hubo un error, no se pudo eliminar la invitación.")
                }
                // console.error(response);
            });
    }
}
</script>
