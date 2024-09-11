<template>

    <Header>
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
            Bienvenid@ {{ $page.props.auth.user.name }}
        </h2>
    </Header>

    <div class="py-12">
        <div class="container mx-auto sm:px-6 lg:px-8 xl:max-w-xl">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-12 space-y-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">¡Bienvenido a la web Tseyor!
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Nos alegra tenerte aquí. Ahora puedes descubrir
                        un universo lleno de oportunidades.</p>
                </div>

                <div class="space-y-4">
                    <Link v-if="perfilCompletado" class="btn btn-primary w-full"
                        :href="route('usuario', $page.props.auth.user.id)">Ver tu perfil</Link>
                    <Link v-else class="btn btn-primary w-full" :href="route('usuario', $page.props.auth.user.id)">
                    Completar tu perfil</Link>
                    <Link class="btn btn-primary w-full" href="/novedades">Novedades de la web</Link>
                    <Link class="btn btn-primary w-full" href="/audios">Audios de Tseyor</Link>
                    <Link class="btn btn-primary w-full" href="/glosario">Glosario de Tseyor</Link>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
const page = usePage()
const user = computed(() => page.props.auth.user)
const tiene_imagen = computed(() => user.value.profile_photo_url && !user.value.profile_photo_url.match(/ui-avatars/))
const perfilCompletado = computed(() => tiene_imagen.value)

</script>
