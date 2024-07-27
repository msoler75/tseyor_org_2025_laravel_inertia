<template>
    <Link :href="route('libro', libro.slug)" class="flex-shrink-0 w-fit h-fit">
    <div class="book-container w-fit mx-auto" :class="cargando?'is-loading':''">
        <Image :src="libro.imagen" class="rounded-sm rounded-tl-lg border-t border-l border-gray-500 border-opacity-15" :class="imageClass" @loaded="cargando=false"/>
    </div>
    </Link>
</template>

<script setup>
const props = defineProps({
    libro: Object,
    imageClass: String
})

const cargando = ref(true)

/*
const bgAnchoOriginal = 400
const bgAltoOriginal = 621
const bgLomoDerecha = 26
const bgLomoAbajo = 19
*/

</script>

<style scoped>
.book-container {
    position: relative;
}

.book-container:after {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: calc(100% + 26 * 100% / 400);
    height: calc(100% + 19 * 100% / 621);
    background: url(/almacen/medios/libros/plantilla/portada-3d.png) no-repeat;
    background-size: 100% 100%;
    opacity: 1;
    @apply transition-opacity duration-200;
}

.book-container.is-loading:after {
    opacity: 0;
}
</style>
