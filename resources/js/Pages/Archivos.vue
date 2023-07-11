<template>
    <div class="h-full flex flex-col">

        <div class="w-full sticky top-4 pt-16 border-b border-gray-300 shadow-sm bg-base-100  px-4 pb-0 z-10 sm:px-6 lg:px-8">
            <div class="lg:container mx-auto w-full flex flex-nowrap justify-between mb-4 lg:mb-7">
                <div :title="ruta" v-if="!seleccionando" class="flex items-center gap-3 text-2xl font-bold">
                    <Icon icon="ph:folder-notch-open-duotone" />
                    <Breadcrumb :path="items.length&&items[0].padre?items[0].ruta:ruta" />
                </div>

                <div class="flex gap-3 flex-nowrap" :class="seleccionando ? 'w-full' : ''">

                    <button v-if="seleccionando" class="btn btn-secondary flex gap-3 items-center"
                        @click="cancelarSeleccion">
                        <Icon icon="material-symbols:close-rounded" />
                        <span>{{ itemsSeleccionados.length }}</span>
                    </button>


                    <button v-if="seleccionando" class="btn btn-secondary ml-auto" @click="seleccionarTodos"
                    title="Seleccionar todos">
                        <Icon icon="ph:selection-all-duotone" class="transform scale-150" />
                    </button>

                    <Link v-if="items.length>1 && items[1].padre" :href="items[1].url" class="btn btn-secondary w-fit"
                        title="Ir a una carpeta superior">
                            <Icon icon="ph:skip-back-duotone" class="transform scale-125"/></Link>

                    <button class="btn btn-secondary" @click="toggleVista"
                    title="Cambiar vista">
                        <Icon v-show="vista == 'lista'" icon="ph:list-dashes-bold" class="transform scale-150" />
                        <Icon v-show="vista == 'grid'" icon="ph:grid-nine-fill" class="transform scale-150" />
                    </button>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="btn btn-secondary p-3">
                                <Icon icon="mdi:dots-vertical" class="text-xl" />
                            </button>
                        </template>

                        <template #content>
                            <div class="bg-gray-50">
                                <!-- Account Management -->
                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2   hover:bg-base-100 cursor-pointer"
                                    @click="modalSubirArchivos = true">
                                    <Icon icon="ph:upload-duotone" />
                                    <span>Subir archivos</span>
                                </div>

                                <div v-if="!seleccionando"
                                                class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                                @click="abrirModalRenombrar(items[0])">
                                                <Icon icon="ph:cursor-text-duotone" />
                                                <span>Renombrar</span>
                                            </div>

                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                    @click="abrirModalCrearCarpeta">
                                    <Icon icon="ph:folder-plus-duotone" />
                                    <span>Crear carpeta</span>
                                </div>


                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                    @click="seleccionando = true">
                                    <Icon icon="ph:check-duotone" />
                                    <span>Seleccionar</span>
                                </div>

                                <div v-else
                                    class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                    @click="cancelarSeleccion">
                                    <Icon icon="ph:x-square-duotone" />
                                    <span>Cancelar selección</span>
                                </div>

                            </div>

                        </template>
                    </Dropdown>
                </div>
            </div>


            <!-- Botones -->
            <div class="w-full flex mb-7 gap-4 select-none  overflow-x-auto scrollbar-hidden" :seleccionando="seleccionando"
                :class="seleccionando ? '' : 'justify-end'">

                <button v-if="store.isMovingFiles || store.isCopyingFiles" class="btn btn-primary flex gap-3 items-center"
                    @click="cancelarOperacion">
                    <Icon icon="material-symbols:close-rounded" />
                    <span>Cancelar</span>
                </button>

                <button v-if="store.isMovingFiles" class="btn btn-primary flex gap-3 items-center"
                    :disabled="store.sourcePath == ruta" @click="moverItems">
                    <Icon icon="ph:clipboard-duotone" />
                    <span>Mover aquí</span>
                </button>

                <button v-else-if="store.isCopyingFiles" class="btn btn-primary flex gap-3 items-center"
                    :disabled="store.sourcePath == ruta" @click="copiarItems">
                    <Icon icon="ph:clipboard-duotone" />
                    <span>Pegar aquí</span>
                </button>

                <template v-else>
                    <button v-if="itemsSeleccionados.length == 1" class="btn btn-primary flex gap-3 items-center"
                        @click="abrirModalRenombrar(itemsSeleccionados[0])">
                        <Icon icon="ph:cursor-text-duotone" />
                        <span>Renombrar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-primary flex gap-3 items-center"
                        @click="prepararMoverItems">
                        <Icon icon="ph:scissors-duotone" /><span>Mover</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-primary flex gap-3 items-center"
                        @click="prepararCopiarItems">
                        <Icon icon="ph:copy-simple-duotone" /><span>Copiar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-primary flex gap-3 items-center"
                        @click="eliminarItems">
                        <Icon icon="ph:trash-duotone" />
                        <span>Eliminar</span>
                    </button>

                    <select v-if="!seleccionando" v-model="ordenarPor" class="rounded">
                        <option value="fechaDesc">Recientes</option>
                        <option value="fechaAsc">Antiguos</option>
                        <option value="nombreAsc">A-Z</option>
                        <option value="nombreDesc">Z-A</option>
                        <option value="tamañoAsc">Pequeños</option>
                        <option value="tamañoDesc">Grandes</option>
                    </select>

                </template>
            </div>


        </div>



        <div :class="vista === 'lista' ? 'lista' : 'grid'"
            class="select-none flex-grow bg-base-100 py-4 px-2 sm:px-6 lg:px-8 pb-14">

            <div v-if="!itemsOrdenados.length" class="flex flex-col justify-center items-center gap-7 text-xl py-12 mb-14">


                <Icon icon="ph:warning-diamond-duotone" class="text-4xl"/>
            <div>
                No hay archivos</div>

                </div>
            <div v-else-if="vista === 'lista'" class="mr-2">
                <table class="w-full lg:w-auto mx-auto">
                    <thead class="hidden sm:table-header-group">
                        <tr>
                            <th v-if="seleccionando" class="hidden md:table-cell"></th>
                            <th></th>
                            <th class="text-left">Nombre</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th>Permisos</th>
                            <th>Propietario</th>
                            <th class="hidden md:table-cell"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in itemsOrdenados"
                            :class="item.clase + ' ' + (item.seleccionado ? 'bg-blue-100' : '')" :key="item.ruta"
                            v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">
                            <td v-if="seleccionando" @click.prevent="toggleItem(item)"
                                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                                <Icon v-else icon="ph:square" />
                            </td>
                            <td>
                                <FolderIcon v-if="item.tipo === 'carpeta'" :private="item.privada" :url="item.url" />
                                <FileIcon v-else :url="item.url" />
                            </td>
                            <td class="sm:hidden">
                                <div class="flex flex-col">
                                    <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)"/>
                                    <div v-else-if="seleccionando" :title="item.nombre" v-html="nombreItem(item)"/>
                                    <a v-else :href="item.url" download v-html="nombreItem(item)"/>
                                    <small class="w-full flex justify-between items-center">
                                        <span v-if="item.tipo === 'carpeta'">{{ item.archivos + item.subcarpetas }}
                                            {{plural('elemento', item.archivos + item.subcarpetas)}}</span>
                                        <FileSize v-else :size="item.tamano" />
                                        <TimeAgo :date="item.fecha_modificacion" />
                                    </small>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell">
                                <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)"/>
                                <span v-else-if="seleccionando" v-html="nombreItem(item)"/>
                                <a v-else :href="item.url" download v-html="nombreItem(item)"/>
                            </td>
                            <td class="hidden sm:table-cell">
                                <span v-if="item.tipo === 'carpeta'" class="text-sm">{{ item.archivos + item.subcarpetas }}
                                    {{plural('elemento',  item.archivos + item.subcarpetas ) }}</span>
                                <FileSize v-else :size="item.tamano" class="block text-right" />
                            </td>
                            <td class="hidden sm:table-cell">
                                <TimeAgo :date="item.fecha_modificacion" class="block text-center" />
                            </td>
                            <td>{{ item.permisos }}</td>
                            <td>{{ item.propietario ? (item.propietario.usuario + '/' + item.propietario.grupo) : '' }}</td>
                            <td class="hidden md:table-cell">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="btn p-3">
                                            <Icon icon="mdi:dots-vertical" class="text-xl" />
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="bg-gray-50">

                                            <div v-if="!seleccionando"
                                                class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                                @click="abrirModalRenombrar(item)">
                                                <Icon icon="ph:cursor-text-duotone" />
                                                <span>Renombrar</span>
                                            </div>

                                            <div v-if="!seleccionando && !item.padre"
                                                class="flex gap-3  items-center px-4 py-2   hover:bg-base-100 cursor-pointer"
                                                @click="abrirEliminarModal(item)">
                                                <Icon icon="ph:trash-duotone" />
                                                <span>Eliminar</span>
                                            </div>

                                            <div v-if="!buscandoCarpetaDestino && !item.padre"
                                                class="flex gap-3  items-center px-4 py-2   hover:bg-base-100 cursor-pointer"
                                                @click="seleccionando = true; item.seleccionado = !item.seleccionado">
                                                <template v-if="!item.seleccionado">
                                                    <Icon icon="ph:check-fat-duotone" />
                                                    <span>Seleccionar</span>
                                                </template>
                                                <template v-else>
                                                    <Icon icon="ph:square" />
                                                    <span>Deseleccionar</span>
                                                </template>
                                            </div>

                                            <div v-if="seleccionando"
                                                class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                @click="cancelarSeleccion">
                                                <Icon icon="ph:x-square-duotone" />
                                                <span>Cancelar selección</span>
                                            </div>


                                        </div>

                                    </template>
                                </Dropdown>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="vista === 'grid'">
                <div class="grid grid-cols-3 gap-4">
                    <div v-for="item in itemsOrdenados" :key="item.ruta"
                        :class="item.clase + ' ' + (item.seleccionado ? 'bg-blue-100' : '')">
                        <div v-if="seleccionando" @click.prevent="toggleItem(item)"
                            class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                            <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                            <Icon v-else icon="ph:square" />
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <FolderIcon v-if="item.tipo === 'carpeta'" :url="item.url" :private="item.privada"
                                class="text-8xl mb-4" />
                            <a v-else-if="isImage(item.nombre)" :href="item.url" class="text-8xl mb-4" download>
                                <img :src="item.url" class="overflow-hidden w-[180px] h-[120px]">
                            </a>
                            <FileIcon v-else :url="item.url" class="text-8xl mb-4" />

                            <div class="text-sm text-center">
                                <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)"/>
                                <span v-else-if="seleccionando" v-html="nombreItem(item)"/>
                                <a v-else :href="item.url" download v-html="nombreItem(item)"/>
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


                            <div class="w-full flex justify-end">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="btn p-1">
                                            <Icon icon="mdi:dots-horizontal" class="text-xl" />
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="bg-gray-50">

                                            <div v-if="!seleccionando"
                                                class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                                @click="abrirModalRenombrar(item)">
                                                <Icon icon="ph:cursor-text-duotone" />
                                                <span>Renombrar</span>
                                            </div>

                                            <div v-if="!seleccionando"
                                                class="flex gap-3  items-center px-4 py-2   hover:bg-base-100 cursor-pointer"
                                                @click="abrirEliminarModal(item)">
                                                <Icon icon="ph:trash-duotone" />
                                                <span>Eliminar</span>
                                            </div>

                                            <div v-if="buscandoCarpetaDestino"
                                                class="flex gap-3  items-center px-4 py-2   hover:bg-base-100 cursor-pointer"
                                                @click="seleccionando = true; item.seleccionado = !item.seleccionado">
                                                <template v-if="!item.seleccionado">
                                                    <Icon icon="ph:check-fat-duotone" />
                                                    <span>Seleccionar</span>
                                                </template>
                                                <template v-else>
                                                    <Icon icon="ph:square" />
                                                    <span>Deseleccionar</span>
                                                </template>
                                            </div>

                                            <div v-if="seleccionando"
                                                class="flex gap-3 items-center px-4 py-2  hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                @click="cancelarSeleccion">
                                                <Icon icon="ph:x-square-duotone" />
                                                <span>Cancelar selección</span>
                                            </div>


                                        </div>

                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Modal Upload -->
        <div v-show="modalSubirArchivos" class="fixed z-10 inset-0 overflow-y-auto" scroll-region>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-base-100 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-base-100 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                        <Dropzone id="dropzone" :options="dropzoneOptions" :useCustomSlot=true
                            v-on:vdropzone-sending="sendingEvent" v-on:vdropzone-success="successEvent">
                            <div class="flex flex-col items-center">
                                <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                                <span>Arrastra los archivos aquí o haz clic para subirlos</span>
                            </div>
                        </Dropzone>

                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="modalSubirArchivos = false" type="button" class="btn btn-secondary">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>




        <!-- Modal Crear Carpeta -->
        <Modal :show="modalCrearCarpeta" @close="modalCrearCarpeta = false" maxWidth="sm">

            <form class="p-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="crearCarpeta">

                <div class="flex flex-col gap-4">
                    <label for="nombreCarpeta">Nombre de la nueva carpeta:</label>
                    <input id="nombreCarpeta" v-model="nombreCarpeta" type="text" required>
                </div>

                <div class="py-3 sm:flex sm:justify-end gap-5">

                    <button @click.prevent="crearCarpeta" type="button" class="btn btn-primary">
                        Crear Carpeta
                    </button>

                    <button @click.prevent="modalCrearCarpeta = false" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                </div>
            </form>
        </Modal>


        <!-- Modal Renombrar Item -->
        <Modal :show="modalRenombrarItem" @close="modalRenombrarItem = false" maxWidth="sm">

            <form class="p-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="renombrarItem">
                <div class="flex flex-col gap-4">
                    <label for="nuevoNombre">Nombre de la nueva carpeta:</label>
                    <input id="nuevoNombre" v-model="nuevoNombre" type="text" required>
                </div>

                <div class="py-3 sm:flex sm:justify-end gap-5">
                    <button @click.prevent="modalRenombrarItem = false" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                    <button @click.prevent="renombrarItem" type="button" class="btn btn-primary">
                        Renombrar
                    </button>
                </div>
            </form>
        </Modal>




        <!-- Modal Confirmación de eliminar Archivo -->
        <ConfirmationModal :show="modalEliminarItem" @close="modalEliminarItem = false">
            <template #content>
                ¿Quieres eliminar {{ itemAEliminar.nombre }}?
            </template>
            <template #footer>
                <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @submit.prevent="crearCarpeta">

                    <button @click.prevent="modalEliminarItem = false" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>

                    <button @click.prevent="eliminarArchivo" type="button" class="btn btn-primary">
                        Eliminar
                    </button>
                </form>
            </template>
        </ConfirmationModal>


    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Dropzone from 'vue2-dropzone-vue3'
