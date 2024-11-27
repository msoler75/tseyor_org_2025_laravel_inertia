
<template>
    <div class="container py-12 mx-auto">

        <AdminLinks modelo="noticia" necesita="administrar contenidos" class="mb-3"/>

        <h1>Noticias</h1>
        <p>Aquí puedes ver las últimas noticias de Tseyor.</p>

        <ContentMain class="flex justify-end mb-5">
            <SearchInput />
        </ContentMain>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">

                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data" :key="contenido.id"
                        :imageLeft="true"
                        :title="contenido.titulo"
                        :image="contenido.imagen"
                        :href="route('noticia', contenido.slug)"
                        :description="contenido.descripcion"
                        :date="contenido.published_at"
                        imageClass="h-60"/>

                </div>

                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="card bg-base-100 shadow min-w-[250px] lg:min-w-[350px] space-y-7 px-5 py-9 self-baseline"
                v-if="listado.first_page_url.indexOf('?buscar=') < 0">
                <h2 class="mb-5">Recientes</h2>
                <ul class="list-disc">
                    <li v-for="noticia in recientes" :key="noticia.id">
                        <Link :href="`/noticias/${noticia.slug}`"
                            class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                        {{ noticia.titulo }}
                        </Link>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</template>

<script setup>


const props = defineProps({
    filtrado: { default: () => "" },
    listado: {
        default: () => { data: [] }
    },
    recientes: {
        default: () => []
    }
});

const listado = ref(props.listado)
const recientes = ref(props.recientes)

</script>
