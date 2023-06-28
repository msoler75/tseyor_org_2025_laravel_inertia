<template>
    <div class="testimonial">
      <div class="testimonial-photo">
        <img :src="testimonial.photo" alt="Testimonial photo" />
      </div>
      <div class="testimonial-text">
        <p>{{ testimonial.text }}</p>
      </div>
      <div class="testimonial-author">
        <div class="testimonial-author-name">{{ testimonial.name }}</div>
        <div class="testimonial-author-role">{{ testimonial.role }}</div>
      </div>
    </div>
  </template>

  <script setup>
  import { defineProps } from 'vue';

  const props = defineProps({
    testimonial: {
      type: Object,
      required: true,
    },
  });
  </script>

  <style scoped>
  .testimonial {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
  }

  .testimonial-photo {
    margin-bottom: 1rem;
  }

  .testimonial-photo img {
    border-radius: 50%;
  }

  .testimonial-text {
    margin-bottom: 1rem;
  }

  .testimonial-author-name {
    font-weight: bold;
  }

  </style>
