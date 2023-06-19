<template>
    <div class="container mx-auto">
      <h2 class="text-3xl font-bold mb-4">{{ sectionTitle }}</h2>
      <div class="flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-6">
          <p class="text-gray-600">{{ description }}</p>
          <div class="mt-4 flex">
            <input type="email" class="border border-gray-300 px-4 py-2 rounded-l-md focus:outline-none" placeholder="Ingresa tu correo electrónico">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-r-md ml-2 hover:bg-blue-600">Suscribirse</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      sectionTitle: {
        type: String,
        required: true,
      },
      description: {
        type: String,
        required: true,
      },
    },
  };
  </script>
