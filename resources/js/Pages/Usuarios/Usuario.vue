<template>
    <Sections>
        <Section class="container mx-auto flex flex-col items-center py-20">
            <div class="avatar">
                <div class="w-32 h-32 rounded-full">
                    <img :src="urlImage" :alt="`Imagen del usuario ${usuario.name || usuario.slug}`" />
                </div>
            </div>

            <h1 class="text-center my-2">
                {{ usuario.name || usuario.slug }}
            </h1>

            <div class="prose my-7">
                <blockquote>
                    <p>{{ usuario.frase }}</p>
                </blockquote>
            </div>

            <Link class="badge badge-neutral" v-for="equipo of usuario.equipos" :key="equipo.id"
                :href="route('equipo', equipo.slug || equipo.id)">
            {{ equipo.nombre }}
            </Link>

        </Section>

        <Section class="py-20">
            <div class="container mx-auto">
                <h2 class="text-center">Últimos comentarios</h2>
                <ul class="list-none space-y-5">
                    <li v-for="comentario of comentarios" class="w-full flex flex-col gap-3 md:flex-row justify-between items-baseline">
                        <Link :href="comentario.url" class="prose">
                        <blockquote>
                            <p>
                                {{ comentario.texto }}
                            </p>
                        </blockquote>
                        </Link>
                        <TimeAgo :date="comentario.created_at" class="text-sm" />
                    </li>
                </ul>
            </div>
        </Section>

    </Sections>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
    comentarios: {
        type: Array,
        required: true
    }
})

const image = computed(() => props.usuario.avatar || props.usuario.profile_ptoho_path || props.usuario.imagen)
const urlImage = computed(() => {
    if (!image.value) return '/storage/profile-photos/user.png'
    if (image.value.match(/^https?:\/\//)) return image.value
    return '/storage/' + image.value
})

</script>
