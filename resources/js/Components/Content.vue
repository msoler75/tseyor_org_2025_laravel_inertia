<template>
    <Prose ref="container" class="text-container">
        <!--
            <Markdown v-if="isMarkdown" :source="content" :html="true" :linkify="true" />
        -->
        <pre>
            {{parts}}
        </pre>
        <template v-if="1" v-for="part of parts">
            <!--<VueShowdown v-else-if="isMarkdown" :markdown="part.text" flavor="github" :options="{ emoji: true }"/> -->
            <Image v-if="part.type == 'image'" :src="part.attributes.src" :alt="part.attributes.alt" :title="part.attributes.title"
            :width="part.attributes.width" :height="part.attributes.height" :style="part.attributes.style"
            @click="handlePreview(part.index)" class="cursor-pointer"/>
            <div v-else v-html="part.text" />
        </template>
    </Prose>
</template>

<script setup>
// import { v3ImgPreviewFn } from 'v3-img-preview'
// import Markdown from 'vue3-markdown-it';
import { detectFormat, MarkdownToHtml } from '@/composables/markdown.js'
// import { VueShowdown } from 'vue-showdown';

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    format: {
        type: String,
        default: ''
    }
});

const isMarkdown = computed(() => props.format == 'md' ? true : ['md', 'ambiguous'].includes(detectFormat(props.content).format))
const container = ref(null)
const images = ref([]) // imagenes del contenido

var  v3ImgPreviewFn = null

const contentPreProcessed = computed(()=> isMarkdown.value? MarkdownToHtml(props.content) : props.content)

// hay que hacer todo esto para reemplazar las etiquetas IMG por componentes Image
const parts = computed(()=>{
    const r = []
    // hace un bucle recorriendo el string contentPreProcessed y buscando la posición del proximo '<img...>'
    // extrae esa etiqueta y divide lo que había antes y después de img
    // todo eso va guardando y así combinando en un array compuesto de partes de texto, e imagenes, intercalándose
    let i = 0
    let contentLeft = contentPreProcessed.value
    console.log('*******PARSE parts begin', contentLeft)
    images.value = []
    while (contentLeft.length) {
        console.log('*parsing', contentLeft)
        // busca <img[^>]+\/?> y necesitamos tanto la posicion inicial como la final
        const m = contentLeft.match(/<img[^>]+\/?>/)
        let beginOfImg = m ? m.index: -1
        var text = ""
        console.log('beginOfImg', beginOfImg)
        if(beginOfImg==-1)   // no hay más imagenes
        {
            // extrae el texto restante
            text = contentLeft
            contentLeft = ''

            r.push({type: 'text', text})
        }
        else {
            // extrae el texto restante
            text = contentLeft.substring(0, beginOfImg)

            r.push({type: 'text', text})

            // extrae la imagen
            const imgTag = contentLeft.substring(beginOfImg, m.index + m[0].length)
            const exp = /(\w+)=("[^"]+"|'[^']+')/g;

            let match;
            const attributes = {};

            while ((match = exp.exec(imgTag)) !== null) {
                console.log('attribute found', match)
                const name = match[1]
                const value = match[2].slice(1, -1) // valor sin comillas
                attributes[name] = value
            }

            // index es para indexar la imagen en la posición dentro de images
            r.push({type: 'image', attributes, index: images.value.length})

            // agregamos la imagen para la vista previa
            images.value.push(attributes.src.replace(/\?.*/, ''))

            // recortamos el texto original a partir de aquí para seguir buscando partes
            contentLeft = contentLeft.substring(m.index + m[0].length)
        }
    }

    return r
})

onMounted(async () => {

    // importación dinámica:
    await import('v3-img-preview').then((module) => {
        v3ImgPreviewFn = module.v3ImgPreviewFn;

        nextTick(() => {


            // AÑADIR ENLACES A NOTAS AL PIE, Y VICEVERSA

            // ESTO SOLO FUNCIONA PARA UN PARSE DE MARKDOWN DE OTRA LIBRERIA

            // ACTUALMENTE NO HACE NADA

            // Obtener todos los enlaces de desplazamiento
            var scrollLinks = document.querySelectorAll('.footnote-ref a, a.footnote-backref');

            console.log({ scrollLinks })

            // Agregar evento de clic a cada enlace
            scrollLinks.forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();

                    console.log('clicked!')

                    var targetId = this.getAttribute('href').substring(1);
                    var targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        console.log('got target')
                        var offset = 90;
                        var targetRect = targetElement.getBoundingClientRect();
                        var targetOffsetTop = window.scrollY + targetRect.top - offset;

                        window.scrollTo({
                            top: targetOffsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });

        });
    });
});

function handlePreview(index) {
    console.log('clicked ', index, images.value[index])
    if(v3ImgPreviewFn)
     v3ImgPreviewFn({ images: images.value, index })
}



</script>


<style scoped>
:deep(img) {
    @apply max-w-full mx-auto mb-3;
}


.text-container :deep(h1) {
    @apply text-2xl;
}

.text-container :deep(h2) {
    @apply text-xl;
}

.text-container :deep(h3) {
    @apply text-lg;
}

/* amplia la imagen en el ancho */
@media (min-width: 1154px) {
    :deep(p.images-wrapper) {
        /* width: 150%;
        margin-left: -25%; */
        text-align: center;
    }
}
</style>
