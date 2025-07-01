<template>
    <div class="h-screen w-full" style="--sectionHeight: 100vh">
        <Carousel
            ref="carousel"
            :items-to-show="1"
            :wrap-around="false"
            v-model="currentSlide"
            :mouseWheel="true"
            dir="ttb"
            height="100vh"
            class="w-full"
            @slide-start="onSlideStart"
            @slide-end="onSlideEnd"
        >
            <!-- Slides dinámicos basados en el slot content -->
            <Slide v-for="(section, index) in sections" :key="index" class="carousel-slide">
                <component :is="section" class="section h-screen flex flex-col justify-center" />
            </Slide>

            <!-- Footer como slide adicional -->
            <Slide class="carousel-slide">
                <div class="section flex flex-col justify-center">
                    <AppFooter class="h-full flex flex-wrap items-center pt-12" />
                </div>
            </Slide>
        </Carousel>

        <TransitionFade>
            <div v-show="showScrollIcons"
                class="transition duration-300 fixed bottom-3 left-0 w-full flex justify-center z-30 mix-blend-exclusion">
                <Icon v-if="!isLastSlide" icon="material-symbols:arrow-cool-down" @click="nextSlide"
                    class="p-1 bg-orange-500 text-black rounded-full text-2xl lg:text-4xl cursor-pointer animate-bounce" />
            </div>
        </TransitionFade>

        <TransitionFade>
            <div v-show="showScrollIcons"
                class="transition duration-300 fixed left-0 w-full flex justify-center z-30 mix-blend-exclusion"
                :class="nav.announce&&!nav.announceClosed?'top-[7rem]':'top-[5rem]'">
                <Icon v-if="!isFirstSlide" icon="material-symbols:arrow-warm-up" @click="prevSlide"
                    class="p-1 bg-orange-500 text-black rounded-full text-2xl lg:text-4xl cursor-pointer animate-bounce" />
            </div>
        </TransitionFade>
    </div>
</template>



<script setup>
import { Carousel, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

const carousel = ref(null);
const nav = useNav();
const currentSlide = ref(0);
const totalSlides = ref(0);

// Obtener las secciones del slot
const slots = useSlots();
const sections = ref([]);

// Computed para determinar si es el primer o último slide
const isFirstSlide = computed(() => currentSlide.value === 0);
const isLastSlide = computed(() => currentSlide.value >= totalSlides.value - 1);

// Variables para mostrar iconos de navegación
let scrollTimer = null;
const showScrollIcons = ref(false);

// Procesar el contenido del slot para extraer las secciones
const processSections = () => {
    if (slots.default) {
        try {
            const slotContent = slots.default();
            sections.value = slotContent.filter(vnode =>
                vnode.type?.name === 'Section' ||
                (typeof vnode.type === 'object' && vnode.type.__name === 'Section')
            );
            totalSlides.value = sections.value.length + 1; // +1 para el footer
        } catch (error) {
            console.warn('Error procesando secciones:', error);
            sections.value = [];
            totalSlides.value = 1;
        }
    }
};

// Funciones de navegación
const nextSlide = () => {
    if (carousel.value && !isLastSlide.value) {
        carousel.value.next();
    }
};

const prevSlide = () => {
    if (carousel.value && !isFirstSlide.value) {
        carousel.value.prev();
    }
};

// Callback cuando cambia el slide
const onSlideStart = (data) => {
    currentSlide.value = data.currentSlideIndex;
};

// Callback cuando termina el cambio de slide
const onSlideEnd = (data) => {
    currentSlide.value = data.currentSlideIndex;
    // Calcular aproximación del scroll basado en el índice del slide
    nav.scrollY = data.currentSlideIndex * window.innerHeight;
};

onMounted(() => {
    try {
        processSections();
        nav.fullPage = true;

        // Mostrar iconos después de 3 segundos
        scrollTimer = setTimeout(() => {
            showScrollIcons.value = true;
        }, 3000);
    } catch (error) {
        console.warn('Error en FullVCarousel onMounted:', error);
        // Fallback seguro
        nav.fullPage = false;
        sections.value = [];
        totalSlides.value = 1;
    }
});

onBeforeUnmount(() => {
    nav.fullPage = false;
    clearTimeout(scrollTimer);
});
</script>

<style scoped>
.carousel-slide {
    height: 100vh;
    width: 100%;
}

:deep(.section) {
    height: 100vh;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

:deep(.carousel__viewport) {
    height: 100vh;
}

:deep(.carousel__track) {
    height: 100vh;
}
</style>
