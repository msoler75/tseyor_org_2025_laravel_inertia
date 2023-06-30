<template>
    <div class="bg-base-200 border-t-2 border-base-3 py-12">
        <pre>
            {{ comentarios }}
        </pre>
        <div class="container mx-auto space-y-2">
            <div class="mx-auto sm:max-w-[640px]">
                <h2 class="mb-9">Comentarios</h2>
                <ResponderComentario v-if="user" class="my-7" :contenido-id="contenidoId" @respondido="nuevoComentario" />
                <div class="w-full" style="--profundidad: 0;">
                    <TransitionGroup name="pop">
                        <Comentario v-for="comentario in comentarios" :key="comentario.id"
                            :autor="comentario.autor" :comentario-id="comentario.id" :contenido-id="contenidoId"
                            :fecha="comentario.created_at" :texto="comentario.texto"
                            :respuestas="comentario.respuestas" />
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </div>
</template>



<script setup>
import { usePage } from '@inertiajs/vue3';


const props = defineProps({
    contenidoId: { type: String }
})


const page = usePage()
const user = page.props.auth.user
const autor = computed(() => (
    {
        id: user.id,
        nombre: user.name,
        imagen: user.profile_photo_path
    }
))

function nuevoComentario(comentario) {
    console.log('comentarios.nuevoComentario', comentario)
    comentarios.value.unshift({
        id: Math.random(),
        autor,
        texto: comentario.texto,
        fecha: new Date()
    })
}

function nuevaRespuesta(respuesta) {
    console.log('comentarios.nuevaRespuesta', respuesta)
    comentarios.value.unshift({
        id: Math.random(),
        autor,
        respuesta_a: respuesta.respuesta_a,
        texto: respuesta.texto,
        fecha: new Date()
    })
}

const comentarios = ref([])



// cargamos los comentarios de este contenido
onMounted(() => {
    axios.get(route('comentarios') + '?contenido_id=' + props.contenidoId).then(response => {
        comentarios.value = response.data.comentarios
    })
})

</script>
