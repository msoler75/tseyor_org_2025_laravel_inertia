<template>
    <svg  xmlns="http://www.w3.org/2000/svg" :width="starsW" :height="starsH"
        :viewBox="'0 0 ' + starsW + ' ' + starsH">
        <g ref="group" fill="none" fill-rule="evenodd">
            <rect width="100%" height="100%" fill="#060606" />
            <circle cx="676.7672874773058" cy="597.1612764655976" r="3.0788920676038387" fill="#FFFFFF"
                fill-opacity="0.4443456812210784"></circle>
        </g>
    </svg>
</template>

<script setup>

const props = defineProps({
    maxWidth: {
        default: 1920
    },
    maxHeight: {
        default: 1080
    }
})

const starsW = ref(1920)
const starsH = ref(1024)
const group = ref(null)

onMounted(() => {
    starsW.value = Math.min(props.maxWidth, screen.width)
    starsH.value = Math.min(props.maxHeight, screen.height)
    makeSvg()
})

function makeSvg() {
    var sizeVariance = 1.9; var starSize = .5;
    var opacityVariance = 1;
    var w = starsW.value
    var h = starsH.value
    var stars = w * h / 1000;

    var g = group.value
    if (g && screen.width >= 1024)
        for (var i = 0; i < stars; i++) {
            var x = getRandomNumber(0, w);
            var y = getRandomNumber(0, h);
            var r = getRandomNumber(Math.max(0.01, (starSize - (starSize * sizeVariance))), (starSize + (starSize * sizeVariance)));
            var o = getRandomNumber((1 - opacityVariance), 1); g.append(makeStar(x, y, r, o));
        }
}

function makeStar(x, y, r, o) {
    var s = document.createElementNS("http://www.w3.org/2000/svg", "circle"); s.setAttribute("cx", x);
    s.setAttribute("cy", y);
    s.setAttribute("r", r);
    s.setAttribute("fill", "#FFFFFF"); s.setAttribute("fill-opacity", o); return s;
}

function getRandomNumber(min, max) {
    var dist = max - min;
    return min + (Math.random() * dist);
}
</script>
