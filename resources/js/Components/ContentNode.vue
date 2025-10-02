<template>
    <component
        v-if="node.tagName && node.tagName.toLowerCase() === 'img'"
        :is="tag"
        v-bind="node.attributes"
        :src-width="width"
        :src-height="height"
        :lazy="node.attributes.loading != 'eager'"
    />
    <component
        v-else-if="node.tagName && node.tagName.toLowerCase() === 'a'"
        :is="tag"
        v-bind="node.attributes"
    >
        <ContentNode
            v-for="element of node.children"
            :node="element"
            :use-image="useImage"
        />
    </component>
    <component v-else-if="node.tagName && node.tagName.toLowerCase() === 'br'" :is="'br'" />
    <component v-else-if="node.tagName" :is="tag" v-bind="node.attributes">
        <ContentNode
            v-for="element of node.children"
            :node="element"
            :use-image="useImage"
        />
    </component>
    <template v-else>
        {{ node.textContent }}
    </template>
</template>

<script setup>
import Image from "./Image.vue";
import Link from "./Link.vue";
import Referencia from "./Referencia.vue";
import { getImageInfo } from "@/Stores/image.js";
import { useEnlacesCortos } from "@/composables/useEnlacesCortos.js";

const props = defineProps({
    node: Object,
    useImage: { type: Boolean, default: true }, // use Image component ?
});

const myDomain = getMyDomain()
const { esEnlaceCorto } = useEnlacesCortos()

const tag = computed(() => {
    if (props.node.tagName == "img" && props.useImage) return Image;

    if (props.node.tagName == "Link") return Link;

    if (props.node.tagName == "referencia") return Referencia;

    if (props.node.tagName == "a") {
        const href = props.node.attributes.href;
        if (!href) return "a";

        // Los enlaces cortos deben tratarse como externos, no como navegación Inertia
        if (esEnlaceCorto(href)) return "a";

        if (href.match(/https?:\/\/[^/]+/)?.[0] === myDomain) return Link;
        if (!href.match(/^https?:\/\/[^/]+/))
            // ya es un enlace relativo
            return Link;
    }

    return props.node.tagName;
});

// intentamos recuperar las dimensiones de la imagen original, si acaso han sido dadas
const imageInfo = computed(() => {
    if (props.node.tagName !== "img") return null;
    return getImageInfo(props.node.attributes.src);
});

const width = computed(() => {
    return imageInfo.value ? imageInfo.value[0] : null;
});

const height = computed(() => {
    return imageInfo.value ? imageInfo.value[1] : null;
});

function onHydrated() {
    console.log("hidratado!", props.node.tagName);
}
</script>
