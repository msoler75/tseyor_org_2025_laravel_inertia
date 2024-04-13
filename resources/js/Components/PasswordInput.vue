<script setup>
defineProps({
    modelValue: String,
    required: {type: Boolean, default: false},
    autocomplete: String
});

defineEmits(['update:modelValue']);

const showPassword = ref(false)
const inputShow = ref(null);
const inputHidden = ref(null);


onMounted(() => {
    if (inputHidden.value.hasAttribute('autofocus')) {
        inputHidden.value.focus();
    }
});

function clickHandle() {
    showPassword.value=!showPassword.value
    const currentInput =  showPassword.value?inputShow.value:inputHidden.value
    nextTick(()=>{       
        currentInput.focus()
    })
}

defineExpose({ focus: () => currentInput.value.focus() });
</script>

<template>
    <div class="relative">
        <input
            v-show="showPassword"
            ref="inputShow"
            type="text"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :autocomplete="autocomplete"
        />
        <input
            v-show="!showPassword"
            ref="inputHidden"
            type="password"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :required="required"
            :autocomplete="autocomplete"
    />
        <div class="absolute right-3 top-0 flex justify-center items-center h-full dark:mix-blend-difference cursor-pointer" title="Mostrar/Ocultar contraseña" @click="clickHandle">
            <Icon v-show="!showPassword" icon="ph:eye-duotone"/>
            <Icon v-show="showPassword" icon="ph:eye-closed-duotone"/>
        </div>
    </div>
</template>
