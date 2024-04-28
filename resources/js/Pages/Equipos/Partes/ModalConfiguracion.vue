<template>
    <Modal :show="modalConfiguracion" maxWidth="lg" @close="modalConfiguracion = false">

        <form class="bg-base-200 p-5 select-none" @submit.prevent="guardarConfiguracion">
            <h3>Configuración del Equipo</h3>

            <tabs  :options="{ disableScrollBehavior: true, useUrlFragment: false }" ref="tabsElem">
                <tab name="General" class="space-y-6">

                    <div>
                        <label for="nombre">Nombre</label>
                        <input id="nombre" v-model="edicion.nombre" required :readonly="equipo.miembros.length >= 3"
                            class="input" />
                        <div v-if="edicion.errors.nombre" class="error">{{ edicion.errors.nombre[0] }}</div>
                        <div v-else class="text-sm">Nombre del equipo. No se puede editar si tiene 3 miembros o
                            más.
                        </div>
                    </div>

                    <div>
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" v-model="edicion.descripcion" required
                            class="shadow textarea w-full"></textarea>
                        <div v-if="edicion.errors.descripcion" class="error">{{ edicion.errors.descripcion[0] }}
                        </div>
                        <div v-else class="text-sm">Descripción del equipo y sus funciones.</div>
                    </div>

                </tab>

                <tab name="imagen">

                    <div>

                        <div class="flex justify-center">
                            <Image v-if="equipo.imagen&&!edicion.imagen" :src="equipo.imagen" class="w-32 h-32 mb-8" />
                        </div>

                        <label for="imagen">Imagen</label>
                        <input type="file" id="imagen" @change="changeInputFile" accept="image/*" class="file-input">
                        <div v-if="edicion.errors.imagen" class="error">{{ edicion.errors.imagen[0] }}</div>
                        <div v-else class="text-sm">Sube una nueva imagen si quieres cambiar la actual.</div>
                    </div>

                </tab>


                <tab name="Anuncio" class="space-y-6">

                    <div>
                        <label for="anuncio">Anuncio</label>
                        <TipTapEditor id="anuncio" v-model="edicion.anuncio" />
                        <div v-if="edicion.errors.anuncio" class="error">{{ edicion.errors.anuncio[0] }}</div>
                        <div v-else class="text-sm">Anuncio de caracter general. Se puede dejar en blanco.</div>
                    </div>

                </tab>

                <tab name="Reuniones">

                    <div>
                        <label for="reuniones">Reuniones</label>
                        <TipTapEditor id="reuniones" v-model="edicion.reuniones"  />
                        <div v-if="edicion.errors.reuniones" class="error">{{ edicion.errors.reuniones[0] }}
                        </div>
                        <div v-else class="text-sm">Ejemplo: Los lunes a las 13h. Se puede dejar en blanco.
                        </div>
                    </div>
                </tab>

                <tab name="Información">
                    <div>
                        <label for="informacion">informacion</label>
                        <TipTapEditor id="informacion" v-model="edicion.informacion" />
                        <div v-if="edicion.errors.informacion" class="error">{{ edicion.errors.informacion[0] }}
                        </div>
                        <div v-else class="text-sm">Información adicional del equipo.</div>
                    </div>
                </tab>
            </tabs>


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
import { Tabs, Tab } from 'vue3-tabs-component';

defineExpose({
    mostrar
});

const props = defineProps({ equipo: { type: Object, required: true } })

const emit = defineEmits(['updated'])

const tabsElem = ref(null)

// Diálogo Modal de Configuracion DEL EQUIPO

const edicion = reactive({ id: props.equipo.id, imagen: null, nombre: '', descripcion: '', anuncio: '', reuniones: '', informacion:'', errors: {}, processing: false })
const campos = ['nombre', 'descripcion', 'imagen', 'anuncio', 'reuniones', 'informacion']
const modalConfiguracion = ref(false)



// mostrar modal
function mostrar() {
    limpiarErrores()
    for (const campo of campos)
        edicion[campo] = props.equipo[campo]
    edicion.imagen = null
    console.log('modalConfiguracion.mostrar:', edicion)
    modalConfiguracion.value = true
}

function changeInputFile(event) {
    // console.log('changeInput', event)
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
    if(v===null||v=='null') return ''
    return v
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

    // actualizamos en el servidor
    console.log(edicion)
    edicion.processing = true;
    axios.post(route('equipo.modificar', props.equipo.id), data).then((response) => {
        edicion.processing = false;

        // console.log('guardado!', response)
        // actualizamos en la página
        //for (const campo of campos)
          //  props.equipo[campo] = edicion[campo]
        //props.equipo.imagen = response.data.imagen

        limpiarErrores();

        // cerramos el modal
        modalConfiguracion.value = false

        emit('updated')

    }).catch(error => {
        edicion.processing = false;
        console.log('error', error)
        if (error.response.data.errors) {
            edicion.errors = error.response.data.errors
            console.log(edicion.errors)
            if (('nombre' in edicion.errors) || ('descripcion' in edicion.errors))
                tabsElem.value.selectTab('#general')
            else if ('imagen' in edicion.errors)
                tabsElem.value.selectTab('#imagen')
            else if ('anuncio' in edicion.errors)
                tabsElem.value.selectTab('#anuncio')
            else if ('reuniones' in edicion.errors)
                tabsElem.value.selectTab('#reuniones')
            else if ('informacion' in edicion.errors)
                tabsElem.value.selectTab('#informacion')
        }
        else //error general
            alert(error.response.data.error)
    });


}

</script>

<style scoped>
 :deep(.ql-editor) {
        max-height: calc(100vh - 500px);
	}
</style>
