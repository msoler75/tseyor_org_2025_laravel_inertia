<template>
        <!-- Modal flotante de búsqueda -->
        <Teleport to="body">
            <TransitionFade>
                <div v-if="showSearch" class="fixed bottom-0 sm:bottom-4 left-0 sm:left-4 right-0 sm:right-4 bg-base-300 p-3 sm:rounded-lg shadow-lg z-50 max-w-md mx-auto flex items-center gap-2">
                        <input
                            ref="inputRef"
                            v-model="searchTerm"
                            @keydown.enter="performSearch"
                            type="search"
                            placeholder="Buscar en el texto..."
                            class="flex-shrink w-[100px] sm:w-auto rounded-lg input-sm input-primary py-1 flex-grow dark:text-neutral"
                        />
                        <button
                            @click="performSearch"
                            :disabled="newSearch"
                            class="btn btn-sm btn-primary"
                        >
                            <Icon class="sm:hidden" icon="ph:magnifying-glass" />
                            <span class="hidden sm:inline">Buscar</span>
                        </button>
                        <div v-if="results.length > 0" class="flex items-center gap-1">
                            <button @click="navigate(-1)" class="btn btn-sm btn-primary">
                                <Icon icon="ph:caret-up" />
                            </button>
                            <button @click="navigate(1)" class="btn btn-sm btn-primary">
                                <Icon icon="ph:caret-down" />
                            </button>
                            <span class="min-w-6 sm:min-w-10 text-xs text-gray-600 dark:text-gray-400 ml-2 whitespace-nowrap font-mono">{{ currentIndex + 1 }}<span class="hidden sm:inline"> de </span><span class="sm:hidden">/</span>{{ results.length }}</span>
                        </div>
                        <div v-else-if="noResults" class="text-red-500 dark:text-red-400 px-2 text-xs text-center">
                            No encontrado
                        </div>
                        <button @click="ui.tools.closeSearch" class="ml-auto text-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        title="Cerrar búsqueda">
                            <Icon icon="ph:x" />
                        </button>
                </div>
            </TransitionFade>
        </Teleport>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import useUi from '@/Stores/ui';

const ui = useUi();

const showSearch = ref(false);
const searchTerm = ref('');
const results = ref([]);
const currentIndex = ref(0);
const noResults = ref(false);
const inputRef = ref(null);



watch(()=>ui.tools.mostrarBuscarTexto, (newVal) => {
    showSearch.value = newVal;
    if (newVal) {
        searchTerm.value = '';
        clearHighlights();
        results.value = [];
        currentIndex.value = 0;
        noResults.value = false;
        nextTick(() => {
            inputRef.value?.focus();
        });
    } else {
          clearHighlights();
    }
});

const newSearch = computed(() => {
    return searchTerm.value.trim() === '' || (results.value.length > 0 && results.value[currentIndex.value]?.textContent === searchTerm.value);
});



const performSearch = () => {
    if (!searchTerm.value.trim()) return;

    clearHighlights();
    results.value = [];
    currentIndex.value = 0;
    noResults.value = false;

    const term = searchTerm.value.trim();

    nextTick(() => {
        const contentContainer = document.querySelector('#page-content');
        if (!contentContainer) return;

        const textNodes = getTextNodes(contentContainer);
        const regex = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');

        textNodes.forEach(node => {
            const text = node.textContent;
            const matches = [...text.matchAll(regex)];
            if (matches.length > 0) {
                const parent = node.parentNode;
                const html = text.replace(regex, '<mark class="highlight">$&</mark>');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const fragment = document.createDocumentFragment();
                while (tempDiv.firstChild) {
                    fragment.appendChild(tempDiv.firstChild);
                }
                parent.replaceChild(fragment, node);
            }
        });

        // Recopilar resultados
        const marks = document.querySelectorAll('#page-content mark.highlight');
        marks.forEach(mark => {
            results.value.push(mark);
        });

        if (results.value.length > 0) {
            scrollToResult(0);
            noResults.value = false;
        } else {
            noResults.value = true;
        }
    });
};

const clearHighlights = () => {
    const marks = document.querySelectorAll('#page-content mark.highlight');
    marks.forEach(mark => {
        const text = document.createTextNode(mark.textContent);
        mark.parentNode.replaceChild(text, mark);
    });
};

const navigate = (direction) => {
    if (results.value.length === 0) return;
    currentIndex.value = (currentIndex.value + direction + results.value.length) % results.value.length;
    scrollToResult(currentIndex.value);
};

const scrollToResult = (index) => {
    if (results.value[index]) {
        results.value[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Resaltar temporalmente
        results.value[index].classList.add('current-highlight');
        setTimeout(() => {
            results.value[index].classList.remove('current-highlight');
        }, 1000);
    }
};

const getTextNodes = (node) => {
    const textNodes = [];
    const walker = document.createTreeWalker(node, NodeFilter.SHOW_TEXT, null, false);
    let currentNode;
    while (currentNode = walker.nextNode()) {
        if (currentNode.textContent.trim() && !isInExcludedElement(currentNode)) {
            textNodes.push(currentNode);
        }
    }
    return textNodes;
};

const isInExcludedElement = (node) => {
    const excludedTags = ['SCRIPT', 'STYLE', 'NOSCRIPT', 'IFRAME', 'OBJECT', 'EMBED'];
    let parent = node.parentNode;
    while (parent) {
        if (excludedTags.includes(parent.tagName)) {
            return true;
        }
        parent = parent.parentNode;
    }
    return false;
};


</script>

<style>
.highlight {
    background-color: yellow;
}

mark { transition: background-color 0.25s ease}
.current-highlight {
    background-color: orange !important;
}
</style>
