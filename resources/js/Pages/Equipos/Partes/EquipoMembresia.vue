<template>
    <div>
    <div v-if="soyMiembro" class="flex gap-3 items-center">
        <div class="uppercase py-3 cursor-pointer">
            <span v-if="soyCoordinador">eres coordinador</span>
            <span v-else>eres miembro</span>
        </div>
        <Dropdown>
            <template #trigger>
                <Icon icon="mdi:dots-vertical" class="text-xl cursor-pointer" />
            </template>
            <template #content>
                <div class="bg-base-100 p-5 flex flex-col gap-5">
                    <!-- <span class="text-gray-800 dark:text-gray-100 text-center">Eres {{ soyCoordinador ? 'coordinador/a' : 'miembro' }}</span> -->
                    <div class="btn btn-xs btn-error cursor-pointer" @click="modalConfirmarSalir=true">Salir del equipo</div>
                </div>
            </template>
        </Dropdown>
    </div>
    <div v-else-if="solicitud" class="uppercase py-3 text-center">
        <span v-if="solicitud.fecha_denegacion">Solicitud denegada</span>
        <span v-else>Solicitud enviada</span>
    </div>
    <button v-else-if="$page.props.auth.user && permitirSolicitudes" class="btn btn-primary w-fit" @click="solicitarIngreso">
        <span v-if="solicitando" class="flex gap-2 items-center">
            <Spinner /> Solicitando...
        </span>
        <span v-else>
            Solicitar Ingreso
        </span>
    </button>


      <!-- Modal Confirmación de eliminar Archivo -->
      <ConfirmationModal :show="modalConfirmarSalir" @close="modalConfirmarSalir = false">
            <template #title>
                Confirmación de abandono
            </template>
            <template #content>
                    ¿Quieres abandonar el equipo?
            </template>
            <template #footer>
                <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @submit.prevent="abandonarEquipo">

                    <button  @click.prevent="modalConfirmarSalir=false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>

                    <button   type="submit" class="btn btn-primary btn-sm">
                        Abandonar
                    </button>
                </form>
            </template>
        </ConfirmationModal>
</div>


</template>

<script setup>
const props = defineProps({
    modelValue: { required: true },
    equipoId: Number,
    soyMiembro: Boolean,
    soyCoordinador: Boolean,
    permitirSolicitudes: Boolean
})

const emit = defineEmits(['update:modelValue', 'updated'])
const solicitud = ref(props.modelValue)

watch(() => props.modelValue, (s) => solicitud.value = s)

// solicitud
const solicitando = ref(false)

function solicitarIngreso() {
    solicitando.value = true
    axios.get(route('equipo.solicitar', props.equipoId))
        .then(response => {
            solicitando.value = false
            solicitud.value = response.data.solicitud
            console.log('vamos a emitir', solicitud.value)
            emit('update:modelValue', solicitud.value)
        })
        .catch(error => {
            solicitando.value = false
            console.log('error', error)
            if (error.response && error.response.data && error.response.data.error)
                alert(error.response.data.error)
            else
                alert("Lo sentimos. Hubo algún error")
        })
}

const modalConfirmarSalir = ref(false)

function  abandonarEquipo (){
    console.log('abandonar')
    modalConfirmarSalir.value = false
    axios.post(route('equipo.abandonar', props.equipoId))
    .then(response => {
        console.log('response', response)
        solicitud.value = null
        emit('updated')
    })
    .catch(error => {
        alert(error.response.data.error)
    })
}
</script>
