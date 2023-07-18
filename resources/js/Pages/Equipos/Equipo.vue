<template>
    <div>
        <div class="sticky top-14 py-5 bg-base-100 border-b border-gray-300 z-30"
            :class="nav.scrollY < 200 ? 'hidden' : ''">
            <div class="container mx-auto flex gap-5 items-center">
                <Image :src="equipo.imagen" alt="Imagen del equipo" class="h-10 object-cover rounded-lg" />
                <h1 class="my-2 text-2xl">
                    {{ equipo.nombre }}
                </h1>
                <div class="hidden sm:flex ml-auto gap-3 text-2xl items-center">
                    <Icon icon="ph:user-duotone" />
                    {{ equipo.usuarios.length }}
                </div>
            </div>
        </div>

        <div class="container mx-auto py-12">
            <GridFill class="gap-7" w="20rem">

                <div class="sm:card sm:bg-base-100 sm:shadow sm:p-5 flex gap-5 sm:col-span-2">
                    <div class="w-full flex flex-wrap sm:flex-nowrap gap-5 justify-center items-center">
                        <Image :src="equipo.imagen" alt="Imagen del equipo"
                            class="w-[200px] sm:h-full sm:w-40 object-cover rounded-lg" />
                        <div
                            class="flex flex-col gap-5 justify-center items-center sm:justify-start sm:items-start text-center sm:text-left">
                            <h3 class="my-0">
                                {{ equipo.nombre }}
                            </h3>
                            <div class="w-full opacity-80">{{ equipo.descripcion }}</div>
                            <div class="badge badge-neutral">{{ equipo.categoria }}</div>
                            <div class="sm:hidden flex justify-center text-2xl mt-12">
                                <Icon icon="ph:user-duotone" />
                                {{ equipo.usuarios.length }}
                            </div>

                        </div>

                        <div class="hidden sm:flex sm:ml-auto gap-3 text-2xl items-center self-end justify-center">
                            <Icon icon="ph:user-duotone" />
                            {{ equipo.usuarios.length }}
                        </div>
                    </div>
                </div>

                <div class="card shadow p-5 bg-base-100 border border-orange-400 justify-center items-center"
                    v-if="equipo.anuncio">
                    <div class="prose" v-html="equipo.anuncio" />
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="equipo.reuniones">
                    <h3>Reuniones</h3>
                    <div class="prose" v-html="equipo.reuniones" />
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="ultimosArchivos.length">
                    <h3>Últimos Archivos</h3>
                    <div class="w-full">
                        <div v-for="item, index of ultimosArchivos" :key="index"
                            class="flex gap-3 items-center py-2 w-full">
                            <FileIcon :url="item.url" :name="item.archivo" />
                            <Link :href="item.url" class="py-1 hover:underline">{{
                                item.url.substring(item.url.lastIndexOf('/') +
                                    1)
                            }}</Link>
                            <TimeAgo class="ml-auto" :date="item.fecha_modificacion" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="carpetas.length">
                    <h3>Carpetas</h3>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline py-2">
                            <FolderIcon :url="item.ruta" />
                            <Link :href="'/' + item.ruta" class="py-1 hover:underline">{{
                                item.ruta.substring(item.ruta.lastIndexOf('/') + 1) }}</Link>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5" v-if="false">
                    <h3>Publicaciones</h3>
                    <div>
                        <ul>
                            <li> item 1</li>
                            <li> item 2</li>
                        </ul>
                    </div>
                </div>

                <div class="card bg-base-100 shadow p-5">
                    <h3>Miembros</h3>
                    <Users v-if="equipo" :users="equipo.usuarios" :count="13" />
                </div>

                <div class="card bg-base-100 shadow p-5">
                    <h3>Libros recomendados</h3>
                    <div>
                        <ul>
                            <li> item 1</li>
                            <li> item 2</li>
                        </ul>
                    </div>
                </div>


                <div class="card bg-base-100 shadow p-5 select-none">
                    <h3>Administración</h3>
                    <ul class="list-none p-0 space-y-2">
                        <li class="flex gap-2 items-center cursor-pointer">
                            <Icon icon="ph:share-fat-duotone" />Enlace del equipo
                        </li>
                        <li class="flex gap-2 items-center">
                            <Icon icon="ph:user-plus-duotone" />Invitar a usuario/s
                        </li>
                        <li class="flex gap-2 items-center cursor-pointer">
                            <Icon icon="ph:envelope-duotone" />Administrar peticiones de ingreso
                        </li>
                        <li class="flex gap-2 items-center cursor-pointer" @click="administrarUsuarios">
                            <Icon icon="ph:users-duotone" />Administrar usuarios
                        </li>
                        <li class="flex gap-2 items-center cursor-pointer" @click="editarConfiguracion">
                            <Icon icon="ph:gear-six-duotone" />Configuración
                        </li>
                    </ul>
                </div>

                <Modal :show="modalConfiguracion" maxWidth="md" @close="modalConfiguracion=false">

                    <form class="bg-base-200 p-5 space-y-7" @submit.prevent="guardarConfiguracion">
                        <h3>Configuración del Equipo</h3>


                            <div>
                                <label for="nombre">Nombre</label>
                                <input id="nombre" v-model="config.nombre" required :readonly="equipo.usuarios.length >= 3"
                                    class="input" />
                                <div v-if="config.errors.nombre" class="error">{{ config.errors.nombre[0] }}</div>
                                <div v-else class="text-sm">Nombre del equipo. No se puede editar si tiene 3 miembros o más.
                                </div>
                            </div>

                            <div>
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" v-model="config.descripcion" required
                                    class="shadow textarea w-full"></textarea>
                                <div v-if="config.errors.descripcion" class="error">{{ config.errors.descripcion[0] }}</div>
                                <div v-else class="text-sm">Descripción del equipo y sus funciones.</div>
                            </div>

                            <div>
                                <label for="imagen">Imagen</label>
                                <input type="file" id="imagen" @change="changeInputFile" accept="image/*"
                                    class="file-input">
                                <div v-if="config.errors.imagen" class="error">{{ config.errors.imagen[0] }}</div>
                                <div v-else class="text-sm">Sube una nueva imagen si quieres cambiar la actual.</div>
                            </div>



                            <div>
                                <label for="anuncio">Anuncio</label>
                                <QuillEditor id="anuncio" theme="snow" v-model:content="config.anuncio"
                                    contentType="html" />
                                <div v-if="config.errors.anuncio" class="error">{{ config.errors.anuncio[0] }}</div>
                                <div v-else class="text-sm">Anuncio de caracter general. Se puede dejar en blanco.</div>
                            </div>

                            <div>
                                <label for="reuniones">Reuniones</label>
                                <QuillEditor id="reuniones" theme="snow" v-model:content="config.reuniones"
                                    contentType="html" />
                                <div v-if="config.errors.reuniones" class="error">{{ config.errors.reuniones[0] }}</div>
                                <div v-else class="text-sm">Ejemplo: Los lunes a las 13h. Se puede dejar en blanco.</div>
                            </div>



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


                <Modal :show="modalUsuarios" maxWidth="sm" @close="modalUsuarios=false">
                    <div class="bg-base-200 p-5 flex flex-col gap-5 max-h-[90vh] select-none">
                        <h3>Miembros del Equipo</h3>

                        <input type="search" class="input shadow flex-shrink-0" placeholder="Buscar..."
                            v-model="usuarioBuscar">
                        <div class="overflow-y-auto bg-base-100 shadow">
                            <table class="table w-full">
                                <tbody class="divide-y">
                                    <tr v-for="user of usuariosFilto" :key="user.id"
                                        class="cursor-pointer" :class="user.pivot.rol == 'coordinador' ? 'bg-blue-50' : ''">
                                        <td>{{ user.name }}</td>
                                        <td>
                                            <select v-model="user.pivot.rol" class="select" @change="changeRol(user)">
                                                <option value="coordinador">coordinador</option>
                                                <option value=""><span class="opacity-50">miembro</span></option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="py-3 flex justify-between sm:justify-end gap-5">
                            <button @click.prevent="modalUsuarios = false" type="button" class="btn btn-neutral">
                                cerrar
                            </button>
                        </div>
                    </div>
                </Modal>


            </GridFill>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useFuse } from '@vueuse/integrations/useFuse'
