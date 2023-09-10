import {ref} from "vue";
import {createGlobalState} from  "@vueuse/core"

export const usePlayerState = createGlobalState(() => {
    const audio = ref(null)
    const radioMode = ref(false)
    const closed = ref(true)
    const mini = ref(true)

    function play(music, isRadio) {
        audio.value = music
        radioMode.value = !!isRadio
        closed.value = false
    }

    return { audio,  radioMode, play, closed, mini};
});
