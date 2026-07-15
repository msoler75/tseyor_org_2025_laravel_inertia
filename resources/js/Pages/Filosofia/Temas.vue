<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back href="/filosofia">Filosofía TSEYOR</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="pagina" necesita="administrar contenidos" />
                </div>
            </div>

            <div class="mx-auto container mb-16 flex flex-col justify-center items-center">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                    Filosofía TSEYOR
                </span>
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black leading-[1.08] tracking-tight text-center">
                    Explora todos los temas
                </h1>
                <p class="text-base-content/60 mt-6 leading-relaxed max-w-2xl text-center">
                    Cada uno de estos conceptos se desarrolla ampliamente en la literatura y los comunicados de TSEYOR.
                </p>
            </div>
        </PageHeader>

        <PageWide>
            <div class="w-full space-y-16">
                <div v-for="grupo in paginasPorCategoria" :key="grupo.categoria">
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                            <Icon :icon="grupo.icono" class="text-xl text-primary" />
                        </span>
                        <h2 class="text-2xl font-display font-bold">{{ grupo.categoria }}</h2>
                    </div>

                    <GridAppear class="gap-8" col-width="16rem">
                        <CardContent
                            v-for="pagina in grupo.paginas"
                            :key="pagina.ruta"
                            :image="pagina.imagen"
                            :title="pagina.titulo"
                            :description="pagina.descripcion"
                            :href="'/' + pagina.ruta"
                            imageClass="h-48"
                        />
                    </GridAppear>
                </div>
            </div>
        </PageWide>
    </Page>
</template>

<script setup>
const props = defineProps({
    paginasPorCategoria: {
        type: Array,
        default: () => [],
    },
})
</script>
