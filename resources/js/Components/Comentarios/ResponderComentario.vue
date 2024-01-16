<template>
    <div class="w-full space-y-1 flex-grow flex gap-3">
        <img :src="autor.imagen" class="w-16 h-16 rounded-full">
        <div class="w-full flex flex-col gap-3 mb-3">
            <!-- body -->
            <form @submit.prevent="responder">
                <textarea class="textarea text-rounded-lg w-full" v-model="texto" placeholder="Escribe tu comentario..."
                    @keydown.ctrl.enter="responder" ref="inputText"></textarea>
                <div class="w-full flex justify-end">
                    <button class="btn btn-primary" @click="responder" :disabled="!texto">Enviar</button>
                </div>
            </form>
            <div v-if="error" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ error }}</span>
            </div>
        </div>
    </div>
</template>


<script setup>
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { nextTick, onMounted } from 'vue';
const page = usePage()
const user = page.props.auth.user
const props = defineProps({
    comentarioId: String | Number,
    url: String,
    focus: Boolean
})

const inputText = ref(null)

onMounted(() => {
    if (props.focus)
        nextTick(() => {
            inputText.value.focus()
        })
})

const emit = defineEmits(['respondido'])

const texto = ref("")
const error = ref(null)
const autor = computed(() => (
    {
        id: user.id,
        nombre: user.name,
        imagen: user.profile_photo_path
    }
))

function responder() {
    // limpiamos el mensaje de error
    error.value = ""

    // guardamos el texto por si lo hemos de recuperar
    const oldTexto = texto.value

    axios.post(route('comentario.nuevo'), {
        url: props.url,
        respuesta_a: props.comentarioId,
        texto: texto.value
    })
        .then(respuesta => {
            emit('respondido', respuesta.data);
        })
        .catch(err => {
            error.value = "No se ha podido enviar el comentario."
            texto.value = oldTexto
        })

    texto.value = ""
}
</script>
