<template>
    <div>
        <input type="text" :name="name" v-model="selected">

        <ModalDropZone v-model="modalSubirImage" @uploaded="uploadedImage($event)"
            placeholder="Arrastra la imagen aquí o haz clic" url="/api/files/upload/image" :mediaFolder="folder" :options="{
                maxFiles: 1,
                acceptedFiles: 'image/*'
            }" />

        <div class="flex overflow-x-auto">
            <div v-for="url of images" :key="url" class="border-4 border-transparent flex-shrink-0 cursor-pointer"
                :title="url" :class="url == selected ? '!border-orange-500' : ''" @click="selected = url">
                <img :src="url" style="height:150px" />
            </div>
            <div @click="modalSubirImage = true" title="Añadir una imagen"
                class="flex justify-center items-center w-[150px] h-[150px] border-gray-700 dark:border-gray-300  border opacity-80 hover:opacity-100 bg-gray-500 cursor-pointer flex-shrink-0">
                <Icon icon="ic:outline-add-photo-alternate" class="text-4xl" />
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    from: String,
    name: String,
    folder: String, // carpeta de destino
    value: String
})

const emit = defineEmits('selected')

const images = ref([])
const imagesUploaded = ref([])
const imagesFrom = ref([])
const selected = ref(props.value)

// watch(() => props.value, (value) => selected.value = value)

var fromValue = null

function updateImages() {
    // console.log('updateImages')
    images.value.splice(0, images.value.length)

    if (inputField && inputField.value != fromValue) {
        fromValue = inputField.value
        console.log('texto from cambió!')

        const regex = /!\[.*?\]\((.*?)\)|<img[^>]+src=["']?([^"']+)["']?/g;

        imagesFrom.value = []

        let match;
        while ((match = regex.exec(fromValue)) !== null) {
            imagesFrom.value.push(match[1] ? match[1] : match[2]);
        }
    }


    for (const url of imagesUploaded.value)
        images.value.push(url)

    if (props.value)
        images.value.push(props.value)

    if (selected.value)
        images.value.push(selected.value)


    for (const url of imagesFrom.value)
        images.value.push(url)

    // elimina repetidos
    images.value = [...new Set(images.value)];

    if (!selected.value && images.value.length)
        selected.value = images.value[0]
}


var inputField = null
var interval = null
onMounted(() => {
    inputField = document.querySelector("[name='" + props.from + "']")

    interval = setInterval(updateImages, 1000)

    updateImages()
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

