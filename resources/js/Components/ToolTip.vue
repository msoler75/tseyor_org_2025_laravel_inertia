<template>
    <span
    class="cursor-pointer"
    :class="[isOpen?'text-secondary':'text-primary']"
    ref="domReference"
    @pointerenter="onRefPointerEnter"
    @pointerleave="onRefPointerLeave"
    @pointerdown="onRefPointerDown"
    @touchend="onRefTouchEnd"
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
        class="z-20 rounded-xl shadow"
    >
        <slot name="content"></slot>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount, computed } from 'vue';
import { useFloating, autoPlacement, autoUpdate } from '@floating-ui/vue';
import { offset, shift, size } from '@floating-ui/core';

const emit = defineEmits(['activated', 'deactivated', 'preload']);
// permitir mantener el tooltip abierto desde fuera
const props = defineProps({
    persistent: { type: Boolean, default: false },
    activationDelay: { type: Number, default: 500 }, // ms para activar el tooltip
});

const domReference = ref(null);

const floating = ref(null);
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

let touchStartY = null;
let touchStartX = null;
const TAP_THRESHOLD = 10; // px
let hideTimer = null;
let showTimer = null; // Timer para delay de activación
const HIDE_DELAY_MS = 120; // retraso al cerrar para permitir clicks dentro del tooltip y evitar flicker

// usar middleware para evitar overflow del viewport y ajustar tamaño disponible
const { floatingStyles, middlewareData, placement, update } = useFloating(domReference, floating, {
    whileElementsMounted: (reference, floating, update) => {
        return autoUpdate(reference, floating, update, {
            // Configuraciones más conservadoras para evitar recálculos innecesarios
            ancestorScroll: true,
            ancestorResize: true,
            elementResize: true,
            layoutShift: false, // Desactivar para evitar recálculos por cambios menores de layout
            animationFrame: false // Solo usar animationFrame cuando sea estrictamente necesario
        });
    },
    middleware: [
        autoPlacement({
            // Mantener el placement preferido cuando sea posible
            allowedPlacements: ['top', 'bottom', 'left', 'right', 'top-start', 'top-end', 'bottom-start', 'bottom-end'],
        }),
        offset(6),
        // asegurar un margen superior mínimo (p. ej. barra nav con mayor z-index)
        shift({
            padding: { top: 82 }
        }),
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
    ],
});


function show() {
    // solo emitir cuando pasamos de cerrado a abierto
    if (!isOpen.value) {
        clearAllTimers();
        isOpen.value = true;
        // esperar al DOM y calcular posición inicial una sola vez
        nextTick(() => {
            // Solo actualizar la posición si es realmente necesario
            if (update) {
                update();
            }
            // empezar a observar cambios en el contenido del tooltip
            startObserving();
            // añadir listener global para cerrar al tocar fuera
            addOutsideListener();
        });
        emit('activated', { text: domReference.value?.innerText });
    } else {
        // Si ya está abierto, solo limpiar el timer de cierre sin recalcular posición
        clearAllTimers();
    }
}

// Mostrar tooltip con delay
function scheduleShow() {
    if (isOpen.value) return; // Ya está abierto

    clearShowTimer();
    clearHideTimer();

    // Programar mostrar tooltip con delay
    showTimer = setTimeout(() => {
        show();
        showTimer = null;
    }, props.activationDelay);
}

// Precargar datos inmediatamente (sin delay)
function triggerPreload() {
    // Emitir preload inmediatamente al pasar el mouse por encima
    emit('preload', { text: domReference.value?.innerText });
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
    clearShowTimer(); // Cancelar cualquier show pendiente
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
    clearAllTimers();
    isOpen.value = false;
    stopObserving();
    removeOutsideListener();
}

function clearHideTimer() {
    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
}

function clearShowTimer() {
    if (showTimer) { clearTimeout(showTimer); showTimer = null; }
}

function clearAllTimers() {
    clearHideTimer();
    clearShowTimer();
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

    // Precargar datos inmediatamente al pasar el mouse por encima
    triggerPreload();

    // Programar mostrar tooltip con delay
    scheduleShow();
}function onRefPointerLeave(e) {
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
    if (lastPointerType === 'touch') {
        // Guardar posición inicial del touch usando pointerdown
        if (e && e.clientX !== undefined && e.clientY !== undefined) {
            touchStartY = e.clientY;
            touchStartX = e.clientX;
        } else {
            touchStartY = null;
            touchStartX = null;
        }
        // Esperar a touchend para decidir si es tap
        return;
    }
    // para punteros no touch, precargar inmediatamente y programar show con delay
    isHoveringReference = true;
    triggerPreload();
    scheduleShow();
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

// Nuevo: manejar touchend para decidir si es tap
function onRefTouchEnd(e) {
    if (touchStartY === null || touchStartX === null) return;

    let endY, endX;
    // Obtener coordenadas del touchend
    if (e.changedTouches && e.changedTouches.length === 1) {
        endY = e.changedTouches[0].clientY;
        endX = e.changedTouches[0].clientX;
    } else if (e.clientX !== undefined && e.clientY !== undefined) {
        endY = e.clientY;
        endX = e.clientX;
    } else {
        // No podemos obtener coordenadas, resetear y salir
        touchStartY = null;
        touchStartX = null;
        return;
    }

    const distY = Math.abs(endY - touchStartY);
    const distX = Math.abs(endX - touchStartX);

    if (distY < TAP_THRESHOLD && distX < TAP_THRESHOLD) {
        // Es un tap, toggle tooltip
        if (isOpen.value) {
            immediateHide();
        } else {
            show();
        }
        // detener propagación para evitar que un click posterior cierre el tooltip
        e && e.stopPropagation && e.stopPropagation();
    }
    // Si fue scroll, no hacer nada
    touchStartY = null;
    touchStartX = null;
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
    clearAllTimers(); // Limpiar todos los timers
    // remover listeners añadidos al elemento flotante si existe
    const el = floating.value;
    if (el) {
        el.removeEventListener('pointerenter', onFloatingPointerEnter);
        el.removeEventListener('pointerdown', clearHideTimer);
        el.removeEventListener('pointerleave', onFloatingPointerLeave);
    }
});
</script>

