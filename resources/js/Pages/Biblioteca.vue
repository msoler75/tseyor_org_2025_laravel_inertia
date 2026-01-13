<template>
    <Page>

        <PageHeader>
        <div class="flex justify-between mb-20">
            <span/>
            <Share />
        </div>

        <!-- Encabezado -->
         <TitleInfo title="Biblioteca Tseyor" label-info="¿qué es?">
                <div>La Biblioteca Tseyor es el resultado de más de 1800 diálogos <Referencia r="telepatía">
                telepáticos</Referencia> con seres humanos de la <Referencia>Confederación de Mundos Habitados
                de la Galaxia</Referencia>, transmitidos a través de <Referencia>Chac-Mool Puente</Referencia>,
                <Referencia r="canalización">canalizador</Referencia> de Tseyor.
                </div>
                <div>Estas denominadas <q>conversaciones interdimensionales</q> o comunicados se han transcrito en
                    textos que inspiran libros, monografías, vídeos y audios.</div>
                <div>Todos los materiales han sido publicados y están preservados por la
                    <Referencia>Universidad Tseyor de Granada</Referencia> para mantener la integridad del mensaje
                    original y sus obras impresas están registradas por la asociación <Referencia>TSEYOR Centro de
                    Estudios Socioculturales</Referencia>.</div>
         </TitleInfo>


         </PageHeader>

        <PageWide>

            <!-- Contenedor de Categorías -->
            <GridAppear class="mt-12 grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Categoría: Libros -->
                <Link :href="seccion.url" v-for="seccion of secciones" :key="seccion.title" class="card bg-base-100 p-6 rounded-lg shadow-md relative group
                transition duration-300  hover:shadow-lg antialiased
                lg:opacity-80 hover:opacity-100
                ">


                <h2 class="flex items-center gap-3 text-2xl font-semibold mb-2 group-hover:text-secondary!">
                    <Icon :icon="seccion.icon" />
                    {{ seccion.title }}
                </h2>

                <p class="opacity-70 group-hover:opacity-100 mb-4">{{ seccion.descripcion }}</p>
                <div class="flex justify-end gap-4 items-center opacity-50 group-hover:opacity-100 mt-auto">
                    <span :class="seccion.count ? 'opacity-50' : 'opacity-0'">{{ seccion.count }} {{
                        seccion.collection ? seccion.collection : seccion.title.toLowerCase() }}</span>
                    <Icon icon="ph-arrow-right" />
                </div>
                </Link>

            </GridAppear>

        </PageWide>
    </Page>
</template>

<script setup>
const props = defineProps({
    stats: Object
})



const secciones = ref([
    {
        title: 'Comunicados',
        descripcion: 'Transcripciones y audios de las conversaciones interdimensionales mantenidas con los hermanos del cosmos. Descubre los mensajes y enseñanzas compartidos por nuestros guías estelares.',
        url: route('comunicados'),
        icon: "ph:flying-saucer-duotone",
        count: computed(() => props.stats ? props.stats.comunicados : 0)
    },
    {
        title: 'Libros',
        descripcion: 'Explora nuestra colección de libros que contienen material genuino de Tseyor. Profundiza en los conocimientos y enseñanzas de nuestra filosofía.',
        url: route('libros'),
        icon: "ph:book-bookmark-duotone",
        count: computed(() => props.stats ? props.stats.libros : 0)
    },
    {
        title: 'Audios',
        descripcion: 'Sumérgete en nuestros talleres, meditaciones, cuentos y canciones. Disfruta de estos recursos auditivos y viaja por el universo.',
        url: route('audios'),
        icon: "ph:music-notes-duotone",
        count: computed(() => props.stats ? props.stats.audios : 0)
    },
    {
        title: 'Blogs',
        url: route('entradas'),
        descripcion: 'Lee los artículos escritos por miembros de nuestra comunidad. Aprende sobre nuestras vivencias, reflexiones y perspectivas únicas.',
        icon: "ph:pencil-line-duotone",
        count: computed(() => props.stats ? props.stats.entradas : 0),
        collection: 'artículos'
    },
    {
        title: 'Vídeos',
        url: route('videos'),
        descripcion: 'Visita nuestro canal de YouTube y descubre los vídeos que comparten las actividades, eventos y enseñanzas de Tseyor.',
        icon: "ph:youtube-logo-duotone",
        count: computed(() => props.stats ? props.stats.videos : 0)
    },
    {
        title: 'Meditaciones',
        url: route('meditaciones'),
        descripcion: 'Encuentra guías de meditación y talleres en texto y audio que te ayudarán a conectar con tu esencia interior.',
        icon: "ph:file-text-duotone",
        count: computed(() => props.stats ? props.stats.meditaciones : 0)

    },
    {
        title: 'Psicografias',
        url: route('psicografias'),
        descripcion: 'Explora nuestra colección de dibujos recibidos telepáticamente y dibujados por nuestro hermano Chac-Mool Puente.',
        icon: "ph:image-duotone",
        count: computed(() => props.stats ? props.stats.psicografias : 0)
    },
     {
        title: 'Descubre',
        url: route('descubre'),
        descripcion: 'Presentación de algunos conceptos centrales de Tseyor, para ofrecer un primer contacto inspirador y claro.',
        icon: "ph:lightbulb-duotone",
        count: computed(() => props.stats ? props.stats.descubre : 0),
        collection: 'páginas',
    },
    {
        title: 'Radio Tseyor',
        url: route('radio'),
        descripcion: 'Sintoniza nuestra radio 24/7 y escucha comunicados, entrevistas y talleres que nutren el espíritu.',
        icon: "ph:radio-duotone",
        count: 0
    }
])
</script>