import { useFilesStore } from '@/Stores/files';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout })

const props = defineProps({
    ruta: {},
    items: {}
});


function nombreItem(item) {
    if(item.actual) return `<span class='text-neutral opacity-70'>&lt;${item.nombre}&gt;</span>`
    if(item.padre) return `<span class='text-neutral opacity-70'>&lt;atrás&gt;</span>`
    return item.nombre
}

function plural(label, count) {
    return label + (count>1?'s':'')
}

// SELECCION

const seleccionando = ref(false)

function cancelarSeleccion() {
    seleccionando.value = false
    props.items.forEach(item => item.seleccionado = false)
}

function seleccionarTodos() {
    props.items.forEach(item => item.seleccionado = true)
}

// verifica que cuando no hay ningun item seleccionado, se termina el modo de selección
function verificarFinSeleccion() {
    if (!seleccionando.value) return
    if (screen.width >= 1024) return
    const alguno = props.items.find(item => item.seleccionado)
    if (!alguno)
        seleccionando.value = false
}

// si hay alfun cambio en los items
watch(() => props.items, verificarFinSeleccion, { deep: true })

// EVENTOS TOUCH

function onTouchStart(item) {
    console.log('touchStart')
    item.touching = true
    if (seleccionando.value)
        item.seleccionado = !item.seleccionado
    else
        item.longTouchTimer = setTimeout(() => {
            item.seleccionado = true;
            seleccionando.value = true
        }, 700); // tiempo en milisegundos para considerar un "long touch"
}

