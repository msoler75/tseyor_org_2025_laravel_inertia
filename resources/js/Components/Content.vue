<template>
    <Prose class="prose-text-container break-words">
        <LazyHydrate>
            <ContentNode :node="arbol" :use-image="optimizeImages" @click="handleClick" />
        </LazyHydrate>

        <ClientOnly>
            <ImagesViewer v-if="images?.length" :show="showImagesViewer" @close="showImagesViewer = false"
            :images="images?.map((x) => x + '?mw=3000&mh=3000')"  :index="imageIndex"
            />
        </ClientOnly>

    </Prose>
</template>

<script setup>
// import Markdown from 'vue3-markdown-it';
import { detectFormat, MarkdownToHtml } from '@/composables/markdown.js'
// import { VueShowdown } from 'vue-showdown';
// import { extractImages, extractReferences } from '@/composables/contentUtils.js'

// Componente ImagesViewer cargado de forma asíncrona
const ImagesViewer = defineAsyncComponent(() => import('./ImagesViewer.vue'))

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    format: {
        type: String,
        default: 'md'
    },
    optimizeImages: { // determina si usa el componente Imagen
        type: Boolean,
        default: true
    },
    onNode: {
        type: Function,
        default: null
    },
    imageAppend: {

    }
});

const isMarkdown = computed(() => props.format == 'md' ? true : ['md', 'ambiguous'].includes(detectFormat(props.content).format))
const images = ref([]) // imagenes del contenido

const contentPreProcessed = computed(() => isMarkdown.value ? MarkdownToHtml(props.content) : props.content ? props.content : "")

const contentProcessed = computed(() => contentPreProcessed.value.replace(/<span class="referencia"(.*?)<\/span>/g, '<referencia$1</referencia>')
    .replace(/<strong><em(.*?)<\/em><\/strong>/g, '<referencia$1</referencia>'))

function parseHTML(textoHTML) {
    images.value = []

    const parser = new DOMParser();
    const doc = parser.parseFromString(textoHTML, 'text/html');
    var firstImage = true

    function parseNode(node) {

        const textNode = !node.tagName
        const obj = {
            tagName: textNode ? null : node.tagName.toLowerCase(),
            textContent: node.textContent,
            node
        }

        if (!textNode) {

            // añadimos la imagen al listado general
            if (node.tagName == 'IMG') {
                node.setAttribute('image-index', images.value.length)
                images.value.push(node.src.replace(/\?.*$/, ''))
                // si es la primera imagen, no será lazy
                if(firstImage)
                    node.setAttribute('loading', 'eager')
                firstImage = false
            }

            if(node.tagName == 'REFERENCIA') {
                console.warn("Referencia encontrada", node.innerHTML);
            }

            obj.attributes = {}
            obj.children = []

            // Parsea los atributos
            if (node.attributes) {
                for (let i = 0; i < node.attributes.length; i++) {
                    const attribute = node.attributes[i];
                    obj.attributes[attribute.name] = attribute.value;
                }
            }
        }

        // Parsea los hijos recursivamente
        for (let i = 0; i < node.childNodes.length; i++) {
            const child = node.childNodes[i];
            if (child.nodeType === 1) {
                const objchild = parseNode(child)
                obj.children.push(objchild);
                if (objchild.tagName == "img" && !objchild.node.src.match(/guias|sello_tseyor/)) // si es una imagen de los guias, no cuenta
                    obj.attributes['has-image'] = ''
                if (props.onNode)
                    props.onNode(objchild)
            } else if (child.nodeType === 3 && child.textContent) {
                // Agrega el texto del nodo de texto
                obj.children.push({
                    tagName: null,
                    textContent: child.textContent,
                });
            }
        }

        return obj;
    }

    // Comienza el análisis desde el nodo raíz
    const r = parseNode(doc.body.firstChild);
    images.value.push()
    return r
}

const arbol = computed(() => parseHTML('<div>' + contentProcessed.value + '</div>'))

onMounted(async () => {

    nextTick(() => {

        // AÑADIR ENLACES A NOTAS AL PIE, Y VICEVERSA

        // ESTO SOLO FUNCIONA PARA UN PARSE DE MARKDOWN DE OTRA LIBRERIA

        // ACTUALMENTE NO HACE NADA

        // Obtener todos los enlaces de desplazamiento
        var scrollLinks = document.querySelectorAll('.footnote-ref a, a.footnote-backref');

        // console.log({ scrollLinks })

        // Agregar evento de clic a cada enlace
        scrollLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                // console.log('clicked!')

                var targetId = this.getAttribute('href').substring(1);
                var targetElement = document.getElementById(targetId);

                if (targetElement) {
                    // console.log('got target')
                    var offset = 90;
                    var targetRect = targetElement.getBoundingClientRect();
                    var targetOffsetTop = window.scrollY + targetRect.top - offset;
                    console.log('scrollto_0_content')
                    window.scrollTo({
                        top: targetOffsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

    });
});

function handleClick(event) {
    // console.log('clicked', event.target)
    if (event.target.tagName == 'IMG') {
        handlePreview(event.target)
    }
}

const showImagesViewer = ref(false)
const imageIndex = ref(0)
function handlePreview(img) {
    showImage(parseInt(img.getAttribute("image-index")))
}

const showImage = (index) =>{
    imageIndex.value = index
    showImagesViewer.value = true
    console.log('clicked ', imageIndex.value, images.value[imageIndex.value])
}

// Exponer la API pública para que componentes padre puedan invocar showImage vía ref
defineExpose({ showImage })

</script>


<style scoped>
@reference "../../css/app.css";

:deep(table) p {
    @apply my-0;
}

:deep(.is-image) {
    @apply max-w-full mx-auto mb-3 mt-[2em];
    cursor: pointer;
}

:deep(.is-image)+br {
    display: none;
}



:deep(p[has-image])+p[style*=center]:not([has-image]),
:deep(.is-image)+br+em,
:deep(.is-image)+em {
    font-size: 90%;
    transform: translateY(-.6rem);
    display: inline-block;
}

:deep(p[has-image])+p[style*=center]:not([has-image]) {
    transform: translateY(-1rem);
    display: block;
}


.prose-text-container :deep(h1) {
    @apply text-2xl;
}

.prose-text-container :deep(h2) {
    @apply text-xl;
}

.prose-text-container :deep(h3) {
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

@media (max-width: 360px) {
    .text-justify {
        text-align: left
    }
}
</style>
