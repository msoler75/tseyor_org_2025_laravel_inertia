<template>
    <a :href="href" class="flex gap-3 items-baseline" download :class="color">
        <Icon :icon="iconType" />
    </a>
</template>

<script setup>
const props = defineProps({
    url: String
})

const href = computed(() => props.url && !props.url.startsWith('/') ? '/' + props.url : props.url)

const fileName = computed(() => props.url.substring(props.url.lastIndexOf('/') + 1))

const iconType = computed(() => {
    const ext = fileName.value.split('.').pop().toLowerCase();
    switch (ext) {
        case 'mp3': return 'teenyicons:mp3-outline';
        case 'wav': return 'uil:file-audio-wave';
        case 'ogg': return 'carbon:document-audio';
        case 'flac': return 'uil:headphones-alt';
        case 'wma': return 'iconoir:file-audio';
        case 'aiff': return 'ant-design:file-audio-outlined';
        case 'm4a': return 'ant-design:file-audio-filled';
        case 'aac': return 'uil:file-audio';
        case 'amr': return 'clarity:file-audio';
        case 'mka': return 'codicon:wave';
        // Más extensiones de audio..
        case 'pdf': return 'fa6-solid:file-pdf';
        case 'zip': return 'fa6-solid:file-zipper';
        case 'rar': return 'mdi:file-powerpoint';
        case '7z': return 'mdi:file-zip-outline';
        // habituales:
        case 'mp3': return 'teenyicons:mp3-outline';
        case 'mp4': return 'la:file-video';
        case 'svg':
        case 'webp':
        case 'jpg':
        case 'jpeg':
        case 'png': return 'streamline:image-picture-landscape-1-photos-photo-landscape-picture-photography-camera-pictures';
        case 'zip': return 'ant-design:file-zip-outlined';
        case 'doc': return 'ant-design:file-word-outlined';
        case 'docx': return 'ant-design:file-word-outlined';
        case 'txt': return 'carbon:document-text-outline';
        case 'xls': return 'ant-design:file-excel-outlined';
        case 'xlsx': return 'ant-design:file-excel-outlined';
        case 'ppt': return 'ant-design:file-powerpoint-outlined';
        case 'pptx': return 'ant-design:file-powerpoint-outlined';
        case 'sql': return 'ant-design:file-sql-outlined';
        case 'js': return 'ant-design:file-js-outlined';
        case 'ts': return 'uil:file-typescript-alt';
        case 'py': return 'uil:file-python';
        // Más extensiones de archivo...
        default: return 'fa:file'; // Ícono predeterminado
    }
})

const color = computed(() => {
    const ext = fileName.value.split('.').pop().toLowerCase();
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
            return 'text-blue-800';

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
            return 'text-green-800';

        // documentos de texto:
        case 'doc':
        case 'docx':
        case 'odt':
        case 'pdf':
        case 'rtf':
        case 'txt':
        case 'wpd':
            return 'text-red-800';

        // hojas de cálculo:
        case 'ods':
        case 'xls':
        case 'xlsx':
            return 'text-indigo-800';

        // presentaciones:
        case 'key':
        case 'odp':
        case 'ppt':
        case 'pptx':
            return 'text-yellow-800';

        // archivos comprimidos:
        case '7z':
        case 'bz2':
        case 'gz':
        case 'rar':
        case 'tar':
        case 'xz':
        case 'zip':
            return 'text-purple-800';

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
            return 'text-pink-800';

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
