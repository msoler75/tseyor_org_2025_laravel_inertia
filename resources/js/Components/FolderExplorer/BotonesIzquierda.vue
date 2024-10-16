<template>


    <ConditionalLink v-if="!store.seleccionando" class="btn btn-neutral btn-sm btn-icon cursor-pointer"
        :href="'/' + store.rutaBase" :tag="store.embed ? 'span' : 'a'" @click="store.clickFolder({ ruta: '/' + store.rutaBase }, $event)"
        :is-link="!store.embed" title="Ir a la carpeta base"
        :class="store.rutaActual == store.rutaBase ? 'opacity-50 pointer-events-none' : ''">
        <Icon icon="ph:house-line-duotone" class="text-2xl" />
    </ConditionalLink>

    <button v-if="!store.seleccionando" class="btn btn-neutral btn-sm flex gap-x items-center"
        @click.prevent="store.actualizar()" title="Recargar contenidos de la carpeta"
        :class="!store.infoCargada ? 'opacity-50 pointer-events-none' : ''">
        <Icon icon="material-symbols:refresh" class="text-lg" />
    </button>

    <ConditionalLink v-if="store.items.length > 1 && !store.seleccionando && store.items[1].tipo == 'carpeta'" :href="store.items[1].url"
        :tag="store.embed ? 'span' : 'a'" class="btn btn-neutral btn-sm btn-icon w-fit" title="Ir a una carpeta superior"
        @click="store.clickFolder(store.items[1], $event)" :is-link="!store.embed"
        :class="store.rutaActual == store.rutaBase ? 'opacity-50 pointer-events-none' : ''">
        <Icon icon="ph:arrow-elbow-right-up-duotone" class="text-2xl" />
    </ConditionalLink>


    <button v-if="store.seleccionando" class="btn btn-neutral btn-sm flex gap-x items-center"
        @click.prevent="store.cancelarSeleccion" title="Cancelar selección">
        <Icon icon="material-symbols:close-rounded" />
        <span>{{ store.itemsSeleccionados.length }}</span>
    </button>

</template>





<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()

</script>
