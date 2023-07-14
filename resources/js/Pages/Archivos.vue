<template>
    <div class="h-full flex flex-col">
        <div
            class="w-full sticky top-4 pt-16 border-b border-gray-300 shadow-sm bg-base-200  px-4 pb-0 sm:px-6 lg:px-8 z-30">
            <div class="lg:container mx-auto w-full flex flex-nowrap justify-between mb-4 lg:mb-7">
                <div :title="rutaActual" v-if="!seleccionando" class="flex items-center gap-3 text-2xl font-bold">
                    <Icon icon="ph:folder-notch-open-duotone" />
                    <Breadcrumb :path="rutaActual" />
                </div>

                <div class="flex gap-3 flex-nowrap" :class="seleccionando ? 'w-full' : ''">

                    <button v-if="seleccionando" class="btn btn-neutral flex gap-3 items-center" @click="cancelarSeleccion"
                        title="Cancelar selección">
                        <Icon icon="material-symbols:close-rounded" />
                        <span>{{ itemsSeleccionados.length }}</span>
                    </button>


                    <button v-if="seleccionando" class="btn btn-neutral ml-auto" @click="seleccionarTodos"
                        title="Seleccionar todos">
                        <Icon icon="ph:selection-all-duotone" class="transform scale-150" />
                    </button>

                    <Link v-if="items.length > 1 && items[1].padre && !seleccionando" :href="items[1].url"
                        class="btn btn-neutral w-fit" title="Ir a una carpeta superior">
                    <Icon icon="ph:skip-back-duotone" class="transform scale-125" />
                    </Link>

                    <Link v-if="propietario && !seleccionando" class="btn btn-neutral" :href="propietario.url"
                        :title="tituloPropietario">
                    <Icon :icon="propietario.tipo == 'equipo' ? 'ph:users-four-duotone' : 'ph:user-duotone'"
                        class="transform scale-150" />
                    </Link>

                    <button class="btn btn-neutral" @click="toggleVista" title="Cambiar vista">
                        <Icon v-show="vista == 'lista'" icon="ph:list-dashes-bold" class="transform scale-150" />
                        <Icon v-show="vista == 'grid'" icon="ph:grid-nine-fill" class="transform scale-150" />
                    </button>

                    <Dropdown v-if="puedeEscribir && !seleccionando" align="right" width="48">
                        <template #trigger>
                            <button class="btn btn-neutral p-3">
                                <Icon icon="mdi:dots-vertical" class="text-xl" />
                            </button>
                        </template>

                        <template #content>
                            <div class="bg-base-100">
                                <!-- Account Management -->
                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2  hover:bg-base-200 cursor-pointer"
                                    @click="modalSubirArchivos = true">
                                    <Icon icon="ph:upload-duotone" />
                                    <span>Subir archivos</span>
                                </div>

                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer"
                                    @click="abrirModalRenombrar(items[0])">
                                    <Icon icon="ph:cursor-text-duotone" />
                                    <span>Renombrar</span>
                                </div>

                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer"
                                    @click="abrirModalCrearCarpeta">
                                    <Icon icon="ph:folder-plus-duotone" />
                                    <span>Crear carpeta</span>
                                </div>


                                <div v-if="!seleccionando"
                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer"
                                    @click="seleccionando = true">
                                    <Icon icon="ph:check-duotone" />
                                    <span>Abrir Selección</span>
                                </div>

                                <div v-else
                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer whitespace-nowrap"
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
            <div class="lg:container mx-auto w-full flex mb-7 gap-4 select-none overflow-x-auto scrollbar-hidden"
                :seleccionando="seleccionando"
                :class="seleccionando || store.isMovingFiles || store.isCopyingFiles ? 'justify-start sm:justify-center' : 'justify-end'">

                <button v-if="store.isMovingFiles || store.isCopyingFiles" class="btn btn-secondary flex gap-3 items-center"
                    @click="cancelarOperacion">
                    <Icon icon="material-symbols:close-rounded" />
                    <span>Cancelar</span>
                </button>

                <button v-if="store.isMovingFiles" class="btn btn-secondary flex gap-3 items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click="moverItems"
                    title="Mover los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Mover aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <button v-else-if="store.isCopyingFiles" class="btn btn-secondary flex gap-3 items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click="copiarItems"
                    title="Copiar los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Pegar aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <template v-else>




                    <button v-if="itemsSeleccionados.length" class="btn btn-secondary flex gap-3 items-center"
                        @click="prepararMoverItems">
                        <Icon icon="ph:scissors-duotone" /><span>Mover</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-secondary flex gap-3 items-center"
                        @click="prepararCopiarItems">
                        <Icon icon="ph:copy-simple-duotone" /><span>Copiar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-secondary flex gap-3 items-center"
                        @click="abrirEliminarModal(null)">
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

                    <button v-if="itemsSeleccionados.length == 1"
                        class="md:hidden btn btn-secondary flex gap-3 items-center"
                        @click="abrirModalRenombrar(itemsSeleccionados[0])">
                        <Icon icon="ph:cursor-text-duotone" />
                        <span>Renombrar</span>
                    </button>

                    <button v-if="seleccionando" class="btn btn-secondary">
                        <Icon icon="ph:info-duotone" />
                        <span>Propiedades</span>
                    </button>


                </template>



            </div>


        </div>



        <div :class="vista === 'lista' ? 'lista' : 'grid'"
            class="select-none flex-grow bg-base-200 py-4 px-2 sm:px-6 lg:px-8 pb-14">

            <div v-if="!itemsOrdenados.length" class="flex flex-col justify-center items-center gap-7 text-xl py-12 mb-14">
                <Icon icon="ph:warning-diamond-duotone" class="text-4xl" />
                <div>No hay archivos</div>
            </div>
            <div v-if="vista === 'lista'" :class="itemsOrdenados.length ? 'mr-2 min-h-[300px]' : ''">
                <table class="w-full lg:w-auto mx-auto">
                    <thead class="hidden sm:table-header-group" :class="itemsOrdenados.length ? '' : 'opacity-0'">
                        <tr>
                            <th v-if="seleccionando" class="hidden md:table-cell"></th>
                            <th></th>
                            <th class="text-left">Nombre</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th class="hidden sm:table-cell">Permisos</th>
                            <th class="hidden sm:table-cell">Propietario</th>
                            <th class="hidden md:table-cell"></th>
                        </tr>
                    </thead>
                    <TransitionGroup tag="tbody" name="files">
                        <tr v-for="item in itemsOrdenados"
                            :class="item.clase + ' ' + (item.seleccionado ? 'bg-base-300' : '')" :key="item.ruta">
                            <td v-if="seleccionando" @click.prevent="toggleItem(item)"
                                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                                <Icon v-else icon="ph:square" />
                            </td>
                            <td class="relative w-4" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">
                                <FolderIcon v-if="item.tipo === 'carpeta'" :private="item.privada" :url="item.url"
                                    :class="seleccionando ? 'pointer-events-none' : ''" />
                                <FileIcon v-else :url="item.url" :class="seleccionando ? 'pointer-events-none' : ''" />
                            </td>
                            <td class="sm:hidden" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">
                                <div class="flex flex-col">
                                    <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)"
                                        :class="seleccionando ? 'pointer-events-none' : ''" />
                                    <div v-else-if="seleccionando" :title="item.nombre" v-html="nombreItem(item)" />
                                    <a v-else :href="item.url" download v-html="nombreItem(item)"
                                        :class="seleccionando ? 'pointer-events-none' : ''" />
                                    <small class="w-full flex justify-between items-center">
                                        <span v-if="item.tipo === 'carpeta'">
                                            {{ plural(item.archivos + item.subcarpetas, 'elemento') }}</span>
                                        <FileSize v-else :size="item.tamano" />
                                        <TimeAgo :date="item.fecha_modificacion" />
                                    </small>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell" v-on:touchstart="onTouchStart(item)"
                                v-on:touchend="onTouchEnd(item)">
                                <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)"
                                    :class="seleccionando ? 'pointer-events-none' : ''" />
                                <span v-else-if="seleccionando" v-html="nombreItem(item)" />
                                <a v-else :href="item.url" download v-html="nombreItem(item)"
                                    :class="seleccionando ? 'pointer-events-none' : ''" />
                            </td>
                            <td class="hidden sm:table-cell py-3 text-center" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">
                                <span v-if="item.tipo === 'carpeta'" class="text-sm">
                                    {{ plural(item.archivos + item.subcarpetas, 'elemento') }}</span>
                                <FileSize v-else :size="item.tamano" class="block text-right" />
                            </td>
                            <td class="hidden sm:table-cell" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">
                                <TimeAgo :date="item.fecha_modificacion" class="block text-center" />
                            </td>
                            <td class="hidden sm:table-cell text-center" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">{{ item.permisos }}</td>
                            <td class="hidden sm:table-cell text-center" v-on:touchstart="onTouchStart(item)" v-on:touchend="onTouchEnd(item)">{{ item.propietario ? (item.propietario.usuario +
                                '/' + item.propietario.grupo) : '' }}</td>
                            <td class="hidden md:table-cell">
                                <Dropdown align="right" width="48" v-if="puedeEscribir">
                                    <template #trigger>
                                        <button class="p-3">
                                            <Icon icon="mdi:dots-vertical" class="text-xl" />
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="bg-base-100">

                                            <div v-if="!seleccionando"
                                                class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer"
                                                @click="abrirModalRenombrar(item)">
                                                <Icon icon="ph:cursor-text-duotone" />
                                                <span>Renombrar</span>
                                            </div>

                                            <div v-if="!seleccionando && !item.padre"
                                                class="flex gap-3  items-center px-4 py-2  hover:bg-base-200 cursor-pointer"
                                                @click="abrirEliminarModal(item)">
                                                <Icon icon="ph:trash-duotone" />
                                                <span>Eliminar</span>
                                            </div>

                                            <div v-if="!buscandoCarpetaDestino && !item.padre"
                                                class="flex gap-3  items-center px-4 py-2  hover:bg-base-200 cursor-pointer"
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
                                                class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer whitespace-nowrap"
                                                @click="cancelarSeleccion">
                                                <Icon icon="ph:x-square-duotone" />
                                                <span>Cancelar selección</span>
                                            </div>


                                        </div>

                                    </template>
                                </Dropdown>
                            </td>
                        </tr>
                    </TransitionGroup>
                </table>
            </div>
            <div v-else-if="vista === 'grid'">
                <div class="grid grid-cols-3 gap-4">
                    <TransitionGroup name="files">
                        <div v-for="item in itemsOrdenados" :key="item.ruta"
                            :class="item.clase + ' ' + (item.seleccionado ? 'bg-base-300' : '')">
                            <div v-if="seleccionando" @click.prevent="toggleItem(item)"
                                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                                <Icon v-else icon="ph:square" />
                            </div>
                            <div class="flex flex-col items-center justify-center relative">
                                <FolderIcon v-if="item.tipo === 'carpeta'" :url="item.url" :private="item.privada"
                                    class="text-8xl mb-4" :disabled="seleccionando" />
                                <a v-else-if="isImage(item.nombre)" :href="item.url" class="text-8xl mb-4" download>
                                    <img :src="item.url" class="overflow-hidden w-[180px] h-[120px]">
                                </a>
                                <FileIcon v-else :url="item.url" class="text-8xl mb-4" />

                                <div class="text-sm text-center">
                                    <Link v-if="item.tipo === 'carpeta'" :href="item.url" v-html="nombreItem(item)" />
                                    <span v-else-if="seleccionando" v-html="nombreItem(item)" />
                                    <a v-else :href="item.url" download v-html="nombreItem(item)" />
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
                                    <Dropdown align="right" width="48" v-if="puedeEscribir">
                                        <template #trigger>
                                            <button class="btn p-1">
                                                <Icon icon="mdi:dots-horizontal" class="text-xl z-20" />
                                            </button>
                                        </template>

                                        <template #content>
                                            <div class="bg-base-100">

                                                <div v-if="!seleccionando"
                                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer"
                                                    @click="abrirModalRenombrar(item)">
                                                    <Icon icon="ph:cursor-text-duotone" />
                                                    <span>Renombrar</span>
                                                </div>

                                                <div v-if="!seleccionando && !item.padre"
                                                    class="flex gap-3  items-center px-4 py-2  hover:bg-base-200 cursor-pointer"
                                                    @click="abrirEliminarModal(item)">
                                                    <Icon icon="ph:trash-duotone" />
                                                    <span>Eliminar</span>
                                                </div>

                                                <div v-if="!buscandoCarpetaDestino && !item.padre"
                                                    class="flex gap-3  items-center px-4 py-2  hover:bg-base-200 cursor-pointer"
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
                                                    class="flex gap-3 items-center px-4 py-2 hover:bg-base-200 cursor-pointer whitespace-nowrap"
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
                    </TransitionGroup>
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
                        <button @click="modalSubirArchivos = false" type="button" class="btn btn-neutral">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>




        <!-- Modal Crear Carpeta -->
        <Modal :show="modalCrearCarpeta" @close="modalCrearCarpeta = false" maxWidth="sm">

            <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="crearCarpeta">

                <div class="flex flex-col gap-4">
                    <label for="nombreCarpeta">Nombre de la nueva carpeta:</label>
                    <input id="nombreCarpeta" v-model="nombreCarpeta" type="text" required>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button @click.prevent="crearCarpeta" type="button" class="btn btn-primary">
                        Crear Carpeta
                    </button>

                    <button @click.prevent="modalCrearCarpeta = false" type="button" class="btn btn-neutral">
                        Cancelar
                    </button>
                </div>
            </form>
        </Modal>


        <!-- Modal Renombrar Item -->
        <Modal :show="modalRenombrarItem" @close="modalRenombrarItem = false">

            <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="renombrarItem">
                <div class="flex flex-col gap-4">
                    <label for="nuevoNombre">Nuevo nombre:</label>
                    <div class="flex items-center gap-1">
                        {{ itemRenombrando.carpeta }}/ <input id="nuevoNombre" v-model="nuevoNombre" type="text" required>
                    </div>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button @click.prevent="modalRenombrarItem = false" type="button" class="btn btn-neutral">
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
            <template #title>
                <div v-if="itemAEliminar">Confirmación de eliminación</div>
                <div v-else>Eliminar {{ plural(itemsSeleccionados.length, 'elemento') }}</div>
            </template>
            <template #content>
                <div v-if="itemAEliminar">
                    ¿Quieres eliminar {{ itemAEliminar.nombre }}?
                </div>
                <div v-else>
                    ¿Quieres eliminar {{ plural(itemsSeleccionados.length, 'elemento') }}?
                </div>
            </template>
            <template #footer>
                <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @submit.prevent="crearCarpeta">

                    <button @click.prevent="modalEliminarItem = false" type="button" class="btn btn-neutral">
                        Cancelar
                    </button>

                    <button @click.prevent="eliminarArchivos" type="button" class="btn btn-primary">
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
import { router, usePage } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout })

