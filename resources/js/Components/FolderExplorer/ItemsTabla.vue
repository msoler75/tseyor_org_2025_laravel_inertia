<template>
    <table class="w-full lg:w-auto mx-auto" :class="transitionActive ? 'animating' : ''">
        <thead class="hidden sm:table-header-group" :class="items.length ? '' : 'opacity-0'">
            <tr>
                <th v-if="store.seleccionando" class="hidden md:table-cell"></th>
                <th></th>
                <th class="min-w-[16rem] lg:min-w-[32rem] text-left cursor-pointer"
                    @click="store.ordenarPor = store.ordenarPor == 'nombreAsc' ? 'nombreDesc' : 'nombreAsc'">Nombre
                    <span v-if="store.ordenarPor == 'nombreDesc'">↑</span><span
                        v-if="store.ordenarPor == 'nombreAsc'">↓</span>
                </th>
                <th class="min-w-[8rem] cursor-pointer"
                    @click="store.ordenarPor = store.ordenarPor == 'tamañoDesc' ? 'tamañoAsc' : 'tamañoDesc'">Tamaño
                    <span v-if="store.ordenarPor == 'tamañoDesc'">↑</span><span
                        v-if="store.ordenarPor == 'tamañoAsc'">↓</span>
                </th>
                <th class="min-w-[12rem] cursor-pointer"
                    @click="store.ordenarPor = store.ordenarPor == 'fechaDesc' ? 'fechaAsc' : 'fechaDesc'">Fecha <span
                        v-if="store.ordenarPor == 'fechaDesc'">↑</span><span
                        v-if="store.ordenarPor == 'fechaAsc'">↓</span></th>
                <th v-if="selectors.mostrarPermisos && !store.mostrandoResultadosBusqueda" class="hidden sm:table-cell">
                    Permisos</th>
                <th v-if="selectors.mostrarPermisos && !store.mostrandoResultadosBusqueda" class="hidden sm:table-cell">
                    Propietario</th>
                <th v-if="store.mostrandoResultadosBusqueda || store.mostrarRutas || store.rutaActual == 'mis_archivos'"
                    class="hidden lg:table-cell text-sm">Ubicación
                </th>
                <th class="hidden md:table-cell"></th>
            </tr>
        </thead>

        <!-- Para Test -->
        <tbody v-if="false" v-disable-right-click>
            <tr v-for="item in items" :key="item.ruta" :class="[item.clase, item.seleccionado ? 'bg-base-300' : '']"
                v-on:touchstart="store.touchStart(item, $event)" v-on:touchend.prevent="store.touchEnd(item, $event)"
                v-on:touchmove="store.touchMove($event)">
                <td v-if="store.seleccionando" @click.prevent="store.toggleItem(item)"
                    class="transform scale-100 text-2xl cursor-pointer opacity-70 hover:opacity-100">
                    <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                    <Icon v-else icon="ph:square" />
                </td>
                <td>
                    <component :is="store.seleccionando ? 'div' : Link" :href="item.url"
                        @click="store.clickItem(item, $event)" :disabled="store.seleccionando"
                        class="inline-block py-3 bg-red-500 w-full cursor-pointer">{{
                            store.nombreItem(item) }}</component>
                </td>

                ...
            </tr>
        </tbody>

        <component v-else :is="transitionActive ? TransitionGroup : 'tbody'" tag="tbody" name="files"
            v-disable-right-click>
            <tr v-for="item in items.slice(0, mostrandoNItems)" :key="item.ruta" class="transition-opacity duration-200"
                :class="[item.clase, item.seleccionado ? 'bg-base-300' : '', item.puedeLeer && (!store.buscandoCarpetaDestino || item.tipo=='carpeta') ? '' : 'opacity-70 pointer-events-none', store.navegando && store.navegando != item.url ? 'opacity-0 pointer-events-none' : '']"
                v-on:touchstart="store.touchStart(item, $event)" v-on:touchend.prevent="store.touchEnd(item, $event)"
                v-on:touchmove="store.touchMove($event)">
                <td v-if="store.seleccionando" @click.prevent="store.toggleItem(item)"
                    class="hidden md:table-cell transform scale-100 text-2xl cursor-pointer opacity-70 hover:opacity-100">
                    <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                    <Icon v-else icon="ph:square" />
                </td>
                <td class="relative w-4">
                    <DiskIcon v-if="item.tipo === 'disco'" :url="item.url" class="cursor-pointer"
                        @click="store.clickItem(item, $event)" :is-link="!store.seleccionando && !store.embed" />
                    <FolderIcon v-else-if="item.tipo === 'carpeta'" :loading="store.navegando == item.url"
                        class="cursor-pointer text-4xl sm:text-xl" :private="item.privada"
                        :owner="item.propietario && item.propietario?.usuario.id === user?.id" :url="item.url"
                        @click="store.clickItem(item, $event)"
                        :is-link="!store.seleccionando && !store.embed && item.puedeLeer"
                        :arrow="!!item.acceso_directo" />
                    <FileIcon v-else :url="item.url" class="cursor-pointer text-4xl sm:text-xl"
                        @click="store.clickFile(item, $event)"
                        :is-link="!store.seleccionando && !store.embed && item.puedeLeer" />
                </td>
                <td class="sm:hidden py-3">
                    <div class="flex flex-col">
                        <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url" v-html="store.nombreItem(item)"
                            class="cursor-pointer" @click="store.clickDisk(item, $event)"
                            :is-link="!store.seleccionando && !store.embed" />
                        <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                            v-html="store.nombreItem(item)" class="cursor-pointer"
                            :class="store.seleccionando ? 'pointer-events-none' : ''"
                            @click="store.clickFolder(item, $event)" :is-link="!store.seleccionando && !store.embed" />
                        <div v-else-if="store.seleccionando" :title="item.nombre" v-html="store.nombreItem(item)" />
                        <a v-else :href="item.url" download v-html="store.nombreItem(item)"
                            :class="store.seleccionando ? 'pointer-events-none' : ''"
                            @click="store.clickFile(item, $event)" :is-link="!store.seleccionando && !store.embed" />

                        <small class="w-full flex justify-between gap-2 items-center opacity-50">
                            <span v-if="item.tipo === 'disco'">****</span>
                            <span v-else-if="item.tipo === 'carpeta'">
                                {{ 'archivos' in item ? plural(item.archivos + item.subcarpetas, 'elemento'
                                ) : ''
                                }}</span>
                            <FileSize v-else :size="item.tamano" />
                            <TimeAgo v-if="item.fecha_modificacion" :date="item.fecha_modificacion" />
                        </small>
                    </div>
                </td>
                <td class="hidden sm:table-cell py-3 max-w-[24rem]">
                    <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url" v-html="store.nombreItem(item)"
                        class="cursor-pointer py-3 hover:underline" @click="store.clickDisk(item, $event)"
                        :is-link="!store.seleccionando && !store.embed" />
                    <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                        v-html="store.nombreItem(item)" class="cursor-pointer py-3 hover:underline"
                        :class="store.seleccionando ? 'pointer-events-none' : ''"
                        @click="store.clickFolder(item, $event)" :is-link="!store.seleccionando && !store.embed" />
                    <span v-else-if="store.seleccionando" v-html="store.nombreItem(item)" />
                    <a v-else :href="item.url" download v-html="store.nombreItem(item)" class="py-3 hover:underline"
                        :class="store.seleccionando ? 'pointer-events-none' : ''"
                        @click="store.clickFile(item, $event)" />
                </td>
                <td class="hidden sm:table-cell text-center">
                    <span v-if="item.tipo === 'disco'">-</span>
                    <span v-else-if="item.tipo === 'carpeta'" class="text-sm">
                        {{ 'archivos' in item ? plural(item.archivos + item.subcarpetas, 'elemento') : ''
                        }}
                    </span>
                    <FileSize v-else :size="item.tamano" class="block text-right" />
                </td>
                <td class="hidden sm:table-cell text-center">
                    <TimeAgo v-if="item.fecha_modificacion" :date="item.fecha_modificacion"
                        class="block text-center text-sm" />
                    <span v-else>-</span>
                </td>
                <td v-if="selectors.mostrarPermisos && !store.mostrandoResultadosBusqueda"
                    class="hidden sm:table-cell text-center text-sm">{{
                        item.permisos || '...' }}
                </td>
                <td v-if="selectors.mostrarPermisos && !store.mostrandoResultadosBusqueda"
                    class="hidden sm:table-cell text-center text-sm min-w-[10rem]">
                    {{ item.propietario?.usuario.nombre || '...' }}/{{ item.propietario?.grupo.nombre ||
                        '...'
                    }}
                </td>
                <td v-if="store.mostrandoResultadosBusqueda || store.mostrarRutas || store.rutaActual == 'mis_archivos'"
                    class="hidden lg:table-cell text-sm">
                    <div class="flex items-center gap-2 lg:min-w-64 xl:min-w-[500px] 2xl:min-w-[700px]">
                        <FolderIcon :arrow="true" :url="item.carpeta" />
                        <Link :href="item.carpeta" class="break-all">/{{ item.carpeta }}</Link>
                    </div>
                </td>
                <td class="hidden md:table-cell">
                    <MenuItem :item='item' />
                </td>
            </tr>
        </component>
    </table>
</template>



<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
import useSelectors from '@/Stores/selectors'
import { plural } from '@/composables/textutils'
import { TransitionGroup } from 'vue'

const page = usePage()
const user = computed(() => page?.props?.auth?.user)

const store = useFolderExplorerStore()
const selectors = useSelectors()

const props = defineProps({
    items: Array,
})


const vDisableRightClick = {
    mounted(el) {
        if (store.esPantallaTactil())
            el.addEventListener('contextmenu', (e) => e.preventDefault())
    },
    unmounted(el) {
        if (store.esPantallaTactil())
            el.removeEventListener('contextmenu', (e) => e.preventDefault())
    }
}




// USAR EFECTO ANIMACION DE TARNSICION?
const transitionActive = ref(false)


const mostrandoNItems = ref(24)

function incrementarItemsMostrados() {
    if (mostrandoNItems.value > props.items.length + 32) return
    mostrandoNItems.value += 32
    setTimeout(() => {
        incrementarItemsMostrados()
    }, 500)
}

setTimeout(incrementarItemsMostrados, 500)


</script>


<style scoped>
table td,
table th {
    @apply px-2;
}
</style>