function onTouchEnd(item) {
    console.log('touchEnd')
    clearTimeout(item.longTouchTimer);
    item.touching = false
}

function toggleItem(item) {
    console.log('toggleItem')
    if (!item.touching)
        item.seleccionado = !item.seleccionado
    item.touching = false
}

const itemsSeleccionados = computed(() => props.items.filter(item => item.seleccionado))


watch(() => itemsSeleccionados.value.length, (value) => {
    console.log('itemsSeleccionados.length=', value)
    if (!value)
        cancelarSeleccion()
})


// COPIAR Y MOVER ITEMS
const store = useFilesStore();

const buscandoCarpetaDestino = computed(() => store.isMovingFiles || store.isCopyingFiles)

function prepararMoverItems() {
    seleccionando.value = false
    store.isMovingFiles = true
    store.sourcePath = props.ruta
    store.filesToMove = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function prepararCopiarItems() {
    seleccionando.value = false
    store.isCopyingFiles = true
    store.sourcePath = props.ruta
    store.filesToCopy = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function copiarItems() {
    axios.post(route('files.copy'), {
        sourceFolder: store.sourcePath,
        targetFolder: props.ruta,
        items: store.filesToCopy
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
    cancelarOperacion()
}

function moverItems() {
    axios.post(route('files.move'), {
        sourceFolder: store.sourcePath,
        targetFolder: props.ruta,
        items: store.filesToMove
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
    cancelarOperacion()
}

function cancelarOperacion() {
    store.isMovingFiles = false
    store.isCopyingFiles = false
    store.filesToMove = []
    store.filesToCopy = []
    props.items.forEach(item => { item.seleccionado = false })
}


// SUBIR ARCHIVOS
const modalSubirArchivos = ref(false)

const modalCrearCarpeta = ref(false)


const dropzoneOptions = ref({
    url: route('files.upload.file'),
    thumbnailWidth: 150,
    maxFilesize: 50
})

function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', props.ruta);
}


var someUploaded = ref(false)
function successEvent(file, response) {
    someUploaded.value = true
    //console.log('successEvent', props.ruta)
    //reloadPage()
}

watch(modalSubirArchivos, (value) => {
    if (value)
        someUploaded.value = false
    else if (someUploaded.value) {
        // recargamos la vista
        reloadPage()
    }
})


// ORDENACION

const ordenarPor = ref("fechaDesc")

const itemsOrdenados = computed(() => {
    // Separar las carpetas y los archivos en dos grupos
    const items = props.items.filter(item=>!item.padre&&!item.actual)
    const carpetas = items.filter(item => item.tipo === 'carpeta')
    const archivos = items.filter(item => item.tipo !== 'carpeta');

    switch (ordenarPor.value) {
        case 'normal':
            // Ordenar las carpetas y los archivos por separado
            carpetas.sort((a, b) => {
                if (a.padre||a.actual) return -Infinity;
                if (b.padre||b.actual) return Infinity;
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
            return items.sort((a, b) => a.nombre.localeCompare(b.nombre));

        case 'nombreDesc':
            // Ordenar todos los elementos por nombre descendente
            return items.sort((a, b) => b.nombre.localeCompare(a.nombre));

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
            return itemsFiltrados;
    }
});


/* function crearCarpetaKeydown(event) {
    if (event.keyCode === 27) {
        modalCrearCarpeta.value = false;
        // La tecla ESC fue presionada
    }
} */


// RENOMBRAR ITEM
const nuevoNombre = ref("")
const itemRenombrando = ref(null)
const modalRenombrarItem = ref(false)

function abrirModalRenombrar(item) {
    item.seleccionado = false // para el caso de renombrar un item seleccionado
    itemRenombrando.value = item
    nuevoNombre.value = item.nombre
    modalRenombrarItem.value = true
    setTimeout(() => {
        if (modalRenombrarItem.value)
            document.querySelector('#nuevoNombre').focus()
    }, 500)
}

function renombrarItem() {
    modalRenombrarItem.value = false
    axios.post(route('files.rename'), {
        folder: itemRenombrando.value.carpeta,
        oldName: itemRenombrando.value.nombre,
        newName: nuevoNombre.value,
    })
        .then(response => {
            console.log({ response })
            const item = props.items.find(it => it.nombre == itemRenombrando.value.nombre)
            item.ruta = item.carpeta + '/' + nuevoNombre.value
            const parts = item.url.split('/')
            parts[parts.length - 1] = parts[parts.length - 1].replace(item.nombre, nuevoNombre.value)
            item.url = parts.join('/')
            item.nombre = nuevoNombre.value
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al renombrar el elemento'
            alert(errorMessage)
        })
}

// CREAR CARPETA

const nombreCarpeta = ref("")

function abrirModalCrearCarpeta() {
    modalCrearCarpeta.value = true
    nombreCarpeta.value = ""
    setTimeout(() => {
        if (modalCrearCarpeta.value)
            document.querySelector('#nombreCarpeta').focus()
    }, 500)
}

function crearCarpeta() {
    modalCrearCarpeta.value = false
    if (!nombreCarpeta.value) return

    axios.put(route('files.mkdir'), {
        folder: props.ruta, name: nombreCarpeta.value
    }).then((response) => {
        console.log({ response })
        reloadPage()
    })
}


// ELIMINAR

const itemAEliminar = ref("")
const modalEliminarItem = ref(false)

function abrirEliminarModal(item) {
    itemAEliminar.value = item
    modalEliminarItem.value = true
}

function eliminarArchivo() {
    const url = ('/api/files/' + props.ruta + "/" + itemAEliminar.value.nombre).replace(/\/\//g, '/')
    modalEliminarItem.value = false
    axios.delete(url)
        .then(response => {
            // reloadPage()
            const idx = props.items.findIndex(item => item.nombre == itemAEliminar.value.nombre)
            if (idx != -1)
                props.items.splice(idx, 1)
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al eliminar el archivo'
            alert(errorMessage)
        })
}


// TIPO DE ITEM: ES IMAGEN?
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



// VISTA DE ITEMS
const vista = ref('lista');

const toggleVista = () => {
    vista.value = vista.value === 'lista' ? 'grid' : 'lista';
}


function reloadPage() {
    router.reload({
        only: ['items']
    })
}
</script>

<style scoped>
table td,
table th {
    @apply px-2;
}

.lista {
    display: block;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

@media (min-width:768px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
}

@media (min-width:1024px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
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
