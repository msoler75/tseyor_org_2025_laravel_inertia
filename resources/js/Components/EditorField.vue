<template>
    <div>
        <div v-html="contenidoMD.replace(/\n/g, '<br>')"></div>
        <input type="hidden" :name="name" v-model="contenidoMD" />
        <Editor v-model="content" full-editor/>
    </div>
</template>


<script setup>
const props = defineProps({
    name: String,
    content: { type: String, default: 'Pon tu texto aquí...' }
})

const contenidoMD = ref(props.content)

</script>
