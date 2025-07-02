<template>
    <h1 class="hidden">Inicio</h1>

    <FullPage down-label="Saber más">

        <FondoVideoPortada
            data-theme="light"
            class="relative"
            style="
                background: linear-gradient(
                    to bottom,
                    #0056b3 0%,
                    #a3daff 100%
                );
            "
        >
            <div class="w-full h-full relative">
                <Hero
                    title="ONG MUNDO ARMÓNICO TSEYOR"
                    textClass="md:max-w-(--breakpoint-md) justify-center"
                    title-class="text-yellow-400 font-bold text-4xl text-shadow-[0_4px_2px_rgb(40,79,157),0_-4px_2px_rgb(40,79,157),-4px_0_2px_rgb(40,79,157),4px_0_2px_rgb(40,79,157),2px_4px_2px_rgb(40,79,157),2px_-4px_2px_rgb(40,79,157),-4px_2px_2px_rgb(40,79,157),4px_2px_2px_rgb(40,79,157),-2px_4px_2px_rgb(40,79,157),-2px_-4px_2px_rgb(40,79,157),-4px_-2px_2px_rgb(40,79,157),4px_-2px_2px_rgb(40,79,157)]"
                >
                    <div
                        class="bg-primary/0 hover:bg-primary/50 hover:backdrop-blur-md hover:shadow-2xl text-white/80 transition duration-500 p-4 md:p-7 rounded-xl text-lg"
                    >
                        Nuestro proyecto es el despertar a una nueva consciencia
                        y la creación de las Sociedades Armónicas a través de la
                        transmutación de nuestro pensamiento.
                    </div>

                    <template #action>
                        <ActionButton
                            v-if="hayProximosEventos"
                            href="eventos"
                            class="btn w-fit btn-secondary my-2 min-w-[10rem] shadow-none"
                            >Próximos Eventos</ActionButton
                        >
                        <ActionButton
                            v-else
                            :href="route('quienes-somos')"
                            class="my-2 min-w-[10rem] shadow-none"
                        >
                            Quiénes Somos
                        </ActionButton>

                        <ActionButton
                            v-if="false"
                            :href="route('quienes-somos')"
                            class="my-2 min-w-[10rem]"
                        >
                            Quiénes somos
                        </ActionButton>

                        <ActionButton
                            v-if="false"
                            href="eventos"
                            class="flex sm:hidden w-fit btn-secondary my-2 min-w-[10rem]"
                            >PRÓXIMOS EVENTOS</ActionButton
                        >
                    </template>
                </Hero>
                <Link
                    v-if="false"
                    href="eventos"
                    class="hidden sm:flex w-fit text-xs gap-2 btn btn-sm btn-secondary absolute bottom-12 sm:bottom-8 right-8 z-40"
                    ><Icon
                        icon="ph:arrow-right-duotone"
                        class="transform scale-150"
                    />
                    PRÓXIMOS EVENTOS</Link
                >
            </div>
        </FondoVideoPortada>

        <!--



        -->

        <!--

        <Section>
            <h2 class="mt-[8rem] text-2xl font-bold mb-0">Miembros de Tseyor</h2>
            <TestimonialSlider :testimonials="testimonials" class="h-full" />
        </Section>
        -->

        <Section>
            <TextImage
                title="La Revolución de la Consciencia"
                full
                cover
                src-image="/almacen/medios/portada/revolucion_consciencia.jpg"
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
            </TextImage>
        </Section>

        <Section>
            <TextImage
                title="Sentir la verdadera alegría"
                image-right
                full
            >
                <p>
                    La desconexión con la naturaleza, con nuestro verdadero
                    Ser... El individualismo exacerbado... El materialismo...
                </p>
                <p>
                    Estamos en un desequilibrio, con estados de desasosiego,
                    miedo, intranquilidad...
                </p>
                <div class="flex justify-center">
                    <button
                        class="btn mt-4 transition-colors duration-300"
                        :class="isEquilibrado ? 'btn-warning' : 'btn-success'"
                        @click="toggleEquilibrio()"
                    >
                        {{
                            isEquilibrado
                                ? "VOLVER AL DESEQUILIBRIO"
                                : "ENCONTRAR EL EQUILIBRIO"
                        }}
                    </button>
                </div>
                <template #image>
                    <figure
                        class="diff aspect-16/9"
                        tabindex="0"
                        ref="diffFigure"
                    >
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
                        <div
                            class="diff-resizer cursor-col-resize"
                            :style="{ width: resizerWidth + 'px' }"
                            @mousedown="startDrag"
                            @touchstart="startDrag"
                        ></div>
                    </figure>
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
                <p>
                    Ofrecemos gratuitamente cientos de libros, meditaciones,
                    audios, psicografías... todo ello procedente de la Fuente
                    original del Fractal de Tseyor, a través de la transmisión
                    telepática por seres humanos de niveles superiores de
                    consciencia.
                </p>
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
                cover
            >
            <p>
                Recibimos las referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia a través de la telepatía,
                y nos ayudan a comprender la filosofía cósmico-crística que nos guía hacia la hermandad, la humildad y la retroalimentación.
            </p>
            </Hero>
        </Section>

       <!-- stats here -->
          <Section ref="contadoresEl">

            <h2 class="px-3 mb-12">Tseyor en números</h2>

            <div
            v-if="stats"
                class="grid grid-cols-2 md:px-5 md:grid-cols-4 gap-3 md:gap-x-7 md:gap-y-10 text-lg px-2"
            >
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

        <Section>
            <Hero
                title="Comunidad Tseyor"
                buttonLabel="¿Quiénes Somos?"
                :href="route('quienes-somos')"
                srcImage="/almacen/medios/portada/quienes-somos8-compressor.jpg"
                textClass="container"
                full
                cover
                image-right
            >
            <p>
                Somos una comunidad de personas que convivimos y trabajamos día a día para la instauración de las Sociedades Armónicas de la Galaxia, a través de la transmutación del ego al Yo superior.
            </p>
            </Hero>
        </Section>

        <Section>
            <Hero
                title="Filosofía Cósmico-Crística"
                buttonLabel="conoce nuestra filosofía"
                :href="route('filosofia')"
                srcImage="/almacen/medios/paginas/filosofia.jpg"
                textClass="container"
                full
                cover
            >
            <p>
                A través de la comunicación con seres humanos de civilizaciones muchos avanzadas estamos aprendiendo la filosofía cósmico-crística, que nos guía hacia
                 la hermandad, la humildad y la retroalimentación.
            </p>
            <p>
                La Unidad en la Diversidad es la clave para la creación de Sociedades Armónicas.
            </p>
            </Hero>
        </Section>

        <Section>
            <div class="flex justify-center items-center w-full">
                <Suscribe />
            </div>
        </Section>
    </FullPage>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

