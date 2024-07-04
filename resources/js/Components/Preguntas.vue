<template>

    <div class="preguntas md:container mx-auto my-12" :class="titulo?'min-h-[60vh]':''">

        <div class="flex container justify-between items-center mb-7">
            <Back :href="backUrl">{{ backText }}</Back>
            <Link href="/libros/preguntas-y-respuestas-tseyor" class="btn btn-sm btn-primary flex gap-2 items-center"
                title='Descarga esta sección en pdf'>
            <Icon icon="ph:download-duotone" />Descargar libro</Link>
        </div>

        <div v-if="titulo" class="py-10 sm:px-10 lg:p-20 max-w-[960px] container mx-auto my-12 bg-base-100 shadow hyphens-auto md:rounded-3xl md:text-justify">

            <h1>{{ titulo }}</h1>

            <slot />
        </div>

    </div>

</template>

<script setup>
const props = defineProps({
    titulo: String,
    backUrl: { type: String, default: '/preguntas-frecuentes' },
    backText: { type: String, default: 'Preguntas frecuentes' },
    fadeOut: { type: Boolean, default: false }
});



onMounted(() => {
    // Obtén todos los enlaces de tipo "#"
    const enlaces = document.querySelectorAll('.preguntas a[href^="#"]');

    console.log('Preguntas: enlaces', enlaces)

    // Agrega un evento de clic a cada enlace
    enlaces.forEach(enlace => {
        enlace.addEventListener('click', function (e) {
            e.preventDefault(); // Evita el comportamiento predeterminado del enlace

            // Obtén el ID del elemento objetivo
            const id = this.getAttribute('href').substring(1);

            scrollToId(id)
        });
    });


    const referencias = document.querySelectorAll('span.Referencia')

    // Agrega un evento de clic a cada enlace
    referencias.forEach(span => {
        const parts = span.textContent.split(/[()]/).filter(x => x.trim()).map(x => x.trim())
        var titles = []
        parts.forEach(ref => {
            if (ref.includes('SLR'))
                titles.push('Ver libro: Conversaciones interdimensionales etapa Sili-Nur')
            else if (ref.includes('DSC'))
                titles.push('Ver libro: El descubrimiento del hombre por el propio hombre')
            else if (ref.includes('RAY'))
                titles.push('Ver libro: El Rayo Sincronizador. Una nueva posición psicológica y mental')
            else if (ref.includes('GLO'))
                titles.push('Ver glosario')
            else if (ref.match(/^Ver \d.*/i))
                titles.push('Ver pregunta ' + ref.replace(/^Ver /, ''));
            else if (ref.match(/^Ver.*/i))
                titles.push(ref);
            else
                titles.push('Ver comunicado ' + ref.replace(/[()]/g, ''));
        })
        span.title = titles.join(' | ')
    })

    // enlace inicial
    if (window.location.hash) {
        setTimeout(() => {
            scrollToId(window.location.hash.substring(1), -60)
        }, 500)
    }
}
)

function scrollToId(id, offset) {
    if (!offset) offset = 0
    console.log('scrolling to', id)
    // Obtén el elemento objetivo
    const objetivo = document.querySelector("a[name='" + id + "']") || document.querySelector(id);

    if (objetivo) {
        // Calcula la posición del elemento objetivo
        const targetY = objetivo.getBoundingClientRect().top;

        const container = document.querySelector('.preguntas')

        const topContainer = container.getBoundingClientRect().top

        // Calcula la altura de la barra de navegación
        const nav = document.querySelector('nav.sticky')
        const alturaNavegacion = nav.offsetHeight;

        const pos = alturaNavegacion - topContainer + targetY

        console.log({ alturaNavegacion, topContainer, targetY, pos })
        // Ajusta la posición del elemento objetivo
        window.scrollTo({
            top: pos - 20 + offset,
            behavior: 'smooth'
        });
    }
}

</script>

<style scoped>
:deep(p a) {
    text-decoration: none;
}

:deep(.MsoToc2) {
    @apply text-xl font-bold;
}

:deep(.MsoToc3) {
    @apply text-lg font-bold;
}

:deep(span.Referencia) {
    @apply text-neutral cursor-vertical-text;
}

:deep(a[name^=_Toc]) {
    @apply !text-left;
}

:deep(h1,h2,h3,h4) {
    @apply sm:hyphens-none;
}
</style>
