<template>
    <input type="checkbox" class="form-checkbox" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" required>
    <span class="ml-2"> Estoy de acuerdo y he leído la
    <a target="_blank" href="/informacion-legal#Política de privacidad" class="underline">política de privacidad</a>.</span>
</template>

<script setup>
defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);
</script>
