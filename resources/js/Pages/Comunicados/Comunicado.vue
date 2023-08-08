<template>
    <div class="container mx-auto px-4 py-8 mt-20">
        <div v-if="isDraft" class="alert alert-warning max-w-sm mx-auto mb-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Pendiente de publicación</span>
            <a :href="editUrl" class="ml-auto underline">Editar</a>
        </div>

        <div class="prose mx-auto">
            <h1>{{ comunicado.titulo }}</h1>

            <div class="text-neutral text-sm mb-2">
                <TimeAgo :date="comunicado.fecha_comunicado" />
            </div>
        </div>

        <Content :content="comunicado.texto" class="pb-12" />

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


const searchParams = new URLSearchParams(window.location.search);
const isPreviewMode = searchParams.get('preview') !== null

const isDraft = ref(props.comunicado.visibilidad != 'P')

const editUrl = ref(`/admin/comunicado/${props.comunicado.id}/edit`)

</script>

