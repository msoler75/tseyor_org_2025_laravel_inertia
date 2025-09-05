<template>
    <div v-if="page.props.auth?.user" class="cursor-pointer" @click="toggleFavorito" :title="favoritoLocal ? 'Quitar de favoritos' : 'Añadir a favoritos'">
        <Icon icon="ph:heart-duotone" v-show="favoritoLocal" />
        <Icon v-show="!favoritoLocal" icon="ph:heart-light"/>
    </div>
</template>

<script setup>
const page = usePage()

const props = defineProps({
    coleccion: { type: String, required: true },
    id: { type: [String, Number], required: true },
    inicial: { type: Boolean, default: false }
})

const emit = defineEmits(['change'])

const loading = ref(false)
const favoritoLocal = ref(props.inicial)

watch(() => props.inicial, (v) => { favoritoLocal.value = v })

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]')
    return meta ? meta.getAttribute('content') : null
}

async function toggleFavorito() {
    if (loading.value) return
    loading.value = true

    const csrf = getCsrfToken()
    const url = `/favoritos/${encodeURIComponent(props.coleccion)}/${encodeURIComponent(props.id)}`

    const prevValue = favoritoLocal.value
    try {
        const headers =   {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
        if (favoritoLocal.value) {
            favoritoLocal.value = false
            // eliminar
            await axios.delete(url, {
                headers,
                withCredentials: true
            })
            emit('change', false)
        } else {
            // crear
            favoritoLocal.value = true
            await axios.post(url, { coleccion: props.coleccion, id_ref: props.id }, {
                headers,
                withCredentials: true
            })
            emit('change', true)
        }
    } catch (e) {
        console.error(e)
        favoritoLocal.value = prevValue
    } finally {
        loading.value = false
    }
}
</script>
