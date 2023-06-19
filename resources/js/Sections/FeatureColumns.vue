<template>
    <div class="container mx-auto">
      <div class="grid grid-cols-2 gap-8">
        <div v-for="feature in features" :key="feature.id" class="p-6 bg-white rounded shadow">
          <div class="flex items-center mb-4">
            <div class="mr-4">
              <img :src="feature.icon" class="h-8 w-8" alt="Feature Icon" />
            </div>
            <h3 class="text-xl font-bold">{{ feature.title }}</h3>
          </div>
          <p class="text-gray-600">{{ feature.description }}</p>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      features: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
