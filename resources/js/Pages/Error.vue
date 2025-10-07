<template>
    <h1 class="hidden">{{ "Error " + codigo }}</h1>

    <div class="container py-12 flex flex-col justify-center h-full mb-24">
        <div
            v-if="codigo"
            class="text-7xl opacity-60 font-bold pt-12 text-center"
        >
            {{ codigo }}
        </div>
        <Hero :title="titulo" :subtitle="mensaje" />

        <div v-if="alternativas?.data?.length" class="flex flex-col gap-4">
            <span>Tal vez quieras visitar:</span>
            <Link
                v-for="a of alternativas.data"
                :key="a.id"
                :href="calcularUrl(a)"
                class="flex gap-3 items-center card shadow-2xs bg-base-100 w-fit flex-row p-3 hover:shadow-md transition-shadow text-primary hover:text-secondary"
            >
                <Icon icon="ph:arrow-right-bold" />
                <span class="font-bold">{{ a.titulo }}</span>
                <div class="badge badge-info">
                    {{ traducir(a.coleccion) }}
                </div>
            </Link>
        </div>
    </div>
</template>

<script setup>
import traducir from "@/composables/traducciones";

const props = defineProps({
    codigo: { type: Number, required: false },
    titulo: { type: String, required: true },
    mensaje: {
        type: String,
        required: true,
    },
    alternativas: {},
});

// copia de globalsearch

function calcularUrl(item) {
    return item.coleccion != "paginas"
        ? route(item.coleccion) + "/" + (item.slug_ref || item.id_ref)
        : "/" + item.slug_ref;
}
</script>
