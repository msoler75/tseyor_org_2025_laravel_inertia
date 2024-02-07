<template>
    <div class="inline text-primary cursor-pointer after:content-['↗']"  ref="referencia"
    @click="buscar">
        <slot></slot>
    </div>
</template>


<script setup>

const props = defineProps({
    r: { type: String, required: false }
})

import {useGlobalSearch} from "@/Stores/globalSearch.js"

const search = useGlobalSearch()

const referencia = ref(null)

function buscar(){
    search.open()
    console.log(referencia.value)
    search.query = props.r? props.r: referencia.value.innerText
}
</script>
