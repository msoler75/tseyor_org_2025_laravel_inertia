<template>
    <Preguntas titulo="Preguntas frecuentes" backUrl="quienes-somos" backText="¿Quiénes somos?">
        <p>Aquí puedes encontrar respuesta a las preguntas más habituales. Accede a las siguientes secciones para
            ampliar la información.</p>

        <ul class="text-lg mt-10">
            <li>
                <Link class="hover:underline" :href="route('preguntas1')">Extraterrestres o hermanos mayores, y otros
                seres</Link>
            </li>
            <li>
                <Link class="hover:underline" :href="route('preguntas2')">El Salto Cuántico</Link>
            </li>
            <li>
                <Link class="hover:underline" :href="route('preguntas3')">Sobre el grupo TSEYOR</Link>
            </li>
        </ul>
    </Preguntas>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })
</script>
