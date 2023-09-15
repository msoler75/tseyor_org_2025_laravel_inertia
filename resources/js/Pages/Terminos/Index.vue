
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
            <span></span>
            <AdminPanel modelo="termino" necesita="administrar contenidos" />
        </div>


        <h1>Glosario - Índice de términos</h1>

        <div class="flex gap-3 mb-12">
            Índice de términos
            |
            <Link href="/guias" class="text-primary hover:underline">Guías Estelares</Link>
            |

                <Link href="lugares" class="text-primary hover:underline">Lugares de la Galaxia</Link>
                |

                <Link href="/libros/glosario" class="text-primary hover:underline">Glosario completo en pdf</Link>
            </div>

            <p>Conoce los conceptos básicos de Tseyor a partir de los comunicados de los Guías Estelares.</p>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(12rem, 1fr))` }">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                    class="capitalize lowercase hover:text-primary transition-color duration-200 w-fit font-bold text-lg card shadow hover:shadow-lg px-5 py-2 bg-base-100">
                    {{ contenido.nombre }}
                    </Link>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div>
                <div class="card bg-base-100 shadow p-5">
                    <Link v-for="letra in letras" :key="letra" class="p-2">
                    {{ letra }}
                    </Link>
                </div>
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
    letras: {}
});

const listado = ref(props.listado);


</script>
