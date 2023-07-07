<template>
    <div v-if="compact" class="avatar-group -space-x-6">
        <Avatar v-for="miembro of equipo.usuarios" :key="miembro.id" :user="miembro"></Avatar>
    </div>
    <GridFill v-else w="4rem" class="gap-1">
        <img v-for="miembro of equipo.usuarios" :key="miembro.id" class="w-12 h-12 rounded-full bg-base-100 shadow"
            :alt="miembro.nombre" :src="miembro.avatar">
    </GridFill>
</template>

<script setup>
defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
    users: { type: Array, required: true },
    count: {
        type: Number,
        default: () => this.users.length
    }
})
</script>
