<template>
    <div :class="full ? '!py-0 w-full h-full max-h-screen flex flex-col justify-center' : 'py-12'">
        <div class="mx-auto text-center" :class="(srcImage ? 'grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-12 ' : '') +
            (full ? 'w-full h-full p-0' : 'container')
            ">
            <div v-if="srcImage" class="flex flex-col items-center gap-1 max-h-screen bg-center" :class="(imageRight ? 'md:order-last ' : '') +
                (full ? 'justify-center ' : '') +
                (full && !cover ? 'pt-[100px] relative max-h-[calc(100vh - 220px)] ' : '')
                +imageSideClass" :style="cover ? {
        'background-image': `url(${srcImage})`,
        'background-size': 'cover'
    } : {}">
                <img v-if="!cover" :src="srcImage" :alt="title" :class="imageClass"
                    :style="{ 'max-height': 'calc(100% - 52px)' }">
                <small v-if="caption">{{ caption }}</small>
            </div>
            <div class="flex flex-col items-center gap-7" :class="(full?'justify-center ':'justify-evenly ')+textClass">
                <h2 v-if="title" class="text-2xl font-bold mb-0">{{ title }}</h2>
                <div v-if="subtitle" class="text-lg text-center my-0">
                    {{ subtitle }}
                </div>
                <div v-show="textPresent" class="prose my-5 mx-auto text-justify hyphens-auto" ref="textdiv">
                    <slot class="text-lg text-justify"></slot>
                </div>
                <Link v-if="buttonLabel && href" :href="href" class="my-2 btn btn-primary">
                {{ buttonLabel }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>


defineProps({
    title: {
        type: String,
        required: false
    },
    subtitle: {
        type: String,
        required: false
    },
    buttonLabel: {
        type: String,
        required: false
    },
    href: {
        type: String,
        required: false
    },
    srcImage: {
        type: String,
        required: false
    },
    imageRight: {
        type: Boolean,
        required: false,
        default: false
    },
    imageClass: {
        type: String,
        required: false
    },
    imageSideClass: {
        type: String,
        required: false
    },
    textClass: {
        type: String,
        required: false,
        default: "container gap-5"
    },
    full: {
        type: Boolean,
        required: false,
        default: false
    },
    cover: {
        type: Boolean,
        default: false
    },
    caption: {
        type: String
    }
})

const textdiv = ref(null)
const textPresent = computed(() => textdiv.value && textdiv.value.children.length)
</script>