defineProps({
    hayProximosEventos: {
        type: Boolean,
        default: false,
    },
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
    if (contadoresEl.value && contadoresEl.value.$el) {
        contadoresTop.value =
            contadoresEl.value.$el.getBoundingClientRect().top;
    } else {
        contadoresTop.value = 99999;
    }
}

const lazyOff = ref(true);
// https://www.danmatthews.me/posts/lazy-loading-inertia-js
// cargamos las estadísticas un poco más tarde para que la portada cargue más rápido
onMounted(() => {
    console.log("on mounted");
    setTimeout(() => {
        lazyOff.value = true;
        nextTick(() => {
            if (contadoresEl.value) {
                calculaHCounter();
            }
            router.reload({
                only: ["stats", "auth"],
            });
        });
    }, 2500);
});

const slides = [
    {
        title: "Meditaciones guiadas",
        icon: "ph:hands-praying-duotone",
        description:
            "Talleres y meditaciones entregados por nuestros Guías Estelares, te ayudamos a reconectar con tu esencia y encontrar la paz interior.",
        link: {
            url: route("audios"),
            label: "Explora los audios",
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
            url: route("cursos.inscripcion.nueva"),
            label: "Únete a la comunidad",
        },
        iconClass: "text-purple-400",
    },
];
const isMobileOrTablet = ref(false);

function checkScreen() {
    isMobileOrTablet.value = window.innerWidth < 1024;
}
onMounted(() => {
    checkScreen();
    window.addEventListener("resize", checkScreen);
});
onBeforeUnmount(() => {
    window.removeEventListener("resize", checkScreen);
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
const resizerWidth = ref(50);
const isDragging = ref(false);

// Computed que se actualiza automáticamente según la posición del resizer
const isEquilibrado = computed(() => {
    if (!diffFigure.value) return false;
    const figureWidth = diffFigure.value.offsetWidth || 400; // valor por defecto
    // Consideramos equilibrado cuando el resizer está más cerca del final (más de 75% del ancho)
    return resizerWidth.value > figureWidth * 0.75;
});

function startDrag(event) {
    isDragging.value = true;
    event.preventDefault();

    const figure = diffFigure.value;
    if (!figure) return;

    const rect = figure.getBoundingClientRect();

    function handleMove(e) {
        if (!isDragging.value) return;

        const clientX = e.clientX || e.touches[0].clientX;
        const newWidth = Math.max(0, Math.min(rect.width, clientX - rect.left));
        resizerWidth.value = newWidth;
    }

    function handleEnd() {
        isDragging.value = false;
        document.removeEventListener("mousemove", handleMove);
        document.removeEventListener("mouseup", handleEnd);
        document.removeEventListener("touchmove", handleMove);
        document.removeEventListener("touchend", handleEnd);
    }

    document.addEventListener("mousemove", handleMove);
    document.addEventListener("mouseup", handleEnd);
    document.addEventListener("touchmove", handleMove);
    document.addEventListener("touchend", handleEnd);
}

function toggleEquilibrio() {
    const figure = diffFigure.value;
    if (!figure) return;

    const targetWidth = isEquilibrado.value ? 50 : figure.offsetWidth;
    const initialWidth = resizerWidth.value;
    const duration = 1500;
    const start = performance.now();

    function animate(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);

        // Usar una función de easing suave
        const easeProgress = 1 - Math.pow(1 - progress, 3);

        const currentWidth =
            initialWidth + (targetWidth - initialWidth) * easeProgress;
        resizerWidth.value = currentWidth;

        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
}
</script>