const props = defineProps({
    items: Array,
    puedeEscribir: Boolean,
    propietario: Object
});

const rutaActual = computed(()=>props.items.length?props.items[0].ruta:'')

const tituloPropietario = computed(() => {
    if (!props.propietario) return ''
    return 'Propietario: ' + props.propietario.nombre
})


function nombreItem(item) {
    if (item.actual) return `<span class='text-neutral opacity-70'>&lt;${item.nombre}&gt;</span>`
    if (item.padre) return `<span class='text-neutral opacity-70'>&lt;atrás&gt;</span>`
    return item.nombre
}

function plural(count, label) {
    return `${count} ${label + (count != 1 ? 's' : '')}`
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
    if (seleccionando.value) {
        item.seleccionado = !item.seleccionado
        console.log('item.seleccionado =', item.seleccionado)
    }
    else
        item.longTouchTimer = setTimeout(() => {
            item.seleccionado = true;
            seleccionando.value = true;
            console.log('item.seleccionado2 =', item.seleccionado)

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

const itemsSeleccionados = computed(() => props.items.filter(item => item.seleccionado && !item.eliminado))


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
    store.sourcePath = rutaActual.value
    store.filesToMove = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function prepararCopiarItems() {
    seleccionando.value = false
    store.isCopyingFiles = true
    store.sourcePath = rutaActual.value
    store.filesToCopy = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function copiarItems() {
    axios.post(route('files.copy'), {
        sourceFolder: store.sourcePath,
        targetFolder: rutaActual.value,
        items: store.filesToCopy
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al copiar los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function moverItems() {
    axios.post(route('files.move'), {
        sourceFolder: store.sourcePath,
        targetFolder: rutaActual.value,
        items: store.filesToMove
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al mover los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function cancelarOperacion() {
    console.log('cancelarOperacion')
    store.isMovingFiles = false
    store.isCopyingFiles = false
    store.filesToMove = []
    store.filesToCopy = []
    props.items.forEach(item => { item.seleccionado = false })
}


// SUBIR ARCHIVOS
const modalSubirArchivos = ref(false)

const modalCrearCarpeta = ref(false)

const page = usePage()
console.log(page)

console.log('csrf', page.props.csrf_token)

const dropzoneOptions = ref({
    url: route('files.upload.file'),
    thumbnailWidth: 150,
    maxFilesize: 50,
    headers: {
        'X-CSRF-Token': page.props.csrf_token,
    },
})


function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', rutaActual.value);
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
    const items = props.items.filter(item => !item.padre && !item.actual && !item.eliminado)
    const carpetas = items.filter(item => item.tipo === 'carpeta')
    const archivos = items.filter(item => item.tipo !== 'carpeta');

    switch (ordenarPor.value) {
        case 'normal':
            // Ordenar las carpetas y los archivos por separado
            carpetas.sort((a, b) => a.nombre.localeCompare(b.nombre));
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
            return items;
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
    // item.seleccionado = false // para el caso de renombrar un item seleccionado
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
    itemRenombrando.value.seleccionado = false
    axios.post(route('files.rename'), {
        folder: itemRenombrando.value.carpeta,
        oldName: itemRenombrando.value.nombre,
        newName: nuevoNombre.value,
    })
        .then(response => {
            console.log({ response })
            const item = itemRenombrando.value // props.items.find(it => it.nombre == itemRenombrando.value.nombre)
            console.log('renombrar item', item)
            item.ruta = item.carpeta + '/' + nuevoNombre.value
            const parts = item.url.split('/')
            parts[parts.length - 1] = parts[parts.length - 1].replace(item.nombre, nuevoNombre.value)
            item.url = parts.join('/')
            item.nombre = nuevoNombre.value
            if(item.actual) {
                // reemplazar la URL actual en el historial del navegador
                window.history.replaceState(null, null, item.ruta);

                // reemplazar el título de la página
                document.title = item.ruta
            }
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
        folder: rutaActual.value, name: nombreCarpeta.value
    }).then((response) => {
        console.log({ response })
        reloadPage()
    })
        .catch(err => {
            console.log({ err })
            alert(err.response.data.error)
        })
}


// ELIMINAR

const itemAEliminar = ref("")
const modalEliminarItem = ref(false)

function abrirEliminarModal(item) {
    itemAEliminar.value = item
    modalEliminarItem.value = true
}

function eliminarArchivos() {
    if (itemAEliminar.value)
        eliminarArchivo(itemAEliminar.value)
    else {
        for (var item of itemsSeleccionados.value)
            eliminarArchivo(item);
    }
    modalEliminarItem.value = false
}

function eliminarArchivo(item) {
    const url = ('/api/files/' + item.items[0].ruta).replace(/\/\//g, '/')
    return axios.delete(url)
        .then(response => {
            item.eliminado = true
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al eliminar el archivo ' + item.nombre
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

/* base */
.file {
    backface-visibility: hidden;
    z-index: 1;
}

/* moving */
.files-move {
    transition: all 600ms ease-in-out 50ms;
}

/* appearing */
.files-enter-active {
    transition: all 400ms ease-out;
    transform: scale(0);
}

/* disappearing */
.files-leave-active {
    transition: all 200ms ease-in;
    position: absolute;
    z-index: 0;
    transform: scale(0);
}

/* appear at / disappear to */
.files-enter-to {
    opacity: 1;
    transform: scale(1);
}

.files-leave-to {
    opacity: 0;
    transform: scale(0);
}
</style>
