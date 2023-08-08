<template>
    <!-- Modal Upload -->
    <div class="inline">
        <Modal :show="localValue" @close="localValue = false">

            <div class="p-5 flex flex-col gap-5 items-center">

                <!--


                    <div v-bind="getRootProps()">
                    <input v-bind="getInputProps()" />

                    <div class="flex flex-col items-center">
                        <div v-if="isDragActive">Arrastra aquí ...</div>
                        <div class="flex flex-col gap-2">
                            <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                            <span>{{ placeholder }}</span>
                        </div>
                    </div>

                    <p v-if="isDragActive">Drop the files here ...</p>
                    <p v-else>Drag 'n' drop some files here, or click to select files</p>
                </div>
                <button @click="open">open</button>
            -->



                <Dropzone class="w-full !hover:bg-orange-200 dark:hover:bg-orange-900 hover:border-orange-500 cursor-pointer" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true
                    v-on:vdropzone-sending="sendingEvent" v-on:vdropzone-success="successEvent">
                    <div class="flex flex-col items-center">
                        <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                        <span>{{ placeholder }}</span>
                    </div>
                </Dropzone>


                <button @click.prevent="localValue = false" type="button" class="btn btn-neutral">
                    Cerrar
                </button>
            </div>

        </Modal>
    </div>
</template>


<script setup>

import Dropzone from 'vue2-dropzone-vue3'
// import { useDropzone } from "vue3-dropzone";

const props = defineProps({
    url: String, // server api url
    modelValue: { type: Boolean },
    placeholder: {
        type: String,
        default: 'Arrastra el archivo aquí o haz clic'
    },
    options: Object // dropzone.js options:  https://docs.dropzone.dev/configuration/basics/configuration-options
})

const emit = defineEmits(['update:modelValue', 'uploaded']);

const localValue = ref(props.modelValue);

watch(localValue, (value) => {
    emit('update:modelValue', value);
});

watch(() => props.modelValue, (value) => {
    localValue.value = value
})

const page = usePage()


/// DROPZONE NEW

/*
const saveFiles = (files) => {
    const formData = new FormData(); // pass data as a form
    for (var x = 0; x < files.length; x++) {
        // append files as array to the form, feel free to change the array name
        formData.append("files[]", files[x]);
    }

    // post the formData to your backend where storage is processed. In the backend, you will need to loop through the array and save each file through the loop.

    axios
        .post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                'X-CSRF-Token': page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then((response) => {
            console.info(response.data);
            if (response.data.filePath) {
                emit('uploaded', response.data.filePath)
            }
        })
        .catch((err) => {
            console.error(err);
        });
};

function onDrop(acceptFiles, rejectReasons) {
    if(acceptFiles&&acceptFiles.length)
        saveFiles(acceptFiles); // saveFiles as callback
    if(rejectReasons)
        alert(rejectReasons)
}

const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop });

*/





/// DROP ZONE OLD

// https://docs.dropzone.dev/configuration/basics/configuration-options
const dropzoneOptions = ref({
    url: props.url,
    thumbnailWidth: 150,
    maxFilesize: 50,
    multiple: false,
    headers: {
        'X-CSRF-Token': page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content,
    },
    ...props.options
})

function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', props.mediaFolder);
}

function successEvent(file, response) {
    if (response.data.filePath) {
        emit('uploaded', response.data.filePath)
    }
}

</script>

