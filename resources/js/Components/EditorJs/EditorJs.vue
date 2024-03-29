<template>
    <div class="editorjs" ref="htmlelement"></div>
</template>
<script setup>
// https://gist.github.com/bettysteger/d7f2b1a52bb1c23a0c24f3a9ff5832d9
import EditorJS from '@editorjs/editorjs';
import EmbedTool from '@editorjs/embed';
import ListTool from '@editorjs/list';
import ImageTool from '@editorjs/image';
// import VideoTool from './editorjs/video.js';
import { onMounted, onUnmounted, ref, watch } from 'vue';
const htmlelement = ref(null);
const props = defineProps(['modelValue', 'placeholder'])
const emit = defineEmits(['update:modelValue'])
let editor;
let updatingModel = false;
// model -> view
function modelToView() {
    if (!props.modelValue) { return; }
    if (typeof props.modelValue === 'string') {
        editor.blocks.renderFromHTML(props.modelValue);
        return;
    }
    editor.render(props.modelValue);
}
// view -> model
function viewToModel(api, event) {
    updatingModel = true;
    editor.save().then((outputData) => {
        console.log(event, 'Saving completed: ', outputData)
        emit('update:modelValue', outputData);
    }).catch((error) => {
        console.log(event, 'Saving failed: ', error)
    }).finally(() => {
        updatingModel = false;
    })
}
onMounted(() => {
    editor = new EditorJS({
        holder: htmlelement.value,
        placeholder: props.placeholder,
        inlineToolbar: ['bold', 'italic', 'link'],
        tools: {
            embed: EmbedTool,
            list: ListTool,
            image: {
                class: ImageTool,
                config: {
                    image: {
                        endpoints: {
                            byFile: 'http://localhost:8008/uploadFile', // Your backend file uploader endpoint
                            byUrl: 'http://localhost:8008/fetchUrl', // Your endpoint that provides uploading by Url
                        },
                        field: 'image',
                        actions: [
                            {
                                name: 'new_button',
                                icon: '<svg>...</svg>',
                                title: 'New Button',
                                toggle: true,
                                action: (name) => {
                                    alert(`${name} button clicked`);
                                }
                            }
                        ]
                    }
                },
            },
            // video: VideoTool,
        },
        minHeight: 'auto',
        data: props.modelValue,
        onReady: modelToView,
        onChange: viewToModel,
    })
})
watch(() => props.modelValue, () => {
    if (!updatingModel) {
        modelToView()
    }
})
onUnmounted(() => {
    editor.destroy()
})
</script>
