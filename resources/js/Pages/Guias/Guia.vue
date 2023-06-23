<template>
    <div class="container mx-auto px-4 py-8 w-full flex flex-wrap lg:flex-nowrap gap-10">
      <div class="w-full max-w-[350px] mx-auto lg:max-w-full lg:w-1/3">
        <div class="w-full h-auto mx-auto">
          <img :src="guia.imagen" :alt="guia.nombre" class="w-full h-auto">
        </div>
      </div>
      <div class="w-full lg:w-2/3 flex-shrink-0 text-left">
        <h1 class="text-center lg:text-left">{{ guia.nombre }}</h1>
        <p class="text-gray-600 text-xs my-5 text-right">
          Última actualización:
          <TimeAgo :date="guia.updated_at" />
        </p>
        <div class="mb-4"></div>
        <tabs>
          <tab v-if="guia.descripcion || guia.texto" name="Descripción">
            <div v-if="guia.texto" class="prose" v-html="guia.texto"></div>
            <div v-else class="prose">{{ guia.descripcion }}</div>
          </tab>

          <tab v-if="guia.experiencia" name="Experiencia">
            <div class="prose" v-html="guia.experiencia"></div>
          </tab>

          <tab v-if="guia.citas" name="Citas">
            <div class="prose" v-html="guia.citas"></div>
          </tab>

          <tab v-if="guia.libros" name="Bibliografía">
            <div v-if="guia.libros" v-for="libro, index of JSON.parse(guia.libros)" :key="index">
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
    guia: {
        type: Object,
        required: true,
    },
});
</script>
