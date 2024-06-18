<template>
    <div class="container mx-auto py-20 text-center">
        <Hero title="Radio Tseyor">

            <div class="flex flex-wrap gap-3 justify-center items-center">
                <Link class="btn btn-primary" v-for="emisora of emisoras" :key="emisora"
                    :href="route('radio.emisora', emisora)">{{ emisora }}</Link>
            </div>

        </Hero>


        <Comentarios :url="radio" />

    </div>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import useSelectors from '@/Stores/selectors'

const selectors = useSelectors()

defineOptions({ layout: AppLayout })

const props = defineProps({
    emisoras: {}
});

if (selectors.emisoraRadio)
    router.visit(route('radio.emisora', selectors.emisoraRadio))

</script>
