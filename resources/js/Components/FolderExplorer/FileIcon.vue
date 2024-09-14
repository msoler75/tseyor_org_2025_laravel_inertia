<template>
    <a v-if="link||!link" :href="href" class="flex gap-3 items-baseline" download :class="color">
        <Icon :icon="iconType" />
    </a>
    <Icon v-else :icon="iconType"  :class="color" />
</template>

<script setup>
import { Icon } from "@iconify/vue";

const props = defineProps({
    url: String,
    link: {type: Boolean, default: true}
})

const href = computed(() => props.url && !props.url?.startsWith('/') ? '/' + props.url : props.url)

const fileName = computed(() => props.url?.substring(props.url.lastIndexOf('/') + 1))

const iconType = computed(() => {
    const ext = fileName.value?.split('.').pop().toLowerCase();
    switch (ext) {
    // Audio
    case 'mp3':
    case 'wav':
    case 'ogg':
    case 'flac':
    case 'wma':
    case 'aiff':
    case 'm4a':
    case 'aac':
    case 'amr':
    case 'mka':
        return 'ph:music-notes-fill';

    // Vídeos
    case 'mp4':
    case 'mkv':
    case 'avi':
    case 'mov':
    case 'wmv':
        return 'file-icons:video';

    // PDF
    case 'pdf':
        return 'file-icons:adobe-acrobat';

    // Archivos comprimidos
    case 'zip':
    case 'rar':
    case '7z':
        return 'ci:file-archive'

    // Documentos de Microsoft Office
    case 'doc':
    case 'docx':
        return 'file-icons:microsoft-word';

    case 'xls':
    case 'xlsx':
        return 'file-icons:microsoft-excel';

    case 'ppt':
    case 'pptx':
        return 'file-icons:microsoft-powerpoint';

    // Otros documentos
    case 'txt':
        return 'ph:file-txt-duotone';

    case 'sql':
        return 'file-icons:database';

    case 'js':
        return 'teenyicons:javascript-outline';

    case 'ts':
        return 'file-icons:typescript';

    case 'py':
        return 'file-icons:python';

    // Imágenes
    case 'svg':
    case 'webp':
    case 'jpg':
    case 'jpeg':
    case 'png':
        return 'bi:image-fill';

    // Otros tipos de archivo
    default:
        return 'file-icons:bloc';
}

})

const color = computed(() => {
    const ext = fileName.value?.split('.').pop().toLowerCase();
    switch (ext) {
        // audio:
        case '3ga':
        case 'aac':
        case 'aiff':
        case 'amr':
        case 'au':
        case 'flac':
        case 'm4a':
        case 'mka':
        case 'mp3':
        case 'oga':
        case 'ogg':
        case 'opus':
        case 'ra':
        case 'wav':
        case 'wma':
            return 'text-purple-800 dark:text-purple-400';

        // imágenes:
        case 'bmp':
        case 'gif':
        case 'heic':
        case 'ico':
        case 'jpeg':
        case 'jpg':
        case 'png':
        case 'svg':
        case 'tif':
        case 'tiff':
        case 'webp':
            return 'text-green-800 dark:text-green-400';

        // documentos de texto:
        case 'odt':
        case 'pdf':
        case 'rtf':
        case 'txt':
        case 'wpd':
            return 'text-red-800 dark:text-red-400';

        // word:
        case 'doc':
        case 'docx':
        return 'text-blue-800 dark:text-blue-400';

        // hojas de cálculo:
        case 'ods':
        case 'xls':
        case 'xlsx':
            return 'text-indigo-800 dark:text-indigo-400';

        // presentaciones:
        case 'key':
        case 'odp':
        case 'ppt':
        case 'pptx':
            return 'text-yellow-800 dark:text-yellow-400';

        // archivos comprimidos:
        case '7z':
        case 'bz2':
        case 'gz':
        case 'rar':
        case 'tar':
        case 'xz':
        case 'zip':
            return 'text-purple-800 dark:text-purple-400';

        // vídeos:
        case 'avi':
        case 'flv':
        case 'mkv':
        case 'mov':
        case 'mp4':
        case 'mpeg':
        case 'mpg':
        case 'webm':
        case 'wmv':
            return 'text-pink-800 dark:text-pink-400';

        // otros:
        case 'csv':
        case 'dbf':
        case 'json':
        case 'js':
        case 'log':
        case 'py':
        case 'sql':
        case 'ts':
        case 'xml':
        default:
            return ''; // Ícono predeterminado
    }
})
</script>
