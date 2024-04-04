<template>
        <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietario="propietario" @updated="reloadFolder"
            @disk="onDisk" @folder="onFolder" @file="onFile" :embed="true" :ruta="ruta" rutaBase="" :cargando="cargando"
            rootLabel="web:"
            rootUrl=""
            :mostrarMisArchivos="(mostrarMisArchivos !== false && mostrarMisArchivos !== 'false' && mostrarMisArchivos !== '0' && mostrarMisArchivos !== 'no') || ['true', '1', 'si'].includes(mostrarMisArchivos) || mostrarMisArchivos===true"
            >
            <Modal :show="mostrandoArchivo" @close="mostrandoArchivo = null" maxWidth="xl">

<div class="bg-base-100 p-3">
    <div class="p-8">{{ mostrandoArchivo.nombre }}</div>

    <div class="flex pt-3 justify-between sm:justify-end gap-3 flex-shrink-0">

        <button @click.prevent="descargarArchivo" type="button" class="btn btn-secondary">
            Descargar
        </button>

        <button @click.prevent="mostrandoArchivo = null" type="button" class="btn btn-neutral">
            Cancelar
        </button>
    </div>
</div>

</Modal>



<Modal :show="mostrandoImagen" @close="mostrandoImagen = null" maxWidth="xl">

<div class="bg-base-100 p-3">
    <img :src="mostrandoImagen.url+'?mw=700&mh=600'" class="w-full max-h-[calc(100vh-170px)] object-contain" />

    <div class="flex pt-3 justify-between sm:justify-end gap-3 flex-shrink-0">
        <button v-if="modoInsertar" @click.prevent="insertarImagen" type="button" class="btn btn-primary">
            Insertar
        </button>

        <button @click.prevent="descargarImagen" type="button" class="btn btn-secondary flex gap-2 items-center">
            <Icon icon="ph:arrow-square-out-duotone" class="text-xl"/> Abrir
        </button>

        <button @click.prevent="mostrandoImagen = null" type="button" class="btn btn-neutral">
            {{modoInsertar?'Cancelar':'Cerrar'}}
        </button>
    </div>
</div>

</Modal>
</FolderExplorer>

</template>


<script setup>
import usePlayer from '@/Stores/player'

const props = defineProps({
    ruta: { type: String, required: false, default: "" },
    modoInsertar : { type: Boolean, default: false},
    contentClass: String,
    mostrarMisArchivos : {type: [Boolean, String], default: true}
});

const player = usePlayer()

const emit = defineEmits(['image'])

const cargando = ref(true)

const items = ref([{
    ruta: props.ruta
}])
const puedeEscribir = ref(false)
const propietario = ref(null)

const currentUrl = ref('/' + props.ruta)

function loadFolder() {
    cargando.value = true
    console.log('loadFolder', currentUrl.value)
    // axios.get(route('filemanager', currentUrl.value).replace(/%2F/g, '/'))
    axios.get('/filemanager' + currentUrl.value)
        .then(response => {
            console.log('response', response)
            items.value = response.data.items
            puedeEscribir.value = response.data.puedeEscribir
            propietario.value = response.data.propietario
            cargando.value = false
        })
        .catch(err => {
            cargando.value = false
            console.log(err)
            const data = err.response?.data
            const message = data?.message
            if (message)
                alert(message)
            else
                alert("Hubo un error al cargar la carpeta")
        })
}

onMounted(() => {
    loadFolder()
})


function reloadFolder() {
    loadFolder()
}


function onDisk(item) {
    console.log('disk change to', item)
    currentUrl.value = '/' + item.ruta
    loadFolder()
}

function onFolder(item) {
    console.log('folder change to', item)
    currentUrl.value = '/' + item.ruta
    loadFolder()
}

const mostrandoImagen = ref(null)
const mostrandoArchivo = ref(null)

function onFile(item) {
    console.log('file clicked', item)
    if (item.url.match(/\.(gif|png|webp|svg|jpe?g)$/i))
        mostrandoImagen.value = item
    else if(player.isPlayable(item.url))
        player.play(item.url, item.nombre)
    else
        mostrandoArchivo.value = item
}


function insertarImagen() {
    console.log('insertarImagen', mostrandoImagen.value)
    emit('image', mostrandoImagen.value.url)
    mostrandoImagen.value = null
}

function descargarImagen() {
    // abre en una nueva ventana
    window.open(mostrandoImagen.value.url, '_blank')
}

function descargarArchivo() {
    // abre en una nueva ventana
    window.open(mostrandoArchivo.value.url, '_blank')
    mostrandoArchivo.value = false
}
</script>

