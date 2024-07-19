<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-7">
            <Back :href="route('guias')" inline>Guías</Back>
            <Link href="/libros/glosario-terminologico" class="btn btn-sm btn-primary flex gap-2 items-center"
            title='Descarga todo el glosario en pdf'>
            <Icon icon="ph:download-duotone" />Descargar libro</Link>
            <AdminLinks modelo="guia" necesita="administrar contenidos" :contenido="guia" />
        </div>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput :doSearch="false" @search="buscarClick" />
        </div>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <Back :href="route('guias')" inline>Guías</Back>
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Glosario
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <Back :href="route('guias')" inline class="pointer-events-none opacity-0">Guías</Back>
            </div>
        </ContentBar>

        <ContentMain class="flex flex-wrap lg:flex-nowrap gap-10">
            <div class="w-full max-w-[350px] mx-auto lg:max-w-full lg:w-1/3">
                <div class="w-full h-auto mx-auto"
                :class="imagenes.length>1?'':'md:sticky md:top-32'">
                    <Image v-for="imagen, index of imagenes" :key="index" :src="imagen" :alt="guia.nombre" class="w-full h-auto mb-4" />
                </div>
            </div>
            <div class="w-full lg:w-2/3 flex-shrink-0 text-left">
                <h1 class="text-center lg:text-left">{{ guia.nombre }}</h1>
                <h3 class="text-center lg:text-left">{{ guia.descripcion }}</h3>
                <p class="text-gray-600 text-xs my-5 text-right">
                    Última actualización:
                    <TimeAgo :date="guia.updated_at" />
                </p>
                <div class="mb-4"></div>
                <tabs  :options="{ disableScrollBehavior: true }" class="animate-fade-in">
                    <tab v-for="seccion, index of secciones" :key="index" :name="seccion.titulo">
                        <div class="prose" v-html="MarkdownToHtml(seccion.texto)"></div>
                    </tab>

                    <tab v-if="libros" name="Bibliografía">
                        <Prose v-if="libros.texto" class="mb-12" v-html="libros.texto"/>
                        <div class="flex flex-wrap gap-5">
                            <Link :href="route('libro', libro.slug)" v-if="libros" v-for="libro, index of libros.items"
                                :key="index" class="flex">
                            <Image :src="libro.imagen" :alt="libro.titulo" class="object-contain rounded-[2px] w-48 shadow-xl"  />
                            </Link>
                        </div>
                    </tab>

                    <!-- ... relacionados -->

                </tabs>
            </div>
        </ContentMain>

        <hr class="my-12" />

        <div class="card bg-base-100 shadow flex-wrap flex-row p-5 lg:p-10 gap-4">
            <div v-for="item of guias" :key="item.slug" class="flex gap-2">
                <Link v-show="item.slug != guia.slug" :href="route('guia', item.slug)">
                <span class="capitalize">{{ item.nombre }}</span>
                </Link>
            </div>
        </div>

    </div>
</template>

<script setup>
import { Tabs, Tab } from 'vue3-tabs-component';
import AppLayout from '@/Layouts/AppLayout.vue'
import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

defineOptions({ layout: AppLayout })


const props = defineProps({
    guia: {
        type: Object,
        required: true,
    },
    guias: {
        type: Array,
        required: true,
    },
    libros: {
        type: Object,
        required: false
    }
});

const format = detectFormat(props.guia.texto)

const texto = ref(props.guia.texto)

if(format.format=='html')
    texto.value = HtmlToMarkdown(texto.value)

// este truco es para tener más de una imagen en un mismo guía estelar (mo y rhaum)
const imagenes = ref([props.guia.imagen]);

const regex = /[?&](imagenes|images)=([^&]+)/;
const matches = props.guia.imagen.match(regex);

if (matches) {
  const imgs = matches[2];
  imagenes.value = imgs.split(',');
}


// divide las secciones segun los títulos en diferentes tabs

const secciones = ref(parseMarkdownToSections(texto.value))

function parseMarkdownToSections(text) {
  const lines = text.split('\n');
  const sections = [];
  let currentSection = null;

  lines.forEach((line) => {
    // Verificar si es un título
    const matches = line.match(/^(#+)\s+(.*)$/);
    if (matches) {
      const level = matches[1].length;
      const titulo = matches[2];
      const texto = '';

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
      currentSection.texto += line + '\n';
    }
  });

  // Añadir la última sección al array de secciones
  if (currentSection !== null) {
    sections.push(currentSection);
  }

  return sections;
}

function buscarClick(query) {
    router.visit(route('guias') + '?buscar=' + query)
}
</script>
