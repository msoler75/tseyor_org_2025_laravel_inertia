<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll h-screen" ref="container" scroll-region
    style="--sectionHeight: 100vh"
    >
        <slot></slot>

        <!-- Footer como una sección más -->
        <Section>
            <AppFooter class="h-full flex flex-wrap items-center pt-12" />
        </Section>

        <TransitionFade>
            <div v-show="showScrollIcons"
                class="transition duration-300 fixed bottom-3 left-0 w-full flex justify-center z-30 mix-blend-exclusion ">
                <Icon v-if="!isLastSection" icon="material-symbols:arrow-cool-down" @click="scrollToNextMandatory" class="p-1 bg-orange-500 text-black rounded-full text-2xl lg:text-4xl cursor-pointer animate-bounce" />
            </div>
        </TransitionFade>

        <TransitionFade>
            <div v-show="showScrollIcons"
                class="transition duration-300 fixed left-0 w-full flex justify-center z-30 mix-blend-exclusion"
                :class="nav.announce&&!nav.announceClosed?'top-[7rem]':'top-[5rem]'">
                <Icon v-if="!isFirstSection" icon="material-symbols:arrow-warm-up" @click="scrollToPreviousMandatory" class="p-1 bg-orange-500 text-black rounded-full text-2xl lg:text-4xl cursor-pointer animate-bounce" />
            </div>
        </TransitionFade>

    </Sections>
</template>



<script setup>

const container = ref(null);
const nav = useNav();
nav.scrollY = 0;

const isLastSection = ref(false)
const isFirstSection = ref(false)

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
    isLastSection.value = rectLast.top <= screen.height * .7;
    isFirstSection.value = rectFirst.top >=0 && rectFirst.top < screen.height * 1.1;
};

let scrollTimer = null;
const showScrollIcons = ref(false);

onMounted(() => {
    handleScroll();
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });
    nav.fullPage = true;
    scrollTimer = setTimeout(() => {
        showScrollIcons.value = true;
    }, 3000);
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
            nextMandatoryElem.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
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
:slotted(.section) {
    @apply snap-center flex flex-col justify-center h-screen;
}

:deep(.section) {
    @apply snap-center flex flex-col justify-center h-screen;
}
</style>
