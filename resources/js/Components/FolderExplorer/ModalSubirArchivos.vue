<template>

    <!-- Modal Upload -->
    <Modal :show="modalSubirArchivos" @close="modalSubirArchivos = false">

        <div class="p-5 flex flex-col gap-5 items-center">
            <Dropzone class="w-full" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true
                v-on:vdropzone-sending="sendingEvent" v-on:vdropzone-success="successEvent">
                <div class="flex flex-col items-center">
                    <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                    <span>Arrastra los archivos aquí o haz clic para subirlos</span>
                </div>
            </Dropzone>


            <button @click.prevent="modalSubirArchivos = false" type="button" class="btn btn-neutral btn-sm">
                Cerrar
            </button>
        </div>

    </Modal>
</template>



<script setup>
import Dropzone from 'vue2-dropzone-vue3'
import useFolderExplorerStore from '@/Stores/folderExplorer';
import { usePage, Link } from '@inertiajs/vue3';

const store = useFolderExplorerStore()

const modalSubirArchivos = ref(false)

store.on('subirArchivos', ()=>modalSubirArchivos.value = true)

const page = usePage()

const someUploaded = ref(false)

const dropzoneOptions = ref({
    url: '/files/upload/file', //route('files.upload.file'),
    thumbnailWidth: 150,
    maxFilesize: 50,
    headers: {
        'X-CSRF-Token': null
    },
})

function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', store.rutaActual);
}

// SUBIR ARCHIVOS

function successEvent(file, response) {
    someUploaded.value = true
    //console.log('successEvent', props.ruta)
    //store.actualizar()
}

watch(modalSubirArchivos, (value) => {
    if (value)
        someUploaded.value = false
    else if (someUploaded.value) {
        someUploaded.value = false
        // recargamos la vista
        store.actualizar()
    }
})


onMounted(() => {
    dropzoneOptions.value.headers['X-CSRF-Token'] = page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content
})

</script>

<style>

.vue-dropzone {
    @apply bg-base-100;
    border-radius: 5px;
    border: 2px dashed rgb(0, 135, 247);
    border-image: none;
    margin-left: auto;
    margin-right: auto;
}

.vue-dropzone>.dz-preview .dz-success-mark,
.vue-dropzone>.dz-preview .dz-error-mark {
    width: unset;
    left: calc(50% - 25px);
}
</style>
