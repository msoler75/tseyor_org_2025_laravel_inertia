<template>
    <div :class="full ? 'py-0! w-full h-full flex flex-col justify-center' : 'py-12'">
        <div class="mx-auto text-center" :class="(srcImage || imageSlotPresent? 'with-image flex flex-col md:grid md:grid-cols-2 gap-7 lg:gap-12 ' : '') +
            (full ? 'w-full h-full p-0' : 'container') + ' '+gridClass
            ">
            <div v-if="srcImage || imageSlotPresent" class="flex flex-col justify-center items-center gap-1 max-h-full bg-center" :class="(imageRight ? 'md:order-last ' : '') +
                (full ? 'justify-center grow ' : '') +
                (full && !cover ? 'relative ' : '')
                + imageSideClass" :style="cover ? {
        'background-image': `url(${srcImageBackground})`,
        'background-size': 'cover'
    } : {}">
                <slot v-if="imageSlotPresent" name="image" :class="imageClass"/>
                <template v-else>
                    <Image v-if="!cover" :src="srcImage" :alt="title" class="image-h" :width="srcWidth" :height="srcHeight"
                    :src-width="srcWidth" :src-height="srcHeight" :class="imageClass" :lazy="imageLazy"/>
                    <small v-if="caption" class="container">{{ caption }}</small>
                </template>
            </div>
            <div class="flex flex-col items-center gap-7 mx-auto pb-5 lg:pb-0"
                :class="(full ? 'justify-center ' : 'justify-evenly ') + textClass">
                <h2 v-if="title" class="text-2xl text-primary font-bold mb-0">{{ title }}</h2>
                <div v-if="subtitle" class="text-lg text-center my-0" v-html="subtitle.replace(/\\n/g, '<br /><br />')"/>
                <div v-show="textPresent" class="md:my-5 text-justify" ref="textdiv">
                    <slot class="text-lg text-justify"></slot>
                </div>
                <a v-if="buttonLabel && href && href.match(/\.(pdf|mp3|mp4|docx|jp?eg|png|webp|ppt|pps)$/i)" :href="href"
                    class="my-2 btn btn-primary flex gap-3" download>
                    <Icon icon="ph:download-duotone" /> {{ buttonLabel }}
                </a>
                <ActionButton v-else-if="buttonLabel && href" :href="href" class="my-2 min-w-[10rem]">
                    {{ buttonLabel }}
                </ActionButton>
                <span v-if="buttonLabel && href" class="md:hidden"></span>
            </div>
        </div>
    </div>
</template>

<script setup>
import  {useSlots} from 'vue'

const props = defineProps({
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
    srcWidth: {
        type: [Number, String],
        required: false,
        default: null
    },
    srcHeight: {
        type: [Number, String],
        required: false,
        default: null
    },
    caption: {
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
        required: false
    },
    imageSideClass: {
        type: String,
        required: false
    },
    gridClass: {
        type: String,
        required: false,
        default: "gap-5"
    },
    textClass: {
        type: String,
        required: false,
        default: "gap-5"
    },
    full: { // full screen
        type: Boolean,
        required: false,
        default: false
    },
    cover: { // image cover all area
        type: Boolean,
        default: false
    },
    imageLazy: {
        type: Boolean,
        default: true
    }
})


const slots = useSlots()
const imageSlotPresent = computed(() => !!slots.image)

const textdiv = ref(null)
const textPresent = computed(() => textdiv.value && (textdiv.value.children.length || textdiv.value.innerText))


const srcImageBackground = computed(() => {
    // mejor no:
    //if (props.srcImage.match(/[\?&]w=\d+/)) return props.srcImage
    //return props.srcImage + '?mw=' + screen.width*1.6
    return props.srcImage.replace(/ /g, '%20')
})
</script>

<style scoped>
.image-h {
    max-height: calc(var(--sectionHeight) *.8 - 2rem);
    width: 100%;
    height: auto;
    object-fit: contain;
}

.with-image {
    grid-template-rows: min(60fr, var(--sectionHeight)) 40fr;
}

@media (min-width: 768px) {
    .image-h {
        max-height: calc(var(--sectionHeight) - 5rem);
    }

    .with-image {
        grid-template-rows: 1fr;
    }
}
</style>
