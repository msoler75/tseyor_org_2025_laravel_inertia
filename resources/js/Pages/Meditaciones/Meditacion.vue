<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Meditaciones</Back>
            <AdminPanel modelo="meditacion" necesita="administrar contenidos" :contenido="meditacion" />
        </div>


        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ meditacion.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <div class="flex gap-2">
                        <span v-for="audio of mp3" :key="audio" class="badge badge-primary cursor-pointer gap-1 select-none"
                            @click="clickPlay(audio)">
                            <Icon icon="material-symbols:volume-up-outline-rounded" class="mr-2" />
                            <template v-if="player.music.value && player.music.value.src == audio.src">
                                <span v-if="player.state.value == 'playing'">Escuchando</span>
                                <span v-else-if="player.state.value == 'paused'">En pausa</span>
                                <span v-else="player.state.value == 'paused'">Escuchaste</span>{{ audio.label }}
                            </template>
                            <template v-else>
                                Escuchar {{ audio.label }}
                            </template>
                        </span>
                    </div>
                    <TimeAgo :date="meditacion.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="meditacion.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('meditacion', meditacion.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePlayer } from '@/Stores/player'

const player = usePlayer()

defineOptions({ layout: AppLayout })

const props = defineProps({
    meditacion: {
        type: Object,
        required: true,
    }
});

const mp3 = ref([])

try {
    const audios = JSON.parse(props.meditacion.audios)
    const r = []
    const some = audios.length > 1
    for (var idx in audios) {
        const audio = audios[idx]
        const label = some ? `Audio ${idx}` : 'Audio'
        const title = some ? `${props.meditacion.titulo} (${idx})` : props.meditacion.titulo
        r.push({ title, label, src: `/storage/${audio}` })
    }
    mp3.value = r
}
catch (err) {

}


function clickPlay(audio) {
    switch (player.state.value) {
        case 'playing':
        case 'paused':
            player.playPause();
            break;
        default:
            player.play(audio);
    }
}



</script>
