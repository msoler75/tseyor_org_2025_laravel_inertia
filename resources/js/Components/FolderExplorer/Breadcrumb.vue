<template>
    <ol
        v-if="items.length"
        class="list-reset flex items-center gap-1 whitespace-nowrap"
    >
        <li v-if="firstVisible > 0">
            <Dropdown :relative="false" align="left" width="auto"
            >
                <template #trigger>
                    <span class="text-xl px-2 btn btn-sm rounded-xl h-1 hover:bg-base-100 cursor-pointer" title="Carpetas anteriores">
                        ⋯
                    </span>
                </template>
                <template #content>
                    <template v-for="(item, index) of items" :key="index">
                        <div
                            v-if="index < firstVisible"
                            class="flex gap-x items-center hover:bg-base-100 cursor-pointer max-w-[90vw] overflow-hidden text-ellipsis"
                        >
                            <Link
                                v-if="links && index < items.length - 1"
                                :href="item.url"
                                @click.native.capture="handleClick(item, $event)"
                                class="whitespace-nowrap truncate hover:underline px-4 py-3"
                                :title="item.label"
                                :class="!links ? 'pointer-events-none' : ''"
                                >{{ item.label }}</Link
                            >
                            <span v-else class="opacity-80 py-2">{{
                                item.label
                            }}</span>
                        </div>
                    </template>
                </template>
            </Dropdown>
        </li>
        <template v-for="(item, index) of items" :key="index">
            <li
                v-if="index >= firstVisible"
                class="flex items-center space-x-1 max-w-[calc(100%_-_40px)]"
            >
                <span
                    v-if="!rootLabel || index > 0"
                    class="mx-2 text-neutral-500 dark:text-neutral-400"
                    >/</span
                >
                <Link
                    v-if="links && index < items.length - 1"
                    :href="item.url"
                    @click.native.capture="handleClick(item, $event)"
                    class="whitespace-nowrap max-w-[90vw] truncate hover:underline py-2"
                    :title="item.label"
                    :class="!links ? 'pointer-events-none' : ''"
                    >{{ item.label }}</Link
                >
                <span v-else class="opacity-80 py-2 text-ellipsis max-w-full overflow-hidden">{{ item.label }}</span>
            </li>
        </template>
    </ol>
    <ol v-else>
        <li class="py-1">
            <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
        </li>
    </ol>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
// import ConditionalLink from './ConditionalLink.vue'

const emit = defineEmits(["folder"]);

const props = defineProps({
    path: String,
    links: { type: Boolean, default: true },
    interceptClick: { type: Boolean, default: false },
    rootLabel: { type: String, require: false },
    rootUrl: { type: String, require: false },
});

const items = computed(() => {
    const r = [];
    if (props.rootLabel) r.push({ label: props.rootLabel, url: props.rootUrl });

    const parts = props.path?.split("/").filter((x) => !!x);
    let url = "";
    if (parts)
        for (var part of parts) {
            url += "/" + part;
            r.push({ label: part, url: url });
        }
    return r;
});

watch(items, () => {
    calculateFirstVisible()
})

function handleClick(item, event) {
    console.log("breadcrumb.handleClick", { item, event });
    if (props.interceptClick) {
        console.log("emit");
        // cancelar evento
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        emit("folder", item);
        return false;
    }
}

const firstVisible = ref(0);

function calculateFirstVisible() {
    const char_width = 15;
    const ww = window.innerWidth
    const w = Math.min(1330, ww);
    const w_gap = 2 * char_width;
    let calculated_width = ww / 8;
    for (let i = items.value.length - 1; i >= 0; i--) {
        const item = items.value[i];
        calculated_width += item.label.length * char_width + w_gap;
        if (calculated_width> w*.92  ) {
            firstVisible.value = Math.min(items.value.length-1, (i+1))
            return;
        }
    }
    firstVisible.value = 0;
}

onMounted(() => {
    calculateFirstVisible();
    nextTick(() => {
        calculateFirstVisible()
    })
    window.addEventListener("resize", calculateFirstVisible);
});

onUnmounted(() => {
    window.removeEventListener("resize", calculateFirstVisible);
});

</script>
