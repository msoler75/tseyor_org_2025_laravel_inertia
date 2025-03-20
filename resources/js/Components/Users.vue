<template>
    <div v-if="compact" class="avatar-group -space-x-6">
        <Avatar v-for="user of users" :key="user.id" :user="user" imageClass="w-14 h-14" />
        <div v-if="count>users.length" class="avatar avatar-placeholder">
            <div class="bg-neutral/50 text-neutral-content w-14 shrink-0 rounded-full">
                <span class="text-2xl">+{{count - users.length}}</span>
            </div>
        </div>
    </div>
    <GridFill v-else col-width="2.9rem" class="gap-1">
        <Avatar v-for="user of users" :key="user.id" :user="user" imageClass="w-14 h-14 ring-neutral hover:ring-4 hover:ring-secondary " />
        <div v-if="count>users.length" class="avatar avatar-placeholder">
            <div class="bg-neutral/50 text-neutral-content w-14 shrink-0 rounded-full">
                <span class="text-2xl">+{{count - users.length}}</span>
            </div>
        </div>
    </GridFill>
</template>

<script setup>

const props = defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
    users: {
        type: Array,
        required: true,
    },
    count: {
        type: Number,
        default: 0,
    },
});

</script>
