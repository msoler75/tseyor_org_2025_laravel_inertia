<template>
    <div>
        <Card class="select-none">
            <h3>Administración</h3>
            <ul class="list-none p-0 space-y-2">
                <li class="flex gap-2 items-center cursor-pointer">
                    <Icon icon="ph:envelope-duotone" />Administrar peticiones de ingreso
                </li>
                <li class="flex gap-2 items-center cursor-pointer">
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

    </div>
</template>


<script setup>
import ModalConfiguracion from './ModalConfiguracion.vue'
import ModalInvitaciones from './ModalInvitaciones.vue'
import ModalMiembros from './ModalMiembros.vue'

const props = defineProps({ equipo: { type: Object, required: true } })

const miembros = ref()
const config = ref()
const invitaciones = ref()

function abrirInvitaciones() {
    console.log(invitaciones)
    invitaciones.value.abrirInvitaciones()
}
</script>
