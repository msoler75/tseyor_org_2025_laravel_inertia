<template>

    <div class="preguntas md:container mx-auto my-12" :class="titulo ? 'min-h-[60vh]' : ''">

        <div class="flex container justify-between items-center mb-7">
            <Back :href="backUrl">{{ backText }}</Back>
            <Link href="/libros/preguntas-y-respuestas-tseyor" class="btn btn-sm btn-primary flex gap-2 items-center"
                title='Descarga esta sección en pdf'>
            <Icon icon="ph:download-duotone" />Descargar libro</Link>
        </div>

        <div v-if="titulo"
            class="py-10 sm:px-10 lg:p-20 max-w-[960px] container mx-auto my-12 bg-base-100 shadow hyphens-auto md:rounded-3xl md:text-justify">

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

const bibliografia = {
    "SLR": "Conversaciones Interdimensionales. Etapa Sili-Nur",
    "SHR": "Conversaciones Interdimensionales. Etapa Shilcars",
    "AUR": "Desde la Constelación de Auriga en Resumen",
    "BRV": "Breviario. Pensamiento desde las estrellas",
    "TRE": "Conversaciones Interdimensionales. Tres primeras conferencias en Internet",
    "OBS": "La autoobservación",
    "PUZ": "Símbolos del puzle holográfico cuántico",
    "FLC": "Filosofía cuántica. La micropartícula como pensamiento trascendente",
    "DSP": "Despertar",
    "RAY": "El rayo sincronizador. Una nueva posición psicológica y mental",
    "TAD": "El traspaso adimensional",
    "HRM": "El hermanamiento",
    "EAR": "Entregando Amor a Raudales",
    "NAV": "La nave Tseyor",
    "TRM": "La transmutación. La prioridad en nuestra vida",
    "DSC": "El descubrimiento del hombre por el propio hombre",
    "MDS": "Más allá del descubrimiento",
    "QUI": "¿Quiénes somos en Tseyor?",
    "PDR": "Todo sobre la piedra",
    "MN1": "Monografías I: Trilogía sobre el perfeccionamiento del pensamiento",
    "MN2": "Monografías II: Trilogía sobre el universo cuántico",
    "MN3": "Monografías III: Trilogía sobre la hermandad",
    "MN4": "Monografias IV",
    "TRI": "Curso Taller de Relaciones Interdimensionales",
    "NVD": "Cuento de Navidad: El Pequeño Christian",
    "LPC": "La Piedra Cósmica",
    "GLO": "Glosario Terminológico",
    "CNT": "Los Cuentos de Tseyor",
    "PLN": "Cuento Cósmico: El Planeta Negro",
    "PUL": "Púlsar Sanador de Tseyor",
};


const nav = useNav()

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
            nav.scrollToId(id)
        });
    });


    const referencias = document.querySelectorAll('span.Referencia')

    // Agrega un evento de clic a cada enlace
    referencias.forEach(span => {
        const parts = span.textContent.split(/[()]/).filter(x => x.trim()).map(x => x.trim())
        var titles = []
        const pattern = /^([A-Z]{3})$/;
        parts.forEach(referencia => {
            const match = referencia.match(pattern);
            if (referencia == "WEB")
                titles.push("Web de Tseyor")
            else if (match) {
                const abreviacion = match[1];
                const titulo = bibliografia[abreviacion];
                if (titulo)
                    titles.push('Ver libro: ' + titulo)
                else
                    titles.push('Ver libro: ' + referencia)
            }
            else if (referencia.match(/^Ver \d.*/i))
                titles.push('Ver pregunta ' + referencia.replace(/^Ver /, ''));
            else if (referencia.match(/^Ver.*/i))
                titles.push(referencia);
            else
                titles.push('Ver comunicado ' + referencia.replace(/[()]/g, ''));
        })
        span.title = titles.join(' | ')
    })

}
)



</script>

<style scoped>
@reference "../../css/app.css";

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

:deep(h1, h2, h3, h4) {
    @apply sm:hyphens-none;
}
</style>
