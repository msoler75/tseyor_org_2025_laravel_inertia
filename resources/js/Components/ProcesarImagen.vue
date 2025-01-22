<template>
    <div class="w-full">
        <h1 class="text-center">{{ titulo || "Procesar imagen" }}</h1>
        <p class="w-full flex justify-center items-baseline gap-4">
            <label>Opcional:</label>
            <a class="btn btn-primary" :href="src" download>1. Descargar</a>
            <span class="btn btn-primary" :href="src" @click="openExternal"
                >2. Abrir {{ nombreAplicacion || "Aplicación externa" }}</span
            >
        </p>

        <div class="mt-6 flex justify-center flex-wrap gap-4 mx-auto">
            <div>
                <h2 class="text-xl pt-2">Imagen original:</h2>
                <img style="width: 400px" :src="src" />
            </div>
            <div class="flex flex-col justify-end">
                <h2>3. Subir nueva imagen</h2>

                <Dropzone
                    ref="myDropzone"
                    class="w-[300px] mb-auto !hover:bg-orange-200 dark:hover:bg-orange-900 hover:border-orange-500 cursor-pointer"
                    :class="classDropzone"
                    id="dropzone"
                    :options="dropzoneOptions"
                    :useCustomSlot="true"
                    v-on:vdropzone-file-added="fileAdded"
                    v-on:vdropzone-sending="sendingEvent"
                    v-on:vdropzone-success="fileUploaded"
                >
                    <div class="flex flex-col items-center">
                        <Icon
                            icon="mdi:cloud-upload-outline"
                            class="text-5xl"
                        />
                        <span
                            >Arrastra y suelta la imagen aquí o haz clic para
                            seleccionar</span
                        >
                    </div>
                </Dropzone>

                <div class="mt-4 flex justify-end">
                    <button
                        class="btn uppercase btn-error text-xl"
                        @click="closeModal"
                    >
                        <Icon icon="material-symbols:cancel-rounded" /> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Dropzone from "vue2-dropzone-vue3";

const props = defineProps({
    titulo: String,
    src: String,
    nombreAplicacion: String,
    urlAplicacion: String,
    carpetaDestino: String,
    classDropzone: String,
});

const emit = defineEmits(["cerrar", "procesada"]);

const myDropzone = ref(null);

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const dropzoneOptions = {
    url: "/files/upload/image", // Ruta de tu servidor para subir la imagen
    thumbnailWidth: 300,
    maxFilesize: 2, // En MB
    multiple: false,
    headers: { "X-CSRF-TOKEN": token },
    dictDefaultMessage:
        "Arrastra y suelta la imagen aquí o haz clic para seleccionar",
    autoProcessQueue: false, // Desactiva el procesamiento automático
    acceptedFiles: "image/*", // Asegura que solo se acepten imágenes
};


const fileAdded = async (file) => {
    if (file.type !== "image/webp") {
        try {
            const webpBlob = await convertToWebP(file);
            // Reemplaza el archivo original con el convertido
            myDropzone.value.removeFile(file);
            myDropzone.value.addFile(
                new File([webpBlob], file.name.replace(/\.[^/.]+$/, ".webp"), {
                    type: "image/webp",
                })
            );
        } catch (error) {
            myDropzone.value.removeFile(file);
            console.error("Error al convertir a WebP:", error);
        }
    }

    // Procesa la cola después de que el archivo se haya añadido (y posiblemente convertido)
    myDropzone.value.processQueue();
};


const fileUploaded = (file, response) => {
    console.log("Archivo subido:", response);
    // Aquí puedes manejar la respuesta del servidor después de subir la imagen
    emit("procesada", response?.data?.filePath.replace(/\(/g, "%28").replace(/\)/g, "%29")); // reemplazamos caracteres de paréntesis
};

const openExternal = () => {
    window.open(props.urlAplicacion, "_blank");
};

const sendingEvent = (file, xhr, formData) => {
    // Reemplaza el archivo original con el convertido si es necesario
    if (file._originalFile) {
        formData.delete("file");
        formData.append("file", file, file.name);
    }
    formData.append("destinationPath", props.carpetaDestino || "");
};
// convierte la imagen a Webp
function convertToWebP(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (event) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement("canvas");
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0);
                canvas.toBlob((blob) => {
                    resolve(
                        new File(
                            [blob],
                            file.name.replace(/\.[^/.]+$/, ".webp"),
                            {
                                type: "image/webp",
                            }
                        )
                    );
                }, "image/webp");
            };
            img.onerror = reject;
            img.src = event.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

const closeModal = () => {
    emit("cerrar");
};
</script>

<style>
body {
    padding: 20px;
}
</style>
