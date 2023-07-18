<template>
    <div v-if="compact" class="avatar-group -space-x-6">
        <Avatar v-for="user of users" :key="user.id" :user="user" />
        <div v-if="count>users.length" class="avatar placeholder">
            <div class="bg-neutral-focus text-neutral-content">
                <span>+{{count - users.length}}</span>
            </div>
        </div>
    </div>
    <GridFill v-else w="2.9rem" class="gap-1">
        <Avatar v-for="user of users" :key="user.id" :user="user" />
        <div v-if="count>users.length" class="avatar placeholder">
            <div class="bg-neutral-focus text-neutral-content">
                <span>+{{count - users.length}}</span>
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

const total = computed(() => Math.max(props.users.length, props.count));
</script>
