<template>
    <div class="container">
        <AdminLinks soloInforma modelo="nodo" necesita="administrar archivos" />
    </div>
    <h1 class="hidden" >{{ ruta }}</h1>
    <FolderExplorer :items="items" :propietarioRef="propietarioRef" @updated="reloadPage"
        :ruta="ruta" :rutaBase="$page.props.auth.user?.id ? 'archivos_raiz' : 'archivos'" />
    <transition name="fade">
        <div v-if="showOverlay" class="fixed ç inset-0 z-50 flex items-center justify-center bg-black/60  select-none"
            :style="{
                border: '8px solid ' + (store.puedeEscribir ? '#22c55e' : '#ef4444'),
                boxSizing: 'border-box',
                padding: '0',
            }"
        >
            <div :class="[
                'px-8 py-6 text-2xl font-bold text-white shadow-xl bg-black/80',
                store.puedeEscribir ? 'border-green-500' : 'border-red-500'
            ]"
            style="border-width:4px; border-style:solid;"
            :style="{ borderColor: store.puedeEscribir ? '#22c55e' : '#ef4444' }"
            >
                {{ store.puedeEscribir ? 'Suelta aquí para subir los archivos' : 'No tienes permisos para escribir en la carpeta' }}
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import useFolderExplorerStore from '@/Stores/folderExplorer'

const props = defineProps({
    ruta: {},
    items: Array,
    propietarioRef: Object,
    errors: Object,
    jetstream: Object,
    auth: Object,
    errorBags: Object,
    flash: Object,
    anuncio: [String, Object],
    meta_image_default: String,
    csrf_token: String,
    timestamp_server: [String, Number],
    api_url: String,
    initialTheme: String,
});

const store = useFolderExplorerStore()
const draggingOver = ref(false)
let dragLeaveTimeout = null

// Necesario para poder remover el listener correctamente
function handleBodyMouseLeave() {
    dragLeaveTimeout = setTimeout(() => {
        draggingOver.value = false;
    }, 50);
}

// Overlay solo si el mouse NO está sobre el dropzone
const showOverlay = computed(() => draggingOver.value)

function reloadPage() {
    router.reload({
        only: ['items']
    })
}

function handlePaste(event) {
    const items = event.clipboardData?.items;
    if (!items) return;
    const files = [];
    for (const item of items) {
        if (item.kind === "file") {
            const file = item.getAsFile();
            if (file) files.push(file);
        }
    }
    if (files.length) {
        openUploadModalAndSend(files);
        event.preventDefault();
    }
}

function handleDrop(event) {
    // Si el drop ocurre sobre el dropzone, no hacemos nada (lo maneja el dropzone)
    let el = event.target;
    while (el) {
        if (el.id === 'dropzone') return;
        el = el.parentElement;
    }
    draggingOver.value = false;
    const files = Array.from(event.dataTransfer?.files || []);
    if (files.length) {
        openUploadModalAndSend(files);
        event.preventDefault();
    }
}

function handleDragOver(event) {
    event.preventDefault();
    const hasFiles = event.dataTransfer && Array.from(event.dataTransfer.types).includes('Files');
    if (!hasFiles) {
        draggingOver.value = false;
        return;
    }
    if (event.target && event.target.id === 'dropzone') {
        draggingOver.value = false;
        return;
    }
    if (!draggingOver.value) draggingOver.value = true;
    if (dragLeaveTimeout) {
        clearTimeout(dragLeaveTimeout);
        dragLeaveTimeout = null;
    }
}

function handleDragEnter(event) {
    event.preventDefault();
    const hasFiles = event.dataTransfer && Array.from(event.dataTransfer.types).includes('Files');
    if (!hasFiles) {
        draggingOver.value = false;
        return;
    }
    if (event.target && event.target.id === 'dropzone') {
        draggingOver.value = false;
        return;
    }
    draggingOver.value = true;
    if (dragLeaveTimeout) {
        clearTimeout(dragLeaveTimeout);
        dragLeaveTimeout = null;
    }
}

function handleDragLeave(event) {
    if (event.target === document || event.target === window || event.clientX <= 0 || event.clientY <= 0 || event.clientX >= window.innerWidth || event.clientY >= window.innerHeight) {
        dragLeaveTimeout = setTimeout(() => {
            draggingOver.value = false;
        }, 50);
    } else if (event.target === event.currentTarget) {
        draggingOver.value = false;
    }
}

function openUploadModalAndSend(files) {
    if (!store.puedeEscribir) {
        alert("No tienes permisos para subir archivos en esta carpeta.");
        return;
    }
    // Abre el modal de subida del FolderExplorer
    store.call('subirArchivos');
    // Emite un evento global para que el dropzone real haga el addFiles
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('archivos:upload', { detail: { files } }));
    }, 500);
}

onMounted(() => {
    window.addEventListener('paste', handlePaste);
    window.addEventListener('drop', handleDrop);
    window.addEventListener('dragover', handleDragOver);
    window.addEventListener('dragenter', handleDragEnter);
    window.addEventListener('dragleave', handleDragLeave);
    document.body.addEventListener('mouseleave', handleBodyMouseLeave);
});

onBeforeUnmount(() => {
    window.removeEventListener('paste', handlePaste);
    window.removeEventListener('drop', handleDrop);
    window.removeEventListener('dragover', handleDragOver);
    window.removeEventListener('dragenter', handleDragEnter);
    window.removeEventListener('dragleave', handleDragLeave);
    document.body.removeEventListener('mouseleave', handleBodyMouseLeave);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
}
</style>
