<template>
    <h1 class="hidden">Inicio</h1>

    <FullPage down-label="Saber más">
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
                        Nuestro proyecto es el despertar a una nueva consciencia
                        y la creación de las Sociedades Armónicas, a través de
                        la transmutación de nuestro pensamiento.
                    </div>
                    <div class="w-full flex justify-center sm:hidden mx-auto mt-4">
                        <Link v-if="hayProximosEventos" href="eventos" class="w-fit text-xs btn btn-secondary">
                            <Icon icon="ph:arrow-right-duotone"
                            class="transform scale-150"/>PRÓXIMOS EVENTOS</Link>
                    </div>
                </Hero>
                <Link
                    v-if="hayProximosEventos"
                    href="eventos"
                    class="w-fit text-xs btn btn-secondary !absolute hidden sm:flex bottom-6 right-8 z-40"
                    ><Icon
                        icon="ph:arrow-right-duotone"
                        class="transform scale-150"
                    />
                    PRÓXIMOS EVENTOS</Link
                >
            </FondoEspacio>
        </Section>

        <Section>
            <Hero
                title="La Revolución de la Consciencia"
                full
                cover
                src-image="/almacen/medios/portada/revolucion_consciencia.jpg"
                src-width="1248"
                src-height="832"
                href="/libros/la-revolucion-de-la-consciencia"
                button-label="Más información"
            >
                <p>
                    La verdadera transformación se logra mediante la
                    autoobservación, el desapego y el sacrificio por la
                    humanidad.
                </p>
                <p>
                    Con la alquimia de un pensamiento sublimado transmutamos el
                    plomo de nuestra personalidad por el oro del espíritu.
                </p>
                <p>
                    Te invitamos a compartir este viaje de autodescubrimiento
                    todos juntos. Ya es hora de despertar del sueño de los
                    sentidos.
                </p>
            </Hero>
        </Section>

        <Section>
            <Hero title="El Rayo Sincronizador" image-right full cover
            src-image="/almacen/medios/portada/rayo_sincronizador.jpg"
            src-width="1024"
            src-height="1024"
            href="/el-rayo-sincronizador"
            button-label="Más información"
            >
                <p>Se está preparando un evento cósmico: el Rayo Sincronizador molecular. Una energía potentísima que actuará en todo el universo manifestado impulsándolo a un nuevo nivel. </p>
                <p>Ahora conviene preparar nuestras mentes y cuerpos para que esta energía pueda actuar en nosotros positivamente. </p>
        </Hero>
        </Section>

        <Section>
            <Hero
                title="Biblioteca Tseyor"
                :href="route('biblioteca')"
                buttonLabel="Biblioteca Tseyor"
                src-image="/almacen/medios/portada/biblioteca_tseyor_libros.jpg"
                src-width="2004"
                src-height="1639"
                textClass="container"
                imageSideClass="bg-right"
                full
            >
                <Prose class="md:max-w-[24rem]">
                    Ofrecemos gratuitamente cientos de libros, meditaciones,
                    audios, psicografías... todo ello procedente de la Fuente
                    original del Fractal de Tseyor, a través de la transmisión
                    telepática por seres humanos de otros niveles de
                    consciencia.
                </Prose>
            </Hero>
        </Section>

        <Section>
            <Hero
                title="Los Guías Estelares"
                :href="route('guias')"
                buttonLabel="Guías Estelares"
                srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
                textClass="container"
                full
                image-right
                cover
            >
                <p>Recibimos las referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia.</p>
                <p>Todos nuestros materiales se basan en comunicaciones mantenidas con nuestros tutores, quienes con paciencia, amor, comprensión, nos guían en este proceso de cambio de Era.</p>
            </Hero>
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

        <Section v-if="lazyOff" ref="contadoresEl">
            <h2 class="px-3 mb-12">Tseyor en números</h2>
            <div
                class="grid grid-cols-2 md:px-5 md:grid-cols-4 gap-3 md:gap-x-7 md:gap-y-10 text-lg px-2"
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
                    :href="route('libros')"
                >
                    <Counter
                        :to="stats.libros"
                        :count="contando"
                        :delay="200"
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
                        :delay="400"
                        class="text-2xl"
                    />
                    <span>Audios</span>
                </Link>
                <Link
                    class="btn flex-col h-auto p-4 border border-gray-500"
                    :href="route('meditaciones')"
                >
                    <Counter
                        :to="stats.meditaciones"
                        :count="contando"
                        :delay="600"
                        class="text-2xl"
                    />
                    <span>Meditaciones</span>
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

defineProps({
    hayProximosEventos: Boolean,
    stats: {},
});

const nav = useNav();
const contadoresEl = ref(null);
const contadoresTop = ref(99999);
const contando = ref(false);

const diffFigure = ref(null);

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

const lazyOff = ref(true);
// https://www.danmatthews.me/posts/lazy-loading-inertia-js
// cargamos las estadísticas un poco más tarde para que la portada cargue más rápido
onMounted(() => {
    // Inicializar el ancho de la figura cuando el componente se monta
    nextTick(() => {
        if (diffFigure.value) {
            figureWidth.value = diffFigure.value.offsetWidth || 400;
        }
        calculaHCounter();
        router.reload({
            only: ["auth"],
        });

        startResizeMonitoring()
    });

    // Listener para actualizar figureWidth cuando cambie el tamaño de ventana
    function updateFigureWidth() {
        if (diffFigure.value) {
            figureWidth.value = diffFigure.value.offsetWidth;
        }
    }

    window.addEventListener('resize', updateFigureWidth);

    onBeforeUnmount(() => {
        window.removeEventListener('resize', updateFigureWidth);
    });

    setTimeout(() => {
        lazyOff.value = true;
        nextTick(() => {
            calculaHCounter();
            router.reload({
                only: ["stats"],
            });
        });
    }, 1500);
});

const slides = [
    {
        title: "Meditaciones",
        icon: "ph:hands-praying-duotone",
        description:
            "Talleres y meditaciones entregados por nuestros Guías Estelares, te ayudamos a reconectar con tu esencia y encontrar la paz interior.",
        link: {
            url: route("meditaciones") + "?categoria=Meditaciones",
            label: "Explora las meditaciones",
        },
        iconClass: "text-blue-400",
    },
    {
        title: "Comunicados",
        icon: "ph:flying-saucer-duotone",
        description:
            "Lee las conversaciones mantenidas con nuestros Guías Estelares, que nos guían hacia la creación de Sociedades Armónicas.",
        link: {
            url: route("comunicados"),
            label: "Lee los comunicados",
        },
        iconClass: "text-green-400",
    },
    {
        title: "Biblioteca Tseyor",
        icon: "ph:book-open-duotone",
        description:
            "Accede a una amplia colección de libros, audios y recursos gratuitos para tu crecimiento espiritual.",
        link: {
            url: route("biblioteca"),
            label: "Visita la biblioteca",
        },
        iconClass: "text-yellow-400",
    },
    {
        title: "Comunidad",
        icon: "ph:users-duotone",
        description:
            "Únete a nuestra comunidad de personas comprometidas con la creación de Sociedades Armónicas.",
        link: {
            url: route("quienes-somos"),
            label: "Únete a la comunidad",
        },
        iconClass: "text-purple-400",
    },
];


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
:deep(.carousel__next--disabled),
:deep(.carousel__prev--disabled) {
    opacity: 0;
}
</style>
