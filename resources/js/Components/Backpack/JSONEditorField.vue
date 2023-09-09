<template>
    <input type="hidden" :name="name" :value="localValueString">
    <JsonEditorVue
    mode="text"
    v-model="localValue"
    v-bind="{/* local props & attrs */}"
    :main-menu-bar="false"
      :navigation-bar="false"
      :status-bar="false"
  />
</template>

<script setup>
import JsonEditorVue from 'json-editor-vue'
import 'vanilla-jsoneditor/themes/jse-theme-dark.css'
// npm i json-editor-vue vanilla-jsoneditor

const props = defineProps({
    name: String,
    value: String
})

const decode = (str) => {
    console.log('decode', str)
    if(!str) return '{}'
    try {
        return JSON.parse(str)
    }
    catch(err){
        return null
    }
}

const localValue = ref(decode(props.value))

const localValueString = computed(()=>JSON.stringify(localValue.value))

console.log({localValue})
</script>
