<template>
    <button type="button"
        class="w-42 flex items-center text-sm leading-6 text-slate-400 rounded-md ring-1 ring-slate-900/10 shadow-2xs py-1.5 pl-2 pr-3 hover:ring-slate-300 dark:bg-slate-800 dark:highlight-white/5 dark:hover:bg-slate-700 select-none flex-nowrap shrink-0"
        @click="search.opened = true">
        <Icon icon="ph:magnifying-glass-bold" class="mr-2" />
        Buscar...<span class="hidden lg:inline ml-auto pl-3 flex-none text-xs font-semibold">Ctrl K</span>

        <Modal :show="search.opened" @close="closeModal" maxWidth="lg">
            <div class="modal-search bg-base-100 flex flex-col text-sm pb-7">
                <div class="flex gap-2 items-center p-3">
                    <Icon v-show="!loading" icon="ph:magnifying-glass-bold" class="text-lg" />
                    <Spinner v-show="loading" class="text-lg" />
                    <div class="grow relative">
                        <input id="global-search-input" ref="input" class="search-input w-full" v-model="search.query"
                            aria-autocomplete="both" autocomplete="off" autocorrect="off" autocapitalize="off"
                            enterkeyhint="go" spellcheck="false" placeholder="Buscar en el sitio web..." maxlength="64"
                            type="search" aria-owns="search-input-list"
                            :aria-activedescendant="itemSeleccionado ? itemSeleccionado.idDom : ''"
                            aria-controls="search-input-list" aria-haspopup="true">
                    </div>

                    <span title="Cerrar búsqueda" class="text-3xl cursor-pointer transition duration-100 transform hover:scale-125 opacity-80 hover:opacity-100" @click="closeModal" >
                        <Icon icon="material-symbols-light:close-rounded" />
                    </span>
                </div>

                <div class="overflow-y-auto max-h-[calc(100vh-170px)] border-t border-gray-500 border-opacity-20"
                    id="search-input-list">

                    <div v-if="!resultadosAgrupados.length" class="p-7">
                        <div v-if="search.lastQuery" class="text-center text-lg text-gray-500">
                            <span v-if="search.valid">No hay resultados para</span>
                            <span v-else>Demasiados resultados para</span> "<span class="text-primary">{{
                                search.lastQuery }}</span>"
                        </div>

                        <div v-if="search.showSuggestions" class="mt-7">
                            <div class="font-bold mb-4">Sugerencias:</div>
                            <div v-for="q of predefinedQueries" :key="q" @click="clickPredefined(q)"
                                class="bg-base-200/50 flex items-center justify-between w-full py-3 px-4 my-1 cursor-pointer rounded-lg text-primary hover:underline">
                                <span class="after:content-['_↗']">
                                    {{ q.query }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else v-for="grupo of resultadosAgrupados" :key="grupo"
                        class="busqueda-resultados flex flex-wrap p-3">
                        <div
                            class="w-full flex justify-between px-1 mt-3 mb-2 font-bold capitalize opacity-75">
                            {{ traducir(grupo.coleccion) }}
                        </div>
                        <!-- Usa el componente Link de Inertia y maneja el evento click correctamente -->
                        <Link v-for="item of grupo.items" :key="item.id" :id="item.idDom"
                            class="w-full py-3 px-4 bg-base-200 rounded-lg my-1 flex gap-3 items-center"
                            role="option"
                            @mouseover="seleccionarItem(item, true)"
                            :href="calcularUrl(item)"
                            @click="onLinkClick($event, item)"
                            :aria-selected="itemSeleccionado && itemSeleccionado.id == item.id"
                            :class="[itemSeleccionado && itemSeleccionado.id == item.id ? 'seleccionado' : '',
                                item.isLinkAll ? '': 'justify-between'
                            ]">
                            <span class="text-lg" v-if="item.isLinkAll">→</span>
                            <div v-html="item.titulo" :class="item.isLinkAll?'text-xs':'text-sm'"/>
                            <span v-if="false && item.isLinkAll && grupo.ocultos" class="text-xs font-italic">(al menos {{ grupo.ocultos }} resultados más)</span>
                            <span class="text-lg" v-if="!item.isLinkAll">›</span>
                        </Link>
                    </div>
                </div>

            </div>
        </Modal>
    </button>
