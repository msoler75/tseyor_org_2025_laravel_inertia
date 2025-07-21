<template>
    <div :class="full ? 'pt-20! pb-6! w-full h-full flex flex-col justify-center' : 'py-12'">
        <div class="mx-auto text-center" :class="
            [   srcImage || imageSlotPresent? 'with-image grid grid-cols-1 md:grid-cols-2' : '',
                full ? 'w-full h-full' : 'container',
                gridClass,
                hasLongText ? 'long-text' : ''
            ]">
                <div v-if="srcImage || imageSlotPresent" class="caja-imagen flex flex-col justify-center items-center bg-center" :class="(imageRight ? 'md:order-last ' : '') +
                (full ? 'justify-center h-full ' : 'py-4 ') +
                (full && !cover ? 'relative ' : '') +
                (caption ? 'gap-2 ' : 'gap-1 ') +
                imageSideClass" :style="cover ? {
        'background-image': `url(${srcImageBackground})`,
        'background-size': 'cover'
    } : {}">
                <slot v-if="imageSlotPresent" name="image" :class="imageClass"/>
                <template v-else>
                    <Image v-if="!cover" :src="srcImage" :alt="title" class="image-h" :class="[imageClass, caption ? 'has-caption' : '']" :width="srcWidth" :height="srcHeight"
                    :src-width="srcWidth" :src-height="srcHeight" :lazy="imageLazy" :root-margin="imageRootMargin" :priority="imagePriority"/>
                    <small v-if="caption" class="image-caption text-gray-600 dark:text-gray-200 bg-neutral-500/10">{{ caption }}</small>
                </template>
            </div>
            <div class="caja-texto flex flex-col items-center gap-4 mx-auto px-4 xs:px-8 md:px-12 max-w-xl pb-16 md:pb-6"
                :class="(full ? 'justify-center py-6 h-full ' : 'justify-evenly py-6 min-h-fit ') + textClass">
                <h2 v-if="title" class="text-2xl text-primary font-bold mb-0" :class="titleClass">{{ title }}</h2>
                <div v-if="subtitle" class="text-lg text-center my-0" v-html="subtitle.replace(/\\n/g, '<br /><br />')"/>
                <div v-show="textPresent" class="md:my-5 text-justify" ref="textdiv">
                    <slot class="text-lg text-justify"></slot>
                </div>
                <slot v-if="actionSlotPresent" name="action"/>
                <a v-else-if="buttonLabel && href && href.match(/\.(pdf|mp3|mp4|docx|jp?eg|png|webp|ppt|pps)$/i)" :href="href"
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
        default: ""
    },
    titleClass: {
        type: String,
        required: false,
        default: ""
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
    },
    imageRootMargin: {
        type: String,
        default: "3000px 0px 3000px 0px"
    },
    imagePriority: {
        type: Boolean,
        default: false
    }
})


const slots = useSlots()
const imageSlotPresent = computed(() => !!slots.image)
const actionSlotPresent = computed(() => !!slots.action)

const textdiv = ref(null)
const textPresent = computed(() => textdiv.value && (textdiv.value.children.length || textdiv.value.innerText))

// Detectar si hay mucho texto para ajustar el espacio de la imagen
const hasLongText = computed(() => {
    if (!textdiv.value) return false
    const textLength = textdiv.value.innerText?.length || 0
    const hasMultipleParagraphs = textdiv.value.children.length > 1
    return textLength > 300 || hasMultipleParagraphs
})


const srcImageBackground = computed(() => {
    // mejor no:
    //if (props.srcImage.match(/[\?&]w=\d+/)) return props.srcImage
    //return props.srcImage + '?mw=' + screen.width*1.6
    return props.srcImage.replace(/ /g, '%20')
})
</script>

<style scoped>
.image-h {
    width: 100%;
    max-width: 100%;
    height: auto;
    object-fit: contain;
    /* Permitir que la imagen crezca más en móvil */
    max-height: min(50vh, 400px);
}

/* Cuando hay caption, reducir la altura de la imagen para hacer espacio */
.image-h.has-caption {
    max-height: min(45vh, 350px);
}

.image-caption {
    display: block;
    width: 100%;
    max-width: 600px;
    margin: 0.5rem auto 0;
    padding: 0.25rem 1rem;
    font-size: 0.875rem;
    line-height: 1.4;
    text-align: center;
    word-wrap: break-word;
    hyphens: auto;
    flex-shrink: 0;
}

.with-image {
    min-height: min(70vh, var(--sectionHeight, 70vh));
    /* En móvil, distribución vertical equilibrada */
    grid-template-rows: minmax(300px, 1fr) minmax(200px, auto);
    align-items: start;
}

/* En móvil, ajustamos para dar más espacio a la imagen si es necesario */
@media (max-width: 767px) {
    .with-image {
        grid-template-rows: minmax(250px, 1fr) minmax(150px, auto);
        gap: 1rem;
    }

    /* Optimización del espacio para párrafos en móvil */
    .caja-texto {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-bottom: 4rem; /* Espacio extra para evitar solapamiento con flecha */
    }

    /* Reducir espaciado entre párrafos en móvil */
    .caja-texto :deep(p) {
        margin-top: 0.75rem;
        line-height: 1.5;
    }

    .caja-texto :deep(p:last-child) {
        margin-bottom: 0;
    }

    .image-h {
        max-height: min(60vh, 500px);
    }

    .image-h.has-caption {
        max-height: min(50vh, 400px);
    }

    .image-caption {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        max-width: 100%;
    }
}

/* Para pantallas muy pequeñas (menos de 400px) */
@media (max-width: 399px) {
    .caja-texto {
        padding-left: 1rem;
        padding-right: 1rem;
        padding-bottom: 5rem; /* Más espacio en pantallas muy pequeñas */
    }

    /* Párrafos aún más compactos en pantallas muy pequeñas */
    .caja-texto :deep(p) {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        line-height: 1.45;
    }

    /* Reducir gap entre elementos */
    .caja-texto {
        gap: 0.75rem;
    }
}

@media (min-width: 768px) {
    .image-h {
        /* En desktop, permitir que la imagen sea más grande */
        max-height: min(80vh, calc(var(--sectionHeight) - 3rem), 600px);
        height: 100%;
    }

    .image-h.has-caption {
        max-height: min(70vh, calc(var(--sectionHeight) - 5rem), 500px);
        height: auto;
    }

    .with-image {
        /* En desktop, distribución horizontal */
        grid-template-rows: 1fr;
        align-items: center;
        min-height: min(80vh, var(--sectionHeight, 80vh));
    }    /* Ajustar contenedores para mejor distribución del espacio */
    .caja-imagen {
        height: 100%;
        display: flex;
        align-items: center;
    }

    .caja-texto {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .image-caption {
        font-size: 0.9rem;
        max-width: 500px;
        margin-top: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
    }
}

/* Cuando hay mucho texto, ajustar la imagen */
@media (min-width: 768px) {
    .with-image.long-text .image-h {
        max-height: min(60vh, 450px);
    }

    .with-image.long-text {
        grid-template-columns: 1fr 1.2fr; /* Dar más espacio al texto */
    }
}

/* Optimización para pantallas grandes */
@media (min-width: 1024px) {
    .image-h {
        max-height: min(85vh, 700px);
    }

    .image-h.has-caption {
        max-height: min(75vh, 600px);
    }

    .with-image.long-text .image-h {
        max-height: min(70vh, 500px);
    }

    .with-image.long-text .image-h.has-caption {
        max-height: min(60vh, 450px);
    }
}
</style>
