<template>
    <Page>

        <PageHeader>
        <div class="flex justify-between mb-20">
            <span/>
            <Share />
        </div>

        <!-- Encabezado -->
         <TitleInfo title="Comunidad Tseyor" label-info="¿qué es?">
                <div>La comunidad Tseyor es un espacio de personas que comparten la filosofía de las estrellas y trabajan juntas en el autoconocimiento, la fraternidad y la construcción de Sociedades Armónicas.</div>
                <div>Aquí encontrarás los contenidos generados por y para la comunidad: experiencias, equipos, salas, galerías, eventos y mucho más.</div>
         </TitleInfo>

         </PageHeader>

        <PageWide>

            <!-- Contenedor de Categorías -->
            <GridAppear class="mt-12 grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Categoría -->
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
        title: 'Experiencias',
        descripcion: 'Sueños, meditaciones, extrapolaciones y trabajos grupales compartidos por la comunidad.',
        url: route('experiencias'),
        icon: "ph:sparkle-duotone",
        count: computed(() => props.stats ? props.stats.experiencias : 0),
        collection: 'experiencias'
    },
    {
        title: 'Equipos',
        descripcion: 'Equipos de trabajo y grupos de la comunidad Tseyor.',
        url: route('equipos'),
        icon: "ph:users-three-duotone",
        count: computed(() => props.stats ? props.stats.equipos : 0),
        collection: 'equipos'
    },
    {
        title: 'Salas',
        descripcion: 'Salas para reuniones virtuales de los distintos equipos.',
        url: route('salas'),
        icon: "ph:video-camera-duotone",
        count: computed(() => props.stats ? props.stats.salas : 0),
        collection: 'salas'
    },
    {
        title: 'Centros Tseyor',
        descripcion: 'Casas Tseyor y Muulasterios en el mundo.',
        url: route('centros'),
        icon: "ph:house-line-duotone",
        count: computed(() => props.stats ? props.stats.centros : 0),
        collection: 'centros'
    },
    {
        title: 'Eventos',
        descripcion: 'Cursos, convivencias y encuentros de la comunidad.',
        url: route('eventos'),
        icon: "ph:calendar-duotone",
        count: computed(() => props.stats ? props.stats.eventos : 0),
        collection: 'eventos'
    },
    {
        title: 'Usuarios',
        descripcion: 'Miembros de la comunidad Tseyor.',
        url: route('usuarios'),
        icon: "ph:user-duotone",
        count: computed(() => props.stats ? props.stats.usuarios : 0),
        collection: 'usuarios'
    },
    {
        title: 'Dónde estamos',
        descripcion: 'Ubicaciones y contactos donde encontrar TSEYOR.',
        url: route('contactos'),
        icon: "ph:map-pin-duotone",
        count: computed(() => props.stats ? props.stats.contactos : 0),
        collection: 'contactos'
    },
    {
        title: 'Galerías',
        descripcion: 'Fotografías, arte y memoria visual de la comunidad.',
        url: route('galerias'),
        icon: "ph:image-duotone",
        count: computed(() => props.stats ? props.stats.galerias : 0)
    },
    {
        title: 'Archivos',
        descripcion: 'Zona de archivos para los usuarios: contenidos públicos y archivos de los equipos.',
        url: '/archivos',
        icon: "ph:folder-duotone",
        count: 0
    },
])
</script>
