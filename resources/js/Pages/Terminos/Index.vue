
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
            <i></i>
            <AdminPanel modelo="termino" necesita="administrar contenidos" />
        </div>


        <h1>Glosario - Índice de términos</h1>

        <GlosarioTabs/>

        <p>Conoce los conceptos básicos de Tseyor a partir de los comunicados de los Guías Estelares.</p>

        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="card bg-base-100 shadow p-5 h-fit sticky top-20">
                <div class="letras grid grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2" :style="{ order: reposicionar(index) }"
                    :href="route('terminos')+'?letra='+letraItem" :class="letra==letraItem?'pointer-events-none font-bold text-primary':''">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <div class="grid gap-8 mb-14" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                        class="hover:text-primary transition-color duration-200 w-fit card shadow hover:shadow-lg px-5 py-2 bg-base-100 h-fit"
                        >
                        <div v-html="contenido.nombre" class="capitalize lowercase font-bold text-lg"/>
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3"/>
                </Link>
                </div>

                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    letras: {},
    letra: {},
    filtrado:{}
});

const listado = ref(props.listado);


function reposicionar(index) {
    const mid = props.letras.length / 2
    return index < mid ? index * 2 : ((index - mid - props.letras.length % 2) * 2 + 1)
}
</script>

