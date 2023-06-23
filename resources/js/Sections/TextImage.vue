<template>
    <div class="py-12">
        <div class="container mx-auto text-center" :class="srcImage ? 'grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-12' : ''">
            <div v-if="srcImage" class="flex flex-col items-center gap-1" :class="imageRight ? 'md:order-last' : ''">
                <img :src="srcImage" :alt="title" class="max-h-[400px]" :class="imageClass" >
                <small v-if="caption">{{caption}}</small>
            </div>
            <div class="flex flex-col items-center" :class="textClass">
                <h2 v-if="title" class="text-2xl font-bold mb-0">{{ title }}</h2>
                <div v-if="subtitle" class="text-lg text-center my-0">
                    {{ subtitle }}
                </div>
                <div v-show="textPresent" class="prose my-5 mx-auto text-justify hyphens-auto" ref="textdiv"><slot class="text-lg text-justify"></slot></div>
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
        required: false,
    },
    subtitle: {
        type: String,
        required: false,
    },
    buttonLabel: {
        type: String,
        required: false,
    },
    href: {
        type: String,
        required: false,
    },
    srcImage: {
        type: String,
        required: false,
        default: null
    },
    imageRight: {
        type: Boolean,
        required: false,
        default: false
    },
    imageClass: {
        type: String,
        required: false,
        default: null
    },
    textClass: {
        type: String,
        required: false,
        default: "justify-evenly gap-5"
    },
    caption: {
        type: String
    }
})

const textdiv = ref(null)
const textPresent = computed(()=>textdiv&&textdiv.value&&textdiv.value.children.length)
</script>
