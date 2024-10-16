<template>
    <GridFill colWidth="14rem" class="gap-4 pt-6" v-disable-right-click>

        <div v-for="item in items" :key="item.ruta" class="transition-opacity duration-200"
            :class="[item.clase, item.seleccionado ? 'bg-base-300' : '', item.puedeLeer ? '' : ' opacity-70 pointer-events-none', store.navegando && store.navegando != item.url ? 'opacity-0 pointer-events-none' : '']">
            <div v-if="store.seleccionando" @click.prevent="store.toggleItem(item)"
                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                <Icon v-else icon="ph:square" />
            </div>
            <div class="flex flex-col items-center justify-center relative h-full pt-2"
                :xclass="store.seleccionando ? 'pointer-events-none' : ''"
                v-on:touchstart="store.touchStart(item, $event)" v-on:touchend.prevent="store.touchEnd(item, $event)">
                <DiskIcon v-if="item.tipo === 'disco'" :url="item.url" class="cursor-pointer text-8xl mb-4"
                    @click="store.clickDisk(item, $event)" :is-link="!store.seleccionando && !store.embed" />
                <FolderIcon v-else-if="item.tipo === 'carpeta'" :url="item.url" :private="item.privada"
                    :loading="store.navegando == item.url"
                    :owner="item.propietario && item.propietario?.usuario.id === user?.id"
                    class="cursor-pointer text-8xl mb-4" :disabled="store.seleccionando"
                    @click="store.clickFolder(item, $event)" :is-link="!store.seleccionando && !store.embed"
                    :arrow="!!item.acceso_directo" />
                <a v-else-if="store.isImage(item.nombre)" :href="item.url" class="text-8xl mb-4" download
                    @click="store.clickFile(item, $event)">
                    <Image :src="item.url" class="overflow-hidden w-[180px] h-[120px] object-contain" />
                </a>
                <FileIcon v-else :url="item.url" class="cursor-pointer text-8xl mb-4"
                    @click="store.clickFile(item, $event)" :is-link="!store.seleccionando && !store.embed" />

                <div class="text-sm text-center">
                    <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url" v-html="store.nombreItem(item)"
                        class="py-1 hover:underline" @click="store.clickDisk(item, $event)"
                        :is-link="!store.seleccionando && !store.embed" />
                    <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                        v-html="store.nombreItem(item)" class="py-1 hover:underline"
                        @click="store.clickFolder(item, $event)" :is-link="!store.seleccionando && !store.embed" />
                    <span v-else-if="store.seleccionando" v-html="store.nombreItem(item)" />
                    <a v-else :href="item.url" download v-html="store.nombreItem(item)"
                        @click="store.clickFile(item, $event)" :is-link="!store.seleccionando && !store.embed"
                        class="py-1 hover:underline" />
                </div>
                <div class="text-gray-500 text-xs">
                    <span v-if="item.tipo === 'disco'" />
                    <template v-else-if="item.tipo === 'carpeta'">{{ 'archivos' in
                        item ? (plural(item.archivos, 'archivo') + ', ' +
                            plural(item.subcarpetas, 'carpeta')) : '&nbsp;' }}
                    </template>
                    <template v-else>
                        <FileSize :size="item.tamano" />&nbsp;
                        <TimeAgo :date="item.fecha_modificacion" />
                    </template>
                </div>
            </div>
            <div class="w-full transform flex justify-center mt-auto relative z-10">
                <MenuItem :item="item" :vertical="false" />
            </div>
        </div>
    </GridFill>
</template>





<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
import { plural } from '@/composables/textutils'


const page = usePage()
const user = computed(() => page?.props?.auth?.user)

const store = useFolderExplorerStore()

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


</script>
