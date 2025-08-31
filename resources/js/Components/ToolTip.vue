<template>
    <span
    class="cursor-pointer"
    :class="[isOpen?'text-secondary':'text-primary']"
    ref="domReference"
    @pointerenter="onRefPointerEnter"
    @pointerleave="onRefPointerLeave"
    @pointerdown="onRefPointerDown"
    @focus="show"
    @blur="onRefBlur"
    >
        <slot></slot>
</span>

    <div
        ref="floating"
        v-show="isOpen"
        :style="floatingStyles"
        role="tooltip"
        class="transition duration-120 z-20"
    >
           <slot name="content"></slot>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount, computed } from 'vue';
import { useFloating, autoPlacement, autoUpdate } from '@floating-ui/vue';
import { offset, shift, size, arrow as arrowMiddleware } from '@floating-ui/core';

const emit = defineEmits(['activated', 'deactivated']);
// permitir mantener el tooltip abierto desde fuera
const props = defineProps({
    persistent: { type: Boolean, default: false },
});

const domReference = ref(null);

const floating = ref(null);
const arrow = ref(null);
const isOpen = ref(false);

// listener global para cerrar al tocar fuera (se añade en capture para ignorar stopPropagation)
function onPointerDownOutside(e) {
    try {
    // si el componente es persistente, ignorar clicks fuera
    if (props.persistent) return;
        const refEl = domReference.value;
        const floatEl = floating.value;
        const target = e && e.composedPath ? e.composedPath()[0] : e.target;
        // si el click/touch está dentro de la referencia o del floating, no cerrar
        if (refEl && (refEl === target || refEl.contains(target))) return;
        if (floatEl && (floatEl === target || floatEl.contains(target))) return;
        // fuera: cerrar inmediatamente
        immediateHide();
    } catch (err) {
        // noop
    }
}

function addOutsideListener() {
    // usar capture para que se ejecute antes de handlers que hagan stopPropagation
    window.addEventListener('pointerdown', onPointerDownOutside, true);
    // fallback para entornos sin pointer events
    window.addEventListener('touchstart', onPointerDownOutside, true);
}

function removeOutsideListener() {
    window.removeEventListener('pointerdown', onPointerDownOutside, true);
    window.removeEventListener('touchstart', onPointerDownOutside, true);
}

// flags para evitar toggles cuando el tooltip y la referencia se solapan
let isHoveringReference = false;
let isHoveringFloating = false;

let hideTimer = null;
const HIDE_DELAY_MS = 120; // retraso al cerrar para permitir clicks dentro del tooltip y evitar flicker

// usar middleware para evitar overflow del viewport y ajustar tamaño disponible
const { floatingStyles, middlewareData, placement, update } = useFloating(domReference, floating, {
      whileElementsMounted: autoUpdate,
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
        //arrowMiddleware({ element: arrow}),
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
            // asegurar que el elemento arrow esté presente para el middleware
            nextTick(() => { if (update) update(); });
            // debug: mostrar datos del middleware y estilos del floating para diagnóstico
            try {
                console.debug('floatingStyles', floatingStyles);
                console.debug('middlewareData', middlewareData && middlewareData.value);
                console.debug('placement', placement && placement.value);
            } catch (e) {
                // noop
            }
            // empezar a observar cambios en el contenido del tooltip
            startObserving();
            // añadir listener global para cerrar al tocar fuera
            addOutsideListener();
        });
        emit('activated', { text: domReference.value?.innerText });
    }
}

// exponer API pública para controlar el tooltip desde el padre
function hide(force = false) {
    immediateHide(force);
}

defineExpose({ show, hide });


function scheduleHideIfNeeded() {
    // si es persistente, no programar cierre
    if (props.persistent) return;
    // programa el cierre solo si el puntero no está ni sobre la referencia ni sobre el floating
    clearHideTimer();
    hideTimer = setTimeout(() => {
        if (!isHoveringReference && !isHoveringFloating) {
            isOpen.value = false;
            stopObserving();
            emit('deactivated', { text: domReference.value?.innerText });
        }
        hideTimer = null;
    }, HIDE_DELAY_MS);
}

function immediateHide(force = false) {
    // si es persistente y no se fuerza, ignorar
    if (props.persistent && !force) return;
    clearHideTimer();
    isOpen.value = false;
    stopObserving();
    removeOutsideListener();
}

function clearHideTimer() {
    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
}

// handlers para eventos pointer — se usan en la plantilla (referencia) y en el elemento floating
// mantener tipo de puntero reciente para distinguir touch vs mouse
let lastPointerType = null;

function onRefPointerEnter(e) {
    // cuando viene de touch, ignoramos enter/leave para evitar toggles rápidos en móvil
    lastPointerType = e && e.pointerType ? e.pointerType : lastPointerType;
    if (lastPointerType === 'touch') return;
    isHoveringReference = true;
    clearHideTimer();
    show();
}

function onRefPointerLeave(e) {
    lastPointerType = e && e.pointerType ? e.pointerType : lastPointerType;
    // si es touch, no programar cierre aquí; el touch usa pointerdown para toggle
    if (lastPointerType === 'touch') {
        isHoveringReference = false;
        return;
    }
    isHoveringReference = false;
    scheduleHideIfNeeded();
}

function onRefPointerDown(e) {
    lastPointerType = e && e.pointerType ? e.pointerType : lastPointerType;
    clearHideTimer();
    // en dispositivos táctiles, queremos que el toque haga toggle (abrir/cerrar)
    if (lastPointerType === 'touch') {
        if (isOpen.value) {
            immediateHide();
        } else {
            show();
        }
        // detener propagación para evitar que un click posterior cierre el tooltip
        // (no usar preventDefault para no romper accesibilidad)
        e && e.stopPropagation && e.stopPropagation();
        return;
    }
    // para punteros no touch, comportarse igual que pointerenter
    isHoveringReference = true;
    show();
}

function onRefBlur() {
    // no cerrar inmediatamente en blur; usar la misma lógica con retraso
    isHoveringReference = false;
    scheduleHideIfNeeded();
}

function onFloatingPointerEnter() {
    isHoveringFloating = true;
    clearHideTimer();
}

function onFloatingPointerLeave() {
    isHoveringFloating = false;
    scheduleHideIfNeeded();
}

// observar cambios en el contenido del slot 'content' y recalcular posición
let mutationObserver = null;
let mutationTimer = null;
const DEBOUNCE_MS = 10;

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
    el.addEventListener('pointerenter', onFloatingPointerEnter);
    el.addEventListener('pointerdown', clearHideTimer);
    el.addEventListener('pointerleave', onFloatingPointerLeave);
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
        el.removeEventListener('pointerenter', onFloatingPointerEnter);
        el.removeEventListener('pointerdown', clearHideTimer);
        el.removeEventListener('pointerleave', onFloatingPointerLeave);
    }
});
</script>

