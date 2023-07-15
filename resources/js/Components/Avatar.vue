<template>
    <div class="avatar">
        <div class="w-32 rounded-full">
            <Link v-if="link" :href="route('usuario', { id: user.slug || user.id })">
            <Image :src="image" :alt="name" :title="name" fallback="/storage/profile-photos/user.png"/>
            </Link>
            <Image v-else :src="image" :alt="name" :title="name"  fallback="/storage/profile-photos/user.png" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true }
})

const name = computed(() => props.user.name || props.user.nombre)
const image = computed(() => props.user.avatar || props.user.profile_photo_path || props.user.imagen)
</script>
