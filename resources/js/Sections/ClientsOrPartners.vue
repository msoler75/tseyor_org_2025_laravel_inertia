<template>
    <div class="container mx-auto">
      <h2 class="text-3xl font-bold mb-4">{{ sectionTitle }}</h2>
      <div class="flex items-center justify-center">
        <div class="grid grid-cols-4 gap-8">
          <div v-for="(client, index) in clients" :key="index" class="flex items-center justify-center">
            <img :src="client.logo" alt="Client Logo" class="w-24 h-24">
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
      clients: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
