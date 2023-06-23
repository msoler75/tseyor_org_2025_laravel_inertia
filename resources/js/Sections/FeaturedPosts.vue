<template>
    <div class="container mx-auto py-12">
      <h2 class="text-3xl font-bold mb-4">{{ title }}</h2>
      <div class="grid gap-8"
      :style="{
        'grid-template-columns': `repeat(auto-fill, minmax(${postWidth}, 1fr))`}">
        <div v-for="(post, index) in posts" :key="index" class="card bg-base-100 shadow p-6">
          <h3 class="text-xl font-semibold mb-2">{{ post.title }}</h3>
          <small v-if="post.date">{{ post.date }}</small>
          <p v-if="post.description" class="text-gray-600">{{ post.description }}</p>
          <Link :href="post.url" class="text-blue-500 hover:underline">{{readMoreLabel}}</Link>
        </div>
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
      posts: {
        type: Array,
        required: true,
      },
      readMoreLabel: {
        type: String,
        required: false,
        default: 'Leer más'
      },
      postWidth: {
        type: String,
        required: false,
        default: '24rem'
      }
    },
  };
  </script>
