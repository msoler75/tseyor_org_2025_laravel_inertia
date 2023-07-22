<template>
    <div>
        <Card class="select-none">
            <h3>Administración</h3>
            <ul class="list-none p-0 space-y-2">
                <li class="flex gap-2 items-center cursor-pointer" @click="solicitudes.mostrar">
                    <Icon icon="ph:envelope-duotone" />Administrar solicitudes
                    <span v-if="numSolicitudesPendientes" class="text-primary text-sm">({{ numSolicitudesPendientes }})</span>
                </li>
                <li class="flex gap-2 items-center cursor-pointer" @click="modalEnlace = true">
                    <Icon icon="ph:share-fat-duotone" />Enlace del equipo
                </li>
                <li class="flex gap-2 items-center cursor-pointer" @click="invitaciones.mostrar">
                    <Icon icon="ph:user-plus-duotone" />Invitar a usuario/s
                </li>
                <li class="flex gap-2 items-center cursor-pointer" @click="miembros.mostrar">
                    <Icon icon="ph:users-duotone" />Administrar miembros
                </li>
                <li class="flex gap-2 items-center cursor-pointer" @click="config.mostrar">
                    <Icon icon="ph:gear-six-duotone" />Configuración
                </li>
            </ul>
        </Card>

        <ModalMiembros :equipo="props.equipo" ref="miembros" />

        <ModalConfiguracion :equipo="props.equipo" ref="config" />

        <ModalInvitaciones :equipo="props.equipo" ref="invitaciones" />

        <ModalSolicitudes :equipo="props.equipo" ref="solicitudes" />

        <Modal :show="modalEnlace" @close="modalEnlace = false" centered>
            <div class="p-5">
                <h3><span class="font-italic">{{ equipo.nombre }}</span></h3>
                <div class="my-7">
                    {{ route('equipo', equipo.slug) }}
                </div>

                <div class="py-3 flex justify-end">
                    <button @click.prevent="modalEnlace = false" type="button" class="btn btn-neutral">
                        cerrar
                    </button>
                </div>
            </div>
        </Modal>

    </div>
</template>


<script setup>
import ModalConfiguracion from './ModalConfiguracion.vue'
import ModalInvitaciones from './ModalInvitaciones.vue'
import ModalMiembros from './ModalMiembros.vue'
import ModalSolicitudes from './ModalSolicitudes.vue'

const props = defineProps({ equipo: { type: Object, required: true } })

const miembros = ref()
const config = ref()
const invitaciones = ref()
const solicitudes = ref()

const modalEnlace = ref(false)

const numSolicitudesPendientes =  computed(()=>{
    return props.equipo.solicitudesPendientes.filter(s=>!s.fecha_aceptacion&&!s.fecha_denegacion).length
})

</script>
