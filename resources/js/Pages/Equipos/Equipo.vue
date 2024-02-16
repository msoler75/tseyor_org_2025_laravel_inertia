<template>
    <div>
        <div class="container mx-auto py-12 flex justify-between items-center">
            <Back>Equipos</Back>
            <EquipoMembresia class="hidden sm:flex mx-auto badge badge-info" :equipo-id="equipo.id" v-model="solicitud"
                :soyMiembro="soyMiembro" :soyCoordinador="soyCoordinador" 
                :permitirSolicitudes="!equipo.ocultarSolicitudes"/>
            <AdminPanel modelo="equipo" necesita="administrar equipos" :contenido="equipo" />
        </div>

        <EquipoCabecera :equipo="equipo" />

        <div class="container mx-auto pb-20">

            <GridFill class="gap-7" w="20rem">

                <EquipoInformacion :equipo="equipo" />

                <EquipoMembresia class="sm:hidden mx-auto badge badge-info" :equipo-id="equipo.id" v-model="solicitud"
                    :soyMiembro="soyMiembro" :soyCoordinador="soyCoordinador"  :permitirSolicitudes="!equipo.ocultarSolicitudes"/>

                <Card v-if="equipo.anuncio" class="border border-orange-400 justify-center items-center">
                    <div class="prose" v-html="equipo.anuncio" />
                </Card>

                <Card v-if="equipo.reuniones">
                    <h3>Reuniones</h3>
                    <div class="prose" v-html="equipo.reuniones" />
                </Card>

                <Card v-if="ultimosInformes.length" class="gap-3">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="mb-0">Últimos Informes</h3>
                        <Link :href="route('equipo.informes', equipo.slug)" class="text-xs ml-auto flex items-center gap-2 hover:underline">Ver todos</Link>
                    </div>
                    <div class="w-full">
                        <Link v-for="item, index of ultimosInformes" :key="index"
                            class="flex gap-3 py-2 w-full items-baseline hover:bg-base-200/40 rounded-xl p-2" :href="route('informe', item.id)">
                        <Icon icon="ph:file-duotone" />
                        <div class="w-full">
                            <div class="mb-2">{{ item.titulo }}</div>
                            <div class="flex justify-between w-full">
                                <span class="badge badge-info">{{ item.categoria }}</span>
                                <TimeAgo class="text-xs" :date="item.updated_at" />
                            </div>
                        </div>
                        </Link>
                    </div>
                </Card>

                <Card v-if="!equipo.ocultarArchivos && ultimosArchivos.length">
                    <h3>Últimos Archivos</h3>
                    <div class="w-full">
                        <div v-for="item, index of ultimosArchivos" :key="index"
                            class="flex gap-3 items-center py-2 w-full">
                            <FileIcon :url="item.url" :name="item.archivo" />
                            <a download :href="item.url" class="py-1 hover:underline">{{
                                item.url.substring(item.url.lastIndexOf('/') + 1) }}</a>
                            <TimeAgo class="ml-auto" :date="item.fecha_modificacion" />
                        </div>
                    </div>
                </Card>

                <Card v-if="!equipo.ocultarCarpetas && carpetas.length" class="max-h-[400px] overflow-y-auto">
                    <h3>Carpetas</h3>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline py-2">
                            <FolderIcon :url="item.ruta" />
                            <Link :href="'/' + item.ruta" class="py-1 hover:underline">{{
                                item.ruta.substring(item.ruta.lastIndexOf('/') + 1) }}</Link>
                        </div>
                    </div>
                </Card>

                

                <Card v-if="!equipo.ocultarMiembros">
                    <h3>Miembros</h3>
                    <Users v-if="equipo" :users="equipo.miembros.slice(0, 17)" :count="equipo.miembros.length" />
                </Card>

                <Card v-if="equipo.informacion">
                    <h3>Información adicional</h3>
                    <div class="prose" v-html="equipo.informacion" />
                </Card>

                <EquipoAdmin v-if="soyCoordinador" :equipo="equipo" />

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
    ultimosInformes: {},
    carpetas: {},
    miSolicitud: {},
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
