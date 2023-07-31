<template>
    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietario="propietario" @updated="reloadFolder"
        @folder="folder" @file="file" :embed="false" />
</template>


<script setup>

const props = defineProps({
    url: {},
});

function reloadFolder() {
    router.reload({
        only: ['items']
    })
}

function folder(item) {
    console.log('folder change to', item)
}

function file(item) {
    console.log('file clicked', item)
}

</script>

