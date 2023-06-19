<template>
    <div class="container mx-auto px-4 py-8 flex flex-col md:flex-row gap-10">

        <div class=" card bg-base-100 shadow-2xl w-fit flex justify-center mx-auto">
            <img :src="libro.imagen" :alt="libro.titulo" class="w-[250px] h-[400px] object-contain">
        </div>

          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">{{ libro.titulo }}</h1>
            <p class="text-gray-600 text-sm mb-2">
              <TimeAgo :date="libro.published_at" />
            </p>
            <p class="mb-8 text-xs">Edición: {{ libro.edicion }}, páginas: {{ libro.paginas }}</p>
            <div class="prose" v-html="libro.descripcion"></div>
            <div class="w-full flex justify-center sm:justify-end mt-7">

            <a class="btn btn-primary  w-fit" :href="libro.pdf" download>Descargar en PDF</a>
          </div></div>
    </div>
  </template>

  <script setup>
  import AppLayout from '@/Layouts/AppLayout.vue'

  defineOptions({ layout: AppLayout })

  const props = defineProps({
    libro: {
      type: Object,
      required: true,
    },
  });
  </script>

  <style scoped>
  .container {
    max-width: 900px;
  }
  </style>
