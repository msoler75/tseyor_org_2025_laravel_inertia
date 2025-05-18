<template>
      <FullPage>
    <!-- HERO PRINCIPAL -->
    <Section data-theme="night">
      <div class="relative flex flex-col items-center justify-center min-h-[90vh] w-full bg-gradient-to-b from-[#060f27] to-black overflow-hidden">
        <img src="/almacen/medios/portada/portada_ong.png" alt="Portada Tseyor" class="absolute inset-0 w-full h-full object-cover opacity-40 pointer-events-none select-none" />

          <Hero
            title="Bienvenid@ a Mundo Armónico Tseyor"
            :href="route('quienes-somos')"
            buttonLabel="Quiénes somos"
            textClass="text-3xl md:text-4xl font-bold mb-4 text-white drop-shadow-lg"
            image-right
          >
            <div class="text-lg md:text-xl text-gray-200 mb-4">
              Descubre una comunidad dedicada a la hermandad, la unidad y el autodescubrimiento a través de la filosofía cósmico-crística y el contacto interplanetario.
            </div>
          </Hero>

      </div>
    </Section>

    <!-- SECCIONES DESTACADAS -->
    <Section>
      <Hero
        title="El proceso de autodescubrimiento"
        subtitle="Meditaciones, talleres y hermanamiento para lograr la Unidad."
        :href="route('cursos')"
        buttonLabel="Curso gratuito"
        srcImage="/almacen/medios/paginas/meditando.jpg"
        textClass="text-xl font-semibold"
        imageSideClass="bg-right"
        full
        cover
      ></Hero>
    </Section>
    <Section>
      <Hero
        title="Los Guías Estelares"
        subtitle="Recibimos referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia."
        :href="route('guias')"
        buttonLabel="Guías Estelares"
        srcImage="/almacen/medios/paginas/todos-los-guias.jpg"
        textClass="text-xl font-semibold"
        full
        cover
        image-right
      ></Hero>
    </Section>

    <Section>
      <Hero
        title="Comunidad Tseyor"
        subtitle="Personas normales y corrientes trabajando hacia las Sociedades Armónicas de la Galaxia."
        buttonLabel="Únete a la comunidad TSEYOR"
        :href="route('quienes-somos')"
        srcImage="/almacen/medios/portada/quienes-somos8-compressor.jpg"
        textClass="text-xl font-semibold"
        full
        cover
      />
    </Section>

    <Section>
      <Hero
        title="Filosofía Cósmico-Crística"
        subtitle="Aprendiendo la hermandad, humildad y retroalimentación a través de la comunicación con civilizaciones avanzadas."
        buttonLabel="Conoce nuestra filosofía"
        :href="route('filosofia')"
        srcImage="/almacen/medios/paginas/filosofia.jpg"
        textClass="text-xl font-semibold"
        full
        cover
        image-right
      />
    </Section>

    <!-- CONTADORES -->
    <Section ref="contadoresEl">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-7 text-lg mt-20 md:mt-0">
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('comunicados')">
          <Counter :to="stats?.comunicados ?? 0" :count="contando" :delay="0" class="text-2xl text-primary" />
          <span>Comunicados</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('comunicados')">
          <Counter :to="stats?.paginas ?? 0" :count="contando" :delay="200" class="text-2xl text-primary" />
          <span>Páginas transcritas</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('libros')">
          <Counter :to="stats?.libros ?? 0" :count="contando" :delay="400" class="text-2xl text-primary" />
          <span>Libros</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('audios')">
          <Counter :to="stats?.audios ?? 0" :count="contando" :delay="600" class="text-2xl text-primary" />
          <span>Audios</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('videos')">
          <Counter :to="stats?.videos ?? 0" :count="contando" :delay="800" class="text-2xl text-primary" />
          <span>Vídeos</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('usuarios')">
          <Counter :to="stats?.usuarios ?? 0" :count="contando" :delay="1000" class="text-2xl text-primary" />
          <span>Voluntarios</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('centros')">
          <Counter :to="stats?.centros ?? 0" :count="contando" :delay="1200" class="text-2xl text-primary" />
          <span>Centros Tseyor</span>
        </Link>
        <Link class="btn flex-col h-auto p-4 border border-gray-500 bg-white/10 hover:bg-white/20 transition" :href="route('entradas')">
          <Counter :to="stats?.entradas ?? 0" :count="contando" :delay="1400" class="text-2xl text-primary" />
          <span>Entradas de Blog</span>
        </Link>
      </div>
    </Section>

    <!-- SUSCRIPCIÓN -->
    <Section>
      <TextText title="Suscríbete" />
      <Suscribe />
    </Section>
  </FullPage>
</template>

<script setup>

import { router } from '@inertiajs/vue3';

defineProps({
    stats: {}
})

const nav = useNav()
const contadoresEl = ref(null)
const contadoresTop = ref(99999)
const contando = ref(false)

watch(() => nav.scrollY, (value) => {
    if (value < contadoresTop.value - screen.height / 2) {
        contando.value = false
    } else if (!contando.value) {
        contando.value = true
    }
})

function calculaHCounter() {
    contadoresTop.value = contadoresEl.value && contadoresEl.value.$el ? contadoresEl.value.$el.getBoundingClientRect().top : 99999
}

// https://www.danmatthews.me/posts/lazy-loading-inertia-js
// cargamos las estadísticas un poco más tarde para que la portada cargue más rápido
onMounted(() => {
    console.log('on mounted')
    setTimeout(() => {
        router.reload({
            only: ['stats', 'auth'],
        })
    }, 1000
    )
    // nav.position = 'fixed'
    // contadoresEl.value.$el.getBoundingClientRect().top
    calculaHCounter()
})


const testimonials = ref([
    {
        name: 'John Doe',
        role: 'CEO',
        photo: 'https://via.placeholder.com/150',
        text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ac leo ullamcorper, consectetur nisi nec, posuere mi.',
    },
    {
        name: 'Jane Smith',
        role: 'Designer',
        photo: 'https://via.placeholder.com/150',
        text: 'Duis mollis, turpis quis dapibus fringilla, tortor ex faucibus justo, eu molestie purus diam vel orci.',
    },
    {
        name: 'Jim Johnson',
        role: 'Developer',
        photo: 'https://via.placeholder.com/150',
        text: 'Etiam ornare nulla non ligula posuere, vel bibendum urna pharetra. Proin vestibulum orci sed ex lobortis, ac tincidunt est consectetur.',
    },
])

</script>

<style scoped>
@reference "../../css/app.css";

.section {
  @apply h-dvh snap-center flex flex-col justify-center;
}
</style>
