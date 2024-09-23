<template>
    <div>
        <div class="container mx-auto py-12 flex justify-between items-center">
            <Back>Equipos</Back>
            <EquipoMembresia class="hidden sm:flex mx-auto" :equipo-id="equipo.id" v-model="solicitud"
                :soyMiembro="soyMiembro" :soyCoordinador="soyCoordinador"
                :permitirSolicitudes="!equipo.oculto" @updated="reloadEquipo" />
            <AdminLinks modelo="equipo" necesita="administrar equipos" :contenido="equipo" />
        </div>

        <EquipoCabecera :equipo="equipo" class="fixed w-full top-14 transition duration-100" :class="useNav().scrollY < 240 ? 'opacity-0 pointer-events-none' : ''"/>

        <div class="container mx-auto pb-20">

            <GridAppear class="gap-7" col-width="20rem">

                <EquipoInformacion :equipo="equipo" />

                <EquipoMembresia class="sm:hidden mx-auto" :equipo-id="equipo.id" v-model="solicitud"
                    :soyMiembro="soyMiembro" :soyCoordinador="soyCoordinador"
                    :permitirSolicitudes="!equipo.oculto" @updated="reloadEquipo" />

                <Card v-if="equipo.anuncio" class="border border-orange-400 justify-center items-center">
                    <div class="prose" v-html="equipo.anuncio" />
                </Card>


                <Card v-if="equipo.reuniones">
                    <h3>Reuniones</h3>
                    <div class="prose" v-html="equipo.reuniones" />
                </Card>

                <Card v-if="!equipo.ocultarMiembros">
                    <h3 class="flex justify-between items-center">
                        <span>Miembros</span>
                        <div v-if="equipo.miembros" class="text-base flex gap-2 items-center self-end justify-center">
                            <Icon icon="ph:user-duotone" />
                            {{ equipo.miembros.length }}
                        </div>
                    </h3>

                    <Users v-if="equipo" :users="equipo.miembros.slice(0, 17)" :count="equipo.miembros.length" />
                </Card>

                <Card v-if="ultimosInformes.length || soyCoordinador || puedoAdministrar" class="gap-3">
                    <div class="flex items-center justify-between mb-3 gap-5">
                        <h3 class="mb-0">Últimos Informes</h3>
                        <Link v-if="ultimosInformes.length" :href="route('equipo.informes', equipo.slug)"
                            class="text-xs ml-auto flex items-center gap-2 hover:underline">Ver todos</Link>
                        <a v-if="soyCoordinador || puedoAdministrar" :href="'/admin/informe/create?equipo_id='+equipo.id"
                            class="btn btn-sm text-xl flex items-center" title="Crear informe">+</a>
                    </div>
                    <div v-if="!ultimosInformes.length">
                        <p>No hay informes.</p>
                    </div>
                    <div class="w-full">
                        <Link v-for="item, index of ultimosInformes" :key="index"
                            class="flex gap-3 py-2 w-full items-baseline hover:bg-base-200/40 rounded-xl p-2"
                            :href="route('informe', item.id)">
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

                <Card v-if="!equipo.ocultarArchivos && ultimosArchivos?.length"
                    class="overflow-y-auto max-h-112 row-span-2">
                    <h3>Últimos Archivos</h3>
                    <div class="w-full text-sm grid grid-cols-[1.5rem,auto,1.5rem,3rem] gap-1 gap-y-3">
                        <template v-for="item, index of ultimosArchivos" :key="index">
                            <FileIcon :url="item.url" :name="item.archivo" class="mt-1"
                            @click="clickFile(item, $event)"/>
                            <a download :href="item.url" class="hover:underline break-all"
                                @click="clickFile(item, $event)"
                            >{{ item.url.substring(item.url.lastIndexOf('/') + 1) }}</a>
                            <FolderIcon :arrow="true" v-if="item.carpeta" :url="item.carpeta" class="mt-1"
                                title="Ir a la carpeta" />
                                <div class="text-center">
                                    <TimeAgo class="text-xs" :date="item.fecha_modificacion" short />
                                </div>
                        </template>
                    </div>
                </Card>

                <Card v-if="!equipo.ocultarCarpetas && carpetas.length" class="max-h-[400px] overflow-y-auto">
                    <h3>Carpetas</h3>
                    <div>
                        <div v-for="item, index of carpetas" :key="index" class="flex gap-3 items-baseline py-2">
                            <FolderIcon :url="item.ubicacion" :arrow="true" />
                            <Link :href="item.ubicacion" class="py-1 hover:underline">{{
                                item.ubicacion?.substring(item.ubicacion?.lastIndexOf('/') + 1) }}</Link>
                        </div>
                    </div>
                </Card>

                <Card v-if="!equipo.ocultarMiembros">
                    <h3>Coordinadores</h3>
                    <Users v-if="equipo" :users="coordinadores" :count="coordinadores.length" />
                </Card>

                <Card v-if="equipo.informacion">
                    <h3>Información adicional</h3>
                    <div class="prose" v-html="equipo.informacion" />
                </Card>

                <EquipoAdmin v-if="soyCoordinador || puedoAdministrar" :equipo="equipo" @updated="reloadEquipo" />

            </GridAppear>
        </div>
    </div>
</template>

<script setup>
import EquipoAdmin from './Partes/EquipoAdmin.vue'
import EquipoCabecera from './Partes/EquipoCabecera.vue'
import EquipoInformacion from './Partes/EquipoInformacion.vue'
import EquipoMembresia from './Partes/EquipoMembresia.vue'
import usePlayer from '@/Stores/player'

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
    soyCoordinador: Boolean,
    puedoAdministrar: Boolean
})

const coordinadores = computed(() => props.equipo.miembros.filter(m => m.pivot.rol == 'coordinador'))

// para reproducir audios
const player = usePlayer()

// solicitud
const solicitud = ref(props.miSolicitud)

function doReload() {
    console.log('Equipo.doReload')

    const modalElements = document.querySelectorAll('div.component-modal');

    // Filtrar los elementos que están visibles (no tienen "display:none")
    const visibleModalElements = Array.from(modalElements).filter(element => {
        const style = window.getComputedStyle(element);
        return style.getPropertyValue('display') !== 'none';
    });

    // si hay algun modal abierto, no hace un reload
    if (visibleModalElements.length) return

    router.reload({
        only: ['equipo', 'ultimosArchivos', 'ultimosInformes', 'carpetas', 'miSolicitud', 'soyMiembro', 'soyCoordinador']
    })
}

function reloadEquipo() {
    console.log('Equipo.reloadEquipo')
    router.reload({
        only: ['equipo', 'miSolicitud', 'soyMiembro', 'soyCoordinador']
    })
}

// va actualizando la página del equipo
var timer = null
onMounted(() => {
    timer = setInterval(doReload, 60000)
/*
    router.reload({
        only: ['ultimosArchivos']
    })
        */

})

onBeforeUnmount(() => {
    clearInterval(timer)
})




function clickFile(item, event) {
    console.log('clickFile', item)

            // si es un audio:
            if (player.isPlayable(item.url)) {
                player.play(item.url, item.archivo)
                event.preventDefault()
            }

}

</script>


<style>
.ql-editor {
    @apply bg-base-100;
}
</style>
