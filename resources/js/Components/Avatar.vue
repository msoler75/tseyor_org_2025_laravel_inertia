<template>
    <div class="avatar" :title="user.name || user.nombre || user.slug">
        <div class="rounded-full" :class="imageClass">
            <User :user="user" :popupCard="popupCard" v-if="link" class="w-full h-full">
                <Image :src="image" :alt="name" :title="name" :fallback="fallbackImage" />
            </User>
            <Image v-else :src="image" :alt="name" :title="name" :fallback="fallbackImage" />
        </div>
    </div>
</template>

<script setup>
const fallbackImage = ref('/almacen/profile-photos/user.png')

const props = defineProps({
    user: { type: Object, required: true },
    link: { type: Boolean, default: true },
    big: { type: Boolean, default: false },
    imageClass: { type: String, required: false },
    popupCard: { type: Boolean, default: true }
})

console.log('preparing avatar. user=', props.user)

const name = computed(() => props.user.name || props.user.nombre)
const image = computed(() => props.user.avatar || props.user.profile_photo_url || props.user.imagen)

const imageClass = computed(() => {
    if(props.imageClass) return props.imageClass
    if (props.big) return 'w-32 h-32'
    return 'w-14 h-14'
})
</script>

<style scoped>
.avatar {
    @apply justify-center flex-shrink-0 flex-grow-0;
}

.avatar>* {
    @apply rounded-full overflow-hidden flex-shrink-0;
}
</style>
