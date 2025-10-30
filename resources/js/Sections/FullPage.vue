<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll smooth-snap"
    :style="containerStyle"
    ref="container" scroll-region
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
            <div v-show="showScrollIcons" class="wrap-flecha">
                <div v-if="!inLastSection" @click="scrollToNextMandatory" class="flecha"
                :class="inFirstSection&&downLabel?'flecha-texto':''" >
                    <span v-if="inFirstSection && downLabel" class="text-sm">{{ downLabel }}</span><Icon icon="material-symbols:arrow-cool-down" />
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

// Altura fija del viewport para evitar cambios por la barra de direcciones móvil
const fixedViewportHeight = ref('100vh');
const containerStyle = computed(() => ({
    height: fixedViewportHeight.value,
    '--sectionHeight': fixedViewportHeight.value
}));

const inLastSection = ref(false)
const inFirstSection = ref(true)

// Función para establecer altura fija del viewport
const setFixedViewportHeight = () => {
    // Usar visualViewport si está disponible (mejor para móviles), sino innerHeight
    const vh = window.visualViewport ? window.visualViewport.height : window.innerHeight;
    fixedViewportHeight.value = `${vh}px`;

    // También actualizar el contenedor si ya está montado
    if (container.value && container.value.$el) {
        container.value.$el.style.height = `${vh}px`;
    }
};

// Función para manejar el evento de scroll
const handleScroll = () => {
    nav.scrollY = container.value.$el.scrollTop;

    // Guardar la posición de scroll en sessionStorage para restauración
    sessionStorage.setItem(`fullpage-scroll-${window.location.pathname}`, nav.scrollY);

    // Verificar si es la última sección
    const cont = container.value.$el;
    const mandatoryElems = cont.querySelectorAll('.sections > .section');
    const firstElem = mandatoryElems[0];
    const lastElem = mandatoryElems[mandatoryElems.length - 1];
    const rectFirst = firstElem.getBoundingClientRect();
    const rectLast = lastElem.getBoundingClientRect();
    // Usar window.innerHeight en lugar de screen.height para mejor precisión en móviles
    const viewportHeight = window.innerHeight;
    inLastSection.value = rectLast.top <= viewportHeight * .7;
    inFirstSection.value = rectFirst.top >=0 && rectFirst.top < viewportHeight * 1.1;
};

let scrollTimer = null;
const showScrollIcons = ref(false);

// Función para ajustar altura - ahora solo actualiza si es necesario
const adjustHeight = () => {
    // Solo actualizar en cambios de orientación significativos
    const currentHeight = window.innerHeight;
    const storedHeight = parseInt(fixedViewportHeight.value);

    // Solo actualizar si hay una diferencia significativa (más de 50px)
    if (Math.abs(currentHeight - storedHeight) > 50) {
        setFixedViewportHeight();
    }
};

// hay que hacerlo aquí para que enseguida se establezca el valor
nav.fullPage = true;

onMounted(() => {
    // Establecer altura fija del viewport ANTES de configurar eventos
    setFixedViewportHeight();

    // Recalcular después de un breve delay para asegurar que visualViewport esté correcto
    setTimeout(() => {
        setFixedViewportHeight();
    }, 100);

    // Restaurar la posición de scroll desde sessionStorage
    const savedScroll = sessionStorage.getItem(`fullpage-scroll-${window.location.pathname}`);
    if (savedScroll) {
        container.value.$el.scrollTo({
            top: parseInt(savedScroll),
            behavior: 'instant'
        });
    }

    handleScroll();
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });

    // Escuchar cambios de orientación en móviles
    window.addEventListener('orientationchange', adjustHeight);
    window.addEventListener('resize', adjustHeight);

    // Escuchar cambios en visualViewport si está disponible
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', setFixedViewportHeight);
    }

    scrollTimer = setTimeout(() => {
        showScrollIcons.value = true;
    }, props.scrollIconsDelay);
});

onBeforeUnmount(() => {
    container.value.$el.removeEventListener('scroll', handleScroll);
    window.removeEventListener('orientationchange', adjustHeight);
    window.removeEventListener('resize', adjustHeight);
    if (window.visualViewport) {
        window.visualViewport.removeEventListener('resize', setFixedViewportHeight);
    }
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

/* Fallback para navegadores que no soporten dvh */
.sections {
    height: 100vh; /* fallback */
    height: var(--sectionHeight, 100vh); /* usar altura fija calculada */
}

/* Transición suave para las secciones */
:slotted(.section) {
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100vh; /* fallback */
    height: var(--sectionHeight, 100vh); /* usar altura fija calculada */
    /* Añadir un poco de margen para el snap */
    scroll-margin-top: 0rem;
    scroll-margin-bottom: 0rem;
}

:deep(.section) {
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100vh; /* fallback */
    height: var(--sectionHeight, 100vh); /* usar altura fija calculada */
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
