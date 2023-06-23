<template>
    <div class="container mx-auto px-4 py-8 w-full flex flex-wrap lg:flex-nowrap gap-10">

        <div class="w-full mx-auto lg:order-last lg:max-w-full lg:w-[40%]">
        <div class="w-full h-auto mx-auto">
          <img :src="lugar.imagen" :alt="lugar.nombre" class="w-full h-auto">
        </div>
      </div>

      <div class="w-full lg:w-[60%] flex-shrink-0 text-left">
        <h1 class="text-center lg:text-left">{{ lugar.nombre }}</h1>
        <p class="text-gray-600 text-xs my-5 text-right">
          Última actualización:
          <TimeAgo :date="lugar.updated_at" />
        </p>
        <div class="mb-4"></div>
        <tabs>
          <tab v-if="lugar.descripcion || lugar.texto" name="Descripción">
            <div v-if="lugar.texto" class="prose" v-html="lugar.texto"></div>
            <div v-else class="prose">{{ lugar.descripcion }}</div>
          </tab>

          <tab v-if="lugar.libros" name="Bibliografía">
            <div v-if="lugar.libros" v-for="libro, index of JSON.parse(lugar.libros)" :key="index">
              {{ libro.titulo }}
            </div>
          </tab>

          <!-- ... relacionados -->

        </tabs>
      </div>


    </div>
  </template>

<script setup>

import { Tabs, Tab } from 'vue3-tabs-component';
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    lugar: {
        type: Object,
        required: true,
    },
});
</script>
