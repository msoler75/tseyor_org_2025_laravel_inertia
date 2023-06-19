<template>
    <div class="container mx-auto">
      <h2 class="text-3xl font-bold mb-4">{{ sectionTitle }}</h2>
      <div class="flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-6">
          <p class="text-gray-600">{{ contactInfo }}</p>
          <div class="flex items-center mt-4">
            <a href="#" class="text-blue-500 hover:underline mr-4">Teléfono</a>
            <a href="#" class="text-blue-500 hover:underline">Correo electrónico</a>
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
      contactInfo: {
        type: String,
        required: true,
      },
    },
  };
  </script>
