<template>
    <Page>

        <PageHeader>
            <h1 class="hidden">Radio Tseyor</h1>

            <div class="flex justify-between items-center mb-20">
                <Back :href="route('biblioteca')">Biblioteca</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="radio-item" necesita="administrar contenidos" />
                </div>
            </div>
        </PageHeader>

        <PageWide>

            <Hero title="" :srcImage="ui.theme.isDark ? darkLogo : lightLogo" srcWidth="1117" srcHeight="801"

                textClass="px-7 gap-4"
                imageSideClass="max-h-[360px]">

                <h3 class="text-center">Elige una emisora:</h3>

                <div class="flex flex-wrap gap-3 justify-center w-full shrink-0">
                    <div v-for="emisora of emisoras" :key="emisora"
                        class="bg-base-100 rounded-lg shadow-2xs hover:bg-secondary hover:text-secondary-content transition duration-200">
                        <Link class="p-4 block" :href="route('radio.emisora', emisora)">{{ emisora }}</Link>
                    </div>
                </div>
            </Hero>

        </PageWide>


        <PageFooter>
            <Comentarios url="radio" />
        </PageFooter>

    </Page>
</template>


<script setup>
const ui = useUi()
const player = ui.player
player.autoplay = true

const base = '/almacen/medios/logos/radio_tseyor'
const lightLogo = base + '.png'
const darkLogo = base + '_dark.png'

const props = defineProps({
    emisoras: {}
});

if (ui.selectors.emisoraRadio)
    router.visit(route('radio.emisora', selectors.emisoraRadio))

</script>
