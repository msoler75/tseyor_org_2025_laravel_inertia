<template>
    <Sections white-first>
        <Section>
            <Text>
                <img src="https://tseyor.org/images/ong-logo.webp?ts=1386336254&thumb=709x278&quality=87&fe=JPG">
                <blockquote>
                    <p>Se está preparando un gran cambio y ello comportará también grandes dificultades. Nuestro grupo, el
                        grupo Tseyor, pondrá su granito de arena para paliarlas.
                    </p>
                </blockquote>
                <blockquote>
                    <p>
                        Habéis recibido cantidad de información; muchos estómagos necesitan de dicho alimento espiritual.
                        Poneros de acuerdo con ese figurado eje central que es la universidad, para preparar trabajos, escritos,
                        monografías...
                    </p>
                    <p>
                        Ahí también está una parte importante de la ayuda humanitaria.
                    </p>
                </blockquote>
            </Text>
        </Section>


        <Section>
            <Text>
                <h2>Objetivos de la ONG Mundo Armónico Tseyor</h2>
                <p>El objeto general de esta Asociación es la cooperación en la consecución de una sociedad armónica, justa
                    y
                    solidaria, partiendo de una verdadera conciencia de la solidaridad, que contribuya a la participación
                    social, a la reflexión sobre las causas de la injusticia y a la creación de sociedades armónicas.</p>

                <p>Este fin se concreta en los objetivos específicos:</p>

                <ol>
                    <li>Proporcionar ayuda humanitaria a personas, grupos y colectivos que la necesiten: alimentación,
                        medios,
                        equipos, semillas y recursos de salud.</li>
                    <li>Cooperar a favor de un desarrollo humano sostenible.</li>
                    <li>Aportar formación cultural, científica y técnica a personas y grupos que la demanden y no tengan
                        acceso
                        a la misma.</li>
                    <li>Realizar actividades que redunden en el beneficio de personas y colectivos, sobre todo de los más
                        carentes, necesitados o indefensos, y en casos de catástrofes naturales o de origen antrópico.</li>
                    <li>Apoyo para la gestión de proyectos, en comunidades empobrecidas, que persigan un desarrollo endógeno
                        sostenible.</li>
                    <li>Sensibilizar a la población en valores relacionados con la armonía, la cooperación, la solidaridad,
                        la
                        justicia social y el respeto de los seres humanos, cualesquiera que sea su condición.</li>
                    <li>Proporcionar medios y formación en el ámbito de la salud, la alimentación, la agricultura, la
                        expresión
                        de ideas y sentimientos que despierten los potenciales creativos de los seres humanos en armonía con
                        el
                        entorno natural.</li>
                </ol>

                <p class="flex justify-end">
                    <a href="1.pdf" class="btn btn-primary" download>Descargar Estatutos de la ONG</a>
                </p>
            </Text>
        </Section>


        <Section>
            <TextImage title="Libro: ONG Mundo Armónico Tseyor"
                srcImage="https://via.placeholder.com/300x400.png/0000ff?text=ONG.jpg" buttonLabel="Descargar libro"
                href="/ong">
                <p>Todo el material recopilado de la ONG de Tseyor.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Muular"
                srcImage="https://via.placeholder.com/1200x400.png/0000ff?text=Muular" buttonLabel="Conoce el Muular"
                :href="route('muular')" image-right>
                <p>La moneda social y espiritual de Tseyor</p>
            </TextImage>
        </Section>


    </Sections>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })
</script>
