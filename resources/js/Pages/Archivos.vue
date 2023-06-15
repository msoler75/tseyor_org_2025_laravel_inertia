<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>{{ ruta }}</h1>
        <div>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :class="item.clase" :key="item.ruta">
                        <td>
                            <Icon :icon="item.tipo === 'carpeta' ? folderIcon : getIconFromFileName(item.nombre)" />
                        </td>
                        <td>
                            <Link v-if="item.tipo === 'carpeta'" :href="item.ruta">{{ item.nombre }}</Link>
                            <a v-else :href="item.ruta" download>{{ item.nombre }}</a>
                        </td>
                        <td>

                            <span v-if="item.tipo === 'carpeta'" class="text-sm">{{ item.archivos }} archivos, {{
                                item.subcarpetas }}
                                subcarpetas</span>
                            <FileSize v-else :size="item.tamano" />
                        </td>
                        <td>
                            <TimeAgo :date="item.fecha_modificacion" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>



<script setup>
import TimeAgo from '@/Components/TimeAgo.vue';
import FileSize from '@/Components/FileSize.vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Icon } from '@iconify/vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    ruta: {},
    items: {}
});


const folderIcon='gg:folder'


function getIconFromFileName(fileName) {
    const ext = fileName.split('.').pop();

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
        case 'zip': return 'ant-design:file-zip-outlined';
        case 'rar': return 'mdi:file-powerpoint';
        case '7z': return 'mdi:file-zip-outline';
        // habituales:
        case 'mp3': return 'teenyicons:mp3-outline';
        case 'mp4': return 'la:file-video';
        case 'svg':
        case 'jpg':
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
}
</script>

<style scoped>
table td {
    @apply px-2;
}</style>
