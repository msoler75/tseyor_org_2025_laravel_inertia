<template>
    <div class="container mx-auto">
      <div class="grid grid-cols-3 gap-4">
        <div v-for="member in teamMembers" :key="member.id" class="text-center">
          <img :src="member.avatar" class="h-32 w-32 mx-auto rounded-full mb-4" :alt="member.name" />
          <h3 class="text-xl font-bold mb-2">{{ member.name }}</h3>
          <p class="text-gray-600">{{ member.position }}</p>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      teamMembers: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
