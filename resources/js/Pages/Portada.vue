<template>
    <h1 class="hidden">Inicio</h1>

    <FullPage>
        <Section data-theme="night">
            <FondoEspacio class="w-full h-full">
                <Hero
                    title="ONG MUNDO ARMÓNICO TSEYOR"
                    :href="route('quienes-somos')"
                    buttonLabel="Quiénes somos"
                    textClass="md:max-w-(--breakpoint-md) justify-center"
                    image-right
                    class="h-[95vh] w-[96vw] absolute"
                >
                    <div
                        class="bg-black/70 hover:bg-black/90 transition duration-500 p-7 rounded-xl"
                    >
                        Te invitamos a viajar juntos hacia el autodescubrimiento
                        con meditaciones, talleres y libros gratuitos.
                        Construyamos juntos Sociedades Armónicas sin líderes,
                        sin enfermedad, donde florezca la paz, la armonía, la
                        creatividad y la auténtica felicidad. Dando sin esperar
                        nada a cambio.
                    </div>
                </Hero>
                <Link
                    href="eventos"
                    class="w-fit text-xs btn btn-secondary opacity-80 !absolute bottom-4 right-4 z-40"
                    ><Icon
                        icon="ph:arrow-right-duotone"
                        class="transform scale-150"
                    />
                    PRÓXIMOS EVENTOS</Link
                >
            </FondoEspacio>
        </Section>

        <Section class="relative px-[30px] bg-base-200" style="overflow: unset">
            <Carousel
                :items-to-show="carouselItemsToShow"
                :wrap-around="true"
                :mouse-drag="true"
                :breakpoints="{
                    0: { itemsToShow: 1 },
                    640: { itemsToShow: 2 },
                    1024: { itemsToShow: 3 },
                    1400: { itemsToShow: 4 }
                }"
                class="w-full"
            >
                <Slide v-for="(feature, idx) in features" :key="feature.title">
                    <div class="rounded-xl bg-base-200 shadow-lg p-6 flex flex-col items-center text-center h-full mx-2">
                        <Icon :icon="feature.icon" :class="'text-4xl mb-2 ' + (feature.iconClass || '')" />
                        <h3 class="font-bold text-lg mb-1">{{ feature.title }}</h3>
                        <p class="mb-2">{{ feature.description }}</p>
                        <Link v-if="feature.link" :href="feature.link.url" class="btn btn-secondary mt-4">{{ feature.link.label }}</Link>
                    </div>
                </Slide>
                <template #addons>
                    <Navigation v-if="isMobileOrTablet" />
                </template>
            </Carousel>
        </Section>

        <Section>
            <TextImage title="La alegría en tiempos difíciles">
                <p>
                    Son tiempos de transformación, debemos afrontar grandes
                    retos a todos los niveles: sociales, personales, económicos
                    y espirituales.
                </p>
                <p>
                    En medio de la incertidumbre, la ansiedad y el cambio
                    constante, es natural preguntarse: ¿cómo podemos recuperar
                    la alegría y la paz interior?
                </p>
                <p>
                    A través de herramientas prácticas, espacios de encuentro y
                    recursos gratuitos, te acompañamos para que puedas
                    reconectar con tu esencia, cultivar la serenidad y encontrar
                    sentido incluso en los momentos más desafiantes.
                </p>
                <template #image>
                    <figure class="diff aspect-16/9" tabindex="0" ref="diffFigure">
                        <div class="diff-item-1" role="img" tabindex="0">
                            <img
                                alt="alegre"
                                src="/almacen/medios/portada/alegre.jpg"
                            />
                        </div>
                        <div class="diff-item-2" role="img">
                            <img
                                alt="ansioso"
                                src="/almacen/medios/portada/ansioso.jpg"
                            />
                        </div>
                        <div class="diff-resizer" style="width: 50px"></div>
                    </figure>
                    <button class="btn btn-success mt-4" @click="equilibrar()">Equilibrar</button>
                </template>
            </TextImage>
        </Section>

        <Section>
            <Hero
                title="Biblioteca Tseyor"
                :href="route('biblioteca')"
                buttonLabel="Biblioteca Tseyor"
                srcImage="/almacen/medios/portada/biblioteca_tseyor_libros.jpg"
                textClass="container"
                imageSideClass="bg-right"
                full
                image-right
            >
            <Prose class="md:max-w-[24rem]">
                Ofrecemos gratuitamente cientos de libros, meditaciones, audios,
                psicografías... todo ello procedente de la Fuente original del
                Fractal de Tseyor, a través de la transmisión telepática por
                seres humanos de otros niveles de consciencia.
            </Prose>
            </Hero>
        </Section>



        <Section>
            <Hero
                title="Los Guías Estelares"
                subtitle="Recibimos las referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia"
                :href="route('guias')"
                buttonLabel="Guías Estelares"
                srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
                textClass="container"
                full
                cover
            />
        </Section>

        <Section>
            <Hero
                title="Comunidad Tseyor"
                subtitle="Somos una comunidad de personas normales y corrientes que trabajamos hacia la instauración
                de las Sociedades Armónicas de la Galaxia."
                buttonLabel="Únete a la comunidad TSEYOR"
                :href="route('quienes-somos')"
                srcImage="/almacen/medios/portada/quienes-somos8-compressor.jpg"
                textClass="container"
                full
                cover
                image-right
            />
        </Section>

        <Section>
            <Hero
                title="Filosofía Cósmico-Crística"
                subtitle="A través de la comunicación con seres de otras civilizaciones avanzadas, estamos aprendiendo la filosofía cósmico-crística que nos guía hacia la hermandad, la humildad y la retroalimentación."
                buttonLabel="conoce nuestra filosofía"
                :href="route('filosofia')"
                srcImage="/almacen/medios/paginas/filosofia.jpg"
                textClass="container"
                full
                cover
            />
        </Section>

        <Section ref="contadoresEl">
            <div
                class="grid grid-cols-2 md:px-5 md:grid-cols-4 gap-x-7 gap-y-10 text-lg mt-20 md:mt-0"
                v-if="stats"
            >
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('comunicados')"
                >
                    <Counter
                        :to="stats.comunicados"
                        :count="contando"
                        :delay="0"
                        class="text-2xl"
                    />
                    <span>Comunicados</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('comunicados')"
                >
                    <Counter
                        :to="stats.paginas"
                        :count="contando"
                        :delay="200"
                        class="text-2xl"
                    />
                    <span>Páginas transcritas</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('libros')"
                >
                    <Counter
                        :to="stats.libros"
                        :count="contando"
                        :delay="400"
                        class="text-2xl"
                    />
                    <span>Libros</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('audios')"
                >
                    <Counter
                        :to="stats.audios"
                        :count="contando"
                        :delay="600"
                        class="text-2xl"
                    />
                    <span>Audios</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('videos')"
                >
                    <Counter
                        :to="stats.videos"
                        :count="contando"
                        :delay="800"
                        class="text-2xl"
                    />
                    <span>Vídeos</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('usuarios')"
                >
                    <Counter
                        :to="stats.usuarios"
                        :count="contando"
                        :delay="1000"
                        class="text-2xl"
                    />
                    <span>Voluntarios</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('centros')"
                >
                    <Counter
                        :to="stats.centros"
                        :count="contando"
                        :delay="1200"
                        class="text-2xl"
                    />
                    <span>Centros Tseyor</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('entradas')"
                >
                    <Counter
                        :to="stats.entradas"
                        :count="contando"
                        :delay="1400"
                        class="text-2xl"
                    />
                    <span>Entradas de Blog</span>
                </Link>
            </div>
        </Section>

        <!--

        <Section>
            <h2 class="mt-[8rem] text-2xl font-bold mb-0">Miembros de Tseyor</h2>
            <TestimonialSlider :testimonials="testimonials" class="h-full" />
        </Section>
        -->

        <Section>
            <TextText title="Suscríbete" />
            <Suscribe />
        </Section>
    </FullPage>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';

