<template>
    <div v-if="nav.announce && !nav.announceClosed"
        class="bg-primary text-primary-content px-4 lg:px-8 py-1 flex justify-between items-center">
        <div class="text-transparent">.....</div>
        <div class="text-sm" v-html="nav.announce" />
        <button class="text-2xl" @click=" nav.announceClosed = true" aria-label="Cerrar anuncio" title="Cerrar anuncio">
            <Icon icon="ph:x-square-duotone" aria-hidden="true"/>
        </button>
    </div>
</template>

<script setup>
    const nav = useNav()
</script>
