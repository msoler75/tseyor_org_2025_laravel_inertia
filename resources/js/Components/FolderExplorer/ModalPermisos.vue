<template>

        <!-- Modal Cambiar Permisos -->
        <Modal :show="modalCambiarPermisos" @close="modalCambiarPermisos = false" maxWidth="sm">
            <div class="p-5">
                <div class="font-bold text-lg">Permisos</div>
                <form>
                    <p>/{{ itemCambiandoPermisos.ruta }}</p>
                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos de propietario:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit8" type="checkbox"
                                    v-model="permisosBits[8]"><label for="bit8">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit7" type="checkbox"
                                    v-model="permisosBits[7]"><label for="bit7">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit6" type="checkbox" v-model="permisosBits[6]"><label for="bit4">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos de grupo:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit5" type="checkbox"
                                    v-model="permisosBits[5]"><label for="bit5">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit4" type="checkbox"
                                    v-model="permisosBits[4]"><label for="bit4">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit3" type="checkbox" v-model="permisosBits[3]"><label for="bit3">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos públicos:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit2" type="checkbox"
                                    v-model="permisosBits[2]"><label for="bit2">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit1" type="checkbox"
                                    v-model="permisosBits[1]"><label for="bit1">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit0" type="checkbox" v-model="permisosBits[0]"><label for="bit0">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset v-if="itemCambiandoPermisos.tipo == 'carpeta'"
                        class="border border-solid border-neutral p-3 select-none">
                        <legend>Proteger contenidos (sticky bit):</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit9" type="checkbox"
                                    v-model="permisosBits[9]"><label for="bit9">Proteger contenidos</label></div>
                        </div>
                        <small>solo los propietarios pueden modificar o eliminar sus contenidos</small>
                    </fieldset>

                    <fieldset class="mt-2 flex gap-2">
                        <label for="permi">Valor numérico:</label><input type="text" v-model="permisosNumerico"
                            class="max-w-[5rem]" @change="permisosNumericoChange" @keyup="permisosNumericoChange">
                    </fieldset>
                </form>

                <div class="py-3 flex justify-between sm:justify-end gap-5">

                    <button @click.prevent="cambiarPermisos" type="button" class="btn btn-primary btn-sm"
                        :disabled="guardandoPermisos">
                        <div v-if="guardandoPermisos" class="flex items-center gap-x">
                            <Spinner />
                            Guardando...
                        </div>
                        <span v-else>
                            Guardar
                        </span>
                    </button>

                    <button @click.prevent="modalCambiarPermisos = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </Modal>
</template>




<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()

store.on('permisos', abrirModalCambiarPermisos)



// PERMISOS

const modalCambiarPermisos = ref(false)
const itemCambiandoPermisos = ref(null)
const permisosBits = ref([])
const permisosNumerico = ref("")
const permisosNumericoComputed = computed(() => {
    let p = 0
    for (let i = 0; i < 10; i++)
        p = p + permisosBits.value[i] * Math.pow(2, i)
    return p.toString(8)
})
const guardandoPermisos = ref(false)

watch(permisosNumericoComputed, (value) => {
    console.log('permisosBits change', value)
    permisosNumerico.value = permisosNumericoComputed.value
})

function calcularPermisosBits() {
    const octalPermisos = permisosNumerico.value // Obtener el número en octal como una cadena de texto
    const decimalPermisos = parseInt(octalPermisos, 8); // Convertir el número octal a decimal
    permisosBits.value = []; // Limpiar el array de bits
    for (let i = 0; i < 10; i++) {
        const bit = (decimalPermisos >> i) & 1; // Extraer el bit en la posición i
        permisosBits.value.push(!!bit); // Agregar el bit al array de bits
    }
}

function abrirModalCambiarPermisos(item) {
    guardandoPermisos.value = false
    itemCambiandoPermisos.value = item
    modalCambiarPermisos.value = true
    permisosNumerico.value = item.permisos
    calcularPermisosBits()
}

function permisosNumericoChange() {
    if (permisosNumerico.value.length >= 3)
        calcularPermisosBits()
}

function cambiarPermisos() {
    guardandoPermisos.value = true
    axios.post('/files/update', {
        ruta: itemCambiandoPermisos.value.ruta,
        permisos: permisosNumerico.value
    })
        .then(response => {
            console.log({response})
            // cierra el modal y actualiza los permisos
            itemCambiandoPermisos.value.permisos = permisosNumerico.value
            modalCambiarPermisos.value = false
            store.permisosModificados = true
        })
        .catch(err => {
            console.warn({err})
            const errorMessage = err.response?.data?.error || 'Ocurrió un error al guardar los cambios'
            alert(errorMessage)
            guardandoPermisos.value = false
        })
}




</script>
