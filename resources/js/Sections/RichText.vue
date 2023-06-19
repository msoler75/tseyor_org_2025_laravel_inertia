<template>
    <div class="container mx-auto">
      <div class="prose max-w-3xl mx-auto" v-html="content"></div>
    </div>
  </template>

  <script>
  export default {
    props: {
      content: {
        type: String,
        required: true,
      },
    },
  };
  </script>
