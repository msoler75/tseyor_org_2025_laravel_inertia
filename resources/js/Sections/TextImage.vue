<template>
    <div :class="full ? 'pt-20! pb-6! w-full h-full flex flex-col justify-center full' : 'py-12'">
        <div class="text-image mx-auto text-center" :class="
            [   srcImage || imageSlotPresent? 'with-image grid grid-cols-1 md:grid-cols-2' : '',
                full ? 'w-full h-full' : 'container',
                gridClass,
                hasLongText ? 'long-text' : ''
            ]">
            <div v-if="srcImage || imageSlotPresent" class="caja-imagen flex flex-col justify-center items-center bg-center" :class="(imageRight ? 'md:order-last ' : '') +
                (full ? 'justify-center h-full ' : 'py-4 ') +
                (full && !cover ? 'relative ' : '') +
                (cover ? 'cover-mode ' : '') +
                (caption ? 'gap-2 ' : 'gap-1 ') +
                imageSideClass" :style="cover ? {
                'background-image': `url(${srcImageBackground})`
            } : {}">
                <slot v-if="imageSlotPresent" name="image" :class="imageClass"/>
                <template v-else>
                    <Image v-if="!cover" :src="srcImage" :alt="title" class="image-h" :class="[imageClass, caption ? 'has-caption' : '']" :width="srcWidth" :height="srcHeight"
                    :src-width="srcWidth" :src-height="srcHeight" :lazy="imageLazy" :root-margin="imageRootMargin" :priority="imagePriority"/>
                    <small v-if="caption" class="image-caption text-gray-600 dark:text-gray-200 bg-neutral-500/10">{{ caption }}</small>
                </template>
            </div>
            <div class="caja-texto flex flex-col items-center gap-4 mx-auto px-2 xs:px-4 md:px-8 max-w-xl pb-6"
                :class="(full ? 'justify-center sm:py-6 h-full ' : 'justify-evenly py-6 min-h-fit ') + textClass">
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
const cajaTexto = ref(null)
const textPresent = computed(() => !!slots.default && slots.default().length > 0)

// Resetear scroll al inicio y cuando cambie el contenido de texto
import { watch, nextTick, onMounted } from 'vue'

onMounted(() => {
    nextTick(() => {
        if (cajaTexto.value) cajaTexto.value.scrollTop = 0
    })
})

watch(textPresent, async () => {
    await nextTick()
    if (cajaTexto.value) cajaTexto.value.scrollTop = 0
})

