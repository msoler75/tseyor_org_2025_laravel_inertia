<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="user" necesita="administrar usuarios"  />
            </div>
        </div>

        <h1>Usuarios</h1>

        <div class="flex justify-end mb-5">
            <SearchInput placeholder="Buscar usuario..."/>
        </div>

        </PageHeader>

        <PageWide>

        <div class="w-full grow">

            <SearchResultsHeader :results="props.listado" />

            <div v-if="props.listado.data.length > 0" class="grid gap-x-6 gap-y-5"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(14rem, 100%), 1fr))` }">
                <div v-if="props.listado.data.length > 0" v-for="usuario in props.listado.data" :key="usuario.id"
                    class="flex items-center gap-4 min-w-0">
                    <Avatar :user="usuario" imageClass="w-16 h-16 shrink-0" />
                    <Link :href="route('usuario', { id: usuario.slug || usuario.id })"
                        class="flex-1 min-w-0 text-base font-bold leading-snug break-words hover:text-primary transition-colors">
                        {{ usuario.nombre || usuario.name }}
                    </Link>
                </div>
            </div>


            <pagination class="mt-6" :links="props.listado.links" />

        </div>
</PageWide>
    </Page>
</template>



<script setup>

const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});


</script>
