<template>
    <div class="bg-hero bg-cover bg-center py-20">
      <div class="container mx-auto text-center">
        <h1 class="text-4xl font-bold text-white mb-4">{{ title }}</h1>
        <p class="text-lg text-white">{{ subtitle }}</p>
        <button class="mt-8 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark">
          {{ buttonLabel }}
        </button>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      title: {
        type: String,
        required: true,
      },
      subtitle: {
        type: String,
        required: true,
      },
      buttonLabel: {
        type: String,
        required: true,
      },
    },
  };
  </script>
