<template>
    <!-- Modal Upload -->
    <Modal :show="modalSubirArchivos" @close="modalSubirArchivos = false">
        <div class="p-5 flex flex-col gap-5 items-center">
            <component
                :is="dropzoneComponent"
                v-if="dropzoneComponent"
                ref="dropzoneRef"
                class="w-full"
                id="dropzone"
                :options="dropzoneOptions"
                :useCustomSlot="true"
                v-on:vdropzone-sending="sendingEvent"
                v-on:vdropzone-success="successEvent"
            >
                <div class="flex flex-col items-center">
                    <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                    <span>Arrastra los archivos aquí o haz clic para subirlos</span>
                </div>
            </component>
            <button @click.prevent="modalSubirArchivos = false" type="button" class="btn btn-neutral btn-sm">
                Cerrar
            </button>
        </div>
    </Modal>
</template>

<script setup>
import { shallowRef, ref, watch, onMounted } from 'vue'
import useFolderExplorerStore from '@/Stores/folderExplorer';
import { usePage, Link } from '@inertiajs/vue3';

const dropzoneComponent = shallowRef(null)
const dropzoneRef = ref(null)
const store = useFolderExplorerStore()
const modalSubirArchivos = ref(false)
store.on('subirArchivos', ()=>modalSubirArchivos.value = true)
const page = usePage()
const someUploaded = ref(false)
const dropzoneOptions = ref({
    url: '/files/upload/file',
    thumbnailWidth: 150,
    maxFilesize: 50,
    headers: {
        'X-CSRF-Token': null
    }
})
function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', store.rutaActual);
}
function successEvent(file, response) {
    someUploaded.value = true
    // store.actualizar(response)
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

onMounted(async () => {
    dropzoneOptions.value.headers['X-CSRF-Token'] = page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content
    const module = await import('vue2-dropzone-vue3')
    dropzoneComponent.value = module.default
    window.addEventListener('archivos:upload', (e) => {
        setTimeout(() => {
            const dzComp = dropzoneRef.value;
            if (dzComp && dzComp.dropzone && typeof dzComp.dropzone.addFile === 'function') {
                for (const file of e.detail.files) {
                    dzComp.dropzone.addFile(file);
                }
            }
        }, 100);
    });
})
</script>

<style>
.vue-dropzone {
    border-radius: 5px;
    border: 2px dashed rgb(0, 135, 247);
    border-image: none;
    margin-left: auto;
    margin-right: auto;
}

div.vue-dropzone>.dz-preview .dz-success-mark,
div.vue-dropzone>.dz-preview .dz-error-mark {
    width: unset;
    left: calc(50% - 25px);
}
</style>


