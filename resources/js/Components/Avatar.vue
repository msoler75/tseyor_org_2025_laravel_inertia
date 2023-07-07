<template>
    <div class="avatar">
        <Link v-if="link" href="/xxxxxxxxxxxxxxxxxxxx" class="w-12">
        <img :src="image" :alt="name" :title="name"/>
        </Link>
        <img v-else :src="image" :alt="name" :title="name"/>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true }
})

const name = computed(()=>props.user.name||props.user.nombre)
const image = computed(()=>props.user.avatar||props.user.profile_ptoho_path||props.user.imagen)
</script>
