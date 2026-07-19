<template>
    <component :is="href ? Link : 'div'" :href="href" class="card bg-base-100 shadow overflow-hidden group
                transition duration-300 hover:shadow-lg
                outline-gray-300 dark:outline-transparent outline-[0.4px] hover:outline
               relative" :class="(imageLeft ? 'flex-row' : '')
                + (draft ? ' bg-base-300' : '')
                " :preserve-page="preservePage" >
        <div v-if="image || $slots.image" class="shrink-0 overflow-hidden"
            :class="(imageLeft ? 'w-1/3 h-full ' : 'h-40 ') + imageClass">
            <div v-if="$slots.image" class="w-full h-full">
                <slot name="image" />
            </div>
            <div v-else-if="skeleton" class="skeleton w-full h-full rounded"></div>
            <div v-else-if="imageContained"
                class="w-full h-full relative transition duration-300 group-hover:scale-105"
                :class="skeleton ? 'skeleton' : ''">
                <div v-if="lqip" class="absolute inset-0 bg-center bg-cover"
                    :style="{ backgroundImage: `url(${lqip})` }" />
                <Image v-if="!lqipOnly" class="w-full h-full relative" :src="image" :lqip="lqip" :optimize="false" />
            </div>
            <div v-else class="w-full h-full bg-cover bg-center transition duration-300 group-hover:scale-110" :style="{
                'background-image': `url('${getImageUrl(image)}?cover&w=${imageWidth}${imageHeight != 'auto' ? '&h=' + imageHeight : ''}')`,
                'view-transition-name': imageViewTransitionName
            }" />
        </div>
        <div v-if="skeleton && (title || tag || description || date)" class="p-4 flex flex-col w-full gap-3">
            <div v-if="title" class="skeleton h-5 w-3/4 rounded" />
            <div v-if="tag" class="skeleton h-4 w-1/3 rounded" />
            <template v-if="description">
                <div class="skeleton h-3 w-full rounded" />
                <div class="skeleton h-3 w-11/12 rounded" />
                <div class="skeleton h-3 w-4/5 rounded" />
                <div class="skeleton h-3 w-2/3 rounded" />
            </template>
            <div v-if="date" class="skeleton h-3 w-16 rounded self-end mt-auto" />
            <slot />
        </div>
        <div v-else-if="title || tag || description || date || $slots.default" class="p-4 flex flex-col w-full">
            <h2 v-if="title"
                class="text-lg text-left font-bold mb-3 transition duration-300 group-hover:text-secondary!  group-hover:drop-shadow-xs leading-5"
                v-html="title" />
            <ConditionalLink tag="div" :is-link="!!tagLink" :href="tagLink" v-if="tag" class="flex justify-between mb-3 max-w-full"
            :preserve-state="preserveState"
            :preserve-page="preservePage"
            >
                <div class="truncate overflow-hidden inline-block badge badge-primary badge-outline max-w-48"
                :class="tagLink?'hover:text-secondary hover:drop-shadow-xs':''">{{
                    tag }}
                </div>
            </ConditionalLink>
            <div v-if="description"
                class="lg:opacity-75 transition duration-300 group-hover:opacity-90 text-sm text-ellipsis overflow-hidden "
                :class="(gradient ? ' text-gradient' : '') + ' ' + descriptionClass" v-html="descriptionFinal" />
            <TimeAgo v-if="date" :date="date" class="text-right mt-auto opacity-50" style="font-size: 60%" />
            <slot />
        </div>
    </component>
</template>

<script setup>
import { getImageUrl } from '@/composables/image.js'
import Link from '@/Components/Link.vue'

const props = defineProps({
    title: String,
    href: String,
    image: String,
    imageContained: Boolean,
    imageLeft: {
        type: Boolean,
        default: false
    },
    imageViewTransitionName: {
        type: String,
        default: ''
    },
    imageWidth: {
        type: [Number, String],
        default: 400
    },
    imageHeight: {
        type: [Number, String],
        default: 400
    },
    tag: String,
    tagLink: {type: String, default: ''},
    draft: { type: Boolean, default: false },
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
    },
    preserveState: { type: Boolean, default: false },
    preservePage: { type: Boolean, default: false },
    //autoScroll: { type: Boolean, default: true },
    gradient: { type: Boolean, default: false },
    skeleton: { type: Boolean, default: false },
    lqip: { type: String, default: null },
    lqipOnly: { type: Boolean, default: false },
})

const descriptionFinal = computed(() => {
    if (props.description.length < props.maxLength)
        return props.description

    return props.description.substring(0, props.maxLength - 3) + '...'
})
</script>


<style scoped>
.text-gradient {
    position: relative;

}
.text-gradient::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background-image: linear-gradient(to bottom, transparent 70%, var(--color-base-100) 100%);
    pointer-events: none;
}
</style>
