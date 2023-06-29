<template>
    <div class="bg-base-200 border-t-2 border-base-3 py-12">
        <div class="container mx-auto">
            <h2>Comentarios</h2>
            <div style="--depth: 0;">
                <UserComment v-for="comment in comments" :key="comment.id" :author="comment.author"
                    :date="comment.created_at" :content="comment.content" :replies="replies_of(comment.id)">
                </UserComment>
            </div>
        </div>
    </div>
</template>



<script setup>
const props = defineProps({
    contenidoId: { type: String }
})

const comentarios = ref([])

const comments = computed(() => comentarios.value.filter(c => !c.respuesta_a).map(c => comment_of(c)))

function replies_of(id) {
    return comentarios.value.filter(c => c.respuesta_a == id).map(c => comment_of(c))
}

function author_of(comentario) {
    return { id: comentario.autor.id, name: comentario.autor.nombre, avatar: comentario.autor.imagen }
}

function comment_of(comentario) {
    return {
        id: comentario.id,
        created_at: comentario.created_at,
        updated_at: comentario.updated_at,
        content: comentario.texto,
        author: author_of(comentario),
        replies: replies_of(comentario.id)
    }
}

onMounted(() => {
    axios.get(route('comentarios') + '?contenido_id=' + props.contenidoId).then(response => {
        comentarios.value = response.data.comentarios
    })
})

</script>
