<template>
    <Sections>

        <Hero title="Muular" subtitle="La moneda social y espiritual de Tseyor" buttonLabel="Descarga el libro del muular"
            href="libro.pdf" />

        <Section>
            <Text>
                <h2>¿Qué es el Muular?</h2>
                <p>El muular es el nombre que recibe el papel moneda tseyoriano. Su denominación deriva de Muul (montículo
                    en maya) y nos ha sido dado por los hermanos mayores del cosmos.</p>
                <p>Es una moneda social de caracter espiritual que se va a utilizar en el ámbito de Tseyor (Muulasterios,
                    Casas y Pueblos Tseyor) y en sus zonas de influencia o proximidad, para un intercambio justo de bienes y
                    servicios que fomente los valores de hermandad, solidaridad y ayuda humanitaria.</p>
            </Text>
        </Section>


        <Section>
            <TextImage title="Muular Electrónico" subtitle="Intercambia muulares electrónicos desde tu dispositivo"
                buttonLabel="Acceso al Muular electrónico" href="muular-electronico">
                <p>
                    Es la moneda en versión electrónica, que puede ser empleada igualmente en la comunidad Tseyor.
                    Especialmente en las casas Tseyor y Muulasterios, pero también entre tseyorianos que convivan o
                    intercambien de un modo u otro, incluso por internet.
                </p>
            </TextImage>
        </Section>


        <Section>
            <TextImage title="Libro: El Muular. El Papel Moneda Tseyoriano"
                srcImage="https://via.placeholder.com/300x400.png/0000ff?text=Muular" buttonLabel="Descargar libro"
                :href="route('libros')">
                <p>Monografía que recoge todas las informaciones vertidas sobre la ONG Mundo Armónico Tseyor.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Libro-Taller: Mejor con el Muular"
                srcImage="https://via.placeholder.com/300x400.png/0000ff?text=Mejor con el Muular"
                buttonLabel="Descargar libro" :href="route('libros')"
                image-right>
                <p>Taller impartido por la hermana Noiwanak acerca del uso del Muular y su comprensión.</p>
            </TextImage>
        </Section>

    </Sections>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })
</script>
