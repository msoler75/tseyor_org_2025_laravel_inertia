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
                    <p class="">Todos los materiales han sido publicados y están preservados por la <Referencia>Universidad Tseyor de Granada</Referencia> para mantener la integridad del mensaje original y sus obras literarias están registradas bajo la asociación <Referencia>TSEYOR Centro de Estudios Socioculturales</Referencia>.</p>
                    <img class="mt-[1.5rem] h-[200px]" src="https://tseyor.org/images/sello-utg.webp?ts=1433235750&thumb=299x435&quality=87&fe=jpg">
                </div>
            </div>

        </div>

        <!-- Contenedor de Categorías -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Categoría: Libros -->
            <div v-for="seccion of secciones" :key="seccion.title" class="card bg-base-100 p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-2">{{seccion.title}}</h2>
                <p class="text-neutral opacity-70 mb-4">{{ seccion.descripcion }}</p>
                <div class="mt-auto flex justify-end">
                    <ActionButton :href="seccion.url">Ver {{seccion.title.toLowerCase()}}</ActionButton>
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
        url: route('libros')
    },
    {
        title: 'Comunicados',
        descripcion: 'Transcripción y audios de las conversaciones interdimensionales mantenidas con los hermanos del Cosmos.',
        url: route('comunicados')
    },
    {
        title: 'Audios',
        descripcion: 'Talleres, meditaciones, cuentos y canciones.',
        url: route('audios')
    },
    {
        title: 'Vídeos',
        url: route('videos'),
        descripcion: 'Vídeos de nuestro canal Youtube.'
    }
])
</script>
