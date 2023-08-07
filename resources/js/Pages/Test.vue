<template>
   <div class="bg-base-300">
    <p>hola perico</p>
       <Editor v-model="content" @ready="ready" @load="ready" @blur="blur" @focus="focus" @change="change" full-editor format="md"/>
       {{ content }}
       <p>como va todo?</p>
    </div>
</template>


<script setup>
const content = ref("# hola mundo!\nfrase 1")

function ready() {
    console.log('READY')
}

    function blur() {
    console.log('BLUR')
}

function focus() {
    console.log('FOCUS')
}

function change() {
    console.log('CHANGE')
}



onMounted(()=>{
    console.log('content', content.value)
})
</script>
