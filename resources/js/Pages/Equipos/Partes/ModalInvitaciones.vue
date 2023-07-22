<template>

    <!-- MODAL DE INVITACIONES -->
    <Modal :show="mostrarInvitar" @close="mostrarInvitar = false" maxWidth="md">
        <div class="p-5 bg-base-200 max-h-full">
            <h3>Invitar al equipo</h3>
            <form @submit.prevent="invitar" class="flex flex-col gap-7 select-none">

                <tabs>


                    <tab name="Buscar usuarios">

                        <div class="min-h-[200px]">
                            <input type="search" class="input shadow flex-shrink-0 rounded-none border-b border-gray-500"
                                placeholder="Buscar usuario..." v-model="usuarioBuscar">

                            <div class="overflow-y-auto max-h-[calc(100vh-480px)] md:max-h-[calc(100vh-420px)] shadow">
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
                                                <div v-else class="btn" @click="agregarInvitado(user)">
                                                    Agregar
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-else-if="usuarioBuscar" class="p-2 bg-base-100">
                                    No hay resultados
                                </div>
                            </div>
                        </div>
                    </tab>


                    <tab :name="`Invitados ${usuariosInvitados.length ? '(' + usuariosInvitados.length + ')' : ''}`">
                        <div class="min-h-[200px]">

                            <div class="overflow-y-auto max-h-[calc(100vh-480px)] md:max-h-[calc(100vh-420px)]  mt-3">

                                <table v-if="usuariosInvitados.length" class="table w-full bg-base-100  shadow">
                                    <tbody class="divide-y">
                                        <tr v-for="user of usuariosInvitados" :key="user.id">
                                            <td>{{ user.nombre }}</td>
                                            <td>
                                                <div v-if="user.invitado" class="btn" @click="removerInvitado(user)">
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

                        <p>A las personas que no disponen de cuenta en tseyor.org puedes invitarlas por correo.</p>

                        <div>
                            <textarea class="w-full" v-model="correos"
                                placeholder="correo1@gmail.com, correo2@yahoo.es, ..."></textarea>
                            <small>Escribe las direcciones de correo separadas por comas, por espacios, o en
                                cada
                                línea.</small>
                        </div>
                    </tab>

                </tabs>

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button type="submit" class="btn btn-primary" :disabled="!numeroInvitados">
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
                usuariosEncontrados.value = response.data;
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
            miembro: props.equipo.miembros.find(eu => eu.id == u.id) ? 1 : 0
        }))
        .sort((a, b) => (a.miembro - b.miembro))

})



function agregarInvitado(user) {
    usuariosInvitados.value.push({ ...user, invitado: true })
}

function removerInvitado(user) {
    // lo quitamos de invitados
    const idx = usuariosInvitados.value.findIndex(u => u.id == user.id)
    if (idx > -1)
        usuariosInvitados.value.splice(idx, 1)
}

function invitar() {
    axios
        .post(route('invitar', { idEquipo: props.equipo.id }), {
            correos: correosInvitados.value,
            usuarios: usuariosInvitados.value.map(u => u.id)
        })
        .then(response => {
            // Procesar la respuesta del controlador si es necesario
            console.log(response.data);
        })
        .catch(error => {
            // Manejar cualquier error de la solicitud
            console.error(error);
        });
};


</script>
