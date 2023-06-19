<template>
    <div class="container mx-auto">
      <div class="space-y-4">
        <h2 class="text-3xl font-semibold">{{ title }}</h2>
        <p class="text-gray-600">{{ description }}</p>
        <div class="flex items-center space-x-4">
          <img :src="image" class="h-32 w-32 rounded-full" :alt="alt" />
          <div>
            <h3 class="text-lg font-semibold">{{ teamMember }}</h3>
            <p class="text-gray-600">{{ position }}</p>
          </div>
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
      description: {
        type: String,
        required: true,
      },
      image: {
        type: String,
        required: true,
      },
      alt: {
        type: String,
        required: true,
      },
      teamMember: {
        type: String,
        required: true,
      },
      position: {
        type: String,
        required: true,
      },
    },
  };
  </script>
