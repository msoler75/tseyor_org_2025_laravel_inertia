<template>
    <div class="container mx-auto">
      <h2 class="text-3xl font-bold mb-4">{{ sectionTitle }}</h2>
      <div class="flex items-center justify-center">
        <div class="grid grid-cols-3 gap-4">
          <div v-for="(post, index) in socialMediaPosts" :key="index" class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
              <img :src="post.image" alt="Post Image" class="w-48 h-32 object-cover">
            </div>
            <h3 class="text-xl font-semibold mb-2">{{ post.title }}</h3>
            <p class="text-gray-600">{{ post.description }}</p>
            <div class="flex items-center justify-between mt-4">
              <a :href="post.link" class="text-blue-500 hover:underline">Leer más</a>
              <div class="flex items-center space-x-2">
                <span class="text-gray-500">{{ post.likes }} Me gusta</span>
                <span class="text-gray-500">{{ post.comments }} Comentarios</span>
              </div>
            </div>
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
      socialMediaPosts: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
