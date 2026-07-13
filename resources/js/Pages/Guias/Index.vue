<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2">
                <Link href="/libros/los-guias-estelares" class="btn btn-xs btn-error flex gap-2 items-center"
                    title='Descarga todo el glosario en pdf'>
                <Icon icon="ph:download-duotone" /><span class="hidden sm:inline">Descargar </span>libro
                </Link>
                <Share />
                <AdminLinks modelo="guia" necesita="administrar contenidos" />
            </div>
        </div>

        <!-- para compartir enlace correctamente -->
        <h1 class="hidden">Guías Estelares</h1>

        <div class="mx-auto flex flex-col justify-center items-center mb-12">
            <h1>Guías Estelares</h1>
            <p class="text-base sm:text-lg text-base-content/60 mt-3 max-w-2xl text-center leading-relaxed">
                Conoce a los tutores de la Confederación de Mundos Habitados que nos guían en este proceso de despertar de la consciencia.
            </p>
        </div>


        <div class="flex justify-start mb-5 gap-5 flex-wrap">
            <TitleInfo title="" modalTitle="¿Qué son los Guías Estelares?" classes="!mb-0">
                <p>Los Guías Estelares son tutores de la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia> que mantienen contacto <Referencia r="telepatía">telepático</Referencia> con el <Referencia>grupo TSEYOR</Referencia> a través del canalizador <Referencia r="canalizador">Chac-Mool Puente</Referencia>.</p>
                <p>El objetivo de este contacto y <Referencia r="Contacto entre ambas civilizaciones">tutelaje entre civilizaciones</Referencia> es servir de referenciación para que la humanidad planetaria de la Tierra pueda prepararse para la llegada del <Referencia>Rayo Sincronizador</Referencia> que resituará la humanidad a su posición real en el <Referencia>Tiempo Real del Universo</Referencia>.</p>
                <div class="mt-6 pt-4 border-t border-base-300">
                    <p class="font-bold mb-2">Preguntas frecuentes relacionadas:</p>
                    <ul class="space-y-1">
                        <li><Link href="/preguntas-frecuentes/extraterrestres#_Toc301129892" class="no-underline font-medium text-primary hover:text-secondary">¿Existen materialmente o son seres de otra dimensión?</Link></li>
                        <li><Link href="/preguntas-frecuentes/extraterrestres#_Toc301129896" class="no-underline font-medium text-primary hover:text-secondary">¿Cómo es la tecnología de los HM? ¿Cómo son sus naves?</Link></li>
                        <li><Link href="/preguntas-frecuentes/extraterrestres#_Toc301129944" class="no-underline font-medium text-primary hover:text-secondary">¿Cómo puede alguien tomar contacto con los HM?</Link></li>
                    </ul>
                </div>
            </TitleInfo>

            <TitleInfo title="" labelInfo="¿Cómo contactan?" modalTitle="¿Cómo contactan los Guías Estelares?" classes="!mb-0">
                <p>La mayoría de veces se comunican <Referencia r="telepatía">telepáticamente</Referencia> vía nuestro canal <Referencia r="canalizador">Chac-Mool Puente</Referencia>.</p>
                <p>La mayoría de ellos vive en otros planetas distantes mientras que algunos residen en bases en este planeta.</p>
                <div class="mt-6 pt-4 border-t border-base-300">
                    <p class="font-bold mb-2">Preguntas frecuentes relacionadas:</p>
                    <ul class="space-y-1">
                        <li><Link href="/preguntas-frecuentes/extraterrestres#_Toc301129944" class="no-underline font-medium text-primary hover:text-secondary">¿Cómo puede alguien tomar contacto con los HM?</Link></li>
                        <li><Link href="/preguntas-frecuentes/extraterrestres#_Toc301129938" class="no-underline font-medium text-primary hover:text-secondary">¿Por qué no se presentan y arreglan la situación mundial?</Link></li>
                    </ul>
                </div>
            </TitleInfo>

            <div class="ml-auto flex gap-3 self-center">
                <Link href="/preguntas-frecuentes/extraterrestres" class="flex items-center gap-2 font-bold text-primary text-xs uppercase tracking-widest hover:gap-3 transition-all">
                            Saber más<Icon icon="ph:arrow-right"/>
                </Link>
            </div>
        </div>

        </PageHeader>

        <PageWide>

        <ScrollToHere if-same-path :fade-on-navigate="false">

            <Categorias v-if="false" title="Vibración" :novedades="false" :categorias="categorias" :counters="false" :url="route('guias')"
            :resultados="!!filtrado" div-class="top-32! min-w-[150px] lg:min-w-[200px]" div-select-class="top-28!"  />

            <FadeOnNavigate class="w-full grow">

                <GridAppear class="gap-8" col-width="12rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                    preserve-state
                preserve-page
                        :href="route('guia', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow-xs">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


            </FadeOnNavigate>


        </ScrollToHere>


        <div class="flex justify-start mb-5 gap-5 mt-14">
            <div class="ml-auto flex gap-3">
                <Link href="/preguntas-frecuentes/extraterrestres" class="flex items-center gap-2 font-bold text-primary text-xs uppercase tracking-widest hover:gap-3 transition-all">
                    Saber más<Icon icon="ph:arrow-right"/>
                </Link>
            </div>
        </div>

    </PageWide>


    </Page>
</template>

<script setup>



const props = defineProps({
    listado: {
        default: () => []
    },
    categorias: {
        type: Array,
        required: true
    },
    filtrado: {type: [String, null],
        required: true
    }
});

</script>
