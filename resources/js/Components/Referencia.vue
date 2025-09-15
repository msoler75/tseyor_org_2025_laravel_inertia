<template>
    <ToolTip
        ref="tt"
        @activated="onToolTipActivate"
        @deactivated="onToolTipDeactivate"
    >
        <template #content>
            <div v-if="!info || noEncontrado" class="bg-base-300 p-3">
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
        <template #arrow>
            <div v-if="!info" class="p-3 text-lg mb-2 w-[320px] h-[240px]">
                <Spinner class="loader" />
            </div>
            <span v-else>XX</span>
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
import truncateText from "@/composables/textutils.js";

const viendoToolTip = ref(false);
const tt = ref(null);
const search = useGlobalSearch();

const noEncontrado = ref(false);
const info = ref(null);
const verGlosario = ref(false);

// Cuando el ToolTip notifica activación le pasamos el texto del trigger o usamos props.r
function onToolTipActivate(payload) {
    viendoToolTip.value = true;
    console.log("Tooltip activated", payload);
    const triggerText = props.r ? props.r : payload.text;
    console.log("triggerText:", triggerText);
    if (info.value) return;

    search.query = triggerText;
    search.lastQuery = null;
    search.showSuggestions = false;
    search.results = null;
    search.restrictToCollections = "terminos";
    search.includeDescription = false;
    search.call().then(() => {
        console.log("search results:", search.results);
        if (!search.results.length) {
            noEncontrado.value = true;
            info.value = " ";
            return;
        }
        const t = search.results.sort(
            (a, b) => b.__tntSearchScore__ - a.__tntSearchScore__
        )[0];

        console.log("search results:", search.results);
        console.log("ok term", t);
        if (!t) return;
        axios.get("/glosario/" + t.slug_ref + "?json").then((res) => {
            const fullText = res.data.texto || "";
            info.value = `<div class='font-bold text-lg mb-2'>${t.titulo.replace(/<[^>]+>/g, '')}</div>` + truncateText(fullText, props.maxLength);
            verGlosario.value =
                info.value.length < fullText.length
                    ? "/glosario/" + t.slug_ref
                    : null;
        });
    });
}

function onToolTipDeactivate() {
    viendoToolTip.value = false;
}

function buscar() {
    const currentQuery = search.query
    search.reset()
    search.query = currentQuery
    search.includeDescription = false;
    search.restrictToCollections = props.colecciones;
    search.open();
    search.call();
}
</script>
