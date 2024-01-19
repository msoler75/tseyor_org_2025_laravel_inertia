<template>
    <div :class="full ? '!py-0 w-full h-full flex flex-col justify-center' : 'py-12'">
        <div class="mx-auto text-center" :class="(srcImage ? 'with-image grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-12 ' : '') +
            (full ? 'w-full h-full p-0' : 'container')
            ">
            <div v-if="srcImage" class="flex flex-col justify-center items-center gap-1 max-h-full bg-center" :class="(imageRight ? 'md:order-last ' : '') +
                (full ? 'justify-center ' : '') +
                (full && !cover ? 'relative ' : '')
                + imageSideClass" :style="cover ? {
        'background-image': `url(${srcImage})`,
        'background-size': 'cover'
    } : {}">
                <Image v-if="!cover" :src="srcImage" :alt="title" class="image-h bg-red-100" :class="imageClass"/>
                <small v-if="caption" class="container">{{ caption }}</small>
            </div>
            <div class="flex flex-col items-center gap-7 container mx-auto"
                :class="(full ? 'justify-center ' : 'justify-evenly ') + textClass">
                <h2 v-if="title" class="text-2xl font-bold mb-0">{{ title }}</h2>
                <div v-if="subtitle" class="text-lg text-center my-0">
                    {{ subtitle }}
                </div>
                <div v-show="textPresent" class="prose md:my-5 text-justify hyphens-auto" ref="textdiv">
                    <slot class="text-lg text-justify"></slot>
                </div>
                <a v-if="buttonLabel && href && href.match(/\.(pdf|mp3|mp4|docx|jp?eg|png|webp|ppt|pps)$/i)" :href="href"
                    class="my-2 btn btn-primary flex gap-3" download>
                    <Icon icon="ph:download-duotone" /> {{ buttonLabel }}
                </a>
                <ActionButton v-else-if="buttonLabel && href" :href="href" class="my-2">
                {{ buttonLabel }}
                </ActionButton>
                <span v-if="buttonLabel && href" class="md:hidden"></span>
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

<style scoped>
.image-h {
    max-height: calc(var(--sectionHeight) *.6 - 2rem);
}

.with-image {
grid-template-rows: min(60fr, var(--sectionHeight)) 40fr;
}

@screen md {
    .image-h {
        max-height: calc(var(--sectionHeight) - 5rem);
    }

    .with-image {
        grid-template-rows: 1fr;
    }
}

</style>
