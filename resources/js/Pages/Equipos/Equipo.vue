<template>
    <div>
        <EquipoCabecera :equipo="equipo" />

        <div class="container mx-auto py-12">

            {{ solicitud }}

            <div class="flex justify-between items-center mb-5">
                <Back :href="route('equipos')">Equipos</Back>

                <EquipoMembresia class="hidden sm:block" :equipo-id="equipo.id" v-model="solicitud" :soyMiembro="soyMiembro"/>

            </div>

            <GridFill class="gap-7" w="20rem">

                <EquipoInformacion :equipo="equipo" />

                <EquipoMembresia class="sm:hidden" :equipo-id="equipo.id" v-model="solicitud" :soyMiembro="soyMiembro"/>

                <Card v-if="equipo.anuncio" class="border border-orange-400 justify-center items-center">
                    <div class="prose" v-html="equipo.anuncio" />
                </Card>

                <Card v-if="equipo.reuniones">
                    <h3>Reuniones</h3>
                    <div class="prose" v-html="equipo.reuniones" />
                </Card>

                <Card v-if="ultimosArchivos.length">
                    <h3>Últimos Archivos</h3>
                    <div class="w-full">
                        <div v-for="item, index of ultimosArchivos" :key="index"
                            class="flex gap-3 items-center py-2 w-full">
                            <FileIcon :url="item.url" :name="item.archivo" />
                            <Link :href="item.url" class="py-1 hover:underline">{{
                                item.url.substring(item.url.lastIndexOf('/') +
                                    1)
                            }}
                            </Link>
                            <TimeAgo class="ml-auto" :date="item.fecha_modificacion" />
                        </div>
                    </div>
                </Card>

                <Card v-if="carpetas.length">
                    <h3>Carpetas</h3>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline py-2">
                            <FolderIcon :url="item.ruta" />
                            <Link :href="'/' + item.ruta" class="py-1 hover:underline">{{
                                item.ruta.substring(item.ruta.lastIndexOf('/') + 1) }}</Link>
                        </div>
                    </div>
                </Card>

                <Card>
                    <h3>Miembros</h3>
                    <Users v-if="equipo" :users="equipo.usuarios.slice(0,17)" :count="equipo.usuarios.length" />
                </Card>

                <Card v-if="equipo.informacion">
                    <h3>Información adicional</h3>
                    <div class="prose" v-html="equipo.informacion" />
                </Card>


                <EquipoAdmin v-if="equipo.admin" :equipo="equipo" />

            </GridFill>
        </div>


        <Modal :show="mostrarMensaje" centered>
            <div class="p-5 mt-auto mb-auto">
                <p class="text-center">{{ $page.props.flash.message }}</p>
                <div class="py-3 flex justify-center">
                    <button @click.prevent="mostrarMensaje = false" type="button" class="btn btn-neutral">
                        Gracias
                    </button>
                </div>
            </div>
        </Modal>

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import EquipoAdmin from './Partes/EquipoAdmin.vue'
import EquipoCabecera from './Partes/EquipoCabecera.vue'
import EquipoInformacion from './Partes/EquipoInformacion.vue'
import EquipoMembresia from './Partes/EquipoMembresia.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    equipo: {
        type: Object,
        required: true,
    },
    ultimosArchivos: {},
    carpetas: {},
    miSolicitud: {},
    solicitudesPendientes: Array,
    soyMiembro: Boolean,
    soyCoordinador: Boolean
})

// MENSAJE FLASH
const page = usePage()
const mostrarMensaje = ref(page.props.flash.message)


// solicitud
const solicitud = ref(props.miSolicitud)

</script>


<style>
.ql-editor {
    @apply bg-base-100;
}
</style>
