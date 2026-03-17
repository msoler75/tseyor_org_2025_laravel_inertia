<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back :href="route('galerias')">Galerías</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="galeria" necesita="administrar contenidos" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <div class="prose mx-auto">
                    <h1>{{ galeria.titulo }}</h1>

                    <div class="text-sm mb-2 flex justify-between">
                        <span />
                        <TimeAgo :date="galeria.created_at" :includeTime="false" />
                    </div>

                    <p v-if="galeria.descripcion">{{ galeria.descripcion }}</p>
                </div>

                <!-- Galería de imágenes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-8">
                    <div v-for="item in galeria.items" :key="item.id" class="card bg-base-100 shadow-xl">
                        <figure>
                            <img :src="item.nodo ? item.nodo.ubicacion : ''" :alt="item.descripcion || 'Imagen'" class="w-full h-48 object-cover" />
                        </figure>
                        <div class="card-body">
                            <p v-if="item.descripcion">{{ item.descripcion }}</p>
                            <p v-if="item.user" class="text-sm text-gray-500">Por: {{ item.user.name }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </PageContent>

        <PageFooter>
            <Comentarios :url="route('galeria', galeria.id)" />
        </PageFooter>
    </Page>
</template>

<script setup>

const props = defineProps({
    galeria: {
        type: Object,
        required: true
    }
})

</script>
