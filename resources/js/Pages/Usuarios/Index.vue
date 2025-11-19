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

            <div v-if="props.listado.data.length > 0" class="grid gap-4"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(10rem, 100%), 1fr))` }">
                <div v-if="props.listado.data.length > 0" v-for="usuario in props.listado.data" :key="usuario.id"
                    class="card bg-base-100 shadow-2xs p-3 space-y-2">
                    <Avatar :user="usuario" />
                    <Link :href="route('usuario', { id: usuario.slug || usuario.id })" class="text-center">
                    {{ usuario.name }}
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
