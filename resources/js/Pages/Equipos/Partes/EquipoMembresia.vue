<template>
    <div v-if="soyMiembro" class="uppercase py-3">
            eres miembro
        </div>
        <div v-else-if="solicitud&&!solicitud.fecha_denegacion" class="uppercase py-3 text-center">
            Solicitud pendiente
        </div>
        <button v-else class="btn btn-primary" @click="solicitarIngreso">
            <span v-if="solicitando" class="flex gap-2 items-center">
                <Spinner/> Solicitando...
            </span>
            <span v-else>
                Solicitar Ingreso
            </span>
        </button>
</template>

<script setup>
const props = defineProps({
    modelValue:{required: true},
    equipoId: Number,
    soyMiembro: Boolean
})

const emit = defineEmits(['update:modelValue'])
const solicitud = ref(props.modelValue)

watch(()=>props.modelValue, (s)=>solicitud.value=s)

// solicitud
const solicitando = ref(false)

function solicitarIngreso() {
    solicitando.value = true
    axios.get(route('equipo.solicitar', props.equipoId))
    .then(response=>{
        solicitando.value = false
        solicitud.value = response.data.solicitud
        console.log('vamos a emitir', solicitud.value)
        emit('update:modelValue', solicitud.value)
    })
    .catch(error=>{
        solicitando.value = false
        console.log('error', error)
        if(error.response&&error.response.data&&error.response.data.error)
        alert(error.response.data.error)
        else
        alert("Lo sentimos. Hubo algún error")
    })
}
</script>
