<template>
    <template v-if="contenido">
        <div v-if="isDraft" class="alert alert-warning w-fit ml-auto mb-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Pendiente de publicación</span>
            <a :href="editUrl" class="ml-auto underline">Editar</a>
        </div>
        <div v-else class="w-fit ml-auto mb-12">
            <a :href="editUrl" class="ml-auto underline">Editar</a>
        </div>
    </template>
    <div v-else class="w-fit ml-auto mb-12">
        <a :href="adminUrl" class="underline">Administrar</a>
    </div>
</template>

<script setup>

const props = defineProps({
    contenido: Object,
    modelo: String
})


// const searchParams = new URLSearchParams(window.location.search);
// const isPreviewMode = searchParams.get('preview') !== null

const isDraft = ref(props.contenido ? props.contenido.visibilidad != 'P' : null)

const editUrl = ref(props.contenido ? `/admin/${props.modelo.replace(/e?s$/, '')}/${props.contenido.id}/edit` : null)

const adminUrl = ref(`/admin/${props.modelo.replace(/e?s$/, '')}`)

</script>