defineProps({
    stats: {},
});

const nav = useNav();
const contadoresEl = ref(null);
const contadoresTop = ref(99999);
const contando = ref(false);

watch(
    () => nav.scrollY,
    (value) => {
        if (value < contadoresTop.value - screen.height / 2) {
            contando.value = false;
        } else if (!contando.value) {
            contando.value = true;
        }
    }
);

function calculaHCounter() {
    contadoresTop.value =
        contadoresEl.value && contadoresEl.value.$el
            ? contadoresEl.value.$el.getBoundingClientRect().top
            : 99999;
}

// https://www.danmatthews.me/posts/lazy-loading-inertia-js
// cargamos las estadísticas un poco más tarde para que la portada cargue más rápido
onMounted(() => {
    console.log("on mounted");
    setTimeout(() => {
        router.reload({
            only: ["stats", "auth"],
        });
    }, 1000);
    // nav.position = 'fixed'
    // contadoresEl.value.$el.getBoundingClientRect().top
    calculaHCounter();
});

const features = [
    {
        title: 'Meditaciones',
        icon: 'ph:hands-praying-duotone',
        description: 'Guiadas por nuestros Guías Estelares, te ayudamos a reconectar con tu esencia y encontrar la paz interior.',
        link: {
            url: route('meditaciones')+'?categoria=Meditaciones',
            label: 'Explora las meditaciones',
        },
        iconClass: 'text-blue-400',
    },
    {
        title: 'Comunicados',
        icon: 'ph:flying-saucer-duotone',
        description: 'Recibimos y compartimos los mensajes de nuestros Guías Estelares, que nos guían hacia la creación de Sociedades Armónicas.',
        link: {
            url: route('comunicados'),
            label: 'Lee los comunicados',
        },
        iconClass: 'text-green-400',
    },
    {
        title: 'Biblioteca Tseyor',
        icon: 'ph:book-open-duotone',
        description: 'Accede a una amplia colección de libros, audios y recursos gratuitos para tu crecimiento espiritual.',
        link: {
            url: route('biblioteca'),
            label: 'Visita la biblioteca',
        },
        iconClass: 'text-yellow-400',
    },
    {
        title: 'Comunidad',
        icon: 'ph:users-duotone',
        description: 'Únete a nuestra comunidad de personas comprometidas con la creación de Sociedades Armónicas.',
        link: {
            url: route('quienes-somos'),
            label: 'Únete a la comunidad',
        },
        iconClass: 'text-purple-400',
    },
];
const carouselItemsToShow = 1; // valor por defecto, se ajusta con breakpoints

