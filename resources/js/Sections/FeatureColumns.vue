<template>
    <div class="container mx-auto py-12">
        <div class="grid gap-8 mx-auto place-items-stretch" :class="gridClass">
            <div v-for="feature in features" :key="feature.title"
                class="p-7 bg-base-100 flex flex-col rounded-xs shadow-2xs justify-start items-center gap-7 max-w-[24rem]">
                <Icon :icon="feature.icon" class="text-4xl text-blue-400" :class="feature.iconClass" :alt="feature.title" />
                <h3 class="text-xl font-bold">{{ feature.title }}</h3>
                <div v-if="feature.description" class="opacity-80 text-center">{{ feature.description }}</div>
                <div v-if="feature.text" class="opacity-80" v-html="feature.text" />
                <Link v-if="feature.link" class="btn btn-primary" :href="feature.link.url" >{{ feature.link.label }}</Link>
            </div>
        </div>
    </div>
</template>

<script setup>


const props = defineProps({
    features: {
        type: Array,
        required: true,
    },
})

const gridClass = computed(() => {

    switch (props.features.length) {
        case 1:
            return 'grid-cols-1 sm:max-w-sm'
        case 2:
            return 'grid-cols-2 lg:max-w-[1024px]'
        case 3:
            return 'grid-cols-2 md:grid-cols-3'
        default:
            return 'grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
    }

})
</script>

