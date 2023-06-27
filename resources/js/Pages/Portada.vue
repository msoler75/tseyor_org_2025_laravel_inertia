<template>
    <FullPage>
        <Section :style="{
            background: 'black url(https://via.placeholder.com/1980x1024.png/00000?text=Espacio) center',
            backgroundSize: 'cover'
        }">
        </Section>
        <Section>
            <div class="container mx-auto">

                Identifica los valores y el mensaje de tu ONG: Como su ONG se enfoca en la meditación, el autodescubrimiento
                y
                la transmutación, puedes utilizar imágenes que reflejen la paz, la serenidad y el crecimiento personal.
                También
                puedes utilizar imágenes relacionadas con el espacio y las estrellas para transmitir el mensaje de que su
                filosofía proviene de las estrellas.

                Utiliza colores apropiados: El azul cielo y el blanco son colores apropiados para transmitir la paz, la
                calma y
                la serenidad. Puedes utilizar otros tonos de azul para complementar el diseño.

                Diseña un encabezado claro y directo: Algunos ejemplos de encabezados que podrías utilizar son: "Meditación
                para
                el autodescubrimiento y la transmutación", "Filosofía cósmico-crística para la hermandad y la
                retroalimentación", "Descubre tu verdadero ser con TSEYOR".

                Crea un diseño limpio y ordenado: Algunas Sectiones que podrías incluir en la portada son: "Sobre nosotros",
                "Filosofía TSEYOR", "Actividades y eventos", "Pueblos Tseyor", "Universidad Tseyor de Granada", y
                "Contacto".

                Utiliza llamadas a la acción: Algunas opciones para las llamadas a la acción podrían ser: "Únete a la
                comunidad
                TSEYOR", "Descubre tu camino de autodescubrimiento", o "Explora nuestro calendario de eventos".

                Espero que estas sugerencias te sean útiles par

                yyy
            </div>
        </Section>

        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
        </Section>

        <Section>
            <div class="container mx-auto">

                Autodescubrimiento: Descubre tu verdadero ser a través de la meditación y la transmutación, y experimenta el
                crecimiento personal que necesitas para alcanzar la plenitud.

                Filosofía cósmico-crística: A través de la comunicación con seres de otras civilizaciones avanzadas, hemos
                aprendido la filosofía "cósmico-crística" que nos guía hacia la hermandad, la humildad y la
                retroalimentación.

                Comunidad TSEYOR: Somos una comunidad de personas normales y corrientes que trabajamos hacia la instauración
                de
                las Sociedades Armónicas de la Galaxia y el bienestar de nuestro planeta.

                Divulgación: A través de la Universidad Tseyor de Granada, activamos departamentos de divulgación para
                divulgar
                nuestro mensaje de las estrellas y lograr un mundo más armónico.

                Respeto: Respetamos todas las creencias y valores, y trabajamos en armonía con todas las personas y
                comunidades
                para lograr nuestro objetivo común.
            </div>


        </Section>

        <Section ref="contadoresEl">
            stats: {{ stats }}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5 text-lg" v-if="stats">
                <div class="flex flex-col items-center gap-3">
                    <Counter :to="stats.comunicados" :count="contando" class="text-2xl" />
                    <span>Comunicados</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <Counter :to="stats.libros" :count="contando" :delay="200" class="text-2xl" />
                    <span>Libros</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <Counter :to="stats.audios" :count="contando" :delay="400" class="text-2xl" />
                    <span>Audios</span>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <Counter :to="stats.usuarios" :count="contando" :delay="600" class="text-2xl" />
                    <span>Usuarios</span>
                </div>
            </div>
        </Section>


        <Section>
            Pueblos Tseyor
        </Section>
        <Section>
            <div class="container mx-auto">

                Sección "Universidad Tseyor de Granada": Información sobre la Universidad Tseyor de Granada, sus
                departamentos
                de divulgación y sus programas de estudio, así como una llamada a la acción que invite a los visitantes a
                explorar más.
            </div>
        </Section>

        <Section>
            Sección "Contacto": Información de contacto de su ONG, incluyendo dirección, correo electrónico y número de
            teléfono, así como un formulario de contacto para que los visitantes puedan enviar sus preguntas o comentarios.
        </Section>



        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Nuestra filosofía" srcImage="https://via.placeholder.com/1263x569.png/000fdd?text=Filosofía"
                buttonLabel="Conoce nuestra filosofía" :href="route('filosofia')">
                <p>Seguimos la filosofía nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia.</p>
            </TextImage>
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


</script>

<style scoped>
.section {
    @apply h-screen snap-center flex flex-col justify-center;
}
</style>
