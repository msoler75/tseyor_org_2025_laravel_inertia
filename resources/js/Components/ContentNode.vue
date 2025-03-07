<template>
    <LazyHydrationWrapper
        v-if="useImage && node.tagName && node.tagName.toLowerCase() === 'img'"
        :when-visible="{ rootMargin: '50px' }"
        @hydrated="onHydrated"
    >
        <component
            :is="tag"
            v-bind="node.attributes"
            :src-width="width"
            :src-height="height"
            :lazy="node.attributes.loading != 'eager'"
        />
    </LazyHydrationWrapper>
    <LazyHydrationWrapper
        v-else-if="node.tagName && node.tagName.toLowerCase() === 'a'"
        :when-visible="{ rootMargin: '50px' }"
        @hydrated="onHydrated"
    >
        <component :is="tag" v-bind="node.attributes">
            <template v-for="element of node.children">
                <ContentNode :node="element" :use-image="useImage" />
            </template>
        </component>
    </LazyHydrationWrapper>
    <LazyHydrationWrapper v-else>
        <component v-if="node.tagName" :is="tag" v-bind="node.attributes">
            <template v-for="element of node.children">
                <ContentNode :node="element" :use-image="useImage" />
            </template>
        </component>
        <template v-else>
            {{ node.textContent }}
        </template>
    </LazyHydrationWrapper>
</template>

<script setup>
// Se hidratan solo los componentes Image y Link

import { LazyHydrationWrapper } from "vue3-lazy-hydration";
import Image from "./Image.vue";
import Link from "./Link.vue";
import { getImageInfo } from "@/Stores/image.js";

const props = defineProps({
    node: Object,
    useImage: { type: Boolean, default: true }, // use Image component ?
});

const myDomain = import.meta.env.VITE_APP_URL;

const tag = computed(() => {
    if (props.node.tagName == "img" && props.useImage) return Image;

    if (props.node.tagName == "Link") return Link;

    if (props.node.tagName == "a") {
        const href = props.node.attributes.href;
        if (!href) return "a";
        if (href.match(/https?:\/\/[^/]+/)?.[0] === myDomain) return Link;
        if (!href.match(/^https?:\/\/[^/]+/))
            // enlace relativo
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
