<template>
    <div class="avatar">
        <div class="w-32 rounded-full">
            <Link v-if="link" :href="route('usuario', { id: user.slug || user.id })">
            <img :src="urlImage" :alt="name" :title="name" />
            </Link>
            <img v-else :src="urlImage" :alt="name" :title="name" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true }
})

const name = computed(() => props.user.name || props.user.nombre)
const image = computed(() => props.user.avatar || props.user.profile_ptoho_path || props.user.imagen)
const urlImage = computed(() => {
    if (!image.value) return '/storage/profile-photos/user.png'
    if (image.value.match(/^https?:\/\//)) return image.value
    return '/storage/' + image.value
})
</script>