const isMobileOrTablet = ref(false);

function checkScreen() {
    isMobileOrTablet.value = window.innerWidth < 1024;
}
onMounted(() => {
    checkScreen();
    window.addEventListener('resize', checkScreen);
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreen);
});

const testimonials = ref([
    {
        name: "John Doe",
        role: "CEO",
        photo: "https://via.placeholder.com/150",
        text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ac leo ullamcorper, consectetur nisi nec, posuere mi.",
    },
    {
        name: "Jane Smith",
        role: "Designer",
        photo: "https://via.placeholder.com/150",
        text: "Duis mollis, turpis quis dapibus fringilla, tortor ex faucibus justo, eu molestie purus diam vel orci.",
    },
    {
        name: "Jim Johnson",
        role: "Developer",
        photo: "https://via.placeholder.com/150",
        text: "Etiam ornare nulla non ligula posuere, vel bibendum urna pharetra. Proin vestibulum orci sed ex lobortis, ac tincidunt est consectetur.",
    },
]);

const diffFigure = ref(null);

function equilibrar() {
    const figure = diffFigure.value;
    if (!figure) return;
    const resizer = figure.querySelector('.diff-resizer');
    if (!resizer) return;
    const targetWidth = figure.offsetWidth;
    const initialWidth = resizer.offsetWidth;
    const duration = 1500; // ms
    const start = performance.now();

    function animate(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const currentWidth = initialWidth + (targetWidth - initialWidth) * progress;
        resizer.style.width = currentWidth + 'px';
        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            resizer.style.width = targetWidth + 'px';
        }
    }
    requestAnimationFrame(animate);
}
</script>

<style scoped>
@reference "../../css/app.css";

.section {
    @apply h-dvh snap-center flex flex-col justify-center;
}

:deep(.carousel__next) {
    right: -30px;
}
:deep(.carousel__prev) {
    left: -30px;
}
:deep(.carousel) {
    overflow: unset;
}
</style>