</template>

<script setup>
import useGlobalSearch from "@/Stores/globalSearch.js"
import traducir from '@/composables/traducciones'
import { removeAccents, levenshtein } from '@/composables/textutils'

const cambioUrl={
    entradas: 'blog'
}

const portada = computed(() => page.url == '/')
const page = usePage()
const nav = useNav()
const search = useGlobalSearch()

const input = ref(null)

const predefinedQueries = ref([
    { query: 'Contacta con nosotros', collections: 'paginas' },
    { query: '¿Dónde estamos?', collections: 'paginas' },
    { query: 'Libros para comenzar', collections: 'paginas' },
    { query: '¿Dónde puedo inscribirme?', collections: 'paginas' },
    { query: 'Ayuda humanitaria' }
])

const currentQueryStd = computed(() => removeAccents(search.lastQuery))

function removeGargabe(txt) {
    return removeAccents(txt.replace(/<em class="search-term">/g, '').replace(/<\/em>/g, ''))
}

function nearness(t) {
    return levenshtein(currentQueryStd.value, removeGargabe(t))
}

const MAX_ITEMS_POR_CATEGORIA = 4 // Cambia este valor para ajustar el máximo de items por categoría

// agrupamos por categorias, pero tenemos en cuenta los resultados de la búsqueda
const resultadosAgrupados = computed(() => {
    if (search.results === null) return []

    const agrupados = {}
    for (const key in search.results.data) {
        const item = search.results.data[key]
        if (!item.idDom)
            item.idDom = 'result-' + item.slug_ref + '-' + Math.floor(Math.random() * 1000)
        if (!agrupados[item.coleccion]) {
            agrupados[item.coleccion] = []
        }
        agrupados[item.coleccion].push(item)
    }

    // Mapeo de colecciones a rutas amigables
    const rutasColeccion = {
        terminos: 'glosario',
        // puedes añadir más si necesitas otras traducciones de rutas
    }

    // Crear el array de objetos agrupados
    const grupos = Object.entries(agrupados).map(([coleccion, items]) => {
        const itemsLimitados = items.slice(0, MAX_ITEMS_POR_CATEGORIA)
        const ocultos = items.length > MAX_ITEMS_POR_CATEGORIA ? (items.length - MAX_ITEMS_POR_CATEGORIA) : 0
        if (coleccion !== 'paginas') {
            const ruta = rutasColeccion[coleccion] || coleccion
            const linkItem = {
                id: `ver-todos-${coleccion}`,
                idDom: `ver-todos-${coleccion}`,
                coleccion,
                // Traducción para el texto del enlace especial
                titulo: `<span class="italic">Buscar en ${traducir(coleccion)}</span>`,
                isLinkAll: true,
                rutaColeccion: ruta,
                __tntSearchScore__: -1
            }
            itemsLimitados.push(linkItem)
        }
        return {
            coleccion,
            items: itemsLimitados,
            ocultos, // <-- número de items ocultos en esta categoría
            score: items.reduce((max, item) => Math.max(max, item.__tntSearchScore__), 0)
        }
    })


    grupos.forEach(grupo => {
        grupo.items.sort((a, b) => {
            // Si alguno es el "link all", siempre va al final
            if (a.isLinkAll) return 1
            if (b.isLinkAll) return -1
            if (a.__tntSearchScore__ == b.__tntSearchScore__) {
                const nA = nearness(a.titulo)
                const nB = nearness(b.titulo)
                if (nA == nB)
                    return a.titulo.length - b.titulo.length
                return nA - nB
            }
            return b.__tntSearchScore__ - a.__tntSearchScore__
        })
    })


    // Ordenar el array de items
    grupos.sort((a, b) => {
        const prioridad = ['libros', 'entradas', 'centros', 'lugares', 'guias', 'terminos', 'paginas'] // paginas es el más prioritario
        if (a.score == b.score)
            return prioridad.indexOf(b.coleccion) - prioridad.indexOf(a.coleccion)
        return b.score - a.score
    })

    nextTick(() => {
        seleccionarItem(null)
        siguienteItem()
    })

    return grupos
})

