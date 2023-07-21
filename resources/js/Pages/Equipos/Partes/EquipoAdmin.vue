<template>
    <div>
        <Card class="select-none">
            <h3>Administración</h3>
            <ul class="list-none p-0 space-y-2">
                <li class="flex gap-2 items-center cursor-pointer" @click="peticiones.mostrar">
                    <Icon icon="ph:envelope-duotone" />Administrar peticiones de ingreso
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

        <ModalPeticiones :equipo="props.equipo" ref="peticiones" />


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
import ModalPeticiones from './ModalPeticiones.vue'

const props = defineProps({ equipo: { type: Object, required: true } })

const miembros = ref()
const config = ref()
const invitaciones = ref()
const peticiones = ref()

const modalEnlace = ref(false)

</script>
