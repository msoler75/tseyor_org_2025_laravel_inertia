<template>
    <div class="bg-hero bg-cover bg-center py-20">
      <div class="container mx-auto text-center">
        <h1 class="mb-4">{{ title }}</h1>
        <p class="text-lg">{{ subtitle }}</p>
        <Link v-if="buttonLabel&&href" :href="href" class="mt-8 btn btn-primary">
          {{ buttonLabel }}
        </Link>
      </div>
    </div>
  </template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    required: true,
  },
  buttonLabel: {
    type: String,
    required: false,
  },
  href: {
    type: String,
    required: false,
  },
})
</script>
