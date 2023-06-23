
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Guías Estelares</h1>
        <p>Tutores de la Confederación y otros hermanos de las estrellas.</p>

         <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="min-w-[150px] lg:min-w-[240px] ">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Listado</h2>
                    <ul class="list-disc">
                        <li v-for="guia in guias" :key="guia.slug">
                            <Link :href="route('enciclopedia.guia', guia.slug)"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                {{ guia.nombre }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="w-full flex-grow">

                <div class="grid gap-8"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(12rem, 1fr))` }">
                    <div v-if="guias.length > 0" v-for="guia in guias" :key="guia.slug"
                        class="card bg-base-100 shadow">
                        <img :src="guia.imagen" :alt="guia.nombre" class="h-48 object-cover w-full" />
                        <div class="p-4 flex flex-col flex-grow">
                            <h2 class="text-lg font-bold mb-2">{{ guia.nombre }}</h2>
                            <!-- <p class="text-gray-700 text-sm">{{ guia.descripcion }}</p> -->
                            <Link :href="route('enciclopedia.guia', guia.slug)"
                                class="btn btn-primary mt-auto">
                                Ver guia
                            </Link>
                        </div>
                    </div>
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
