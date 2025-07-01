<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll h-screen smooth-snap py-20" ref="container" scroll-region
    style="--sectionHeight: 100vh"
    >
        <slot></slot>

        <!-- Footer como una sección más -->
        <Section>
            <AppFooter class="h-full flex flex-wrap items-center pt-12" />
        </Section>

        <TransitionFade>
            <div v-show="showScrollIcons"
                class="wrap-flecha wrap-flecha-arriba"
                :class="nav.announce&&!nav.announceClosed?'top-[7rem]':'top-[5rem]'">
                <div v-if="!inFirstSection" @click="scrollToPreviousMandatory" class="flecha flecha-simple">
                    <Icon icon="material-symbols:arrow-warm-up" />
                </div>
            </div>
        </TransitionFade>


        <TransitionFade>
            <div v-show="showScrollIcons"
                class="wrap-flecha">
                <div @click="scrollToNextMandatory" class="flecha"
                :class="inFirstSection&&downLabel?'flecha-texto':''" >
                    <span v-if="inFirstSection && downLabel" class="text-sm">{{ downLabel }}</span><Icon v-if="!inLastSection" icon="material-symbols:arrow-cool-down" />
                </div>
            </div>
        </TransitionFade>

    </Sections>
</template>



<script setup>

const props = defineProps({
    downLabel: {
        type: String,
        default: ''
    },
    scrollIconsDelay: {
        type: Number,
        default: 3000
    }
});

const container = ref(null);
const nav = useNav();
nav.scrollY = 0;

const inLastSection = ref(false)
const inFirstSection = ref(true)

// Función para manejar el evento de scroll
const handleScroll = () => {
    nav.scrollY = container.value.$el.scrollTop;

    // Verificar si es la última sección
    const cont = container.value.$el;
    const mandatoryElems = cont.querySelectorAll('.sections > .section');
    const firstElem = mandatoryElems[0];
    const lastElem = mandatoryElems[mandatoryElems.length - 1];
    const rectFirst = firstElem.getBoundingClientRect();
    const rectLast = lastElem.getBoundingClientRect();
    inLastSection.value = rectLast.top <= screen.height * .7;
    inFirstSection.value = rectFirst.top >=0 && rectFirst.top < screen.height * 1.1;
};

let scrollTimer = null;
const showScrollIcons = ref(false);

onMounted(() => {
    handleScroll();
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });
    nav.fullPage = true;
    scrollTimer = setTimeout(() => {
        showScrollIcons.value = true;
    }, props.scrollIconsDelay);
});

onBeforeUnmount(() => {
    container.value.$el.removeEventListener('scroll', handleScroll);
    nav.fullPage = false;
    clearTimeout(scrollTimer);
});

// Función genérica para hacer scroll hacia arriba o abajo
function scrollToMandatory(scrollDown) {
    const cont = container.value.$el;
    const containerHeight = cont.clientHeight;
    const halfContainerHeight = containerHeight / 2;
    const mandatoryElems = document.querySelectorAll('.sections > .section');
    let currentElem = null;

    for (let i = 0; i < mandatoryElems.length; i++) {
        const mandatoryElem = mandatoryElems[i];
        const rect = mandatoryElem.getBoundingClientRect();
        // Verifica si la sección actual está en la mitad del contenedor
        if (scrollDown ? (rect.top <= halfContainerHeight && rect.bottom >= halfContainerHeight) :
            (rect.bottom >= halfContainerHeight && rect.top <= halfContainerHeight)) {
            currentElem = mandatoryElem;
            break;
        }
    }

    if (currentElem) {
        let nextMandatoryElem = null;
        for (let i = 0; i < mandatoryElems.length; i++) {
            const mandatoryElem = mandatoryElems[i];
            const rect = mandatoryElem.getBoundingClientRect();
            // Verifica si la parte inferior o superior de la siguiente sección es visible en el contenedor
            if (scrollDown ? (rect.top >= currentElem.getBoundingClientRect().bottom) :
                (rect.bottom >= currentElem.getBoundingClientRect().top)) {
                nextMandatoryElem = mandatoryElem;
                break;
            }
        }

        if (nextMandatoryElem) {
            // Usar un scroll más suave y controlado
            const elementTop = nextMandatoryElem.getBoundingClientRect().top + cont.scrollTop;
            cont.scrollTo({
                top: elementTop,
                behavior: 'smooth'
            });
        }
    }
}

// Funciones específicas para hacer scroll hacia arriba o hacia abajo
function scrollToNextMandatory() {
    scrollToMandatory(true);
}

function scrollToPreviousMandatory() {
    scrollToMandatory(false);
}
</script>




<style scoped>
@reference "../../css/app.css";

/* Suavizado del scroll snap - balance entre mandatory y proximity */
.smooth-snap {
    scroll-behavior: smooth;
    /* Usar mandatory pero con configuración menos agresiva */
    scroll-snap-type: y mandatory;
    /* Reducir el padding para que sea menos agresivo */
    scroll-padding-top: 0rem;
    scroll-padding-bottom: 0rem;
    /* Añadir transición suave */
    transition: scroll-snap-type 0.3s ease;
}

/* Transición suave para las secciones */
:slotted(.section) {
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100vh;
    /* Añadir un poco de margen para el snap */
    scroll-margin-top: 0rem;
    scroll-margin-bottom: 0rem;
}

:deep(.section) {
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100vh;
    /* Reducir el margen para un snap más firme pero no agresivo */
    scroll-margin-top: 0rem;
    scroll-margin-bottom: 0rem;
    /* Añadir transición suave para el contenido */
    transition: transform 0.3s ease-out;
}

.wrap-flecha {
    @apply transition duration-300 fixed bottom-3 left-0 w-full flex justify-center z-30 pointer-events-none;
}
.wrap-flecha-arriba {
    @apply top-[5rem] bottom-auto;
}
.flecha {
    @apply p-1 bg-orange-500 text-black rounded-full text-2xl cursor-pointer animate-bounce pointer-events-auto hover:outline-3 hover:outline-blue-500 transition duration-100;
}
.flecha-simple {
    @apply w-auto h-auto;
}
.flecha-texto {
    @apply w-fit flex flex-nowrap gap-1 items-center pl-2;
}
</style>
