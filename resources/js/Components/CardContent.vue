<template>
    <component :is="href?Link:'div'" :href="href" class="card bg-base-100 shadow overflow-hidden group
                transition duration-300 hover:shadow-lg
                outline-gray-300 dark:outline-transparent outline-[0.4px] hover:outline
               relative" :class="(imageLeft ? 'flex-row' : '')
               +(draft?' bg-base-300':'')
               "
               :preserve-page="preservePage"
               :auto-scroll="autoScroll">
        <div v-if="image" class="flex-shrink-0 overflow-hidden" :class="(imageLeft ? 'w-1/3 h-full ' : 'h-40 ') + imageClass">
            <div v-if="skeleton" class="skeleton w-full h-full rounded-none"></div>
            <div v-else-if="imageContained" class="w-full h-full bg-center transition duration-300 group-hover:scale-105"
            :class="skeleton?'skeleton':''">
                <Image class="w-full h-full" :src="image"/>
            </div>
            <div v-else class="w-full h-full bg-cover bg-center transition duration-300 group-hover:scale-110" :style="{
                'background-image': `url('${getImageUrl(image)}?cover&w=${imageWidth}${imageHeight!='auto'?'&h='+imageHeight:''}')`,
                'view-transition-name': imageViewTransitionName
            }"/>
        </div>
        <div v-if="skeleton && (title || tag || description || date)" class="space-y-2 p-4 flex flex-col w-full">
            <div v-if="title" class="skeleton max-w-full w-[40ch] h-[1.5rem] mb-3" />
            <div v-if="tag" class="skeleton max-w-full w-[12ch] h-[1.25rem]"/>
            <template v-if="description">
            <div class="skeleton w-full h-[1rem]"/>
            <div class="skeleton w-full h-[1rem]"/>
            <div class="skeleton w-full h-[1rem]"/>
            <div class="skeleton w-full h-[1rem]"/>
            </template>
            <div v-if="date" class="skeleton inline ml-auto mt-auto w-[4rem] h-[.8rem]" />
        </div>
        <div v-else-if="title || tag || description || date" class="p-4 flex flex-col w-full">
            <h2 v-if="title"
                class="text-lg text-left font-bold mb-3 transition duration-300 group-hover:!text-secondary  group-hover:drop-shadow leading-5"
                v-html="title" />
            <div v-if="tag" class="flex justify-between mb-3 max-w-full">
                <div class="truncate overflow-hidden inline-block badge badge-primary badge-outline max-w-[12rem]">{{ tag }}</div>
            </div>
            <div v-if="description" class="lg:opacity-50 transition duration-300 group-hover:opacity-90 text-sm text-ellipsis overflow-hidden "
            :class="(gradient?' text-gradient':'')+' '+descriptionClass"
                v-html="descriptionFinal" />
            <TimeAgo v-if="date" :date="date" class="text-right mt-auto opacity-50" style="font-size: 60%" />
        </div>
        <slot />
    </component>
</template>

<script setup>
import { getImageUrl } from '@/composables/imageutils.js'
import  Link  from '@/Components/Link.vue'

const props = defineProps({
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
        default: 300
    },
    imageHeight: {
        type: [Number, String],
        default: 300
    },
    imageContained: Boolean,
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
    },
    preservePage: {type: Boolean, default: false},
    autoScroll:  {type: Boolean, default: true},
    gradient: {type: Boolean, default: true},
    skeleton: {type: Boolean, default: false}
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
