<template>
    <div id="scroll-to-here" class="transition-opacity duration-200"
            :class="[fadeOnNavigate && nav.navigating?'opacity-0 pointer-events-none':'',
                ifSamePage&&!ifSamePath?'if-same-page':'',
                ifSamePath?'if-same-path':'',
                ifOriginPath?'if-origin-path-'+ifOriginPath.replace(/\//g,'-'):'',
                instant?'instant':''
            ]">
        <slot/>
    </div>
</template>

<script setup>
defineProps({
    fadeOnNavigate: {type: Boolean, default: true},
    ifSamePage: {type: Boolean, default: true}, // si navegamos a la misma pagina, hacer scroll aquí
    ifSamePath: {type: Boolean, default: false}, // si navegamos a la misma ruta base /terminos/{id}, hacer scroll aquí
    ifOriginPath: {type: String, default: ""}, // si navegamos desde una ruta con path dada, hacer scroll aquí
    instant: {type: Boolean, default: false}
})

const nav = useNav()


</script>
