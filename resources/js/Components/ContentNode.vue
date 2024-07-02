<template>
        <component v-if="node.tagName" :is="tag" v-bind="node.attributes">
            <template v-for="element of node.children">
                <ContentNode :node="element" :use-image="useImage" />
            </template>
        </component>
        <template v-else>
            {{ node.textContent}}
        </template>
</template>


<script setup>
import Image from './Image.vue'
import Referencia from './Referencia.vue'

const props = defineProps({
        node: Object,
        useImage: {type: Boolean, default: true}
    })

const tag = computed(()=>{
    if (props.node.tagName == 'referencia')
        return Referencia

    if (props.node.tagName == 'img' && props.useImage)
        return Image

    if (props.node.tagName == 'Link')
        return Link

    return props.node.tagName
})
</script>
