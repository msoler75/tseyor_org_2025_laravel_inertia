<template>
    <button type="button"
        class="w-42 lg:flex items-center text-sm leading-6 text-slate-400 rounded-md ring-1 ring-slate-900/10 shadow-sm py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700"
        @click="mostrarModal = true">
        <Icon icon="ph:magnifying-glass-bold" class="mr-2" />

        Buscar...<span class="ml-auto pl-3 flex-none text-xs font-semibold">Ctrl K</span>
    </button>


    <Modal :show="mostrarModal" @close="mostrarModal = false" maxWidth="lg" class="modal-search">
        <div class="bg-base-100 flex flex-col text-sm pb-7">
            <div class="flex gap-2 items-center  p-3">
                <Icon v-show="!loading" icon="ph:magnifying-glass-bold" class="text-lg" />
                <Spinner v-show="loading" class="text-lg" />
                <div class="flex-grow relative">
                    <input ref="input" class="search-input" v-model="query" aria-autocomplete="both"
                        aria-labelledby="search-input-label" autocomplete="off" autocorrect="off" autocapitalize="off"
                        enterkeyhint="go" spellcheck="false" placeholder="Buscar en el sitio web..." maxlength="64"
                        type="search" aria-activedescendant="search-input-item-1" aria-controls="search-input-list">
                </div>

                <kbd class="kbd cursor-pointer select-none text-xs font-semibold" @click="mostrarModal = false">ESC</kbd>

            </div>
            <div v-if="false"
                class="p-3 flex flex-wrap gap-3 select-none border-t border-b border-gray-500 border-opacity-30 shadow">
                <div v-if="colecciones.length > 1" v-for="col of colecciones" :key="col"
                    class="badge badge-info cursor-pointer capitalize" @click="toggleFiltro(col)"
                    :class="filtrada(col) == 0 ? 'opacity-50 hover:opacity-100' : ''">
                    <!--<Icon icon="ph:dot-outline-bold" v-show="noFiltros" />
                    <Icon icon="ph:check-duotone" v-show="filtrada(col) == 1" />
                    <Icon icon="ph:x-duotone" v-show="filtrada(col) == 1" />
                -->
                    {{ col == 'paginas' ? 'secciones web' : col }}
                </div>
            </div>

            <div  v-for="grupo of resultadosAgrupados" :key="grupo" class="flex flex-wrap p-3">
                <div class="w-full flex justify-between px-2 mt-3 mb-2 font-bold capitalize">{{ grupo.coleccion }}</div>
                <Link v-for="item of grupo.items" :key="item.id"
                class="w-full py-3 px-4 bg-base-200 bg-opacity-50 rounded-lg m-2 flex justify-between"
                :href="item.coleccion != 'paginas' ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref)) : '/' + item.slug_ref"
                             @click="mostrarModal = false"
                >
                <div v-html="item.titulo"/>
                <span class="text-lg">›</span>
            </Link>
            </div>

        </div>
    </Modal>
</template>

<script setup>

const mostrarModal = ref(false)

const input = ref(null)

const query = ref("")

const results = ref({ data: [] })

const resultadosAgrupados = computed(() => {
    const agrupados = {}

    for (var item of results.value.data) {
        var coleccion = item.coleccion=='paginas'?'páginas':item.coleccion
        if (!agrupados[coleccion]) {
            agrupados[coleccion] = []
        }
        agrupados[coleccion].push(item)
    }

    // Crear el array de objetos agrupados
    const items = Object.entries(agrupados).map(([coleccion, items]) => ({
        coleccion,
        items
    }))

    // Ordenar el array de items
    items.sort((a, b) => {
        const prioridad = ['libros', 'centros', 'páginas'] // paginas es el más prioritario

        const indexA = prioridad.indexOf(a.coleccion)
        const indexB = prioridad.indexOf(b.coleccion)

        return indexB - indexA
    })
    return items
})

onMounted(() => {
    window.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault()
            mostrarModal.value = true
        }
    });
})

watch(mostrarModal, (value) => {
    if (value)
        nextTick(() => {
            input.value.focus()
        })
})

const loading = ref(false)

function buscar() {
    console.log('buscar', query.value)
    if (query.value) {
        loading.value = true
        axios.get(route('buscar') + '?q=' + query.value)
            .then(response => {
                console.log(response)
                results.value = response.data
                loading.value = false
            })
            .catch(error => {
                loading.value = false
            })
    }

}

var timer = null

watch(query, (value) => {
    console.log('query changed', value)
    clearTimeout(timer)
    if (value)
        timer = setTimeout(buscar, 250)
    else
        results.value = { data: [] }
})

const colecciones = computed(() => {
    const r = { todo: 1 }
    for (var item of results.value.data)
        r[item.coleccion] = 1
    return Object.keys(r)
})

const filtros = ref({})

function toggleFiltro(coleccion) {
    if (coleccion == 'todo') {
        filtros.value = {}
        return
    }
    filtros.value[coleccion] = !filtros.value[coleccion]
    var alguno = false
    for (var k in filtros.value) {
        if (filtros.value[k]) alguno = true
    }
    if (!alguno)
        filtros.value = {}
}

// hay algun filtro?
const noFiltros = computed(() => !Object.keys(filtros.value).length)

// está seleccionado o deseleccionado un filtro en concreto?
function filtrada(coleccion) {
    if (noFiltros.value) return null
    return filtros.value[coleccion] ? 1 : 0
}


</script>

<style scoped>
/*.search-input {
    @apply bg-transparent !border-none hover: !border-none active: !border-none focus: !border-none focus: !outline-none focus: !ring-0;
}*/
</style>
