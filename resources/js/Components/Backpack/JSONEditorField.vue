<template>
    <input type="hidden" :name="name" v-model="localValue">
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

const decode = (str) => JSON.parse(str)

const localValue = ref(decode(props.value))

console.log({localValue})
</script>
