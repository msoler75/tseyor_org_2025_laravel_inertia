<template>
    <Icon icon="ph:arrow-circle-up-duotone" class="cursor-pointer transition duration-250"
    :class="show?'opacity-100':'opacity-0 pointer-events-none'"
    @click="scrollToTop" />
</template>


<script setup>

const nav = useNav()

const heightToShow = 700 // altura de scroll para esta lógica de mostrar/ocultar con scroll
const wrapToShow = 120 // nº de pixeles de recorrido scroll arriba para mostrar el boton
const wrapToHide = 70 // nº de pixeles de recorrido scroll abajo para ocultar el boton

var show = ref(false)

var prevY = -10000 // marca de valor inicial sin computar
var subiendo = false
var recorridoUp = 0
var recorridoDown = 0

watch(() => nav.scrollY, (y) => {
    if (prevY != 10000) {
        var dy = y - prevY
        if (y < heightToShow)
            show.value = false
        else
            if (dy > 0) {
                if (subiendo) {
                    recorridoDown = dy
                } else {
                    recorridoDown += dy
                    if (recorridoDown > wrapToHide)
                        show.value = false
                }
                subiendo = false
                // bajando
            } else {
                // subiendo
                if (!subiendo) {
                    recorridoUp = dy
                } else {
                    recorridoUp += dy
                    if (recorridoUp < -1 * wrapToShow)
                        show.value = true
                }
                subiendo = true
            }
    }
    prevY = y
})

function scrollToTop() {
    console.log('scrollto_top')
    // si existe un elemento div.sections, hacemos el scroll en ese elemento, si no, en window
    const div = document.querySelector("div.sections.snap-mandatory")
    if(div)
    div.scrollTo({ top: 0, behavior: 'smooth' })
    else
    window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
