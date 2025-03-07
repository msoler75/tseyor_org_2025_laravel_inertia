<template>
    <div class="bg-base-200 border-t-2 border-base-3 py-12">
        <div class="container mx-auto space-y-2">
            <div class="mx-auto sm:max-w-[640px]">
                <h2 class="mb-9">Comentarios</h2>
                <ResponderComentario v-if="user" class="my-7" :url="url" @respondido="nuevoComentario" />
                <div class="w-full" style="--profundidad: 0;">
                    <TransitionGroup v-if="comentarios.length" name="comment">
                        <Comentario v-for="comentario in comentarios" :key="comentario.id" :autor="comentario.autor"
                            :comentario-id="comentario.id" :url="url" :fecha="comentario.created_at"
                            :texto="comentario.texto" :respuestas="comentario.respuestas" />
                    </TransitionGroup>
                    <p v-if="!cargando && !comentarios.length">No hay comentarios</p>
                </div>
            </div>
        </div>
    </div>
</template>



<script setup>
const page = usePage()
const props = defineProps({
    url: {
        type: String,
        default() {
            return null;
        }
    }
})

const user = page.props.auth?.user
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

function nuevoComentario(comentario) {
    console.log('comentarios.nuevoComentario', comentario)
    comentarios.value.unshift({
        id: comentario.id,
        autor: nuevoAutor,
        texto: comentario.texto,
        fecha: new Date()
    })
}

const comentarios = ref([])
const cargando = ref(true)

// cargamos los comentarios de este contenido
onMounted(() => {
    const url = props.url ? props.url: page.url
    axios.get(route('comentarios') + '?url=' + url).then(response => {
        comentarios.value = response.data.comentarios
        cargando.value = false
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
