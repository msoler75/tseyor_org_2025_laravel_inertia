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

    <!-- Contenedor de posicionamiento floating-ui - siempre presente cuando isOpen -->
    <div
        v-if="isOpen"
        ref="floating"
        :style="floatingStyles"
        role="tooltip"
        class="z-20"
    >
        <!-- Transición aplicada al contenido cuando está listo -->
        <TransitionAppear
            name="tooltip"
            :duration="120"
            :origin="transformOrigin"
        >
            <div v-if="internalVisibleFloating" class="tooltip-content rounded-xl overflow-visible" :class="floatingClass">
                <slot name="content"></slot>
                <div ref="arrowRef" :style="arrowStyles" class="absolute rotate-45 z-10"
                :class="arrowClass"></div>
            </div>
        </TransitionAppear>
    </div>
</template>

<script setup>
import { useFloating, autoPlacement, autoUpdate, arrow } from '@floating-ui/vue';
import { offset, shift, size } from '@floating-ui/core';

const emit = defineEmits(['activated', 'deactivated', 'preload']);
// permitir mantener el tooltip abierto desde fuera
const props = defineProps({
    persistent: { type: Boolean, default: false },
    activationDelay: { type: Number, default: 150 }, // ms para activar el tooltip
    floatingClass: { type: String, default: '' },
    arrowClass: { type: String, default: 'w-3 h-3' },
    visibleFloating: { type: Boolean, default: true }
});

const domReference = ref(null);
const floating = ref(null);
const arrowRef = ref(null);
const isOpen = ref(false);

// Estado interno para controlar la visibilidad del contenido flotante
// Permite que la animación se ejecute incluso si visibleFloating cambia inmediatamente
const internalVisibleFloating = ref(false);


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
        offset(12),
        // asegurar un margen superior mínimo (p. ej. barra nav con mayor z-index)
        shift({
            padding: { top: 84 }
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
        arrow({
            element: arrowRef,
            padding: 5, // evitar que la flecha toque los bordes redondeados
        }),
    ],
});

// Calcular estilos del arrow dinámicamente
const arrowStyles = computed(() => {
    if (!middlewareData.value?.arrow) {
        return { display: 'none' };
    }

    const { x, y } = middlewareData.value.arrow;
    const currentPlacement = placement.value;

    // Determinar el lado estático basado en el placement
    const staticSide = {
        top: 'bottom',
        right: 'left',
        bottom: 'top',
        left: 'right',
    }[currentPlacement.split('-')[0]];

    const styles = {
        left: x != null ? `${x}px` : '',
        top: y != null ? `${y}px` : '',
        right: '',
        bottom: '',
        [staticSide]: '-6px', // posicionar 6px fuera del tooltip para compensar el tamaño de 12px
    };

    return styles;
});

const transformOrigin = computed(() => {
    const arrowPos = arrowStyles.value;
    const floatEl = floating.value;

    if (!floatEl || !middlewareData.value?.arrow) {
        return 'center center';
    }

    console.log('Arrow Styles:', arrowPos); // Debug

    const rect = floatEl.getBoundingClientRect();

    // Helper para calcular coordenada basada en posición del arrow
    const getCoordinate = (positive, negative, dimension) => {
        if (positive && positive !== '') {
            return `${parseFloat(positive) + 6}px`;
        }
        if (negative && negative !== '') {
            const offset = parseFloat(negative.replace('px', ''));
            return `${dimension + (offset < 0 ? offset + 6 : -offset - 6)}px`;
        }
        return 'center';
    };

    const originX = getCoordinate(arrowPos.left, arrowPos.right, rect.width);
    const originY = getCoordinate(arrowPos.top, arrowPos.bottom, rect.height);

    const transformOrigin = `${originX} ${originY}`;
    console.log('Computed transformOrigin:', transformOrigin); // Debug
    return transformOrigin;
});

// Watcher para manejar cambios en visibleFloating con nextTick
// Esto garantiza que la animación se ejecute incluso si el contenido cambia inmediatamente
watch(() => props.visibleFloating, (newValue) => {
    if (newValue && isOpen.value) {
        // Si debe mostrarse y el tooltip está abierto, usar nextTick para permitir la animación
        nextTick(() => {
            internalVisibleFloating.value = true;
            // Reconfigurar listeners cuando el contenido se hace visible
            setupFloatingListeners();
        });
    } else {
        // Si debe ocultarse, hacerlo inmediatamente
        internalVisibleFloating.value = false;
    }
});

