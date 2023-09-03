
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="guia" necesita="administrar contenidos" class="mb-3"/>



        <h1>Guías Estelares</h1>
        <p>Tutores de la Confederación y otros hermanos de las estrellas.</p>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="min-w-[150px] lg:min-w-[240px] ">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Listado</h2>
                    <ul class="list-disc">
                        <li v-for="guia in guias" :key="guia.slug">
                            <Link :href="route('guia', guia.slug)"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            {{ guia.nombre }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="w-full flex-grow">

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(12rem, 1fr))` }">
                    <CardContent v-for="contenido in guias" :key="contenido.id" :image="contenido.imagen"
                        :href="route('guia', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 group-hover:text-primary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </div>


            </div>


        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    guias: {
        default: () => []
    }
});

</script>