// Detectar si hay mucho texto para ajustar el espacio de la imagen
const hasLongText = computed(() => {
    const slotContent = slots.default?.()
    if (!slotContent || slotContent.length === 0) return false

    // Función recursiva para extraer texto plano de VNodes
    const extractText = (vnodes) => {
        return vnodes.map(vnode => {
            if (typeof vnode === 'string') return vnode
            if (vnode.children) {
                if (typeof vnode.children === 'string') return vnode.children
                if (Array.isArray(vnode.children)) return extractText(vnode.children)
            }
            return ''
        }).join('')
    }

    const text = extractText(slotContent).trim()
    const textLength = text.length

    // Contar elementos que podrían ser párrafos (elementos con tag 'p' o saltos de línea)
    const paragraphCount = slotContent.filter(vnode =>
        vnode.type === 'p' || (typeof vnode === 'string' && vnode.includes('\n'))
    ).length

    return textLength > 300 || paragraphCount > 1
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
    /* Para uso normal, altura más moderada */
    max-height: 400px;
}

/* Cuando hay caption, reducir la altura de la imagen para hacer espacio */
.image-h.has-caption {
    max-height: 350px;
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
    /* Sin altura mínima para uso normal */
    align-items: start;
}

/* Solo cuando full=true, aplicar alturas de pantalla completa */
.full .with-image {
    min-height: min(70vh, var(--sectionHeight, 70vh));
    /* En móvil, distribución vertical equilibrada */
    grid-template-rows: minmax(300px, 1fr) minmax(200px, auto);
}

.full .image-h {
    max-height: min(50vh, 400px);
}

.full .image-h.has-caption {
    max-height: min(45vh, 350px);
}

/* En móvil, ajustamos para dar más espacio a la imagen si es necesario */
@media (max-width: 767px) {
    .full .with-image {
        grid-template-rows: minmax(250px, 1fr) minmax(150px, auto);
        gap: 1rem;
    }

    /* Ajustes para que la imagen se adapte y el caption sea visible en móvil */
    .caja-imagen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0;
        position: relative;
        min-height: 0;
        max-height: 55vh; /* limitar la caja para que la img no la sobrepase */
        overflow: hidden;
    }

    /* Asegurar que la imagen interna se escala dentro de la caja y no se recorta */
    .caja-imagen .image-h {
        max-height: 100%;
        height: auto;
        display: block;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        margin: 0 auto;
    }

    /* Poner el texto por encima visualmente cuando sea necesario */
    .caja-texto {
        position: relative;
        z-index: 2;
    }

    .caja-imagen {
        z-index: 1;
    }

    /* Evitar solapamiento: forzar que el grid apile imagen y texto y dar prioridad
       de altura a la celda de texto cuando ésta crece. Usar filas explícitas: imagen
       toma su alto natural (auto) y texto puede crecer (1fr). */
    .with-image {
        grid-template-columns: 1fr;
        /* Priorizar crecimiento de la fila de texto: imagen toma su alto natural (auto)
           y la fila de texto toma el resto con minmax(0, 1fr) para permitir shrink */
        grid-template-rows: auto minmax(0, 1fr);
        align-items: start;
    }

    /* La caja de imagen no debe forzar la altura del grid; permitir que se recorte */
    .with-image > .caja-imagen {
        min-height: 0; /* permitir que overflow:hidden funcione */
        overflow: hidden;
    }

    /* La caja de texto puede crecer y debe mantener su contenido visible */
    .with-image > .caja-texto {
        /* Permitir que la celda de texto crezca y si hace falta haga scroll internamente */
        min-height: 0;
        overflow: auto;
        position: relative;
        z-index: 3;
        justify-content: flex-start; /* asegurar inicio del contenido en móvil */
    }

    /* Si la caja usa background-image (cover mode), mostrar la imagen completa en móvil */
    .caja-imagen.cover-mode {
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain !important;
        padding: 0.5rem 0;
        min-height: 0;
        max-height: 55vh;
    }

    /* Asegurar que el caption aparece debajo y se ve */
    .caja-imagen .image-caption {
        order: 2;
        margin-top: 0.5rem;
    }

   /* Reducir espaciado entre párrafos en móvil */
    .caja-texto :deep(p) {
        margin-top: 0.75rem;
        line-height: 1.5;
    }

    .caja-texto :deep(p:last-child) {
        margin-bottom: 0;
    }

    .full .image-h {
        max-height: min(60vh, 500px);
    }

    .full .image-h.has-caption {
        max-height: min(50vh, 400px);
    }

    /* Forzar que la imagen dentro de la caja respete el tamaño del padre */
    .with-image .caja-imagen .image-h {
        max-height: 100% !important;
        height: auto;
        object-fit: contain;
    }

    /* Si la imagen tiene caption, dejar espacio para el caption dentro de la caja */
    .with-image .caja-imagen .image-h.has-caption {
        max-height: calc(100% - 3.5rem) !important;
    }

    .image-caption {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        max-width: 100%;
    }
}

/* Para pantallas muy pequeñas (menos de 400px) */
@media (max-width: 399px) {

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
        /* En desktop, tamaño más moderado para uso normal */
        max-height: 500px;
    }

    .image-h.has-caption {
        max-height: 450px;
    }

    /* Solo cuando full=true, usar alturas de pantalla completa */
    .full .image-h {
        max-height: min(80vh, calc(var(--sectionHeight) - 3rem), 600px);
        height: 100%;
    }

    .full .image-h.has-caption {
        max-height: min(70vh, calc(var(--sectionHeight) - 5rem), 500px);
        height: auto;
    }

    .full .with-image {
        /* En desktop, distribución horizontal */
        grid-template-rows: 1fr;
        align-items: center;
        min-height: min(80vh, var(--sectionHeight, 80vh));
    }

    /* Ajustar contenedores para mejor distribución del espacio */
    .full .caja-imagen {
        height: 100%;
        display: flex;
        align-items: center;
    }

    .full .caja-texto {
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
    .full .with-image.long-text .image-h {
        max-height: min(60vh, 450px);
    }

    .full .with-image.long-text {
        grid-template-columns: 1fr 1.2fr; /* Dar más espacio al texto */
    }
}

/* Optimización para pantallas grandes */
@media (min-width: 1024px) {
    .image-h {
        max-height: 400px;
    }

    .image-h.has-caption {
        max-height: 350px;
    }

    .full .image-h {
        max-height: min(85vh, 700px);
    }

    .full .image-h.has-caption {
        max-height: min(75vh, 600px);
    }

    .full .with-image.long-text .image-h {
        max-height: min(70vh, 500px);
    }

    .full .with-image.long-text .image-h.has-caption {
        max-height: min(60vh, 450px);
    }
}
</style>
