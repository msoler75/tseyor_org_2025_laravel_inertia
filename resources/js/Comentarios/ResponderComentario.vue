<template>
    <div class="w-full space-y-1 flex-grow flex gap-3">
        <img :src="'/storage/' + autor.imagen" class="w-16 h-16 rounded-full">
        <div class="w-full flex flex-col gap-3 mb-3">
            <!-- body -->
            cid: {{ comentarioId }}
            <textarea class="rounded-lg w-full" v-model="texto"></textarea>
            <div class="w-full flex justify-end">
                <button class="btn btn-primary" @click="responder" :disabled="!texto">Enviar</button>
            </div>
        </div>
    </div>
</template>


<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage()
const user = page.props.auth.user
const props = defineProps({
    comentarioId: { type: String | Number }
})

const emit = defineEmits(['respondido'])

const texto = ref("")
const autor = computed(() => (
    {
        id: user.id,
        nombre: user.name,
        imagen: user.profile_photo_path
    }
))

function responder() {
    const respuesta = { respuesta_a: props.comentarioId, texto: texto.value }
    console.log('respondercomentario.derponder', respuesta)
    emit('respondido', respuesta);
    texto.value = ""
}
</script>
