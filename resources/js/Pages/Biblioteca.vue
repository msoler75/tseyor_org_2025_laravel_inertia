<template>

<div class="container py-12 mx-auto">
        <!-- Encabezado -->
        <h1 class="mb-4">Biblioteca Tseyor</h1>

        <div class="text-pretty my-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-14">
                <div>
                    <p>La Biblioteca Tseyor es el resultado de más de 1500 diálogos <Referencia r="telepatía">telepáticos</Referencia> con seres de la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia>, transmitidos a través de <Referencia>Chac-Mool Puente</Referencia>, <Referencia r="canalización">canalizador</Referencia> de Tseyor.</p>
                    <p>Estas denominadas <q>conversaciones interdimensionales</q> o comunicados se han transcrito en textos que inspiran libros, monografías, vídeos y audios.</p>
                </div>
                <div class="flex gap-5">
                    <p class="">Todos los materiales han sido publicados y están preservados por la <Referencia>Universidad Tseyor de Granada</Referencia> para mantener la integridad del mensaje original y sus obras impresas están registradas por la asociación <Referencia>TSEYOR Centro de Estudios Socioculturales</Referencia>.</p>
                    <Image class="mt-[1.5rem]" width="137" height="200" src="https://tseyor.org/images/sello-utg.webp?ts=1433235750&thumb=299x435&quality=87&fe=jpg"/>
                </div>
            </div>

        </div>

        <!-- Contenedor de Categorías -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Categoría: Libros -->
            <div v-for="seccion of secciones" :key="seccion.title" class="card bg-base-100 p-6 rounded-lg shadow-md relative group
            transition duration-300  hover:shadow-lg antialiased
            lg:opacity-80 hover:opacity-100
            ">
                <Icon :icon="seccion.icon" class="text-secondary absolute right-3 top-3 text-8xl z-0 transition antialiased  duration-300 opacity-5 group-hover:opacity-20"/>
                <h2 class="text-2xl font-semibold mb-2">{{seccion.title}}</h2>
                <p class="text-neutral opacity-70 mb-4">{{ seccion.descripcion }}</p>
                <div class="mt-auto flex justify-end">
                    <ActionButton :href="seccion.url" class="w-[220px]">Ver {{seccion.title.toLowerCase()}}</ActionButton>
                </div>
            </div>


        </div>
    </div>


</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const secciones = ref([
    {
        title: 'Libros',
        descripcion: 'Más de 300 libros con material genuino de Tseyor.',
        url: route('libros'),
        icon: "ph:book-bookmark-duotone"
    },
    {
        title: 'Comunicados',
        descripcion: 'Transcripción y audios de las conversaciones interdimensionales mantenidas con los hermanos del Cosmos.',
        url: route('comunicados'),
        icon: "ph:flying-saucer-duotone"
    },
    {
        title: 'Audios',
        descripcion: 'Talleres, meditaciones, cuentos y canciones.',
        url: route('audios'),
        icon: "ph:music-notes-duotone",
    },
    {
        title: 'Blogs',
        url: route('entradas'),
        descripcion: 'Artículos acerca de nuestra comunidad.',
        icon: "ph:pencil-line-duotone",
    },
    {
        title: 'Vídeos',
        url: route('videos'),
        descripcion: 'Vídeos de nuestro canal Youtube.',
        icon: "ph:youtube-logo-duotone",
    },
    {
        title: 'Meditaciones',
        url: route('meditaciones'),
        descripcion: 'Meditaciones y talleres en texto y audio.',
        icon: "ph:file-text-duotone"
    },
    {
        title: 'Radio Tseyor',
        url: route('radio'),
        descripcion: 'Escucha comunicados, entrevistas, y talleres 24/7.',
        icon: "ph:radio-duotone",
    }
])
</script>
