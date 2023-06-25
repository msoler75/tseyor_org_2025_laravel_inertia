<template>
    <Head>
        <!-- EN DESUSO -->
        <meta name="robots" content="max-snippet:-1,max-image-preview:large,max-video-preview:-1">
        <link rel="canonical" :href="page.props.ziggy.location">
        <link rel="sitemap" href="/sitemap.xml" title="Sitemap" type="application/xml">
        <meta name="author" content="tseyor">
        <title>{{ title }}</title>
        <meta name="description" :content="descripcion">
        <meta name="image" :content="image">
        <link href="/favicon.ico" rel="shortcut icon">
        <meta property="og:title" :content="title">
        <meta property="og:description" :content="descripcion">
        <meta property="og:locale" content="es">
        <meta property="og:image" :content="image">
        <meta property="og:url" :content="page.props.ziggy.location">
        <meta property="og:site_name" content="TSEYOR.org">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:image" :content="image">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" :content="image">
        <meta name="twitter:title" :content="title">
        <meta name="twitter:description" :content="descripcion">
        <meta name="twitter:site" content="@TSEYOR">
    </Head>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
const page = usePage()

defineProps({
    title: {},
    image: {
        default: () => ''
    },
    description: {}
})
</script>
