<template>
    <div>
        <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietario="propietario"
            @updated="reloadFolder" @folder="onFolder" @file="onFile" :embed="true"
            :ruta = "ruta"
            :rutaBase = "ruta"
            :cargando="cargando"
            :contentClass="contentClass"
            />

        <Modal :show="mostrandoImagen" @close="mostrandoImagen = null" maxWidth="xl">

            <div class="bg-base-100 p-3">
                <Image :src="mostrandoImagen.url" class="w-full max-h-[calc(100vh-170px)] object-contain" />

                <div class="flex pt-3 justify-between sm:justify-end gap-3 flex-shrink-0">
                    <button @click.prevent="insertarImagen" type="button" class="btn btn-primary">
                        Insertar
                    </button>

                    <button @click.prevent="mostrandoImagen = null" type="button" class="btn btn-neutral">
                        Cancelar
                    </button>
                </div>
            </div>

        </Modal>
    </div>
</template>


<script setup>
const props = defineProps({
    ruta: { type: String, required: false, default: "" },
    contentClass: String
});


const emit = defineEmits(['image:value'])

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
            console.log(err)
            alert("Hubo un error al cargar la carpeta")
        })
}

onMounted(() => {
    loadFolder()
})


function reloadFolder() {
    loadFolder()
}

function onFolder(item) {
    console.log('folder change to', item)
    currentUrl.value = item.url
    loadFolder()
}

const mostrandoImagen = ref(null)

function onFile(item) {
    console.log('file clicked', item)
    if (item.url.match(/\.(gif|png|webp|svg|jpe?g)$/i))
        mostrandoImagen.value = item
}


function insertarImagen() {
    console.log('insertarImagen', mostrandoImagen.value)
    emit('image', mostrandoImagen.value.url)
    mostrandoImagen.value = null
}
</script>

