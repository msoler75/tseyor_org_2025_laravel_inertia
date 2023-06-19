<template>
    <div class="container mx-auto">
      <h2 class="text-3xl font-bold mb-4">{{ sectionTitle }}</h2>
      <div class="grid grid-cols-3 gap-8">
        <div v-for="(post, index) in blogPosts" :key="index" class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-xl font-semibold mb-2">{{ post.title }}</h3>
          <p class="text-gray-600">{{ post.excerpt }}</p>
          <a href="#" class="text-blue-500 hover:underline">Leer más</a>
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
      blogPosts: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
