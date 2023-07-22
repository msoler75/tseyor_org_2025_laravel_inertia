<template>
    <Modal :show="modalConfiguracion" maxWidth="lg" @close="modalConfiguracion = false">

        <form class="bg-base-200 p-5 select-none" @submit.prevent="guardarConfiguracion">
            <h3>Configuración del Equipo</h3>

            <tabs :options="{ useUrlFragment: false }">
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
                            <Image v-if="equipo.imagen" :src="equipo.imagen" class="h-32 mb-8" />
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
                        <QuillEditor id="anuncio" theme="snow" v-model:content="edicion.anuncio" contentType="html" />
                        <div v-if="edicion.errors.anuncio" class="error">{{ edicion.errors.anuncio[0] }}</div>
                        <div v-else class="text-sm">Anuncio de caracter general. Se puede dejar en blanco.</div>
                    </div>

                </tab>

                <tab name="Reuniones">

                    <div>
                        <label for="reuniones">Reuniones</label>
                        <QuillEditor id="reuniones" theme="snow" v-model:content="edicion.reuniones" contentType="html" />
                        <div v-if="edicion.errors.reuniones" class="error">{{ edicion.errors.reuniones[0] }}
                        </div>
                        <div v-else class="text-sm">Ejemplo: Los lunes a las 13h. Se puede dejar en blanco.
                        </div>
                    </div>
                </tab>

                <tab name="Información">
                    <div>
                        <label for="informacion">informacion</label>
                        <QuillEditor id="informacion" theme="snow" v-model:content="edicion.informacion"
                            contentType="html" />
                        <div v-if="edicion.errors.informacion" class="error">{{ edicion.errors.informacion[0] }}
                        </div>
                        <div v-else class="text-sm">Información adicional del equipo.</div>
                    </div>
                </tab>
            </tabs>


            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button type="submit" class="btn btn-primary">
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
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

defineExpose({
    mostrar
});


const props = defineProps({ equipo: { type: Object, required: true } })

// Diálogo Modal de Configuracion DEL EQUIPO

const edicion = reactive({ id: props.equipo.id, imagen: null, nombre: null, descripcion: null, anuncio: null, reuniones: null, errors: {}, processing: false })
const campos = ['nombre', 'descripcion', 'imagen', 'anuncio', 'reuniones', 'informacion']
const modalConfiguracion = ref(false)


// mostrar modal
function mostrar() {
    for (const campo of campos)
        edicion[campo] = props.equipo[campo]
    edicion.imagen = null
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
    limpiarErrores()
    modalConfiguracion.value = false
}

function guardarConfiguracion() {

    const data = new FormData();
    data.append('nombre', edicion.nombre);
    data.append('descripcion', edicion.descripcion);
    if (edicion.imagen)
        data.append('imagen', edicion.imagen);
    data.append('anuncio', edicion.anuncio);
    data.append('reuniones', edicion.reuniones);
    data.append('informacion', edicion.informacion);

    // actualizamos en el servidor
    console.log(edicion)
    edicion.processing = true;
    axios.post(route('equipo.modificar', props.equipo.id), data).then((response) => {
        edicion.processing = false;

        console.log('guardado!', response)
        // actualizamos en la página
        for (const campo of campos)
            props.equipo[campo] = edicion[campo]
        props.equipo.imagen = response.data.imagen

        limpiarErrores();

        // cerramos el modal
        modalConfiguracion.value = false

    }).catch(error => {
        edicion.processing = false;
        console.log('error', error)
        if (error.response.data.errors)
            edicion.errors = error.response.data.errors
        else //error general
            alert(error.response.data.error)
    });


}

</script>
