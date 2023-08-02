<template>
    <div v-if="cargaInicial" class="w-full p-12 flex justify-center items-center text-3xl">
        <Spinner />
    </div>
    <FolderExplorer v-else :items="items" :puedeEscribir="puedeEscribir" :propietario="propietario" @updated="reloadFolder"
        @folder="folder" @file="file" :embed="true" />
</template>


<script setup>
const props = defineProps({
    url: { type: String, required: false, default:"/" },
});

const cargaInicial = ref(true)

const items = ref([])
const puedeEscribir = ref(false)
const propietario = ref(null)

const currentUrl = ref(props.url)

function loadFolder() {
    console.log('loadFolder', currentUrl.value)
    // axios.get(route('filemanager', currentUrl.value).replace(/%2F/g, '/'))
     axios.get('/filemanager'+ currentUrl.value)
        .then(response => {
            console.log('response', response)
            items.value = response.data.items
            puedeEscribir.value = response.data.puedeEscribir
            propietario.value = response.data.propietario
            cargaInicial.value = false
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

function folder(item) {
    console.log('folder change to', item)
    currentUrl.value = item.url
    loadFolder()
}

function file(item) {
    console.log('file clicked', item)
}

</script>

