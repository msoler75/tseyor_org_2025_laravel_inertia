<template>
    <div class="container mx-auto px-4 py-8">
        <div class="prose mx-auto">
            <h1 >{{ comunicado.titulo }}</h1>

            <div class="text-neutral text-sm mb-2">
            <TimeAgo :date="comunicado.fecha_comunicado" />
        </div>
    </div>

       <Content :content="comunicado.texto" class="pb-12"/>

       <Comentarios :url="route('comunicado', comunicado.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    },
});

</script>

