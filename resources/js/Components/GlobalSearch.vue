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
                    <input id="global-search-input" ref="input" class="search-input w-full" v-model="query"
                        aria-autocomplete="both" autocomplete="off" autocorrect="off" autocapitalize="off" enterkeyhint="go"
                        spellcheck="false" placeholder="Buscar en el sitio web..." maxlength="64" type="search"
                        aria-owns="search-input-list"
                        :aria-activedescendant="itemSeleccionado ? itemSeleccionado.idDom : ''"
                        aria-controls="search-input-list" aria-haspopup="true">
                </div>

                <kbd class="kbd cursor-pointer select-none text-xs font-semibold" @click="mostrarModal = false">ESC</kbd>

            </div>

            <div class="overflow-y-auto max-h-[calc(100vh-170px)] border-t border-gray-500 border-opacity-20"
                id="search-input-list">

                <div v-if="!resultadosAgrupados.length" class="p-7">
                    <div v-if="lastQuery" class="text-center text-lg text-gray-500">
                        No hay resultados para "<span class="text-primary">{{ lastQuery }}</span>"
                    </div>

                    <div class="mt-14">
                        <span class="font-bold">Prueba a buscar:</span>
                        <div v-for="q of queries" :key="q" @click="query = q"
                            class="bg-base-200 bg-opacity-50 flex items-center justify-between w-full py-3 px-4 my-2 cursor-pointer rounded-lg">
                            <span>
                                {{ q }}
                            </span>
                            <span class="text-lg">›</span>
                        </div>
                    </div>
                </div>

                <div v-else v-for="grupo of resultadosAgrupados" :key="grupo"
                    class="busqueda-resultados flex flex-wrap p-3">
                    <div class="w-full flex justify-between px-2 mt-3 mb-2 font-bold capitalize">{{
                        traducir(grupo.coleccion) }}
                    </div>
                    <Link v-for="item of grupo.items" :key="item.id" :id="item.idDom"
                        class="w-full py-3 px-4 bg-base-200 bg-opacity-50 rounded-lg m-2 flex gap-3 justify-between items-center"
                        role="option" @mouseover="seleccionarItem(item, true)"
                        :href="item.coleccion != 'paginas' ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref)) : '/' + item.slug_ref"
                        @click="mostrarModal = false" :aria-selected="itemSeleccionado && itemSeleccionado.id == item.id"
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

const lastQuery = ref("")

const queries = ref(['Contacta con nosotros', '¿Dónde estamos?', 'Libros para comenzar', '¿Dónde puedo inscribirme?', 'Ayuda humanitaria'])

const results = ref({ data: [] })

const resultadosAgrupados = computed(() => {
    const agrupados = {}

    for (var key in results.value.data) {
        const item = results.value.data[key]

        if (!item.idDom)
            item.idDom = 'result-' + item.slug_ref + '-' + Math.floor(Math.random() * 1000)

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
const queryLoading = ref("")
var timer = null

function buscar() {
    if(loading.value)
    {
        console.log('esperando carga de anterior busqueda', queryLoading.value, query.value)
        // clearTimeout(timer)
        timer = setTimeout(buscar, 250)
        return
    }
    if (query.value) {
        var currentQuery = query.value
        queryLoading.value = currentQuery
        loading.value = true
        axios.get(route('buscar') + '?query=' + query.value)
            .then(response => {
                results.value = response.data
                loading.value = false
            })
            .catch(error => {
                loading.value = false
            })
            .finally(()=>{
                lastQuery.value = currentQuery
            })
    }

}

watch(query, (value) => {
    clearTimeout(timer)
    if (value)
        timer = setTimeout(buscar, 250)
    else {
        lastQuery.value = null
        results.value = { data: [] }
    }
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


// SELECCIONES DE ITEM

const itemSeleccionado = ref(null)

function seleccionarItem(item, noScroll) {
    itemSeleccionado.value = item
    if (item && !noScroll) {
        // comprueba si el resultado está fuera de visión, en tal caso desplaza el scroll
        const element = document.querySelector("#" + item.idDom)
        if (!element) return

        const container = document.getElementById('search-input-list');

        const containerRect = container.getBoundingClientRect();
        const elementRect = element.getBoundingClientRect();

        // Verificar si el elemento está fuera del contenedor
        if (
            elementRect.bottom > containerRect.bottom ||
            elementRect.top < containerRect.top
        ) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
        }

    }
}

function siguienteItem() {
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