const itemsArray = computed(() => {
    const items = []
    for (var grupo of resultadosAgrupados.value) {
        for (var item of grupo.items)
            items.push(item)
    }
    return items
})

var usoTeclas = false
let modalHistoryState = false // Flag para controlar si hemos agregado estado al historial
let savedScrollPosition = 0 // Posición de scroll guardada

// Función para manejar el evento popstate (botón atrás)
function handlePopState(event) {
    console.log('handlePopState: evento popstate detectado', { opened: search.opened, modalHistoryState })
    if (search.opened && modalHistoryState) {
        // Cerrar el modal sin navegar
        search.opened = false
        modalHistoryState = false

        // Restaurar la posición de scroll después de un pequeño delay
        nextTick(() => {
            setTimeout(() => {
                window.scrollTo(0, savedScrollPosition)
                console.log('handlePopState: scroll restaurado a', savedScrollPosition)
            }, 10)
        })

        // Agregar de nuevo el estado actual para mantener la URL con la posición de scroll
        window.history.pushState({
            modalOpen: false,
            scrollY: savedScrollPosition
        }, '', window.location.href)
        console.log('handlePopState: modal cerrado, estado restaurado')
    }
}

onMounted(() => {
    window.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key === 'k') {
            event.preventDefault()
            search.opened = true
            console.log('keydown: ctrl+k, search.opened = true')
        }
        else if (search.opened) {
            console.log(event.key)
            switch (event.key) {
                case 'Escape':
                    event.preventDefault()
                    closeModal()
                    break;
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
                    usoTeclas = true
                    break;
                case 'ArrowUp':
                    event.preventDefault()
                    anteriorItem()
                    usoTeclas = true
                    break;
                case 'Home':
                    if (usoTeclas) {
                        event.preventDefault()
                        primerItem()
                    }
                    break;
                case 'End':
                    if (usoTeclas) {
                        event.preventDefault()
                        ultimoItem()
                    }
                    break;
            }
        }
    });

    // Gestión del botón "atrás" para cerrar el modal
    window.addEventListener('popstate', handlePopState);

    console.log('onMounted: event listeners añadidos')
})

onBeforeUnmount(() => {
    // Limpiar event listeners
    window.removeEventListener('popstate', handlePopState)
    console.log('onBeforeUnmount: event listeners removidos')
})

watch(() => search.opened, (value) => {
    console.log('watch search.opened', value)
    usoTeclas = false
    if (value) {
        nav.sideBarShow = false // cerramos la sidebar
        currentUrl = usePage().url

        // Guardar la posición actual de scroll antes de abrir el modal
        savedScrollPosition = window.scrollY || window.pageYOffset || document.documentElement.scrollTop
        console.log('watch search.opened: scroll guardado en', savedScrollPosition)

        // Agregar estado al historial para capturar el botón "atrás"
        if (!modalHistoryState) {
            window.history.pushState({
                modalOpen: true,
                scrollY: savedScrollPosition
            }, '', window.location.href)
            modalHistoryState = true
            console.log('watch search.opened: estado de historial agregado')
        }

        nextTick(() => {
            console.log('watch search.opened: nextTick focus')
            input.value.focus()
        })
    } else {
        // Al cerrar el modal, limpiar el estado del historial si es necesario
        if (modalHistoryState) {
            modalHistoryState = false
            console.log('watch search.opened: modal cerrado, flag de historial limpiado')
        }
    }
})

function calcularUrl(item) {
    // Si es el "item" especial de ver todos
    if (item.isLinkAll) {
        // Usa la ruta traducida si existe
        const coleccion = item.rutaColeccion || item.coleccion
        let base = '/' + coleccion
        if(cambioUrl[coleccion])
            base = '/' + cambioUrl[coleccion]
        return `${base}?buscar=${encodeURIComponent(search.query || search.lastQuery || '')}`
    }
    return item.coleccion != 'paginas'
        ? (route(item.coleccion) + '/' + (item.slug_ref || item.id_ref))
        : '/' + item.slug_ref
}

// para buscar
const loading = ref(false)
const queryLoading = ref("")
var timerBuscar = null

