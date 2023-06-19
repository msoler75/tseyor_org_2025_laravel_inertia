<template>
    <div class="container mx-auto text-center">
      <h2 class="text-3xl font-bold mb-4">{{ title }}</h2>
      <p class="text-lg text-gray-600 mb-8">{{ description }}</p>
      <button class="px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark">
        {{ buttonLabel }}
      </button>
    </div>
  </template>

  <script>
  export default {
    props: {
      title: {
        type: String,
        required: true,
      },
      description: {
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
