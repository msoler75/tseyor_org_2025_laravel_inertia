<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Psicografías</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="psicografia" necesita="administrar contenidos" :contenido="psicografia" />
            </div>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ psicografia.titulo }}</h1>

                <div class="text-neutral text-sm mb-20 flex justify-between">
                    <Link :href="route('psicografias')+'?categoria='+psicografia.categoria" class="no-underline badge badge-info hover:badge-secondary">{{ psicografia.categoria }}</Link>
                    <TimeAgo :date="psicografia.updated_at" :includeTime="false" />
                </div>

                <div>{{ psicografia.descripcion }}</div>

                <Content :content="`<img src='${psicografia.imagen}'>`" class="mx-auto" />

            </div>

        </div>

        <Comentarios :url="route('psicografia', psicografia.id)" />

    </Page>
</template>

<script setup>
import { parseFiles } from '@/composables/parseFiles'


const props = defineProps({
    psicografia: {
        type: Object,
        required: true,
    }
});

</script>
