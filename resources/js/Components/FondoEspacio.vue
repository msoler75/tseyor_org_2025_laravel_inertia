<template>
    <div ref="fondo" class="fondo-espacio relative" @mousemove="move">
        <div id='nebula'></div>
        <div ref="stars1" id='stars1' class="stars"></div>
        <div ref="stars2" id='stars2' class="stars"></div>
        <div ref="stars3" id='stars3' class="stars"></div>
        <div ref="stars4" id='stars4' class="stars"></div>
        <div id='tierra'></div>
        <slot />
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
const z1 = .4
const z2 = .5
const z3 = 1
const z4 = 2

onMounted(() => {
    console.time('stars');
    const density = screen.width / 198
    let bg1 = generateStars(40 * density, z1, '#fff')
    let bg2 = generateStars(30 * density, z2, '#fff')
    let bg3 = generateStars(20* density, z3, '#fff')
    let bg4 = generateStars(4* density, z4, '#fff')
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
    const dx = (screen.width - x) / 20
    const dy = (screen.height - y) / 10
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
    --tierra-scale: 3.0;
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


.stars,
#tierra,
#nebula {
    z-index: 0;
}


#tierra,
#nebula {
    position: absolute; 
    width: 1px;
    height: 1px;
    background: transparent;
    height: 100%;
    left: 0;
    top: 0;
    z-index: 0;
}

#tierra:after {
    content: "";
    display: none;
    position: absolute;
    left: calc(var(--tierra-scale) * -0.0vw);
    bottom: calc(var(--tierra-scale) * -55vw);
    width: 100vw;
    height: 100vw;
    background: white;
    background: url(/almacen/medios/portada/earth-1365995.jpg) center no-repeat;
    background-size: cover;
    border-radius: 100%;
    box-shadow: 0 0 30px rgba(200, 215, 255, .5);
    transform: scale(var(--tierra-scale)) translate(0, var(--move-y));
}


#nebula:after {
    content: "";
    opacity: .5;
    display: block;
    position: absolute;
    left: -5vw;
    right: 0;
    width: 100vw;
    height: 100vh;
    background: url(/almacen/medios/portada/space-nebula.jpg) center no-repeat;
    background-size: cover;
    transform: scale(var(--nebula-scale)) translate(calc(var(--move-x) * 0.1), calc(var(--move-y) * 0.1));
}

.stars,
#tierra:after,
#nebula:after {
    transition: transform 0.4s ease-out;
}
</style>