// para guardar estadísticas de búsqueda
var currentUrl = usePage().url
var timerGuardarBusqueda = null
var busquedaId = null

function buscar() {
    if (loading.value) {
        console.log('esperando carga de anterior busqueda', queryLoading.value, search.query)
        // clearTimeout(timerBuscar)
        timerBuscar = setTimeout(buscar, 250)
        return
    }
    if (search.query) {
        var currentQuery = search.query
        queryLoading.value = currentQuery
        loading.value = true
        axios.get(route('buscar') + '?query=' + search.query
            + (search.restrictToCollections ? '&collections=' + search.restrictToCollections : '')
        )
            .then(response => {
                console.log('response-data', response.data)
                search.results = response.data.listado
                search.valid = response.data.busquedaValida
                loading.value = false
                timerGuardarBusqueda = setTimeout(guardarBusqueda, 2000)
            })
            .catch(error => {
                loading.value = false
            })
            .finally(() => {
                search.lastQuery = currentQuery
            })
    }

}



async function guardarBusqueda(url) {
    clearTimeout(timerGuardarBusqueda)
    console.log('guardarBusqueda', url)
    const data = new FormData();
    data.append('query', search.query ? search.query : search.lastQuery);
    data.append('origen', currentUrl);
    if (busquedaId)
        data.append('id', busquedaId);
    if (url)
        data.append('click_url', url);

    return axios.post(route('busqueda.guardar'), data)
        .then(response => {
            busquedaId = response.data.id
        })
}

watch(() => search.query, (value) => {
    busquedaId = null // borramos id de la busqueda actual
    clearTimeout(timerGuardarBusqueda) // borramos contador de tiempo para guardar los datos de la busqueda actual, seguramente el usuario está escribiendo aún
    clearTimeout(timerBuscar) // borramos contador de tiempo para ejecutar la busqueda
    if (value)
        timerBuscar = setTimeout(buscar, 250)
    else {
        search.results = null
        search.lastQuery = null
        search.restrictToCollections = null
    }
})


// Función para cerrar el modal y restaurar scroll si es necesario
function closeModal() {
    if (search.opened) {
        search.opened = false
        // Si se cierra manualmente y estamos en la misma página, restaurar scroll
        if (modalHistoryState && currentUrl === usePage().url) {
            nextTick(() => {
                setTimeout(() => {
                    window.scrollTo(0, savedScrollPosition)
                    console.log('closeModal: scroll restaurado a', savedScrollPosition)
                }, 10)
            })
        }
    }
}

function clickPredefined(q) {
    search.query = q.query
    search.restrictToCollections = q.collections
}

function clickHandle(url, item) {
    console.log('clickHandle: inicio', { url, item, opened: search.opened })
    // Cierra el modal y fuerza el refresco del estado
    search.opened = false
    console.log('clickHandle: search.opened = false')

    // Si el modal se cerró manualmente, no necesitamos restaurar scroll ya que vamos a navegar
    setTimeout(() => {
        console.log('clickHandle: setTimeout ejecutado', { opened: search.opened })
        // Si es el "item" especial de ver todos, solo navega, no guarda búsqueda
        if (item && item.isLinkAll) {
            console.log('clickHandle: router.visit', url)
            router.visit(url)
            return
        }
        guardarBusqueda(url).then(() => {
            console.log('clickHandle: guardarBusqueda done')
            search.showSuggestions = true
        })
    }, 10)
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

function primerItem() {
    if (itemsArray.value.length)
        seleccionarItem(itemsArray.value[0])
}

function ultimoItem() {
    if (itemsArray.value.length)
        seleccionarItem(itemsArray.value[itemsArray.value.length - 1])
}

function onLinkClick(e, item) {
    // Siempre previene el comportamiento por defecto
    if (e && typeof e.preventDefault === 'function') e.preventDefault()
    clickHandle(calcularUrl(item), item)
}
</script>

<style scoped>
@reference "../../css/app.css";

a.seleccionado {
    @apply bg-primary text-white dark:text-black;
}

a.seleccionado :deep(em.search-term) {
    @apply text-white;
}

[data-theme='night'] a.seleccionado :deep(em.search-term),
.dark a.seleccionado :deep(em.search-term) {
    @apply text-black;
}
</style>