function show() {
    // solo emitir cuando pasamos de cerrado a abierto
    if (!isOpen.value) {
        clearAllTimers();
        isOpen.value = true;
        // Agregar listener para cerrar al tocar fuera
        nextTick(() => {
            window.addEventListener('pointerdown', onPointerDownOutside, true);
            window.addEventListener('touchstart', onPointerDownOutside, true);
        });
        emit('activated', { text: domReference.value?.innerText });

        // Configurar listeners del elemento flotante
        setupFloatingListeners();

        // Si visibleFloating ya es true al abrir, activar el contenido interno con nextTick
        if (props.visibleFloating) {
            nextTick(() => {
                internalVisibleFloating.value = true;
            });
        }
    } else {
        // Si ya está abierto, solo limpiar el timer de cierre
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
    // Resetear el estado interno del contenido flotante
    internalVisibleFloating.value = false;
    // Remover listeners del elemento flotante
    removeFloatingListeners();
    // Remover listener de click fuera
    removeOutsideListener();
    emit('deactivated', { text: domReference.value?.innerText });
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

// Configurar listeners del elemento flotante
function setupFloatingListeners() {
    nextTick(() => {
        const el = floating.value;
        if (!el) return;

        // Remover listeners existentes para evitar duplicados
        el.removeEventListener('pointerenter', onFloatingPointerEnter);
        el.removeEventListener('pointerdown', clearHideTimer);
        el.removeEventListener('pointerleave', onFloatingPointerLeave);

        // Añadir listeners
        el.addEventListener('pointerenter', onFloatingPointerEnter);
        el.addEventListener('pointerdown', clearHideTimer);
        el.addEventListener('pointerleave', onFloatingPointerLeave);
    });
}

// Remover listeners del elemento flotante
function removeFloatingListeners() {
    const el = floating.value;
    if (el) {
        el.removeEventListener('pointerenter', onFloatingPointerEnter);
        el.removeEventListener('pointerdown', clearHideTimer);
        el.removeEventListener('pointerleave', onFloatingPointerLeave);
    }
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
            // Antes de mostrar el tooltip, emitir preload para cargar datos
            triggerPreload();
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
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', safeUpdate);
    window.removeEventListener('scroll', safeUpdate, true);
    stopObserving();
    clearAllTimers();
    removeOutsideListener();
    removeFloatingListeners();
});
</script>

<style scoped>
/* Hacer las transiciones más evidentes para debug */
.tooltip-enter-active {
    transition: all 0.4s ease-out !important;
}

.tooltip-leave-active {
    transition: all 0.3s ease-in !important;
}

/* Estados más dramáticos para que se vean */
.tooltip-enter-from {
    opacity: 0 !important;
    transform: scale(0.5) translateY(20px) !important;
}

.tooltip-enter-to {
    opacity: 1 !important;
    transform: scale(1) translateY(0px) !important;
}

.tooltip-leave-from {
    opacity: 1 !important;
    transform: scale(1) translateY(0px) !important;
}

.tooltip-leave-to {
    opacity: 0 !important;
    transform: scale(0.7) translateY(-15px) !important;
}

/* Asegurar que las transformaciones se apliquen */
.tooltip-content {
    transform-origin: center center;
    will-change: transform, opacity;
}

/* Asegurar que el contenido del tooltip sea visible durante transiciones */
.tooltip-content {
    transform-origin: inherit;
    backface-visibility: hidden;
    will-change: transform, opacity, filter;
}

/* Mejorar el rendimiento y visibilidad durante transiciones */
.tooltip-enter-active .tooltip-content,
.tooltip-leave-active .tooltip-content {
    isolation: isolate;
}

/* Añadir una sombra sutil que también se anime */
.tooltip-enter-from .tooltip-content {
    box-shadow: 0 0 0 rgba(0, 0, 0, 0);
}

.tooltip-enter-to .tooltip-content {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.tooltip-leave-from .tooltip-content {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.tooltip-leave-to .tooltip-content {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
</style>
