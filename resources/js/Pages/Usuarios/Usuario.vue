<template>
    <div class="max-w-[960px] container mx-auto flex flex-col justify-center items-center p-5 lg:p-10 text-center">
        <div class="avatar">
            <div class="w-32 h-32 rounded-full">
                <img :src="urlImage" :alt="`Imagen del usuario ${usuario.name || usuario.slug}`" />
            </div>
        </div>
        <h1 class="container mx-auto my-2">
            {{ usuario.name || usuario.slug }}
        </h1>
        <div class="prose">
            <blockquote>
                <p>{{ usuario.frase }}</p>
            </blockquote>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
})

const image = computed(() => props.usuario.avatar || props.usuario.profile_ptoho_path || props.usuario.imagen)
const urlImage = computed(() => {
    if (!image.value) return '/storage/profile-photos/user.png'
    if (image.value.match(/^https?:\/\//)) return image.value
    return '/storage/' + image.value
})

</script>
