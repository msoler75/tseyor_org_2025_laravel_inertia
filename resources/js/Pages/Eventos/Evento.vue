<template>
    <div class="bg-base-100 cardoverflow-hidden">
      <div class="h-64 bg-cover bg-center" :style="{ backgroundImage: 'url(' + evento.imagen + ')' }">
        <div class="flex justify-end items-end h-full">
          <div class="p-4 bg-gray-800 bg-opacity-75 text-white">
            <p class="text-sm">{{ evento.fecha_inicio | formatDate }} - {{ evento.fecha_fin | formatDate }}</p>
          </div>
        </div>
      </div>
      <div class="px-6 py-4">
        <div class="mb-2">
          <h1 class="text-2xl font-bold text-gray-800">{{ evento.nombre }}</h1>
          <p class="text-gray-600 text-sm mt-1"><TimeAgo :date="evento.published_at" /></p>
        </div>
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-gray-600 mr-2" viewBox="0 0 24 24"><path fill="currentColor" d="M16 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 12l-4-3 4-3v6z"></path></svg>
          <p class="text-gray-700 text-sm">{{ evento.lugar }}</p>
        </div>
        <div class="flex items-center mb-4">
          <svg class="w-6 h-6 text-gray-600 mr-2" viewBox="0 0 24 24"><path fill="currentColor" d="M19.7,12.3l-3.4-3.4c-0.4-0.4-1-0.4-1.4,0l-2.3,2.3l-2.3-2.3c-0.4-0.4-1-0.4-1.4,0l-3.4,3.4c-0.4,0.4-0.4,1,0,1.4l2.3,2.3l-2.3,2.3c-0.4,0.4-0.4,1,0,1.4l3.4,3.4c0.4,0.4,1,0.4,1.4,0l2.3-2.3l2.3,2.3c0.4,0.4,1,0.4,1.4,0l3.4-3.4c0.4-0.4,0.4-1,0-1.4L19.7,12.3z"></path></svg>
          <p class="text-gray-700 text-sm">{{ evento.categoria }}</p>
        </div>
        <div class="mb-4">
          <p class="text-gray-700">{{ evento.descripcion }}</p>
        </div>
        <hr class="my-4">
        <div class="prose" v-html="evento.texto"></div>
      </div>
    </div>
  </template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
});
</script>
