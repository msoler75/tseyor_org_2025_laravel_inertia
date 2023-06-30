<template>
    <div class="w-full flex gap-3">
        <img :src="'/storage/' + autor.imagen" class="w-16 h-16 rounded-full">
        <div class="flex-grow">
            <div class="flex flex-col gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <!-- body -->
                    <div class="card bg-base-100 shadow p-3 gap-3 w-full">
                        <div class="w-full flex justify-between">
                            <strong>{{ autor.nombre }}</strong>
                            <TimeAgo :fecha="fecha" class="text-xs" />
                        </div>
                        <div>{{ texto }}</div>
                    </div>
                    <!-- actions -->
                    <div class="pl-3 flex gap-5 text-sm" v-if="page.props.auth.user && !respondiendo">
                        <!-- <a href="#">Me gusta</a> -->
                        <span @click="respondiendo = true" class="cursor-pointer">Responder</span>
                    </div>
                </div>
            </div>

            <ResponderComentario v-if="respondiendo" class="my-7" :contenido-id="contenidoId" :responder-a="comentarioId"
                @respondido="nuevaRespuesta" />

            <div v-if="respuestas.length" class="list" :style="'--profundidad: ' + profundidad">
                <Comentario v-for="respuesta in respuestas" :key="respuesta.id" :autor="respuesta.autor"
                    :fecha="respuesta.created_at" :texto="respuesta.texto" :respuestas="respuesta.respuestas"
                    :profundidad="profundidad + 1"></Comentario>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage()

const props = defineProps({
    comentarioId: String | Number,
    contenidoId: String | Number,
    autor: Object,
    texto: String,
    fecha: Number | String,
    respuestas: {
        type: Array,
        default: () => []
    },
    profundidad: {
        type: Number,
        default: 0
    }
});

const respuestas = ref(props.respuestas)

const respondiendo = ref(false)

function nuevaRespuesta(respuesta) {
    respondiendo.value = false
    respuestas.value.unshift({
        autor,
        texto: respuesta,
        fecha: new Date()
    })
}

const user = page.props.auth.user
const autor = computed(() => (
    {
        id: user.id,
        nombre: user.name,
        imagen: user.profile_photo_path
    }
))

</script>

