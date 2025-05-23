<template>
    <div id="composicion" class="relative">
        <div id="fondo" class="w-full overflow-x-hidden">
            <img src="/almacen/medios/portada/nueva/PORTADA_fondo_cielo.png" class="w-full h-auto" />
        </div>
        <div id="montanyas" class="w-full overflow-x-hidden">
            <img src="/almacen/medios/portada/nueva/PORTADA_fondo_montanyas.png" class="w-full absolute h-auto" />
        </div>
        <div id="personas" class="w-full overflow-x-hidden">
            <img src="/almacen/medios/portada/nueva/PORTADA_personas_campo2.png" class="w-full absolute h-auto" />
        </div>
    </div>
</template>

<script setup>
const nav = useNav()

// Configuración: offset final e inicial desde el bottom del fondo para cada layer (como ratio de la altura del fondo)
const portadaLayers = [
    { id: 'montanyas', finalOffsetBottomRatio: 0.0, initialOffsetBottomRatio: 0.0, initialScaleAdd: 0, finalScaleAdd: 0 },
    { id: 'personas', finalOffsetBottomRatio: 0.0, initialOffsetBottomRatio: 0.5, initialScaleAdd: 0, finalScaleAdd: 0 },
];

function setLayerTops() {
    const fondoImg = document.querySelector('#fondo img');
    const fondoHeight = fondoImg?.naturalHeight || fondoImg?.clientHeight || window.innerHeight;
    console.log('fondoHeight', fondoHeight);
    portadaLayers.forEach(layer => {
        const el = document.querySelector('#'+layer.id+' img');
        if (el) {
            const layerHeight = el.naturalHeight || el.clientHeight || 0;
            // Offset final desde el bottom, en px
            const finalOffsetBottom = (layer.finalOffsetBottomRatio ?? 0) * fondoHeight;

            console.log(layer.id, 'bottomRadio:', layer.finalOffsetBottomRatio, 'finalOffsetBottom:', finalOffsetBottom, 'layerHeight:', layerHeight);
            // Posición final: fondoHeight - layerHeight - finalOffsetBottom
            const top = Math.max(0, fondoHeight - layerHeight - finalOffsetBottom);
            el.style.setProperty('--top', `${Math.round(top)}px`);
        }
    });
}

function waitForImagesToLoad(selectors, callback) {
    const imgs = selectors.map(sel => document.querySelector(sel)).filter(Boolean);
    let loaded = 0;
    const total = imgs.length;
    if (total === 0) {
        callback();
        return;
    }
    imgs.forEach(img => {
        if (img.complete) {
            loaded++;
            if (loaded === total) {
                callback();
            }
        } else {
            img.addEventListener('load', () => {
                loaded++;
                if (loaded === total) {
                    callback();
                }
            }, { once: true });
        }
    });
}

onMounted(() => {
    console.log('Portada Nueva')
    nav.fullPage = true;
    waitForImagesToLoad([
        '#fondo img',
        '#montanyas img',
        '#campo img',
        '#personas img'
    ], setLayerTops);
    window.addEventListener('resize', setLayerTops);
})

onBeforeUnmount(() => {
    console.log('Portada Nueva - Desmontando')
    nav.fullPage=false
    window.removeEventListener('resize', setLayerTops);
})

// --- NUEVO: cálculo generalista de scroll y animación proporcional ---

function getFondoHeight() {
    const fondoImg = document.querySelector('#fondo img');
    if (!fondoImg) return window.innerHeight;
    // Si la imagen ya está cargada, usar naturalHeight; si no, fallback a clientHeight
    return fondoImg.naturalHeight || fondoImg.clientHeight || window.innerHeight;
}

watch(()=>nav.scrollY, (newValue) => {
    const fondoHeight = getFondoHeight();
    const viewportHeight = window.innerHeight;
    const scrollableHeight = Math.max(1, fondoHeight - viewportHeight); // evitar división por cero
    const scrollPercent = Math.min(1, Math.max(0, newValue / scrollableHeight));

    portadaLayers.forEach(layer => {
        const el = document.querySelector('#'+layer.id+' img');
        if (el) {
            const layerHeight = el.naturalHeight || el.clientHeight || 0;
            // Offset inicial y final desde el bottom, en px
            const initialOffsetBottom = (layer.initialOffsetBottomRatio ?? 0) * fondoHeight;
            const finalOffsetBottom = (layer.finalOffsetBottomRatio ?? 0) * fondoHeight;
            // Interpolación de offset
            const offsetBottom = initialOffsetBottom + (finalOffsetBottom - initialOffsetBottom) * scrollPercent;
            // Posición top: fondoHeight - layerHeight - offsetBottom
            const top = Math.max(0, fondoHeight - layerHeight - offsetBottom);
            el.style.setProperty('--top', `${Math.round(top)}px`);
            // Interpolación de escala
            const scaleAdd = layer.initialScaleAdd + (layer.finalScaleAdd - layer.initialScaleAdd) * scrollPercent;
            el.style.setProperty('--scale-add', scaleAdd.toFixed(3));
        }
    });
})

</script>


<style scoped>

img {
    position: absolute;
    --top: 0px;
    --scroll-y: 0px;
    --scale-add: 0;
    top: calc(var(--top) - var(--scroll-y));
    transform: scale(calc(1 + var(--scale-add)));
    will-change: transform, top;
}

/* Eliminados los valores fijos de --top; ahora se asignan por JS. */

</style>
