<template>
    <div>
        <input type="hidden" :name="name" :value="imagenesJson" />
        <input type="hidden" :name="coverName" :value="coverValue" />

        <ModalDropZone v-model="modalSubirImage" @uploaded="uploadedImage($event)"
            placeholder="Arrastra la imagen aquí o haz clic" url="/files/upload/image" :mediaFolder="folder" :options="{
                maxFiles: 10,
                acceptedFiles: 'image/*'
            }" />

        <div ref="sortableContainer" class="flex flex-wrap items-start gap-2 mt-2">
            <div v-for="(url, index) of imagenes" :key="url"
                class="relative border-4 shrink-0 cursor-default group"
                :class="index === 0 ? 'border-orange-500!' : 'border-transparent'"
                @click="index > 0 && hacerPortada(index)">
                <div class="drag-handle" title="Arrastrar para reordenar" style="cursor:grab">
                    <Image :src="url.startsWith('/') ? url + '?h=150' : url" style="height:150px; pointer-events:none" error-icon />
                </div>
                <div v-if="index === 0"
                    class="absolute top-1 left-1 bg-orange-600 text-white text-xs px-1 rounded font-bold">
                    MINIATURA
                </div>
                <div @click.stop="borrarImagen(index)" title="Borrar imagen"
                    class="absolute bottom-1 right-1 bg-red-600 text-white hidden group-hover:block opacity-50 hover:opacity-100! p-1 rounded-xs cursor-pointer">
                    <Icon icon="ph:trash" />
                </div>
            </div>
            <div @click="modalSubirImage = true" title="Añadir una imagen"
                class="flex justify-center items-center w-[80px] h-[158px] border-gray-700 dark:border-gray-300 border opacity-80 hover:opacity-100! bg-gray-500 cursor-pointer shrink-0">
                <Icon icon="ic:outline-add-photo-alternate" class="text-4xl" />
            </div>
        </div>

        <p class="text-xs text-gray-500 mt-1">
            ↕️ Arrastra las imágenes para reordenarlas. La primera (borde naranja) es la portada que se usa en las tarjetas.
        </p>
    </div>
</template>

<script setup>
import Sortable from 'sortablejs'
import Image from '@/Components/Image.vue'
import ModalDropZone from '@/Components/ModalDropZone.vue'

const props = defineProps({
    name: String,
    coverName: String,
    folder: String,
    value: { type: String, default: null },
    initialImages: { type: [Array, String], default: null },
})

const imagenes = ref([])
const modalSubirImage = ref(false)
const sortableContainer = ref(null)

if (props.value) {
    imagenes.value.push(props.value)
}

if (props.initialImages) {
    const arr = typeof props.initialImages === 'string'
        ? props.initialImages.split(',')
        : props.initialImages
    arr.forEach(img => {
        if (img && !imagenes.value.includes(img)) {
            imagenes.value.push(img)
        }
    })
}

const imagenesJson = computed(() => JSON.stringify(imagenes.value))
const coverValue = computed(() => imagenes.value.length > 0 ? imagenes.value[0] : '')

function uploadedImage(url) {
    imagenes.value.push(url)
    modalSubirImage.value = false
}

function borrarImagen(index) {
    imagenes.value.splice(index, 1)
}

function hacerPortada(index) {
    if (index === 0) return
    const item = imagenes.value.splice(index, 1)[0]
    imagenes.value.unshift(item)
}

let sortableInstance = null

onMounted(() => {
    if (sortableContainer.value && typeof Sortable !== 'undefined') {
        sortableInstance = Sortable.create(sortableContainer.value, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => {
                const reordered = [...imagenes.value]
                const [moved] = reordered.splice(evt.oldIndex, 1)
                reordered.splice(evt.newIndex, 0, moved)
                imagenes.value = reordered
            }
        })
    }
})

onUnmounted(() => {
    if (sortableInstance) {
        sortableInstance.destroy()
    }
})
</script>
