<template>
    <span
    class="cursor-pointer"
    :class="[isOpen?'text-secondary':'text-primary']"
        ref="domReference"
        @mouseenter="show"
        @mouseleave="hide"
        @focus="show"
        @blur="hide"
    >
        <slot></slot>
</span>

    <div
        ref="floating"
        v-show="isOpen"
        :style="floatingStyles"
        role="tooltip"
        class="tooltip bg-base-300"
    >
        <div ref="arrow" class="tooltip-arrow" data-popper-arrow></div>
        <slot name="content">Tooltip</slot>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount, watch } from 'vue';
import { useFloating, autoPlacement } from '@floating-ui/vue';
import { offset, shift, size, arrow as arrowMiddleware } from '@floating-ui/core';

const emit = defineEmits(['activated', 'deactivated']);

const domReference = ref(null);
// virtual reference that returns the last client rect when the element is wrapped
// across multiple lines. Floating UI will consume this object's
// getBoundingClientRect() to place the floating element near the last
// fragment (last word) instead of between fragments.
const virtualReference = ref({
    getBoundingClientRect() {
        const el = domReference.value;
        if (!el) return { top: 0, left: 0, right: 0, bottom: 0, width: 0, height: 0, x: 0, y: 0, toJSON() { } };
        try {
            const rects = el.getClientRects ? el.getClientRects() : null;
            const rect = rects && rects.length ? rects[rects.length - 1] : el.getBoundingClientRect();
            return rect;
        } catch (e) {
            return el.getBoundingClientRect();
        }
    },
    // contextElement can help Floating UI with event handling / offsets
    contextElement: domReference,
});
const floating = ref(null);
const arrow = ref(null);
const isOpen = ref(false);

let hideTimer = null;
const HIDE_DELAY_MS = 50; // retraso al cerrar para permitir clicks dentro del tooltip

// usar middleware para evitar overflow del viewport y ajustar tamaño disponible
const { floatingStyles, middlewareData, placement, update } = useFloating(virtualReference, floating, {
    middleware: [
    autoPlacement(),
    offset(6),
    // asegurar un margen superior mínimo (p. ej. barra nav con mayor z-index)
    shift({ padding: { top: 72 } }),
        size({
            apply({ availableWidth, elements }) {
                // limitar ancho del tooltip al espacio disponible (alto lo maneja CSS/flujo)
                const el = elements.floating;
                const mw = Math.min(availableWidth, 400);
                if (el && el.style) {
                    el.style.maxWidth = `${Math.floor(mw)}px`;
                }
            },
        }),
        // arrow middleware — colocará la flecha correctamente
        // arrowMiddleware({ element: arrow }),
    ],
});


function show() {
    // solo emitir cuando pasamos de cerrado a abierto
    if (!isOpen.value) {
        clearHideTimer();
        isOpen.value = true;
        // esperar al DOM y forzar recálculo de posición
        nextTick(() => {
            if (update) update();
            // empezar a observar cambios en el contenido del tooltip
            startObserving();
        });
        emit('activated', { text: domReference.value?.innerText });
    }
}

function hide() {
    // iniciar temporizador para cerrar (permite clicks dentro del tooltip)
    clearHideTimer();
    hideTimer = setTimeout(() => {
        isOpen.value = false;
        stopObserving();
        hideTimer = null;
        emit('deactivated', { text: domReference.value?.innerText });
    }, HIDE_DELAY_MS);
}

function immediateHide() {
    clearHideTimer();
    isOpen.value = false;
    stopObserving();
}

function clearHideTimer() {
    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
}

// observar cambios en el contenido del slot 'content' y recalcular posición
let mutationObserver = null;
let mutationTimer = null;
const DEBOUNCE_MS = 50;

function startObserving() {
    // si no existe MutationObserver en el entorno, no hacemos nada
    if (typeof MutationObserver === 'undefined') return;
    stopObserving();
    const observeTarget = () => {
        const el = floating.value;
        if (!el) return;
        mutationObserver = new MutationObserver(() => {
            if (mutationTimer) clearTimeout(mutationTimer);
            mutationTimer = setTimeout(() => {
                safeUpdate();
            }, DEBOUNCE_MS);
        });
        mutationObserver.observe(el, { childList: true, subtree: true, characterData: true });
    };
    nextTick(observeTarget);
}

function stopObserving() {
    if (mutationTimer) { clearTimeout(mutationTimer); mutationTimer = null; }
    if (mutationObserver) { mutationObserver.disconnect(); mutationObserver = null; }
}

// recalcular cuando cambie el viewport (resize / scroll)
function safeUpdate() {
    try {
        if (update) update();
    } catch (e) {
        // noop
    }
}

onMounted(() => {
    window.addEventListener('resize', safeUpdate);
    window.addEventListener('scroll', safeUpdate, true);
    // listeners para el elemento flotante: cancelar cierre cuando el puntero está dentro
    nextTick(() => {
        const el = floating.value;
        if (!el) return;
        el.addEventListener('pointerenter', clearHideTimer);
        el.addEventListener('pointerdown', clearHideTimer);
        el.addEventListener('pointerleave', () => {
            // iniciar el temporizador de cierre cuando el puntero salga
            hide();
        });
    });
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', safeUpdate);
    window.removeEventListener('scroll', safeUpdate, true);
    stopObserving();
    clearHideTimer();
    // remover listeners añadidos al elemento flotante si existe
    const el = floating.value;
    if (el) {
        el.removeEventListener('pointerenter', clearHideTimer);
        el.removeEventListener('pointerdown', clearHideTimer);
        el.removeEventListener('pointerleave', hide);
    }
});
</script>

<style scoped>
/* asegurar que no aparezca scrollbar en el tooltip */
.tooltip {
    overflow: visible !important;
    max-height: none !important;
}

/* estilo de la flecha */
.tooltip-arrow {
    position: absolute;
    width: 10px;
    height: 10px;
    background: inherit; /* toma el mismo fondo que el tooltip */
    transform: rotate(45deg);
    z-index: -1; /* para que quede detrás del contenido */
}

/* desplazar la flecha fuera del contenedor según la orientación */
.tooltip[data-popper-placement^="top"] .tooltip-arrow {
    bottom: -5px;
}
.tooltip[data-popper-placement^="bottom"] .tooltip-arrow {
    top: -5px;
}
.tooltip[data-popper-placement^="left"] .tooltip-arrow {
    right: -5px;
}
.tooltip[data-popper-placement^="right"] .tooltip-arrow {
    left: -5px;
}
</style>
