<script setup>
// Modelo compatible con v-model para Vue 3.4+ (usando defineModel())
const model = defineModel()
// Para Vue <=3.3 usa: defineProps(['modelValue']); defineEmits(['update:modelValue']);

// Si quieres una prop placeholder personalizada:
defineProps({
  placeholder: { type: String, default: 'Buscar...' }
})
</script>

<template>
    <input
      type="search"
      v-model="model"
      :placeholder="placeholder"
      class="w-full pr-10 [&::-webkit-search-cancel-button]:hidden"
    >
    <button
      v-if="model"
      type="button"
      @click="model = ''"
      class="absolute right-2 top-1/2 -translate-y-1/2 p-0 cursor-pointer"
      tabindex="-1"
      aria-label="Borrar búsqueda"
    >
    <Icon icon="jam:rubber" class="text-primary"/>
    </button>
</input>
</template>
