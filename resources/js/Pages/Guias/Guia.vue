<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('guias')" inline>Guías</Back>
            <div class="flex gap-2">
                <Share />
                <Link
                    href="/libros/glosario-terminologico"
                    class="btn btn-xs btn-primary flex gap-2 items-center"
                    title="Descarga todo el glosario en pdf"
                >
                    <Icon icon="ph:download-duotone" />Descargar libro</Link
                >
                <AdminLinks
                    modelo="guia"
                    necesita="administrar contenidos"
                    :contenido="guia"
                />
            </div>
        </div>

        <!-- para compartir enlace correctamente -->
        <h1 class="hidden">{{ guia.nombre }}</h1>
        <h1 class="hidden">Guía Estelar</h1>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>

        </PageHeader>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <Back :href="route('guias')" inline class="opacity-100!"
                    >Guías</Back
                >
                <div
                    @click="useNav().scrollToTopPage"
                    class="flex items-center gap-2 font-bold"
                >
                    Consulta
                    <Icon
                        icon="ph:arrow-circle-up-duotone"
                        class="transform scale-150"
                    />
                </div>
                <Back
                    :href="route('guias')"
                    inline
                    class="pointer-events-none opacity-0!"
                    >Guías</Back
                >
            </div>
        </ContentBar>

        <PageWide>

        <ContentMain class="flex flex-wrap lg:flex-nowrap gap-10">
            <div class="w-full max-w-[350px] mx-auto lg:max-w-full lg:w-1/3">
                <div
                    class="w-full h-auto mx-auto"
                    :class="imagenes.length > 1 ? '' : 'md:sticky md:top-32'"
                >
                    <Image
                        v-for="(imagen, index) of imagenes"
                        :key="index"
                        :src="imagen"
                        :alt="guia.nombre"
                        class="w-full h-auto mb-4"
                    />
                </div>
            </div>
            <div class="w-full lg:w-2/3 shrink-0 text-left bg-base-100 px-4 py-12 sm:px-8 max-w-[60ch] mx-auto lg:mx-0 rounded-lg shadow-2xs">
                <h1 class="text-center lg:text-left">{{ guia.nombre }}</h1>
                <h3 class="text-center lg:text-left">{{ guia.descripcion }}</h3>
                <p class="prose text-gray-600 my-5 text-right">
                    <span class="text-xs">
                        Última actualización:
                        <TimeAgo :date="guia.updated_at" />
                    </span>
                </p>
                <div class="mb-4"></div>
                <div
                    :options="{ disableScrollBehavior: true }"
                    class="animate-fade-in"
                >
                    <div
                        v-for="(seccion, index) of secciones"
                        :key="index"
                        :name="seccion.titulo"
                    >
                        <p
                            v-if="seccion.titulo"
                            :class="`text-${seccion.level-2}xl font-bold mb-4`"
                        >
                            {{ seccion.titulo }}
                        </p>
                        <Prose
                            v-html="MarkdownToHtml(seccion.texto)"
                        ></Prose>
                    </div>

                    <div v-if="libros" name="Bibliografía">
                        <h2 class="text-2xl font-bold mt-8 mb-4">Bibliografía</h2>
                        <Prose
                            v-if="guia.bibliografia"
                            class="mb-12"
                            v-html="bibliografiaHtml"
                        />
                        <div class="flex flex-wrap gap-10">
                            <Libro3d
                                v-for="(libro, index) of libros"
                                :key="index"
                                :libro="libro"
                                imageClass="w-[150px]"
                            />
                        </div>
                    </div>

                    <!-- ... relacionados -->
                </div>
            </div>
        </ContentMain>





        <div class="mt-12 grid gap-2 mb-12 grid-cols-1 xs:grid-cols-[180px_1fr_180px] sm:grid-cols-[220px_1fr_220px]">
            <CardContent v-if="anterior" :imageLeft="false" :key="anterior.id" :title="'Anterior: ' + anterior.nombre"
                class="rounded-none sm:rounded-lg" :image="anterior.imagen" :href="route('guia', anterior.slug)"
               imageClass="h-60" />
               <span v-else></span>
               <div/>
            <CardContent v-if="siguiente" :imageLeft="false" :key="siguiente.id" class="rounded-none sm:rounded-lg"
                :title="'Siguiente: ' + siguiente.nombre" :image="siguiente.imagen"
                :href="route('guia', siguiente.slug)"
                 imageClass="h-60" />
                 <span v-else></span>
        </div>

        </PageWide>

    </Page>
</template>

<script setup>
import { Tabs, Tab } from "vue3-tabs-component";
import {
    HtmlToMarkdown,
    MarkdownToHtml,
    detectFormat,
} from "@/composables/markdown.js";

const props = defineProps({
    guia: {
        type: Object,
        required: true,
    },
    libros: {
        type: Array,
        required: false,
    },
    siguiente: {
        type: [Object, null],
        required: true,
    },
    anterior: {
        type: [Object, null],
        required: true,
    },
});

const format = detectFormat(props.guia.texto);

const texto = ref(props.guia.texto);

if (format.format == "html") texto.value = HtmlToMarkdown(texto.value);

const bibliografiaHtml = computed(() => MarkdownToHtml(props.guia.bibliografia));

// este truco es para tener más de una imagen en un mismo guía estelar (mo y rhaum)
const imagenes = ref([props.guia.imagen]);

const regex = /[?&](imagenes|images)=([^&]+)/;
const matches = props.guia.imagen.match(regex);

if (matches) {
    const imgs = matches[2];
    imagenes.value = imgs.split(",");
}

// divide las secciones segun los títulos en diferentes tabs

const secciones = computed(() => parseMarkdownToSections(texto.value));

function parseMarkdownToSections(text) {
    const lines = text.split("\n");
    const sections = [];
    let currentSection = null;

    lines.forEach((line) => {
        // Verificar si es un título
        const matches = line.match(/^(#{2,6})\s+(.*)$/m);
        if (matches) {
            const level = matches[1].length;
            const titulo = matches[2];
            const texto = "";

            // Si ya hay una sección actual, almacenarla
            if (currentSection !== null) {
                sections.push(currentSection);
            }

            // Crear una nueva sección con el título y contenido vacío
            currentSection = {
                titulo,
                texto,
                level,
            };
        } else if (currentSection !== null) {
            // Agregar el contenido a la sección actual
            currentSection.texto += line + "\n";
        }
    });

    // Añadir la última sección al array de secciones
    if (currentSection !== null) {
        sections.push(currentSection);
    }

    return sections;
}

function buscarClick(query) {
    router.visit(route("guias") + "?buscar=" + query);
}
</script>
