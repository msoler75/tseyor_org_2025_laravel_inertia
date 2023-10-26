<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Experiencias</Back>
            <AdminPanel modelo="experiencia" necesita="administrar experiencias" :contenido="experiencia" />
        </div>

        <div class="cloud py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ experiencia.nombre }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-end">
                    <TimeAgo :date="experiencia.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="experiencia.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('experiencia', experiencia.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    experiencia: {
        type: Object,
        required: true,
    }
});

</script>

<style scoped>
.cloud {
    background-color: transparent;
    background-image: radial-gradient(white 70%, transparent 30%);
    background-size: calc(var(--border-thickness) * 2) calc(var(--border-thickness) * 2);
    background-position: 0 0;
    position: relative;
    z-index: 1;
    --box-size: 100%;
  --border-thickness: 5%;
  box-shadow: none;
}

.dark .cloud {
    background-image: radial-gradient(black 70%, transparent 30%);
}

.cloud::before {
    content: "";
    display: block;
    background-color: hsla(0, 0%, 100%, 1);
    position: absolute;
    top: var(--border-thickness);
    left: var(--border-thickness);
    height: calc(var(--box-size) - var(--border-thickness) * 2);
    width: calc(var(--box-size) - var(--border-thickness) * 2);
    z-index: -1;
}

.dark .cloud::before {
    background-color: rgb(0, 0, 0);
}
</style>
