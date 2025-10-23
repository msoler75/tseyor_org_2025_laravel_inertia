<template>
    <div class="text-justify">
        <input type="checkbox" class="form-checkbox transform scale-125 translate-x-0.5 -translate-y-1" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)" required>
        <span class="ml-1"> He leído y acepto la <a target="_blank" href="/informacion-legal#Política de privacidad" class="underline">política de privacidad</a>
            y doy mi consentimiento para el tratamiento de mis datos personales por parte de TSEYOR con la finalidad de {{ finalidad }}.</span>
        </div>
    <div class="text-xs text-justify">Puede ejercer sus derechos de acceso, rectificación, supresión y otros derechos de protección de datos contactando con <a href="mailto:secretaria@tseyor.org">secretaria@tseyor.org</a></div>
</template>

<script setup>
defineProps({
    modelValue: Boolean,
    finalidad: String,
});

defineEmits(['update:modelValue']);
</script>
