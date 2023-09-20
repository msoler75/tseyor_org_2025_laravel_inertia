<template>
    <div class="container mx-auto px-4 py-8 w-full">

        <div class="flex justify-between items-center mb-20">
            <Back>Glosario</Back>
            <AdminPanel modelo="termino" necesita="administrar contenidos" :contenido="termino" />
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-20 px-7 md:px-0 rounded-xl">

            <div class="prose mx-auto">
                <h1 class="text-center lg:text-left capitalize lowercase">{{ termino.nombre }}</h1>
                <p class="text-gray-600 text-xs my-5 text-right">
                    Última actualización:
                    <TimeAgo :date="termino.updated_at" />
                </p>
                <div class="mb-4"></div>
                <Content :content="termino.texto" format="md" />
            </div>

        </div>



        <div v-if="referencias.terminos.length" class="flex flex-wrap sm:flex-nowrap gap-6 items-baseline mb-12 max-w-[80ch] mx-auto">
            <h3 class="mr-5 whitespace-nowrap">Véase también:</h3>
            <div class="flex gap-6 flex-wrap">
                <Link v-for="contenido in referencias.terminos" :key="contenido.id" :href="route('termino', contenido.slug)"
                    class="capitalize lowercase hover:text-primary transition-color duration-200 w-fit h-fit font-bold text-lg card shadow hover:shadow-lg px-5 py-2 bg-base-100">
                {{ contenido.nombre }}
                </Link>
            </div>
        </div>

        <div v-if="referencias.libros.length" class="grid gap-4 mt-4 max-w-[80ch] mx-auto"
            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">

            <CardContent v-for="contenido in referencias.libros" :key="contenido.id" :title="contenido.titulo"
                :image="contenido.imagen" :href="route('libro', contenido.slug)" :description="contenido.descripcion"
                :date="contenido.published_at" :tag="contenido.categoria" image-left class="h-[300px]"
                imageClass="w-[200px] h-[300px]" />
        </div>

        <hr class="my-12" />


        <div class="flex justify-between my-12">
            <Link v-if="anterior" :href="anterior.slug" class="hover:underline">‹&nbsp;&nbsp; {{ anterior.nombre }}</Link>
            <span v-else />
            <Link v-if="siguiente" :href="siguiente.slug" class="hover:underline">{{ siguiente.nombre }} &nbsp;&nbsp;›
            </Link>
            <span v-else />
        </div>

    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    termino: {
        type: Object,
        required: true,
    },
    anterior: {
        type: Object,
        required: true,
    },
    siguiente: {
        type: Object,
        required: true,
    },
    referencias: {
        type: Object,
        required: true,
    },
});
</script>
