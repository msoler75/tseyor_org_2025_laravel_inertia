<template>
    <Modal :show="modalConfiguracion" maxWidth="2xl" @close="modalConfiguracion = false">

        <form class="bg-base-200 p-5 select-none" @submit.prevent="guardarConfiguracion">
            <h3>Configuración del Equipo</h3>

            <!-- Select para móvil -->
            <select v-model="tabActiva" class="select select-bordered w-full mb-4 sm:hidden">
                <option v-for="t in tabs" :key="t.id" :value="t.id">{{ t.label }}</option>
            </select>

            <!-- Tabs para desktop -->
            <div class="hidden sm:flex gap-1 border-b border-base-300 mb-4">
                <button v-for="t in tabs" :key="t.id" type="button"
                    class="px-3 py-1.5 text-xs font-medium rounded-t transition-colors"
                    :class="tabActiva === t.id ? 'bg-base-100 text-primary border-b-2 border-primary' : 'text-base-content/60 hover:text-base-content'"
                    @click="tabActiva = t.id">
                    {{ t.label }}
                </button>
            </div>

            <!-- Contenido de pestañas -->
            <div class="tabs-panel">
                <!-- General -->
                <div v-show="tabActiva === 'general'" class="space-y-6">
                    <div>
                        <label for="nombre">Nombre</label>
                        <input id="nombre" v-model="edicion.nombre" required :readonly="equipo.miembros.length >= 3"
                            class="input" maxlength="48" />
                        <div v-if="edicion.errors.nombre" class="error">{{ edicion.errors.nombre[0] }}</div>
                        <div v-else class="text-sm">Nombre del equipo. No se puede editar si tiene 3 miembros o
                            más.
                        </div>
                    </div>

                    <div>
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" v-model="edicion.descripcion" required maxlength="400"
                            class="shadow-2xs textarea w-full"></textarea>
                        <div v-if="edicion.errors.descripcion" class="error">{{ edicion.errors.descripcion[0] }}
                        </div>
                        <div v-else class="text-sm">Descripción del equipo y sus funciones.</div>
                    </div>
                </div>

                <!-- Imagen -->
                <div v-show="tabActiva === 'imagen'">
                    <div>
                        <div class="flex justify-center">
                            <Image v-if="equipo.imagen && !edicion.imagen" :src="equipo.imagen" class="w-32 h-32 mb-8" />
                        </div>

                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" @change="changeInputFile" accept="image/*" class="file-input">
                        <div v-if="edicion.errors.imagen" class="error">{{ edicion.errors.imagen[0] }}</div>
                        <div v-else class="text-sm">Sube una nueva imagen si quieres cambiar la actual.</div>
                    </div>
                </div>

                <!-- Anuncio -->
                <div v-show="tabActiva === 'anuncio'" class="space-y-6">
                    <div>
                        <label for="anuncio">Anuncio</label>
                        <ClientOnly>
                            <TipTapEditor id="anuncio" v-model="edicion.anuncio" />
                        </ClientOnly>
                        <div v-if="edicion.errors.anuncio" class="error">{{ edicion.errors.anuncio[0] }}</div>
                        <div v-else class="text-sm">Anuncio de caracter general. Se puede dejar en blanco.</div>
                    </div>
                </div>

                <!-- Reuniones -->
                <div v-show="tabActiva === 'reuniones'">
                    <div>
                        <label for="reuniones">Reuniones</label>
                        <ClientOnly>
                            <TipTapEditor id="reuniones" v-model="edicion.reuniones" />
                        </ClientOnly>
                        <div v-if="edicion.errors.reuniones" class="error">{{ edicion.errors.reuniones[0] }}
                        </div>
                        <div v-else class="text-sm">Ejemplo: Los lunes a las 13h. Se puede dejar en blanco.
                        </div>
                    </div>
                </div>

                <!-- Información -->
                <div v-show="tabActiva === 'informacion'">
                    <div>
                        <label for="informacion">informacion</label>
                        <ClientOnly>
                            <TipTapEditor id="informacion" v-model="edicion.informacion" />
                        </ClientOnly>
                        <div v-if="edicion.errors.informacion" class="error">{{ edicion.errors.informacion[0] }}
                        </div>
                        <div v-else class="text-sm">Información adicional del equipo.</div>
                    </div>
                </div>

                <!-- Carpetas -->
                <div v-show="tabActiva === 'carpetas'" class="space-y-3">
                    <p class="text-sm">Elige qué carpetas mostrar y en qué orden. Arrastra para reordenar.</p>

                    <div v-if="!carpetasEquipo.length" class="text-sm opacity-60">
                        Este equipo no tiene carpetas.
                    </div>

                    <div v-else class="space-y-1">
                        <div v-for="(carpeta, index) in carpetasEquipo" :key="carpeta.id"
                            class="flex items-center gap-3 p-2 rounded bg-base-100 border border-base-300/50 cursor-grab active:cursor-grabbing"
                            :class="{ 'opacity-40': !carpeta.visible }"
                            draggable="true"
                            @dragstart="onDragStart(index)"
                            @dragover.prevent="onDragOver(index)"
                            @drop="onDrop(index)"
                            @dragend="onDragEnd">
                            <Icon icon="ph:grip-vertical" class="text-base-content/40 shrink-0" />
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                                v-model="carpeta.visible" />
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">{{ carpeta.nombre }}</div>
                                <div class="text-xs text-base-content/50 truncate">{{ carpeta.ubicacion }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" class="btn btn-xs btn-ghost"
                            @click="restablecerCarpetas">
                            Restablecer (mostrar todas)
                        </button>
                    </div>
                </div>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <div v-if="edicion.processing" class="flex gap-3 btn">
                    <Spinner /> Guardando...
                </div>

                <button v-else type="submit" class="btn btn-primary">
                    Guardar
                </button>

                <button @click.prevent="cerrarConfiguracion" type="button" class="btn btn-neutral">
                    Cancelar
                </button>
            </div>
        </form>
    </Modal>
</template>


<script setup>
defineExpose({
    mostrar
});

const props = defineProps({ equipo: { type: Object, required: true }, carpetas: { type: Array, default: () => [] } })

const emit = defineEmits(['updated'])

// Tabs
const tabs = [
    { id: 'general', label: 'General' },
    { id: 'imagen', label: 'Imagen' },
    { id: 'anuncio', label: 'Anuncio' },
    { id: 'reuniones', label: 'Reuniones' },
    { id: 'informacion', label: 'Información' },
    { id: 'carpetas', label: 'Carpetas' },
]
const tabActiva = ref('general')

// Diálogo Modal de Configuracion DEL EQUIPO

const edicion = reactive({ id: props.equipo.id, imagen: null, nombre: '', descripcion: '', anuncio: '', reuniones: '', informacion: '', errors: {}, processing: false })
const campos = ['nombre', 'descripcion', 'imagen', 'anuncio', 'reuniones', 'informacion']
const modalConfiguracion = ref(false)

// Carpetas: lista con estado de visibilidad y orden
const carpetasEquipo = ref([])
const dragIndex = ref(null)

// mostrar modal
function mostrar() {
    limpiarErrores()
    for (const campo of campos)
        edicion[campo] = props.equipo[campo]
    edicion.imagen = null
    tabActiva.value = 'general'

    // Inicializar lista de carpetas
    const ordenGuardado = props.equipo.carpetas_orden
    carpetasEquipo.value = props.carpetas.map(c => ({
        id: c.id,
        nombre: c.ubicacion?.substring(c.ubicacion?.lastIndexOf('/') + 1),
        ubicacion: c.ubicacion,
        visible: ordenGuardado ? ordenGuardado.includes(c.id) : true,
        orden: ordenGuardado ? ordenGuardado.indexOf(c.id) : 0,
    }))

    // Si hay orden guardado, ordenar según ese array
    if (ordenGuardado) {
        carpetasEquipo.value.sort((a, b) => {
            const ia = ordenGuardado.indexOf(a.id)
            const ib = ordenGuardado.indexOf(b.id)
            return (ia === -1 ? 9999 : ia) - (ib === -1 ? 9999 : ib)
        })
    }

    modalConfiguracion.value = true
}

function changeInputFile(event) {
    edicion.imagen = event.target.files[0]
}

function limpiarErrores() {
    Object.keys(edicion.errors).forEach(key => {
        delete edicion.errors[key];
    });
}

function cerrarConfiguracion() {
    modalConfiguracion.value = false
}

function limpiarNull(v) {
    if (v === null || v == 'null') return ''
    return v
}

// --- Drag and Drop para carpetas ---

function onDragStart(index) {
    dragIndex.value = index
}

function onDragOver(index) {
    if (dragIndex.value === null || dragIndex.value === index) return
    const item = carpetasEquipo.value.splice(dragIndex.value, 1)[0]
    carpetasEquipo.value.splice(index, 0, item)
    dragIndex.value = index
}

function onDrop(index) {
    dragIndex.value = null
}

function onDragEnd() {
    dragIndex.value = null
}

function restablecerCarpetas() {
    carpetasEquipo.value.forEach(c => c.visible = true)
}

function guardarConfiguracion() {

    console.log('guardar Configuracion', edicion)
    const data = new FormData();
    data.append('nombre', edicion.nombre);
    data.append('descripcion', limpiarNull(edicion.descripcion));
    if (edicion.imagen)
        data.append('imagen', edicion.imagen);
    data.append('anuncio', limpiarNull(edicion.anuncio))
    data.append('reuniones', limpiarNull(edicion.reuniones))
    data.append('informacion', limpiarNull(edicion.informacion))

    // Guardar orden de carpetas: solo las visibles, en su orden
    const carpetasVisibles = carpetasEquipo.value.filter(c => c.visible).map(c => c.id)
    const todasVisibles = carpetasVisibles.length === props.carpetas.length
    data.append('carpetas_orden', JSON.stringify(todasVisibles ? null : carpetasVisibles))

    // actualizamos en el servidor
    edicion.processing = true;
    axios.post(route('equipo.modificar', props.equipo.id), data).then((response) => {
        edicion.processing = false;

        limpiarErrores();
        modalConfiguracion.value = false
        emit('updated')

    }).catch(error => {
        edicion.processing = false;
        console.log('error', error)
        if (error.response.data.errors) {
            edicion.errors = error.response.data.errors
            if (('nombre' in edicion.errors) || ('descripcion' in edicion.errors))
                tabActiva.value = 'general'
            else if ('imagen' in edicion.errors)
                tabActiva.value = 'imagen'
            else if ('anuncio' in edicion.errors)
                tabActiva.value = 'anuncio'
            else if ('reuniones' in edicion.errors)
                tabActiva.value = 'reuniones'
            else if ('informacion' in edicion.errors)
                tabActiva.value = 'informacion'
        }
        else
            alert(error.response.data.error)
    });
}

</script>

<style scoped>
:deep(.ql-editor) {
    max-height: calc(100vh - 500px);
}
.tabs-panel {
    max-height: calc(98vh - 250px);
    overflow-y: auto;
}
@media (max-width: 640px) {
    .tabs-panel {
        max-height: calc(98vh - 300px);
    }
}
</style>
