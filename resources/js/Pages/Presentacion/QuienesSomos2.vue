<template>
    <Sections height="500">

        <!-- 1. Hero -->
        <Section>
            <EvaluacionSeccion seccion="Hero" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero title="¿Quiénes Somos?"
                srcImage="/almacen/medios/paginas/quienes-somos.jpg"
                src-width="1600"
                src-height="1200"
                buttonLabel="Conoce nuestros orígenes"
                :href="route('origenes-de-tseyor')">
                <p><strong>Mundo Armónico TSEYOR</strong> es una ONG sin ánimo de lucro dedicada al autodescubrimiento, la ayuda humanitaria y la creación de Sociedades Armónicas.</p>
                <p>Nuestra filosofía proviene de la experimentación de la filosofía cósmico-crística que nos traen nuestros Guías Estelares. Es una filosofía viva, no dogmática, en estos tiempos de transformación.</p>
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <span class="badge badge-primary text-xs">ONG sin ánimo de lucro</span>
                    <span class="badge badge-secondary text-xs">Ayuda humanitaria</span>
                    <span class="badge badge-accent text-xs">Comunidad de miembros</span>
                    <span class="badge badge-ghost text-xs">Conocimiento gratuito</span>
                    <span class="badge badge-neutral text-xs">Sin dogmas</span>
                </div>
                <template #action>
                    <Link :href="route('cursos')" class="btn btn-secondary btn-sm">Ver Curso Holístico</Link>
                </template>
            </Hero>
        </Section>

        <!-- 2. Blog: Vida de la comunidad (fotos de eventos) -->
        <Section>
            <EvaluacionSeccion seccion="Blog" :idoneidad="90" :claridad="85" :composicion="85" :posicion="90" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Vida de la comunidad</h2>
                <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">Conócenos mejor a través de nuestro blog, que contiene crónicas de nuestros encuentros, talleres y convivencias.</p>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    <CardContent v-for="entrada in entradasRecientes" :key="entrada.slug"
                        :href="'/blog/' + entrada.slug"
                        :title="entrada.titulo"
                        :image="entrada.imagen"
                        :date="entrada.fecha"
                        imageHeight="240" />
                </div>
            </div>
        </Section>

        <!-- 3. Chac-Mool Puente -->
        <Section>
            <EvaluacionSeccion seccion="Chac-Mool Puente" :idoneidad="85" :claridad="80" :composicion="70" :posicion="70" />
            <TextImage title="Chac-Mool Puente: el canal de comunicación"
                srcImage="/almacen/medios/paginas/Puente.jpg" buttonLabel="Ver Comunicados"
                href="/comunicados"
                image-right
                gridClass="md:grid-cols-[2fr_1fr]">
                <p>Los comunicados son conversaciones interdimensionales realizadas telepáticamente por nuestro hermano Chac-Mool Puente, intermediario entre los dos mundos.</p>
                <p>Conversaciones con la Confederación de Mundos Habitados de la Galaxia para impulsar sociedades sin líderes y la auténtica libertad del individuo.</p>
                <Link :href="route('biblioteca')" class="btn btn-secondary btn-sm mt-2">Explorar biblioteca</Link>
                <div class="mt-4">
                    <Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover text-sm">Preguntas frecuentes sobre los Guías Estelares →</Link>
                </div>
            </TextImage>
        </Section>

        <!-- 4. ¿Dónde estamos? -->
        <Section>
            <EvaluacionSeccion seccion="¿Dónde estamos?" :idoneidad="75" :claridad="85" :composicion="80" :posicion="85" />
            <TextImage title="¿Donde estamos?" srcImage="/almacen/medios/paginas/mapa.jpg"
                buttonLabel="Ver Mapa" :href="route('contactos')" image-right>
                <p>Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.</p>
                <p>Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno! Únete a nuestro proyecto y te acompañaremos en el camino.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-2">Quiero unirme</Link>
            </TextImage>
        </Section>

        <!-- 5. Preguntas frecuentes -->
        <Section>
            <EvaluacionSeccion seccion="Preguntas frecuentes" :idoneidad="85" :claridad="90" :composicion="85" :posicion="85" />
            <div class="container mx-auto py-16 px-4 max-w-4xl">
                <h2 class="text-center mb-4">Preguntas frecuentes</h2>
                <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">Las dudas más comunes sobre TSEYOR, los Guías Estelares y el Salto Cuántico.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Sobre TSEYOR</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué es el grupo Tseyor?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Es esto una secta?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué significa la palabra Tseyor?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Guías Estelares</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Cómo contactan con nosotros?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué están aquí?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué no se presentan?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Salto Cuántico</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿En qué consiste el cambio?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Cómo nos afectará?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Podemos prepararnos?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 6. CTA final -->
        <Section>
            <EvaluacionSeccion seccion="CTA final" :idoneidad="85" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Quiero ser de Tseyor" buttonLabel="Curso Holístico"
                :href="route('cursos')" textClass="space-y-8">
                <p>Para formar parte de la comunidad de miembros de la ONG debes realizar nuestro Curso Holístico, totalmente gratuito.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-lg mt-4">Inscríbete</Link>
            </Hero>
        </Section>

    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"
import CardContent from "@/Components/CardContent.vue"

defineProps({
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
})
</script>
