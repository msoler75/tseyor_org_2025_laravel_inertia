<template>
    <div ref="fondo" class="fondo-espacio relative" @mousemove="move">
        <div id='galaxy' class="galaxy  hidden lg:block"></div>
        <div id='nebula' class="nebula"></div>
        <div ref="stars1" id='stars1' class="stars hidden lg:block"></div>
        <div ref="stars2" id='stars2' class="stars hidden lg:block"></div>
        <div ref="stars3" id='stars3' class="stars hidden lg:block"></div>
        <div ref="stars4" id='stars4' class="stars hidden lg:block"></div>
        <slot/>
    </div>
</template>

<script setup>

function generateStars(n, scale, color) {
    const points = []
    for (let i = 0; i < n; i++) {
        const w = screen.width * 1 / scale
        const h = screen.height * 1 / scale
        const x = Math.random() * (w + 400) - 200
        const y = Math.random() * (h + 400) - 200
        const p = `${Math.round(x)}px ${Math.round(y)}px ${color}`
        points.push(p)
    }
    return points.join(", ")
}

const fondo = ref(null)
const stars1 = ref(null)
const stars2 = ref(null)
const stars3 = ref(null)
const stars4 = ref(null)
const z1 = .25
const z2 = .5
const z3 = 1
const z4 = 2.7

onMounted(() => {
    console.time('stars');
    const density = 3 * screen.width / 198
    let bg1 = generateStars(40 * density, z1, '#fff')
    let bg2 = generateStars(30 * density, z2, '#fff')
    let bg3 = generateStars(20* density, z3, '#fff')
    let bg4 = generateStars(4 * density, z4, '#fff')
    stars1.value.style.boxShadow = bg1
    stars2.value.style.boxShadow = bg2
    stars3.value.style.boxShadow = bg3
    stars4.value.style.boxShadow = bg4
    stars1.value.style.setProperty('--stars-scale', z1)
    stars2.value.style.setProperty('--stars-scale', z2)
    stars3.value.style.setProperty('--stars-scale', z3)
    stars4.value.style.setProperty('--stars-scale', z4)
    console.timeEnd('stars');
})

function move(event) {
    // get mouse position in screen:
    const x = event.clientX
    const y = event.clientY
    const dx = (screen.width - x) / 25
    const dy = (screen.height - y) / 15
    fondo.value.style.setProperty('--move-x', dx + 'px');
    fondo.value.style.setProperty('--move-y', dy + 'px');
}
</script>

<style scoped>
.fondo-espacio {
    background: black;
    background: radial-gradient(ellipse at center, #060f27 0%, #000 100%);
    overflow: hidden;
    --move-x: 0;
    --move-y: 0;
    --stars-scale: 1;
    --nebula-scale: 1.1;
    overflow: hidden;
}

.stars {
    width: 1px;
    height: 1px;
    background: transparent;
    will-change: transform;
    transform: scale(var(--stars-scale)) translate(var(--move-x), var(--move-y));
}

#stars1 {
    opacity: .4;
}

#stars2 {
    opacity: .6;
}

#stars3 {
    opacity: .8;
}

#stars4 {
    opacity: 1;
}


.galaxy,
.stars,
.nebula{
    z-index: 0;
}


.galaxy
{
    position: absolute;
    width: 1px;
    height: 1px;
    background: transparent;
    height: 100%;
    left: 0;
    top: 0;
    z-index: 0;
    mix-blend-mode: plus-lighter;
}


.galaxy:after {
    content: "";
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    width: 100dvw;
    height: 100dvh;
    background: url(/almacen/medios/paginas/galaxy.webp) 9% 73% no-repeat;
    opacity: .7;
}

.nebula
{
    position: absolute;
    width: 1px;
    height: 1px;
    background: transparent;
    height: 100%;
    left: 0;
    top: 0;
    z-index: 0;
    mix-blend-mode: plus-lighter;
}

.nebula:after {
    content: "";
    opacity: 1;
    display: block;
    position: absolute;
    top: 0;
    left: 0 ;
    width: 100vw;
    height: 100vh;
    width: 100dvw;
    height: 100dvh;
    background: url(/almacen/medios/paginas/nebula-space.webp) center no-repeat;
    background-size: cover;
    transform: scale(var(--nebula-scale)) translate(calc(var(--move-x) * .15), calc(var(--move-y) * .15));
}


.stars,
.nebula:after {
    transition: transform 0.4s ease-out;
}



</style>
