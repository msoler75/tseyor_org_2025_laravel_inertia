<template>
    <ToolTip
        ref="tt"
        @preload="onToolTipActivate"
        @deactivated="onToolTipDeactivate"
        :activationDelay="200"
    >
        <template #content>
            <div v-if="!info || noEncontrado" class="bg-base-300 p-2 rounded-xl shadow">
                <div v-if="!info" class="p-3 text-lg mb-2">
                    <Spinner class="loader" />
                </div>
                <template v-else-if="noEncontrado">
                    <button class="btn btn-xs btn-primary" @click="buscar">
                        Saber más
                    </button>
                </template>
            </div>
            <div v-else class="bg-base-300 p-3 rounded shadow">
                <h3>{{ info.title }}</h3>
                <div>
                    <Content :content="info" class="prose max-w-none" />
                </div>
                <div class="flex justify-between items-center mt-2">
                    <Link
                        :href="verGlosario"
                        class="btn btn-xs font-bold btn-secondary no-underline"
                        v-if="verGlosario"
                    >
                        Ver en Glosario
                    </Link>
                    <button class="btn btn-xs btn-primary" @click="buscar">
                        Saber más
                    </button>
                </div>
            </div>
        </template>
        <slot></slot>
    </ToolTip>
</template>

<script setup>
const props = defineProps({
    r: { type: String, required: false },
    colecciones: { default: null },
    maxLength: { default: 280 }, // Límite de caracteres para el tooltip
});

import useGlobalSearch from "@/Stores/globalSearch.js";

const viendoToolTip = ref(false);
const tt = ref(null);
const search = useGlobalSearch(); // Solo para la función buscar()

const noEncontrado = ref(false);
const info = ref(null);
const verGlosario = ref(false);

// Cuando el ToolTip notifica activación le pasamos el texto del trigger o usamos props.r
function onToolTipActivate(payload) {
    viendoToolTip.value = true;
    console.log("Tooltip activated", payload);

    // Obtener el texto de búsqueda: props.r > payload.text > slot text
    const triggerText = props.r || payload.text || getSlotText();

    console.log("triggerText:", triggerText);

    if (!triggerText) {
        console.warn("No se pudo obtener texto para el tooltip");
        return;
    }

    if (info.value) return;

    // Nueva implementación: una sola llamada que busca y devuelve el texto del término
    axios.get(route('buscar.termino'), {
        params: {
            q: triggerText,
            limite: props.maxLength
        }
    }).then((res) => {
        console.log("search response:", res.data);

        if (!res.data.encontrado) {
            noEncontrado.value = true;
            info.value = " ";
            return;
        }

        const termino = res.data;
        const tituloLimpio = termino.titulo.replace(/<[^>]+>/g, '');

        info.value = `<div class='font-bold text-lg mb-2'>${tituloLimpio}</div>${termino.texto}`;

        // Mostrar siempre el enlace "Ver en Glosario"
        verGlosario.value = termino.url_glosario;

    }).catch((error) => {
        console.error("Error en búsqueda de término:", error);
        noEncontrado.value = true;
        info.value = " ";
    });
}

function onToolTipDeactivate() {
    viendoToolTip.value = false;
}

const slots = useSlots();

function buscar() {
    // Obtener el texto de búsqueda desde props.r o del slot
    const triggerText = props.r || getSlotText();

    if (!triggerText) {
        console.warn("No se pudo obtener texto para buscar");
        return;
    }

    search.reset();
    search.configure({
        query: triggerText,
        includeDescription: false,
        restrictToCollections: props.colecciones,
        autoFocus: false,
        opened: true
    });
}

// Función auxiliar simple para obtener texto del slot
function getSlotText() {
    if (!slots.default) return '';

    const slotContent = slots.default();
    if (!slotContent || !slotContent.length) return '';

    // Obtener el texto del primer nodo
    const firstNode = slotContent[0];
    return typeof firstNode.children === 'string' ? firstNode.children.trim() : '';
}
</script>
