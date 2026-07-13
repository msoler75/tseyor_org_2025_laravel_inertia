<template>
    <Page class="pb-0!">
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2 items-center">
                <Link
                    href="/libros/preguntas-y-respuestas-tseyor"
                    class="btn btn-xs btn-error flex gap-2 items-center"
                    title="Descarga todo el glosario en pdf"
                    >
                    <Icon icon="ph:download-duotone" /><span class="hidden sm:inline">Descargar </span>libro
                </Link>
                <Share />
            </div>
        </div>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Preguntas frecuentes</h1>
            <p class="text-base sm:text-lg text-base-content/60 mt-3 max-w-lg text-center">
                Elige la sección que más te interese y descubre las respuestas a las preguntas más frecuentes.
            </p>
        </div>

    </PageHeader>

        <PageWide>

        <div class="bg-gradient-to-b from-base-200/50 to-transparent">
            <Section class="container max-w-6xl mx-auto pb-20 pt-10 px-4 sm:px-8">
                

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(seccion, index) of secciones"
                        :key="seccion.id"
                        class="group relative rounded-3xl p-8 shadow-sm border flex flex-col transition-all duration-300 hover:-translate-y-1.5"
                        :class="index === secciones.length - 1
                            ? 'bg-base-100/60 border-secondary/20 hover:border-secondary/40 hover:shadow-xl'
                            : 'bg-base-100 border-base-200 hover:shadow-xl hover:border-secondary/30'"
                    >
                        <template v-if="index === secciones.length - 1">
                            <div class="absolute top-0 left-8 right-8 h-1 rounded-full bg-gradient-to-r from-secondary to-primary" />
                        </template>
                        <div class="flex items-center gap-4 mb-0">
                            <Icon
                                :icon="iconoSeccion(seccion.slug)"
                                class="text-3xl text-secondary shrink-0"
                            />
                            <h3 class="text-xl font-bold leading-tight mb-0">{{ seccion.titulo }}</h3>
                        </div>

                        <ul v-if="seccion.ejemplos?.length" class="space-y-3 mt-6 mb-8 flex-1 ml-0 pl-0 list-none">
                            <li
                                v-for="(pregunta, i) in seccion.ejemplos.slice(0, 5)"
                                :key="i"
                                class="flex items-start gap-3"
                            >
                                <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-secondary shrink-0" />
                                <Link
                                    :href="route('preguntas.seccion', seccion.slug) + '#' + pregunta.anchor"
                                    class="text-base text-base-content/80 hover:text-secondary leading-relaxed transition-colors"
                                >
                                    {{ pregunta.text }}
                                </Link>
                            </li>
                        </ul>

                        <div v-else class="flex-1" />

                        <Link
                            :href="route('preguntas.seccion', seccion.slug)"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md"
                        >
                            Ver sección
                            <Icon icon="ph:arrow-right-duotone" class="transition-transform duration-300 group-hover:translate-x-0.5" />
                        </Link>
                    </div>
                </div>
            </Section>
        </div>



    </PageWide>

    <SectionBook
        titulo="Libro: Preguntas y respuestas"
        :slug="libro.slug"
        :portada="libro.imagen"
        enlace="/libros/preguntas-y-respuestas-tseyor"
        buttonLabel="Ver en Biblioteca">
        <p>Todas las preguntas y respuestas recopiladas en un solo libro. Incluye más de 200 preguntas organizadas por tema, con las respuestas de nuestros Guías Estelares. Descárgalo gratis.</p>
    </SectionBook>

    </Page>

</template>

<script setup>
const props = defineProps({
    secciones: {
        type: Array,
        required: true,
    },
    libro: Object
});

const iconos = {
    'extraterrestres': 'ph:planet-duotone',
    'salto-cuantico': 'ph:lightning-duotone',
    'grupo-tseyor': 'ph:users-three-duotone',
};

function iconoSeccion(slug) {
    return iconos[slug] || 'ph:book-open-duotone';
}
</script>
