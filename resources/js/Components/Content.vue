<template>
    <Prose ref="container" class="text-container">
        <ContentNode :node="arbol" :use-image="optimizeImages" />
    </Prose>
</template>

<script setup>
// import { v3ImgPreviewFn } from 'v3-img-preview'
// import Markdown from 'vue3-markdown-it';
import { detectFormat, MarkdownToHtml } from '@/composables/markdown.js'
// import { VueShowdown } from 'vue-showdown';
// import { extractImages, extractReferences } from '@/composables/contentUtils.js'

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    format: {
        type: String,
        default: ''
    },
    optimizeImages: {
        type: Boolean,
        default: true
    }
});

const isMarkdown = computed(() => props.format == 'md' ? true : ['md', 'ambiguous'].includes(detectFormat(props.content).format))
const container = ref(null)
const images = ref([]) // imagenes del contenido

var v3ImgPreviewFn = null

const contentPreProcessed = computed(() => isMarkdown.value ? MarkdownToHtml(props.content) : props.content)

const contentProcessed = computed(() => contentPreProcessed.value.replace(/<span class="referencia"(.*?)<\/span>/g, '<referencia$1</referencia>'))

function parseHTML(textoHTML) {
    images.value = []

    const parser = new DOMParser();
    const doc = parser.parseFromString(textoHTML, 'text/html');

    function parseNode(node) {

        const textNode = !node.tagName
        const obj = {
            tagName: textNode ? null : node.tagName.toLowerCase(),
            textContent: node.textContent.trim(),
        };

        if (!textNode) {

            // añadimos la imagen al listado general
            if (node.tagName == 'IMG') {
                node.setAttribute('image-index', images.value.length)
                images.value.push(node.src.replace(/\?.*$/, ''))
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
                obj.children.push(parseNode(child));
            } else if (child.nodeType === 3 && child.textContent.trim()) {
                // Agrega el texto del nodo de texto
                obj.children.push({
                    tagName: null,
                    textContent: child.textContent.trim(),
                });
            }
        }

        return obj;
    }

    // Comienza el análisis desde el nodo raíz
    return parseNode(doc.body.firstChild);
}

const arbol = computed(() => parseHTML('<div>' + contentProcessed.value + '</div>'))

onMounted(async () => {

    // importación dinámica:
    await import('v3-img-preview').then((module) => {
        v3ImgPreviewFn = module.v3ImgPreviewFn;

        // buscamos todas las imagenes
        const elems = container.value.$el.querySelectorAll('img')
        for (const img of elems)
            img.onclick = (event) => handlePreview(event.target)


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

function handlePreview(img) {
    const index = img.getAttribute("image-index")
    console.log('clicked ', index, images.value[index])
    if (v3ImgPreviewFn)
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
