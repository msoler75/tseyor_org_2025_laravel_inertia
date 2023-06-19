<template>
    <div class="container mx-auto">
      <div class="grid grid-cols-3 gap-4">
        <div v-for="image in images" :key="image.id" class="relative">
          <img :src="image.url" :alt="image.alt" class="w-full h-auto object-cover rounded" />
          <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
            <button class="px-4 py-2 bg-primary text-white rounded-full hover:bg-primary-dark">
              View
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      images: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
