<template>
    <div>
        <input type="hidden" id="xxx" :name="name" v-model="contenido" />
        <Editor v-model="contenido" full-editor :format="format"/>
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

