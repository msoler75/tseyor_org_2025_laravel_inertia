<template>
    <div class="bg-base-200 border-t-2 border-base-3 py-12">
        <div class="container mx-auto space-y-2">
            <div class="mx-auto sm:max-w-[640px]">
                <h2 class="mb-9">Comentarios</h2>
                <ResponderComentario class="my-7" :contenido-id="contenidoId" @respondido="nuevoComentario" />
                <div class="w-full" style="--profundidad: 0;">
                    <Comentario v-for="comentario in comentarios_primer_nivel" :key="comentario.id"
                        :autor="comentario.autor" :comentario-id="comentario.id" :contenido-id="contenidoId"
                        :fecha="comentario.created_at" :texto="comentario.texto" :respuestas="respuestas_de(comentario.id)">
                    </Comentario>
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


function nuevoComentario(respuesta) {
    console.log('nuevoComentario', respuesta)
    comentarios.value.unshift({
        autor,
        texto: respuesta,
        fecha: new Date()
    })
}


const comentarios = ref([])

const comentarios_primer_nivel = computed(() => comentarios.value.filter(c => !c.respuesta_a).map(c => comentario_de(c)))

function respuestas_de(id) {
    return comentarios.value.filter(c => c.respuesta_a == id).map(c => comentario_de(c))
}

function autor_de(comentario) {
    return { id: comentario.autor.id, nombre: comentario.autor.nombre, imagen: comentario.autor.imagen }
}

function comentario_de(comentario) {
    return {
        id: comentario.id,
        fecha: comentario.created_at,
        texto: comentario.texto,
        autor: autor_de(comentario),
        respuestas: respuestas_de(comentario.id)
    }
}

// cargamos los comentarios de este contenido
onMounted(() => {
    axios.get(route('comentarios') + '?contenido_id=' + props.contenidoId).then(response => {
        comentarios.value = response.data.comentarios
    })
})

</script>
