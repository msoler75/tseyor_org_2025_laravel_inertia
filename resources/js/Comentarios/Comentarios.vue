<template>
    <div class="bg-base-200 border-t-2 border-base-3 py-12">
        <div class="container mx-auto space-y-2">
            <div class="mx-auto sm:max-w-[640px]">
                <h2 class="mb-9">Comentarios</h2>
                <ResponderComentario v-if="user" class="my-7" :contenido-id="contenidoId" @respondido="nuevoComentario" />
                <div class="w-full" style="--profundidad: 0;">
                    <TransitionGroup name="comment">
                        <Comentario v-for="comentario in comentarios" :key="comentario.id" :autor="comentario.autor"
                            :comentario-id="comentario.id" :contenido-id="contenidoId" :fecha="comentario.created_at"
                            :texto="comentario.texto" :respuestas="comentario.respuestas" />
                    </TransitionGroup>
                </div>
            </div>
        </div>
    </div>
</template>



<script setup>
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    contenidoId: String
})

const page = usePage()
const user = page.props.auth.user

function nuevoComentario(comentario) {
    console.log('comentarios.nuevoComentario', comentario)
    comentarios.value.unshift(comentario)
}

const comentarios = ref([])

// cargamos los comentarios de este contenido
onMounted(() => {
    axios.get(route('comentarios') + '?contenido_id=' + props.contenidoId).then(response => {
        comentarios.value = response.data.comentarios
    })
})

</script>


<style>
/* base */
.comment {
    backface-visibility: hidden;
    z-index: 1;
}

/* moving */
.comment-move {
    transition: all 600ms ease-in-out 50ms;
}

/* appearing */
.comment-enter-active {
    transition: all 400ms ease-out;
    transform: scale(0);
}

/* disappearing */
.comment-leave-active {
    transition: all 200ms ease-in;
    position: absolute;
    z-index: 0;
    transform: scale(0);
}

/* appear at / disappear to */
.comment-enter-to {
    opacity: 1;
    transform: scale(1);
}

.comment-leave-to {
    opacity: 0;
    transform: scale(0);
}
</style>
