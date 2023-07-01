<script setup>
  import { Link } from '@inertiajs/vue3';
  const props = defineProps({
    href: String,
    active: Boolean,
  });

  const baseClass = 'inline-flex items-center px-1 pt-1 border-b-4 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';

  const classes = computed(() => {
    return [
      baseClass,
      'text-gray-500 dark:text-gray-100 focus:text-gray-700 dark:focus:text-gray-300 focus:border-blue-300 dark:focus:border-blue-700 hover:border-blue-400 hover:dark:border-blue-600',
      props.active ? 'border-blue-400 dark:border-blue-600 active' : 'border-transparent'
    ].join(' ');
  });
</script>

<template>
  <component :is="href ? Link : 'div'" :href="href" :class="classes" class="text-blue-900 dark:text-blue-50">
    <slot />
  </component>
</template>
