<template>
    <div class="w-full flex gap-3">
        <Avatar :user="autor" />
        <div class="flex-grow">
            <div class="flex flex-col gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <!-- body -->
                    <div class="card bg-base-100 shadow p-3 gap-3 w-full">
                        <div class="w-full flex justify-between">
                            <strong>{{ autor?.nombre }}</strong>
                            <TimeAgo :fecha="fecha" class="text-xs" />
                        </div>
                        <div>{{ texto }}</div>
                    </div>
                    <!-- actions -->
                    <div class="pl-3 flex gap-5 text-sm" v-if="user && !respondiendo">
                        <!-- <a href="#">Me gusta</a> -->
                        <span @click="respondiendo = true" class="cursor-pointer">Responder</span>
                    </div>
                </div>
            </div>

            <ResponderComentario v-if="user && respondiendo" class="my-7" :url="url" :key="comentarioId" :focus="true"
                :comentario-id="comentarioId" @respondido="nuevaRespuesta" />

            <div v-show="respuestasList.length" :style="'--profundidad: ' + profundidad">
                <TransitionGroup name="comment">
                    <Comentario v-for="respuesta in respuestasList" :key="respuesta.id" :autor="respuesta.autor"
                        :comentario-id="respuesta.id" :url="url" :fecha="respuesta.created_at" :texto="respuesta.texto"
                        :respuestas="respuesta.respuestas" :profundidad="profundidad + 1" />
                </TransitionGroup>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    comentarioId: String | Number,
    url: String | Number,
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

const respondiendo = ref(false)
const respuestasList = ref(props.respuestas)

function nuevaRespuesta(respuesta) {
    console.log('comentario.nuevaRespuesta', respuesta)
    if (respuesta.respuesta_a == props.comentarioId)
        respondiendo.value = false
    respuestasList.value.unshift({
        id: Math.random(),
        autor: nuevoAutor,
        texto: respuesta.texto,
        respuesta_a: props.comentarioId,
        fecha: new Date()
    })
}

const page = usePage()
const user = page.props.auth.user
const nuevoAutor = /*computed(() => user */ user ?
    {
        id: user.id,
        nombre: user.name,
        imagen: user.profile_photo_url
    } : {
        id: 0,
        nombre: "",
        imagen: ""
    }
// )

</script>

