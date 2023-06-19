<template>
    <div class="container mx-auto">
      <div class="grid grid-cols-3 gap-8">
        <div v-for="(service, index) in services" :key="index" class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-center h-12 w-12 bg-gray-100 rounded-lg mb-4">
            <svg class="w-6 h-6 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
              <path v-if="service.icon === 'icon-1'" d="M10 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zM5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" />
              <path v-else-if="service.icon === 'icon-2'" d="M10 3a1 1 0 011 1v2h2a1 1 0 110 2h-2v2a1 1 0 11-2 0V8H7a1 1 0 110-2h2V4a1 1 0 011-1zM3 10a1 1 0 011-1h2V7a1 1 0 112 0v2h2a1 1 0 110 2H7v2a1 1 0 11-2 0v-2H4a1 1 0 01-1-1z" />
              <path v-else-if="service.icon === 'icon-3'" d="M10 4a1 1 0 011 1v2h2a1 1 0 110 2h-2v2a1 1 0 11-2 0V9H7a1 1 0 110-2h2V5a1 1 0 011-1zM3 11a1 1 0 011-1h2V8a1 1 0 112 0v2h2a1 1 0 110 2H8v2a1 1 0 11-2 0v-2H4a1 1 0 01-1-1z" />
              <path v-else-if="service.icon === 'icon-4'" d="M10 3a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1H9a1 1 0 110-2H10V4a1 1 0 011-1zM7 10a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1V8a1 1 0 112 0v1h1zM10 17a1 1 0 01-1-1v-1h-1a1 1 0 110-2h1v-1a1 1 0 112 0v1h1a1 1 0 110 2h-1v1a1 1 0 01-1 1zM15 10a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1V9a1 1 0 112 0v1h1z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-2">{{ service.title }}</h3>
          <p class="text-gray-600">{{ service.description }}</p>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      services: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
