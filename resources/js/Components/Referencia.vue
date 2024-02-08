<template>
    <div class="inline text-primary cursor-pointer after:content-['↗'] hover:underline"  ref="referencia"
    @click="buscar">
        <slot></slot>
    </div>
</template>


<script setup>


const props = defineProps({
    r: { type: String, required: false },
    colecciones: {default: null}
})

import {useGlobalSearch} from "@/Stores/globalSearch.js"

const search = useGlobalSearch()



const referencia = ref(null)

function buscar(){
    search.open()
    console.log(referencia.value)
    search.query = props.r? props.r: referencia.value.innerText
    search.lastQuery = null
    search.showSuggestions = false
    search.results = null
    search.restrictToCollections = props.colecciones
}
</script>


