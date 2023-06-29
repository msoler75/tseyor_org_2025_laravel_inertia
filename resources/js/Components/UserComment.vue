<template>
    <div class="flex gap-3">
        <img :src="'/storage/'+author.avatar" class="w-16 h-16 rounded-full">
        <div>
            <div class="flex flex-col gap-3 mb-3">
                <div class="flex flex-col gap-1">
                    <!-- body -->
                    <div class="card bg-base-100 shadow p-3 gap-3 w-full">
                        <div class="w-full flex justify-between">
                            <strong>{{ author.name }}</strong>
                            <TimeAgo :date="date" class="text-xs" />
                        </div>
                        <div>{{ content }}</div>
                    </div>
                    <!-- actions -->
                    <div class="pl-3 flex gap-5 text-sm">
                        <a href="#">Me gusta</a>
                        <a href="#">Responder</a>
                    </div>
                </div>
            </div>
            <div v-if="replies.length" class="list" :style="'--depth: ' + depth">
                <UserComment v-for="reply in replies" :key="reply.id" :author="reply.author" :date="reply.created_at"
                    :content="reply.content" :replies="reply.replies" :depth="depth + 1"></UserComment>
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    author: Object,
    content: String,
    date: Number | String,
    replies: {
        type: Array,
        default: () => []
    },
    depth: {
        type: Number,
        default: 0
    }
});
</script>

