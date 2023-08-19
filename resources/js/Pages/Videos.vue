<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">

            <AdminPanel modelo="video" necesita="administrar contenidos" class="mb-3"/>

<section class="py-10 space-y-20 container mx-auto">
    <h1 class="text-4xl font-bold text-center mb-10">Vídeos TSEYOR</h1>
    <div class="flex flex-wrap justify-center items-center gap-10 card flex-row bg-base-100 shadow rounded-lg p-6">
      <p class="text-lg">Visita nuestro canal de Youtube:</p>
      <a href="https://www.youtube.com/@tseyor" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold text-lg">youtube.com/@tseyor</a>
      <div>
        <a href="https://www.youtube.com/@tseyor" target="_blank">
          <Image src="/biblioteca/videos/YouTube_logo_2017.svg?ts=1679515913" alt="Logo de Youtube" class="w-16 h-16 mx-auto"/>
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-10 mt-10">
      <div>
        <h3 class="text-2xl font-bold mb-4">Curso Holístico Tseyor</h3>
        <div class="aspect-w-16 aspect-h-9">
          <iframe src="https://www.youtube.com/embed/AkOZbfGdXbU" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-4">Comunicado y avistamientos en Temuco</h3>
        <div class="aspect-w-16 aspect-h-9">
          <iframe src="https://www.youtube.com/embed/O1NdCJncxFY" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-4">Cuentos</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="aspect-w-16 aspect-h-9">
            <iframe src="https://www.youtube.com/embed/w_LHIxIyI2Q" frameborder="0" allowfullscreen></iframe>
          </div>
          <div class="aspect-w-16 aspect-h-9">
            <iframe src="https://www.youtube.com/embed/gHCOWEG5kFs" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-4">Universidad Tseyor</h3>
        <div class="aspect-w-16 aspect-h-9">
          <iframe src="https://www.youtube.com/embed/playlist?list=PLFhMyyl8F1jZZkMAzNyKeK6iaypDb61ft" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-4">Entrevistas</h3>
        <div class="aspect-w-16 aspect-h-9">
          <iframe src="https://www.youtube.com/embed/playlist?list=PLFhMyyl8F1jbIfjGgD2lkGQk7vdDq7M45" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
      <div>
        <h3 class="text-2xl font-bold mb-4">ONG</h3>
        <div class="aspect-w-16 aspect-h-9">
          <iframe src="https://www.youtube.com/embed/playlist?list=PLFhMyyl8F1jYLD4d-J1pQZ11ScOPloVGo" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </section></div>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })
</script>
