<template>
    <div class="container mx-auto">
      <div class="flex justify-center">
        <div class="max-w-lg mx-auto">
          <div v-for="testimonial in testimonials" :key="testimonial.id" class="p-6 bg-white rounded shadow mb-8">
            <p class="text-gray-600 mb-4">{{ testimonial.text }}</p>
            <div class="flex items-center">
              <img :src="testimonial.avatar" class="h-10 w-10 rounded-full mr-4" alt="Testimonial Avatar" />
              <div>
                <h4 class="font-bold">{{ testimonial.name }}</h4>
                <p class="text-sm text-gray-500">{{ testimonial.subtitle }}</p>
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
      testimonials: {
        type: Array,
        required: true,
      },
    },
  };
  </script>
