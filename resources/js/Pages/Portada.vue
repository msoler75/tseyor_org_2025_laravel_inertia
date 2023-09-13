<template>
    <FullPage>

        <Section :style="{
            background: 'black url(/storage/imagenes/portada/earth-1756274.jpg) bottom',
            backgroundSize: 'cover'
            }" data-theme="winter">
            <Hero title="Contacto Extraterrestre" xsrcImage="/storage/imagenes/portada/unidad.jpg" :href="route('cursos')"
                buttonLabel="Curso gratuito" textClass="bg-black bg-opacity-70 p-5 rounded-xl justify-center" image-right>
                <em>En el cosmos existen muchas criaturas. Existen seres de muy diversas categorías vibracionales.
                    Nosotros aquí en este nivel estamos reforzados particularmente por la Confederación de Mundos Habitados
                    de la Galaxia.</em>
            </Hero>
        </Section>

        <Section>
            <Hero title="El proceso de autodescubrimiento"
                subtitle="A través de meditaciones, talleres y un gran sentimiento de hermanamiento conseguimos transmutar nuestra personalidad hasta lograr la Unidad."
                :href="route('cursos')" buttonLabel="Curso gratuito"
                srcImage="/storage/imagenes/portada/1560780877_017869_1560780990_noticia_normal_recorte1.jpg"
                textClass="container" imageSideClass="bg-right" full cover />
        </Section>

        <Section>
            <Hero title="Los Guías Estelares"
                subtitle="Recibimos las referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia"
                :href="route('guias')" buttonLabel="Guías Estelares"
                srcImage="/storage/imagenes/portada/todos-los-guias-compressor.jpg" textClass="container" full cover
                image-right />
        </Section>


        <Section>
            <Hero title="Comunidad Tseyor" subtitle="Somos una comunidad de personas normales y corrientes que trabajamos hacia la instauración
                de las Sociedades Armónicas de la Galaxia." buttonLabel="Únete a la comunidad TSEYOR"
                :href="route('cursos')" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Comunidad%20Tseyor"
                textClass="container" full cover />
        </Section>

        <Section>
            <Hero title="Filosofía Cósmico-Crística"
                subtitle="A través de la comunicación con seres de otras civilizaciones avanzadas, hemos aprendido la filosofía cósmico-crística que nos guía hacia la hermandad, la humildad y la retroalimentación."
                buttonLabel="conoce nuestra filosofía" :href="route('filosofia')"
                srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía" textClass="container" full cover
                image-right />
        </Section>


        <Section ref="contadoresEl" >
            <div class="grid grid-cols-2 md:px-5 md:grid-cols-4 gap-x-7 gap-y-20 text-lg mt-20 md:mt-0" v-if="stats">
                <Link class="btn flex-col h-auto p-4" :href="route('comunicados')">
                <Counter :to="stats.comunicados" :count="contando" :delay="0" class="text-2xl" />
                <span>Comunicados</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('comunicados')">
                <Counter :to="stats.paginas" :count="contando" :delay="200" class="text-2xl" />
                <span>Páginas transcritas</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('libros')">
                <Counter :to="stats.libros" :count="contando" :delay="400" class="text-2xl" />
                <span>Libros</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('audios')">
                <Counter :to="stats.audios" :count="contando" :delay="600" class="text-2xl" />
                <span>Audios</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('videos')">
                <Counter :to="stats.videos" :count="contando" :delay="800" class="text-2xl" />
                <span>Vídeos</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('libros')">
                <Counter :to="stats.usuarios" :count="contando" :delay="1000" class="text-2xl" />
                <span>Voluntarios</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('centros')">
                <Counter :to="stats.centros" :count="contando" :delay="1200" class="text-2xl" />
                <span>Centros Tseyor</span>
                </Link>
                <Link class="btn flex-col h-auto p-4" :href="route('entradas')">
                <Counter :to="stats.entradas" :count="contando" :delay="1400" class="text-2xl" />
                <span>Entradas de Blog</span>
                </Link>
            </div>
        </Section>



        <Section >
            <h2 class="mt-[8rem] text-2xl font-bold mb-0">Miembros de Tseyor</h2>
            <TestimonialSlider :testimonials="testimonials" class="h-full"/>
        </Section>




        <Section>
            <TextText title="Suscríbete" subtitle="Recibe nuestro boletín con los últimos comunicados, noticias, eventos..."
                buttonLabel="RECIBIR BOLEtín" :href="route('filosofia')" textClass="container" full cover />
            <div class="flex gap-5">
                <input type="email" class="max-w-[300px] mx-auto" placeholder="correo@..." /> <button
                    class="btn btn-primary">Suscribirse</button>
            </div>
        </Section>

    </FullPage>
</template>

<script setup>
import { useNav } from '@/Stores/nav'
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

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
    setTimeout(() => {
        router.reload({
            only: ['stats']
        })
    }, 3000
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
.section {
    @apply h-screen snap-center flex flex-col justify-center;
}
</style>
