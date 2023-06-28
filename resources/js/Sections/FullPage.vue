<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll h-screen" ref="container" :style="{
        '--sectionHeight': sectionHeight
    }">
        <slot></slot>
        <TransitionFade>
            <div v-show="showScrollDown"
                class="transition duration-300 fixed bottom-3 left-0 w-full flex justify-center z-30 text-white mix-blend-exclusion">
                <Icon icon="ph:caret-double-down-duotone" @click="scrollToNextMandatory" class="" />
            </div>
        </TransitionFade>
    </Sections>
</template>

<script setup>
import { onBeforeUnmount } from 'vue'
import { useNav } from '@/Stores/nav'

defineProps({
    sectionHeight: {
        type: String,
        required: false,
        default: '100vh'
    }
})

const container = ref(null)

const nav = useNav()
nav.scrollY = 0
// const inUpperPage = computed(()=>scrollY.value<screen.height*.7)

const handleScroll = () => {
    nav.scrollY = container.value.$el.scrollTop;
    // Hacer algo con la posición actual del scroll Y, como actualizar una variable reactiva
};

var scrollTimer = null
const showScrollDown = ref(false)

onMounted(() => {
    // primer establecimiento de valor
    handleScroll()
    // Agregar un evento para el desplazamiento de la ventana
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });
    // marcamos que estamos en modo "full page" para que la barra de navegación sea flotante
    nav.fullPage = true
    scrollTimer = setTimeout(() => {
        showScrollDown.value = true
    }, 3000)
});



onBeforeUnmount(() => {
    container.value.$el.removeEventListener('scroll', handleScroll);
    nav.fullPage = false
    clearTimeout(scrollTimer)
});



function scrollToNextMandatory() {
    // Obtenemos el elemento contenedor
    console.log({ container })
    const cont = container.value.$el;

    // Calculamos la altura de la mitad del contenedor
    const containerHeight = cont.clientHeight;
    const halfContainerHeight = containerHeight / 2;

    // Obtenemos todos los elementos hijos con la propiedad scroll-snap-type: mandatory
    const mandatoryElems = document.querySelectorAll('.sections > .section');

    // Encontramos el elemento actualmente visible
    let currentElem = null;
    for (let i = 0; i < mandatoryElems.length; i++) {
        const mandatoryElem = mandatoryElems[i];
        const rect = mandatoryElem.getBoundingClientRect();
        if (rect.top <= halfContainerHeight && rect.bottom >= halfContainerHeight) {
            currentElem = mandatoryElem;
            break;
        }
    }

    // Si encontramos el elemento actualmente visible, buscamos el siguiente elemento
    if (currentElem) {
        let nextMandatoryElem = null;
        for (let i = 0; i < mandatoryElems.length; i++) {
            const mandatoryElem = mandatoryElems[i];
            const rect = mandatoryElem.getBoundingClientRect();
            if (rect.top >= currentElem.getBoundingClientRect().bottom) {
                nextMandatoryElem = mandatoryElem;
                break;
            }
        }

        // Si encontramos un elemento, hacemos scroll hacia él con animación
        if (nextMandatoryElem) {
            nextMandatoryElem.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
}
</script>


<style scoped>
:slotted(.section) {
    height: var(--sectionHeight);
    @apply snap-center flex flex-col justify-center;
}
</style>
