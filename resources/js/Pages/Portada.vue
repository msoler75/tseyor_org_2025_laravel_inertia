<template>
    <Sections>
        <Section :style="{height: `calc(100% - 45)`}">
            xxx
        </Section>
        <Section>
            yyy
        </Section>
        <Section>
            zzz
        </Section>
    </Sections>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })
</script>
