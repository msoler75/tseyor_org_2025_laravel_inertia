<template>
    <button type="button"
        class="w-42 lg:flex items-center text-sm leading-6 text-slate-400 rounded-md ring-1 ring-slate-900/10 shadow-sm py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700"
        @click="mostrarModal = true">
        <Icon icon="ph:magnifying-glass-bold" class="mr-2" />

        Buscar...<span class="ml-auto pl-3 flex-none text-xs font-semibold">Ctrl K</span>
    </button>


    <Modal :show="mostrarModal" @close="mostrarModal = false" maxWidth="lg">
        <div class="modal-search bg-base-100 flex flex-col text-sm pb-7">
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

            <div class="overflow-y-auto max-h-[calc(100vh-170px)] border-t border-gray-500 border-opacity-20">

                <div v-if="resultadosAgrupados.length" class="p-7">
                    <div v-if="query">
                        <div class="text-center text-lg text-gray-500">
                            No hay resultados para "<span class="text-primary">{{query}}</span>"
                        </div>

                        <div class="mt-14">
                            <span class="font-bold">Prueba a buscar:</span>
                            <div v-for="q of queries" :key="q" @click="query=q" class="bg-base-200 bg-opacity-50 flex items-center justify-between w-full py-3 px-4 my-2 cursor-pointer rounded-lg">
                                <span>
                                    {{q}}
                                </span>
                                <span class="text-lg">›</span>
                        </div>
                        </div>
                    </div>
                </div>

                <div v-else v-for="grupo of resultadosAgrupados" :key="grupo"
                    class="busqueda-resultados flex flex-wrap p-3">
                    <div class="w-full flex justify-between px-2 mt-3 mb-2 font-bold capitalize">{{
                        traducir(grupo.coleccion) }}
                    </div>
                    <Link v-for="item of grupo.items" :key="item.id"
                        class="w-full py-3 px-4 bg-base-200 bg-opacity-50 rounded-lg m-2 flex gap-3 justify-between items-center"
                        @mouseover="seleccionarItem(item)"
                        :href="item.coleccion != 'paginas' ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref)) : '/' + item.slug_ref"
                        @click="mostrarModal = false"
                        :class="itemSeleccionado && itemSeleccionado.id == item.id ? 'seleccionado bg-primary' : ''">
                    <div v-html="item.titulo" />
                    <span class="text-lg">›</span>
                    </Link>
                </div>
            </div>

        </div>
    </Modal>
</template>

<script setup>

const mostrarModal = ref(false)

const input = ref(null)

const query = ref("")

const queries = ref(['libros de referencia', 'donde puedo inscribirme', 'ayuda humanitaria'])

const results = ref({ data: [] })

const resultadosAgrupados = computed(() => {
    const agrupados = {}

    for (var item of results.value.data) {
        if (!agrupados[item.coleccion]) {
            agrupados[item.coleccion] = []
        }
        agrupados[item.coleccion].push(item)
    }

    // Crear el array de objetos agrupados
    const items = Object.entries(agrupados).map(([coleccion, items]) => ({
        coleccion,
        items
    }))

    // Ordenar el array de items
    items.sort((a, b) => {
        const prioridad = ['libros', 'centros', 'lugares', 'guias', 'terminos', 'paginas'] // paginas es el más prioritario

        const indexA = prioridad.indexOf(a.coleccion)
        const indexB = prioridad.indexOf(b.coleccion)

        return indexB - indexA
    })

    nextTick(() => {
        seleccionarItem(null)
        siguienteItem()
    })

    return items
})

const itemsArray = computed(() => {
    const items = []
    for (var grupo of resultadosAgrupados.value) {
        for (var item of grupo.items)
            items.push(item)
    }
    return items
})


onMounted(() => {
    window.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault()
            mostrarModal.value = true
        }
        if (mostrarModal.value) {
            switch (event.key) {
                case 'Enter':
                    if (itemSeleccionado.value) {
                        event.preventDefault()
                        const elem = document.querySelector('.busqueda-resultados .seleccionado')
                        elem.click()
                    }
                    break;
                case 'ArrowDown':
                    event.preventDefault()
                    siguienteItem()
                    break;
                case 'ArrowUp':
                    event.preventDefault()
                    anteriorItem()
                    break;
            }
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

const traducciones = {
    paginas: 'páginas',
    guias: 'guías estelares',
    terminos: 'glosario',
    lugares: 'lugares de la galaxia'
}

function traducir(col) {
    return traducciones[col] || col
}


const itemSeleccionado = ref(null)

function seleccionarItem(item) {
    itemSeleccionado.value = item
}

function siguienteItem() {
    console.log('siguienteItem', itemsArray)
    if (!itemSeleccionado.value) {
        itemSeleccionado.value = itemsArray.value[0]
        return
    }
    var idx = itemsArray.value.findIndex(x => x.id == itemSeleccionado.value.id)
    idx = (idx + 1) % itemsArray.value.length
    seleccionarItem(itemsArray.value[idx])
}

function anteriorItem() {
    if (!itemSeleccionado.value) {
        itemSeleccionado.value = itemsArray.value[itemsArray.length - 1]
        return
    }
    var idx = itemsArray.value.findIndex(x => x.id == itemSeleccionado.value.id)
    idx = (idx + itemsArray.value.length - 1) % itemsArray.value.length
    seleccionarItem(itemsArray.value[idx])
}

</script>

<style scoped>
.search-input {
    @apply bg-transparent !border-none hover:!border-none active:!border-none focus:!border-none focus:!outline-none focus:!ring-0;
}
</style>
