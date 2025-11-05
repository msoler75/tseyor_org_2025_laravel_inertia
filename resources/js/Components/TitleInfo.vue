<template>
    <!-- Encabezado -->
    <div class="flex flex-wrap gap-x-12 gap-y-5 items-center mb-16 lg:mb-24" :class="classes">
        <h1 v-if="title" class="mb-0">{{ title }}</h1>
        <span
            class="flex items-center gap-1 text-sm uppercase btn btn-sm dark:border-1 dark:border-gray-500/25"
            @click="modalInfo = true"
        >
            {{ labelInfo }}<Icon icon="ph:info"/></span>
    </div>

    <Modal
        title="¿Qué es la Biblioteca Tseyor?"
        class="max-w-3xl"
        :show="modalInfo"
        @close="modalInfo = false"
        :z-index="30"
        modal-class="mt-20"
    >
        <div class="p-5 sm:p-12 bg-base-200">
            <Prose class="mx-auto space-y-5">
                <slot />
            </Prose>
            <div class="mt-10 flex justify-center">
                <span
                    class="btn btn-primary cursor-pointer"
                    @click="modalInfo = false"
                    >Entendido</span
                >
            </div>
        </div>
    </Modal>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    labelInfo: {
        type: String,
        default: "¿qué son?"
    },
    classes: {
        type: String,
        default: '',
    },
});

const modalInfo = ref(false);

const nav = useNav()

watch(()=>nav.navigating, (value) => {
    if (value) {
        modalInfo.value = false
    }
})
</script>
