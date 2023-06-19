<template>
    <div class="container mx-auto">
      <div class="aspect-w-16 aspect-h-9">
        <iframe :src="videoUrl" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      videoUrl: {
        type: String,
        required: true,
      },
    },
  };
  </script>
