<template>
    <div>
        <input type="hidden" :name="name" v-model="contenido" />
        <ClientOnly>
            <TipTapEditor :full="true" v-model="contenido" full-editor :format="format" :mediaFolder="folder"/>
        </ClientOnly>
        <!-- <div v-html="contenido.replace(/\n/g, '<br>')"></div> -->
    </div>
</template>


<script setup>

const props = defineProps({
    name: String,
    content: { type: String, default: '' },
    format: { type: String, default: 'detect' }, // 'md', 'html'
    folder: String
})

const contenido = ref(props.content)

</script>

