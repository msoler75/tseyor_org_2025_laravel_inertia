<template>
    <span :title="titulo">{{ tamanoFormateado }}</span>
</template>

<script>
export default {
    props: { size: { type: Number, required: true } },
    computed: {
        bytes() { return this.size || 0 },
        unidades() { return ['bytes', 'KB', 'MB', 'GB', 'TB'] },
        tamanoFormateado() {
            let bytes = parseFloat(this.bytes)
            let i
            for (i = 0; bytes >= 1024 && i < this.unidades.length - 1; i++) { bytes /= 1024; }
            return parseFloat(bytes).toFixed(2) + ' ' + this.unidades[i]
        }, titulo() {
            return this.tamanoFormateado + ' (' + this.bytes.toLocaleString() + ' bytes)'
        }
    }
}
</script>
