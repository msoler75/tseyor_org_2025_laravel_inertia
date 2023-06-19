<template>
    <div class="container mx-auto">
      <div class="space-y-4">
        <div v-for="(item, index) in faqItems" :key="index">
          <div @click="toggleFAQ(index)" class="flex items-center justify-between px-4 py-2 bg-gray-100 cursor-pointer">
            <h3 class="text-lg font-semibold">{{ item.question }}</h3>
            <svg :class="isOpen[index] ? 'transform rotate-180' : ''" class="w-5 h-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
              <path v-if="isOpen[index]" fill-rule="evenodd" clip-rule="evenodd" d="M10 14.828L5.757 10.586 4.343 12l5.657 5.657L16.657 12l-1.414-1.414L10 14.828z" />
              <path v-else fill-rule="evenodd" clip-rule="evenodd" d="M10 5.172L14.243 9.414 15.657 8l-5.657-5.657L4.343 8 5.757 9.414 10 5.172z" />
            </svg>
          </div>
          <div v-show="isOpen[index]" class="px-4 py-2 bg-white">
            <p class="text-gray-600">{{ item.answer }}</p>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      faqItems: {
        type: Array,
        required: true,
      },
    },
    data() {
      return {
        isOpen: [],
      };
    },
    methods: {
      toggleFAQ(index) {
        this.isOpen[index] = !this.isOpen[index];
      },
    },
  };
  </script>
