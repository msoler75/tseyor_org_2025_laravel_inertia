<template>
    <div>
        <input type="text" :name="name" v-model="selected">

        <ModalDropZone v-model="modalSubirImage" @uploaded="uploadedImage($event)"
            placeholder="Arrastra la imagen aquí o haz clic" url="/api/files/upload/image" :options="{
                maxFiles: 1,
                acceptedFiles: 'image/*'
            }" />

        <div class="flex overflow-x-auto">
            <div v-for="url of images" :key="url" class="border-4 border-transparent flex-shrink-0 cursor-pointer"
            :title="url"
                :class="url == selected ? '!border-orange-500' : ''" @click="selected = url">
                <img :src="url" style="height:150px" />
            </div>
            <div @click="modalSubirImage = true"
            title="Añadir una imagen"
                class="flex justify-center items-center w-[150px] border-gray-700 dark:border-gray-300  border opacity-80 hover:opacity-100 bg-gray-500 cursor-pointer flex-shrink-0">
                <Icon icon="ic:outline-add-photo-alternate" class="text-4xl" />
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    from: String,
    name: String,
    value: String
})

const emit = defineEmits('selected')

const images = ref([])
const imagesUploaded = ref([])
const selected = ref(props.value)

// watch(() => props.value, (value) => selected.value = value)

var fromValue = null

function updateImages() {
    if (inputField.value != fromValue) {
        fromValue = inputField.value

        const regex = /!\[.*?\]\((.*?)\)/g;
        images.value.splice(0, images.value.length)

        let match;
        while ((match = regex.exec(fromValue)) !== null) {
            images.value.push(match[1]);
        }

        if (selected.value)
            images.value.push(selected.value)

        for (const url of imagesUploaded.value)
            images.value.push(url)

        // elimina repetidos
        images.value = [...new Set(images.value)];

        if (images.value.length && !selected.value)
            selected.value = images.value[0]
    }
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

