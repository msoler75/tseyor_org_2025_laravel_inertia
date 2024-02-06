<template>
    <component :is="href?Link:'div'" :href="href" class="card bg-base-100 shadow overflow-hidden group
                transition duration-300 hover:shadow-lg
                outline-gray-300 dark:outline-transparent outline-[0.4px] hover:outline
               relative" :class="(imageLeft ? 'flex-row' : '')
               +(draft?' bg-base-300':'')
               ">
        <div class="flex-shrink-0 overflow-hidden" :class="(imageLeft ? 'w-1/3 h-full ' : 'h-40 ') + imageClass">
            <div class="w-full h-full bg-cover bg-center transition duration-300 group-hover:scale-110" :style="{
                'background-image': 'url(\'' + getImageUrl(image) + '?w=300\')'
            }" />
        </div>
        <div v-if="title || tag || description || date" class="p-4 flex flex-col w-full">
            <h2 v-if="title"
                class="text-lg font-bold mb-3 transition duration-300 group-hover:text-primary  group-hover:drop-shadow"
                v-html="title" />
            <div v-if="tag" class="flex justify-between mb-3 max-w-full">
                <div class="badge badge-primary badge-outline h-fit">{{ tag }} </div>
            </div>
            <div v-if="description" class="text-gradient lg:opacity-50 transition duration-300 group-hover:opacity-90 text-sm text-ellipsis overflow-hidden "
            :class="descriptionClass"
                v-html="descriptionFinal" />
            <TimeAgo v-if="date" :date="date" class="text-right mt-auto opacity-50" style="font-size: 60%" />
        </div>
        <slot />
    </component>
</template>

<script setup>
import { getImageUrl } from '@/composables/imageutils.js'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    imageLeft: {
        type: Boolean,
        default: false
    },
    title: String,
    href: String,
    image: String,
    draft: {type: Boolean, default: false},
    tag: String,
    tagLink: String,
    description: String,
    maxLength: { type: Number, default: 1024 },
    date: String,
    imageClass: {
        type: String,
        default: ''
    },
    descriptionClass: {
        type: String,
        default: ''
    }
})

const descriptionFinal = computed(() => {
    if (props.description.length < props.maxLength)
        return props.description

    return props.description.substring(0, props.maxLength - 3) + '...'
})
</script>


<style scoped>
  .text-gradient {
    height: 100%;
    background-image: linear-gradient(to bottom, #000 0%, #000 70%, #fff 95%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
.dark .text-gradient {
    background-image: linear-gradient(to bottom,#fff 0%,#fff 70%,#0000 95%)
  }
  </style>
