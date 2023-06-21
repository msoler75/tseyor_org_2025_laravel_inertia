<template>
    <div class="container mx-auto py-5 flex justify-center">
        <iframe class="w-full max-w-[800px] aspect-video" :src="videoUrl" frameborder="0" allowfullscreen></iframe>
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
