<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>{{ ruta }}</h1>
        <Dropzone id="dropzone" :options="dropzoneOptions" :useCustomSlot=true v-on:vdropzone-sending="sendingEvent"
            v-on:vdropzone-success="successEvent">
            <div class="flex flex-col items-center">
                <Icon icon="mdi:cloud-upload-outline" class="text-5xl icon" />
                <span>Arrastra los archivos aquí o haz clic para subirlos</span>
            </div>
        </Dropzone>
        <div class="flex justify-end mb-4 gap-4">

            <select v-model="ordenarPor">
                <option value="fechaDesc">Recientes</option>
                <option value="fechaAsc">Antiguos</option>
                <option value="nombreAsc">A-Z</option>
                <option value="nombreDesc">Z-A</option>
                <option value="tamañoAsc">Pequeños</option>
                <option value="tamañoDesc">Grandes</option>
            </select>

            <button
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-md active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue"
                @click="toggleVista">
                {{ vista !== 'lista' ? 'Miniaturas' : 'Lista' }}
            </button>
        </div>
        <div :class="vista === 'lista' ? 'lista' : 'grid'">
            <div v-if="vista === 'lista'">
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
                        <tr v-for="item in itemsOrdenados" :class="item.clase" :key="item.ruta">
                            <td>
                                <Icon :icon="item.tipo === 'carpeta' ? folderIcon : getIconFromFileName(item.nombre)"
                                    :class="item.tipo === 'carpeta' ? 'text-yellow-500 transform scale-150' : ''" />
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
            <div v-else-if="vista === 'grid'">
                <div class="grid grid-cols-3 gap-4">
                    <div v-for="item in itemsOrdenados" :class="item.clase" :key="item.ruta">
                        <div class="flex flex-col items-center justify-center">
                            <Link v-if="item.tipo === 'carpeta'" :href="item.ruta">
                            <img v-if="isImage(item.nombre)" :src="item.ruta" class="overflow-hidden w-[180px] h-[120px]">
                            <Icon v-else :icon="folderIcon" class="text-8xl mb-4 text-yellow-500 transform scale-150" />
                            </Link>
                            <a v-else :href="item.ruta" download>
                                <img v-if="isImage(item.nombre)" :src="item.ruta"
                                    class="overflow-hidden w-[180px] h-[120px]">
                                <Icon v-else :icon="getIconFromFileName(item.nombre)" class="text-8xl mb-4" />
                            </a>
                            <div class="text-sm text-center">
                                <Link v-if="item.tipo === 'carpeta'" :href="item.ruta">{{ item.nombre }}</Link>
                                <a v-else :href="item.ruta" download>{{ item.nombre }}</a>
                            </div>
                            <div class="text-gray-500 text-xs">
                                <template v-if="item.tipo === 'carpeta'">{{ item.archivos + ' archivos, ' +
                                    item.subcarpetas + ' subcarpetas' }}
                                </template>
                                <template v-else>
                                    <FileSize :size="item.tamano" /> -
                                    <TimeAgo :date="item.fecha_modificacion" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import TimeAgo from '@/Components/TimeAgo.vue';
import FileSize from '@/Components/FileSize.vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Icon } from '@iconify/vue';
// import Uploader from '@/Components/Uploader.vue'
import Dropzone from 'vue2-dropzone-vue3'
import Dropdown from '@/Components/Dropdown.vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    ruta: {},
    items: {}
});



const ordenarPor = ref("fechaDesc")

const itemsOrdenados = computed(() => {
  // Separar las carpetas y los archivos en dos grupos
  const carpetas = props.items.filter(item => item.tipo === 'carpeta');
  const archivos = props.items.filter(item => item.tipo !== 'carpeta');

  switch (ordenarPor.value) {
    case 'normal':
      // Ordenar las carpetas y los archivos por separado
      carpetas.sort((a, b) => {
        if (a.nombre === '..') return -Infinity;
        if (b.nombre === '..') return Infinity;
        return a.nombre.localeCompare(b.nombre);
      });
      archivos.sort((a, b) => a.fecha_modificacion - b.fecha_modificacion);

      // Combinar los grupos en el orden adecuado
      return [...carpetas, ...archivos];

    case 'fechaAsc':
      // Ordenar los archivos por fecha de modificación ascendente
      archivos.sort((a, b) => a.fecha_modificacion - b.fecha_modificacion);
      return [...carpetas, ...archivos];

    case 'fechaDesc':
      // Ordenar los archivos por fecha de modificación descendente
      archivos.sort((a, b) => b.fecha_modificacion - a.fecha_modificacion);
      return [...carpetas, ...archivos];

    case 'nombreAsc':
      // Ordenar todos los elementos por nombre ascendente
      return props.items.sort((a, b) => a.nombre.localeCompare(b.nombre));

    case 'nombreDesc':
      // Ordenar todos los elementos por nombre descendente
      return props.items.sort((a, b) => b.nombre.localeCompare(a.nombre));

    case 'tamañoAsc':
      // Ordenar los archivos por tamaño ascendente
      archivos.sort((a, b) => a.tamano - b.tamano);
      return [...archivos, ...carpetas];

    case 'tamañoDesc':
      // Ordenar los archivos por tamaño descendente
      archivos.sort((a, b) => b.tamano - a.tamano);
      return [...archivos, ...carpetas];

    default:
      // Si el criterio de ordenación no coincide, devolver el listado sin cambios
      return props.items;
  }
});





const dropzoneOptions = ref({
    url: route('files.upload.file'),
    thumbnailWidth: 150,
    maxFilesize: 50
})

function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', props.ruta);
}

var someUploaded = false
function successEvent(file, response) {
    someUploaded = true
    console.log('successEvent', props.ruta)
    router.get("/" + props.ruta)
}

const folderIcon = 'iconamoon:folder-duotone';

function isImage(fileName) {
    const ext = fileName.split('.').pop().toLowerCase();

    switch (ext) {
        case 'svg':
        case 'jpg':
        case 'jpeg':
        case 'webp':
        case 'png': return true;
    }
    return false
}




const getIconFromFileName = (fileName) => {
    const ext = fileName.split('.').pop().toLowerCase();

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
}

const vista = ref('lista');

const toggleVista = () => {
    vista.value = vista.value === 'lista' ? 'grid' : 'lista';
}

</script>

<style scoped>
table td {
    @apply px-2;
}

.lista {
    display: block;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}
</style>


<style>
.vue-dropzone {
    background: white;
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
