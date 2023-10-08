
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
            <Back class="opacity-0 pointer-events-none">Glosario</Back>
            <AdminPanel modelo="guia" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div
                class="w-full h-fit md:w-auto min-w-[150px] lg:min-w-[240px] card bg-base-100 shadow p-10 md:sticky md:top-20 flex-row md:flex-col flex-wrap md:gap-1">
                <Link v-for="guia in guias.data" :key="guia.slug" :href="route('guia', guia.slug)"
                    class="inline font-semibold p-3 md:px-0 md:py-1 w-fit">
                {{ guia.nombre }}
                </Link>


            </div>

            <div class="w-full flex-grow">

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(12rem, 1fr))` }">
                    <CardContent v-for="contenido in guias.data" :key="contenido.id" :image="contenido.imagen"
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
