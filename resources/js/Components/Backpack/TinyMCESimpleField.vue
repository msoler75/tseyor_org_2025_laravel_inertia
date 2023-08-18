<template>
    <div>
        <input type="hidden" :name="name" v-model="contenido" />
        <TinyMCE v-model="contenido" :format="format"/>
        <!-- <div v-html="contenido.replace(/\n/g, '<br>')"></div> -->
    </div>
</template>


<script setup>
const props = defineProps({
    name: String,
    content: { type: String, default: '' },
    format: { type: String, default: 'md' } // 'md', 'html'
})

const contenido = ref(props.content)

</script>

