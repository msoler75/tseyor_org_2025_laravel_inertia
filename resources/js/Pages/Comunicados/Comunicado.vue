<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Comunicados</Back>
            <AdminPanel modelo="comunicado" necesita="administrar contenidos" :contenido="comunicado" />
        </div>


        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ comunicado.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <div class="flex gap-2">
                        <span v-for="audio of mp3" :key="audio" class="badge badge-primary cursor-pointer gap-2 select-none"
                        @click="clickAudio(audio)">
                            <Icon icon="material-symbols:volume-up-outline-rounded"/>
                            Escuchar {{ audio.label }}
                        </span>
                    </div>
                    <TimeAgo :date="comunicado.fecha_comunicado" :includeTime="false"/>
                </div>
            </div>

            <Content :content="comunicado.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('comunicado', comunicado.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePlayerState } from '@/Stores/player'

const player = usePlayerState()

defineOptions({ layout: AppLayout })

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    }
});

const mp3 = ref([])

try {
    const audios = JSON.parse(props.comunicado.audios)
    const r = []
    const some = audios.length>1
    for(var idx in audios) {
        const audio = audios[idx]
        const label = some?`Audio ${idx}`:'Audio'
        const title = some?`${props.comunicado.titulo} (${idx})`:props.comunicado.titulo
        r.push({title, label, src: `/storage/${audio}`})
    }
    mp3.value = r
}
catch(err) {

}


function clickAudio(audio) {
    player.play(audio)
}

</script>
