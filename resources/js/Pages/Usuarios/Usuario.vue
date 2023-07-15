<template>
    <Sections>
        <Section class="container mx-auto flex flex-col items-center py-20">
            <div class="avatar">
                <div class="w-32 h-32 rounded-full">
                    <Image :src="urlImage" :alt="`Imagen del usuario ${usuario.name || usuario.slug}`" />
                </div>
            </div>

            <h1 class="text-center my-2">
                {{ usuario.name || usuario.slug }}
            </h1>

            <div class="prose my-7">
                <blockquote>
                    <p>{{ usuario.frase }}</p>
                </blockquote>
            </div>

            <div class="flex flex-wrap justify-center gap-5">
                <Link class="badge badge-neutral gap-2" v-for="equipo of usuario.equipos" :key="equipo.id"
                    :href="route('equipo', equipo.slug || equipo.id)">
                <span v-if="administrar" @click.prevent="abrirModalEliminarEquipo(equipo)">x</span>
                {{ equipo.nombre }}
                </Link>

            </div>

            <div v-if="administrar&&equiposFiltrados.length" class="mt-7">
                <form @submit.prevent="agregarEquipo" class="flex gap-3">
                    <select v-model="equipoSeleccionado" placeholder="Elige un equipo..." class="text-gray-900">
                        <option v-for="equipo of equiposFiltrados" :key="equipo.id" :value="equipo.id">{{ equipo.nombre }}</option>
                    </select>
                    <input type="submit" class="btn btn-primary" value="Agregar Equipo" :disabled="!equipoSeleccionado">
                </form>
            </div>

        </Section>

        <Section class="py-20">
            <div class="container mx-auto">
                <h2 class="text-center">Últimos comentarios</h2>
                <ul class="list-none space-y-5">
                    <li v-for="comentario of comentarios"
                        class="w-full flex flex-col gap-3 md:flex-row justify-between items-baseline">
                        <Link :href="comentario.url" class="prose">
                        <blockquote>
                            <p>
                                {{ comentario.texto }}
                            </p>
                        </blockquote>
                        </Link>
                        <TimeAgo :date="comentario.created_at" class="text-sm" />
                    </li>
                </ul>
            </div>
        </Section>


        <!-- Modal Confirmación de eliminar Archivo -->
        <ConfirmationModal :show="modalEliminarEquipo" @close="modalEliminarEquipo = false">
            <template #title>
                Confirmación de eliminación
            </template>
            <template #content>
                ¿Quieres eliminar {{ equipoAEliminar.nombre }} de este usuario?
            </template>
            <template #footer>
                <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @submit.prevent="eliminarEquipo">

                    <button @click.prevent="modalEliminarEquipo = false" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Eliminar
                    </button>
                </form>
            </template>
        </ConfirmationModal>

    </Sections>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
    comentarios: {
        type: Array,
        required: true
    },
    administrar: {
        type: Boolean,
        default: false
    },
    equipos: {
        type: Array,
        default: []
    }
})

// todos los equipos, excepto los que el usuario ya es miembro
const equiposFiltrados = computed(()=>props.equipos.filter(e=>!props.usuario.equipos.find(ue=>ue.id==e.id)))

const image = computed(() => props.usuario.avatar || props.usuario.profile_ptoho_path || props.usuario.imagen)
const urlImage = computed(() => {
    if (!image.value) return '/storage/profile-photos/user.png'
    if (image.value.match(/^https?:\/\//)) return image.value
    return '/storage/' + image.value
})


const modalEliminarEquipo = ref(false)
const equipoAEliminar = ref(null)
const equipoSeleccionado = ref(null)
const error = ref(null)

function abrirModalEliminarEquipo(equipo) {
    equipoAEliminar.value = equipo
    modalEliminarEquipo.value = true
}

function eliminarEquipo() {
    console.log('eliminar Equipo', equipoAEliminar.value, 'user:', props.usuario.id)
    modalEliminarEquipo.value = false
    axios.put(route('equipo.remover', { idEquipo: equipoAEliminar.value.id, idUsuario: props.usuario.id }))
        .then(respuesta => {
            reload()
        })
        .catch(err => {
            console.log({ err })
            error.value = "No se ha podido remover el equipo."
        })
}

function reload() {
    console.log('reload')
    router.reload({
        only: ['usuario']
    })
}

function agregarEquipo() {
    console.log('agregar Equipo', { idEquipo: equipoSeleccionado.value, idUsuario: props.usuario.id })
    axios.put(route('equipo.agregar', { idEquipo: equipoSeleccionado.value, idUsuario: props.usuario.id }))
        .then(respuesta => {
            reload()
        })
        .catch(err => {
            console.log({ err })
            error.value = "No se ha podido agregar el equipo."
        })
}
</script>