import { useNav } from '@/Stores/nav'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

defineOptions({ layout: AppLayout })

const props = defineProps({
    equipo: {
        type: Object,
        required: true,
    },
    usuarios: {},
    totalMiembros: Number,
    ultimosArchivos: {},
    carpetas: {}
})

const nav = useNav()

// Configuracion DEL EQUIPOS

const config = reactive({ id: props.equipo.id, imagen: null, nombre: null, descripcion: null, anuncio: null, reuniones: null, errors: {}, processing: false })
const campos = ['nombre', 'descripcion', 'imagen', 'anuncio', 'reuniones']
const modalConfiguracion = ref(false)

function editarConfiguracion() {
    for (const campo of campos)
        config[campo] = props.equipo[campo]
    config.imagen = null
    modalConfiguracion.value = true
}

function changeInputFile(event) {
    console.log('changeInput', event)
    config.imagen = event.target.files[0]
}

function limpiarErrores() {
    Object.keys(config.errors).forEach(key => {
        delete config.errors[key];
    });
}

function cerrarConfiguracion() {
    limpiarErrores()
    modalConfiguracion.value = false
}

function guardarConfiguracion() {

    const data = new FormData();
    data.append('nombre', config.nombre);
    data.append('descripcion', config.descripcion);
    if (config.imagen)
        data.append('imagen', config.imagen);
    data.append('anuncio', config.anuncio);
    data.append('reuniones', config.reuniones);

    // actualizamos en el servidor
    console.log(config)
    config.processing = true;
    axios.post(route('equipo.modificar', props.equipo.id), data).then((response) => {
        config.processing = false;

        // actualizamos en la página
        for (const campo of campos)
            props.equipo[campo] = config[campo]
        props.equipo.imagen = response.data.imagen

        limpiarErrores();

        // cerramos el modal
        modalConfiguracion.value = false

    }).catch(error => {
        config.processing = false;
        console.log('error', error)
        config.errors = error.response.data.errors
    });


}

// ADMINISTRAR USUARIOS

const modalUsuarios = ref(false)
const usuarioBuscar = ref("")
const usuariosFuse = useFuse(usuarioBuscar, () => props.usuarios, { fuseOptions: { keys: ['name', 'email'], threshold: 0.3 } })

const usuariosFilto = computed(() => {
    if (!props.usuarios) return []
    if (usuarioBuscar.value)
        return usuariosFuse.results.value.map(r => ({ id: r.item.id, name: r.item.name /* +r.refIndex*/, pivot: r.item.pivot }))
    return props.usuarios
});

function administrarUsuarios() {
    modalUsuarios.value = true
    if (!props.usuarios) {
        router.reload({
            only: ['usuarios']
        })
    }
}

function changeRol(user) {
    console.log('changedRol', user)
    axios.put(route('equipo.modificarRol', {idEquipo: props.equipo.id, idUsuario: user.id, rol:user.pivot.rol ||'miembro'}))
    .catch(err=>{
        alert("No se han podido guardar los cambios")
    });
}


</script>


<style>
.ql-editor {
    @apply bg-base-100;
}</style>
