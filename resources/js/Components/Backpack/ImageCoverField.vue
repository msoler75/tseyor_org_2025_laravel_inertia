<template>
    <div>
        <input type="text" :name="name" v-model="selected" class="w-full sm:w-[600px]">

        <ModalDropZone v-model="modalSubirImage" @uploaded="uploadedImage($event)"
        placeholder="Arrastra la imagen aquí o haz clic" url="/files/upload/image" :mediaFolder="folder" :options="{
            maxFiles: 1,
            acceptedFiles: 'image/*'
        }" />

        <div class="flex mt-2">
            <div class="flex items-center overflow-x-auto">
                <div v-for="url of images" :key="url"
                    class="relative border-4 border-transparent shrink-0 cursor-pointer group" :title="url"
                    :class="url == selected ? 'border-orange-500!' : ''" @click="selected = url">
                    <Image :src="url.startsWith('/') ? url + '?h=150' : url" style="height:150px" error-icon />
                    <div v-if="canDelete" @click.stop="borrarImagen(url)" title="Borrar imagen"
                        class="absolute bottom-1 right-1 bg-red-600 text-white hidden group-hover:block opacity-50 hover:opacity-100! p-1 rounded-xs">
                        <Icon icon="ph:trash" />
                    </div>
                </div>
            </div>
            <div @click="modalSubirImage = true" title="Añadir una imagen"
                class="flex justify-center items-center w-[80px] h-[158px] border-gray-700 dark:border-gray-300  border opacity-80 hover:opacity-100! bg-gray-500 cursor-pointer shrink-0">
                <Icon icon="ic:outline-add-photo-alternate" class="text-4xl" />
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    from: String, // campo input de texto con formato html or markdown de donde extrae las imagenes
    name: String, // name of the field
    folder: String, // carpeta de destino
    value: String,
    canDelete: { type: Boolean, default: false }, // se puede borrar una imagen (requiere permisos de escritura en carpeta folder)
    listImages: { type: Boolean, default: false }, // carga todas las imagenes de la carpeta folder
    initialImages: { type: [Array, String], default: null, required: false }
})

const emit = defineEmits('selected')

const images = ref([])
if (props.value)
    images.value = [props.value]
const imagesUploaded = ref([])
const imagesFrom = ref([])
const selected = ref(props.value)
// const imagesUrl = computed(()=>images.value.map(src=>src))

// Route::delete('files{ruta}', [ArchivosController::class, 'delete'])->where(['ruta' => '(\/.+)?'])->name('files.delete');
// watch(() => props.value, (value) => selected.value = value)

var fromValue = null
const initialImages = computed(() => {
    if (typeof props.initialImages == 'string')
        return props.initialImages.split(',')
    return props.initialImages
})

function updateImages() {
    // console.log('updateImages')
    images.value.splice(0, images.value.length);

    if (inputField && inputField.value != fromValue) {
        fromValue = inputField.value;
        console.log('texto from cambió!');

        // Expresión regular mejorada para manejar múltiples paréntesis
        const regex = /!\[.*?\]\(((?:[^()]+|\([^()]*\))+)\)|<img[^>]+src=["']?([^"']+)["']?/g;

        imagesFrom.value = [];

        let match;
        while ((match = regex.exec(fromValue)) !== null) {
            let url = match[1] || match[2];
            // Limpiamos la URL de posibles espacios al final
            url = url.trim();
            imagesFrom.value.push(url);
        }
    }

    for (const url of imagesUploaded.value)
        images.value.push(url)

    if (props.value)
        images.value.push(props.value)

    for (const url of imagesFrom.value)
        images.value.push(url)

    if (selected.value)
        images.value.push(selected.value)

    if (initialImages.value)
        initialImages.value.forEach(img => images.value.push(img))

    // elimina repetidos
    images.value = [...new Set(images.value)];

    // eliminamos imagenes en blanco
    images.value = images.value.filter(img=>img)

    if (!selected.value && images.value.length)
        selected.value = images.value[0]
}

function loadAllImages() {
    axios.get('/admin/list-images' + props.folder)
        .then(response => {
            images.value = response.data
            // incluimos, si acaso no está, la imagen seleccionada
            if (!images.value.includes(props.value))
                images.value.unshift(props.value)

        })
}

function borrarImagen(url) {
    const idx = images.value.indexOf(url)
    if (idx == -1) return false
    axios.delete('/files' + url)
        .then(response => {
            console.log('delete', { response })
            images.value.splice(idx, 1)
            if (url == selected.value)
                selected.value = images.value.length ? images.value[0] : null
        })
}

var inputField = null
var interval = null
onMounted(() => {
    inputField = document.querySelector("[name='" + props.from + "']")

    if (props.from) {
        inputField = document.querySelector("[name='" + props.from + "']")
        if (inputField)
            interval = setInterval(updateImages, 1000)
    }

    updateImages()

    if (props.listImages)
        loadAllImages()
})

onUnmounted(() => {
    clearInterval(interval)
})

// SUBIR IMAGEN

const modalSubirImage = ref(false)

function uploadedImage(url) {
    imagesUploaded.value.push(url)
    images.value.push(url)
    modalSubirImage.value = false
    selected.value = url
}

</script>
