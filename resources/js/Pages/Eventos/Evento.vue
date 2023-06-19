<template>
    <div class="max-w-[960px] pt-7 mx-auto space-y-12 mt-12">
        <h1>{{ evento.titulo }} {{ evento.titulo }}</h1>
        <div class="flex gap-12 flex-wrap">
      <div class="max-w-[500px]">
        <img :src="evento.imagen" alt="Imagen del evento" class="w-full mb-4">
        <p class="mb-4">{{ evento.texto }}</p>
      </div>
      <div>
        <div class="card bg-base-100 shadow p-4">
          <p class="mb-2 flex gap-3 items-center">
            <Icon icon="ph:calendar-check-duotone" class="text-xl"/> Inicio: {{ evento.fecha_inicio }}</p>
          <p v-if="evento.fecha_fin" class="mb-2 flex gap-3 items-center"><Icon icon="ph:calendar-x-duotone" class="text-xl"/>  Fin: {{ evento.fecha_fin }}</p>
          <p v-if="evento.hora_inicio" class="mb-2 flex gap-3"><Icon icon="ph:alarm-duotone" class="text-xl"/>  Hora de inicio: {{ evento.hora_inicio }}</p>
          <p v-if="evento.hora_fin" class="mb-2"><Icon icon="ph:clock-countdown-duotone" class="text-xl"/>  Hora de fin: {{ evento.hora_fin }}</p>
          <p v-if="evento.lugar" class="mb-2 flex gap-3"><Icon icon="ph:map-pin-duotone" class="text-xl"/> Lugar: {{ evento.lugar }}</p>
        </div>
        <hr class="my-7"/>
        <p class="mt-4">{{ evento.descripcion }}</p>
      </div>
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
  })

  </script>
