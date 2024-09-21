<template>
    <div class="h-full flex flex-col relative">
        <div class="w-full sticky border-b border-gray-300 shadow-sm bg-base-100  px-4 pb-0 sm:px-6 lg:px-8 z-30"
            :class="embed ? 'pt-[2rem] top-0' : ' pt-[4rem] top-[1.5rem]'">

            <div :class="embed ? '' : 'lg:container mx-auto'">
                <Breadcrumb :path="rutaActual" :links="true" :intercept-click="embed" @folder="clickBreadcrumb($event)"
                    title="Ruta actual" class="flex-wrap text-2xl font-bold items-center mb-5" :rootLabel="rootLabel"
                    :rootUrl="rootUrl" />
            </div>

            <div class="w-full flex flex-nowrap justify-between mb-4" :class="embed ? '' : 'lg:container mx-auto'">

                <div class="flex gap-x items-center w-full" v-if="!seleccionando && mostrandoResultados">

                    <span class="text-lg">Resultados de la búsqueda <span class="font-bold">'{{ buscar }}'</span>:</span>

                    <button class="ml-auto  btn btn-neutral btn-sm btn-icon" @click.prevent="toggleVista"
                        title="Cambiar vista">
                        <Icon v-show="selectors.archivosVista == 'lista'" icon="ph:list-dashes-bold"
                            class="transform scale-150" />
                        <Icon v-show="selectors.archivosVista == 'grid'" icon="ph:grid-nine-fill"
                            class="transform scale-150" />
                    </button>

                    <button class="btn btn-neutral btn-sm btn-icon !w-fit whitespace-nowrap" title="Cerrar búsqueda"
                        @click="mostrandoResultados = false">
                        <Icon icon="ph:magnifying-glass-duotone" class="transform scale-150" />
                        <Icon icon="ph:x-bold" />
                    </button>

                </div>

                <div v-if="!mostrandoResultados" class="flex gap-x w-full max-w-full">
                    <ConditionalLink v-if="!seleccionando" class="btn btn-neutral btn-sm btn-icon cursor-pointer"
                        :href="'/' + rutaBase" :tag="embed ? 'span' : 'a'"
                        @click="clickFolder({ ruta: '/' + rutaBase }, $event)" :is-link="!embed"
                        title="Ir a la carpeta base"
                        :class="rutaActual == rutaBase ? 'opacity-50 pointer-events-none' : ''">
                        <Icon icon="ph:house-line-duotone" class="text-2xl" />
                    </ConditionalLink>

                    <ConditionalLink v-if="items.length > 1 && !seleccionando && items[1].tipo == 'carpeta'"
                        :href="items[1].url" :tag="embed ? 'span' : 'a'" class="btn btn-neutral btn-sm btn-icon w-fit"
                        title="Ir a una carpeta superior" @click="clickFolder(items[1], $event)" :is-link="!embed"
                        :class="rutaActual == rutaBase ? 'opacity-50 pointer-events-none' : ''">
                        <Icon icon="ph:arrow-bend-left-up-duotone" class="text-2xl" />
                    </ConditionalLink>

                    <button v-if="seleccionando" class="btn btn-neutral btn-sm flex gap-x items-center"
                        @click.prevent="cancelarSeleccion" title="Cancelar selección">
                        <Icon icon="material-symbols:close-rounded" />
                        <span>{{ itemsSeleccionados.length }}</span>
                    </button>

                    <div class="ml-auto flex gap-x">

                        <button class="btn btn-neutral btn-sm" @click.prevent="toggleVista" title="Cambiar vista">
                            <Icon v-show="selectors.archivosVista == 'lista'" icon="ph:list-dashes-bold"
                                class="transform scale-150" />
                            <Icon v-show="selectors.archivosVista == 'grid'" icon="ph:grid-nine-fill"
                                class="transform scale-150" />
                        </button>

                        <button v-if="seleccionando" class="btn btn-neutral btn-sm btn-icon mr-auto"
                            @click.prevent="seleccionarTodos" title="Seleccionar todos">
                            <Icon icon="ph:selection-all-duotone" class="transform scale-150" />
                        </button>

                        <button v-if="!seleccionando && !mostrandoResultados" class="btn btn-neutral btn-sm btn-icon"
                            title="Buscar archivos" @click="showSearch">
                            <Icon icon="ph:magnifying-glass-duotone" class="transform scale-150" />
                        </button>


                        <Dropdown>
                            <template #trigger>
                                <span class="btn btn-neutral btn-sm btn-icon" title="Ordenar los elementos">
                                    <Icon icon="lucide:arrow-down-wide-narrow" class="text-2xl" />
                                </span>
                            </template>
                            <template #content>
                                <div class="select-none">
                                    <div v-for="label, value in ordenaciones" :key="value"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                        @click="ordenarPor = value">
                                        <div class="w-3">
                                            <Icon icon="ph:check" v-if="ordenarPor == value" />
                                        </div>
                                        {{ label }}
                                    </div>
                                </div>
                            </template>
                        </Dropdown>


                        <Dropdown v-if="!enRaiz && !seleccionando" align="right" width="48"
                            :class="!info_cargada ? 'opacity-50 pointer-events-none' : ''">
                            <template #trigger>
                                <div class="btn btn-neutral btn-sm btn-icon cursor-pointer">
                                    <Icon icon="mdi:dots-vertical" class="text-xl" />
                                </div>
                            </template>

                            <template #content>
                                <div class="select-none">
                                    <!-- Account Management -->
                                    <div v-if="puedeEscribir && !seleccionando"
                                        class="flex gap-x items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                        @click="modalSubirArchivos = true">
                                        <Icon icon="ph:upload-duotone" />
                                        <span>Subir archivos</span>
                                    </div>

                                    <div v-if="puedeEscribir && !seleccionando && itemsShow[0].ruta != 'archivos' && itemsShow[0].ruta != 'medios'"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                        @click="abrirModalRenombrar(itemsShow[0])">
                                        <Icon icon="ph:cursor-text-duotone" />
                                        <span>Renombrar</span>
                                    </div>

                                    <div v-if="puedeEscribir && !seleccionando"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                        @click="abrirModalCrearCarpeta">
                                        <Icon icon="ph:folder-plus-duotone" />
                                        <span>Crear carpeta</span>
                                    </div>


                                    <div v-if="!enRaiz && puedeLeer && !seleccionando && itemsShow.filter(x => !x.padre).length > 1"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                        @click="seleccionando = true">
                                        <Icon icon="ph:check-duotone" />
                                        <span>Abrir Selección</span>
                                    </div>

                                    <div v-else-if="seleccionando"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                        @click="cancelarSeleccion">
                                        <Icon icon="ph:x-square-duotone" />
                                        <span>Cancelar selección</span>
                                    </div>

                                    <Link v-if="!embed && propietarioRef && !seleccionando"
                                        class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                        :href="propietarioRef.url" :title="tituloPropietario">
                                    <Icon
                                        :icon="propietarioRef.tipo == 'equipo' ? 'ph:users-four-duotone' : 'ph:user-duotone'" />
                                    <span v-if="propietarioRef.tipo == 'equipo'">Ver equipo</span>
                                    <span v-else>Ver usuario</span>
                                    </Link>


                                    <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                        @click.prevent="abrirModalPropiedades(itemsShow[0])">
                                        <Icon icon="ph:info-duotone" />
                                        <span>Propiedades</span>
                                    </div>

                                </div>

                            </template>
                        </Dropdown>

                    </div>
                </div>

            </div>




            <!--  Botones de Operaciones (embed) -->

            <div class="mb-2 flex gap-4 select-none overflow-x-auto scrollbar-hidden"
                v-if="embed && (itemsSeleccionados.length || store.isMovingFiles || store.isCopyingFiles)"
                :seleccionando="seleccionando"
                :class="seleccionando || store.isMovingFiles || store.isCopyingFiles ? 'justify-start sm:justify-center' : 'justify-end'">

                <button v-if="modoInsertar && imagenesSeleccionadas.length"
                    class="btn btn-secondary flex gap-x items-center" @click.prevent="insertarImagenes">
                    <Icon icon="material-symbols:close-rounded" />
                    <span>Insertar</span>
                </button>


                <button v-if="store.isMovingFiles || store.isCopyingFiles"
                    class="btn btn-secondary flex gap-x items-center" @click.prevent="cancelarOperacion">
                    <Icon icon="material-symbols:close-rounded" />
                    <span>Cancelar</span>
                </button>

                <button v-if="store.isMovingFiles" class="btn btn-secondary flex gap-x items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click.prevent="moverItems"
                    title="Mover los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Mover aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <button v-else-if="store.isCopyingFiles" class="btn btn-secondary flex gap-x items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click.prevent="copiarItems"
                    title="Copiar los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Pegar aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <template v-else>
                    <button v-if="itemsSeleccionados.length && puedeMoverSeleccionados"
                        class="btn btn-secondary flex gap-x items-center" @click.prevent="prepararMoverItems">
                        <Icon icon="ph:scissors-duotone" /><span>Mover</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-secondary flex gap-x items-center"
                        @click.prevent="prepararCopiarItems">
                        <Icon icon="ph:copy-simple-duotone" /><span>Copiar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length && puedeBorrarSeleccionados" class="btn btn-secondary flex gap-x items-center"
                        @click.prevent="abrirEliminarModal(null)">
                        <Icon icon="ph:trash-duotone" />
                        <span>Eliminar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length == 1"
                        class="md:hidden btn btn-secondary flex gap-x items-center"
                        @click.prevent="abrirModalRenombrar(itemsSeleccionados[0])">
                        <Icon icon="ph:cursor-text-duotone" />
                        <span>Renombrar</span>
                    </button>

                    <button v-if="seleccionando && itemsSeleccionados.length > 0" class="btn btn-secondary"
                        @click.prevent="abrirModalPropiedades()">
                        <Icon icon="ph:info-duotone" />
                        <span>Propiedades</span>
                    </button>

                </template>
            </div>

            <!--  ---    -->

        </div>







        <div class="folder-content select-none flex-grow bg-base-100 py-4 px-2 sm:px-6 lg:px-8 pb-14 h-full"
            :class="contentClass">

            <div v-if="cargando" class="w-full h-full p-12 flex justify-center items-center text-4xl">
                <Spinner />
            </div>
            <div v-else-if="!mostrandoResultados && !itemsOrdenados.length"
                class="flex flex-col justify-center items-center gap-7 text-xl py-12 mb-14">
                <Icon icon="ph:warning-diamond-duotone" class="text-4xl" />
                <div>No hay archivos</div>
            </div>
            <div v-else-if="itemsMostrar.length && selectors.archivosVista === 'lista'"
                :class="itemsMostrar.length ? 'mr-2' : ''">
                <table class="w-full lg:w-auto mx-auto" :class="transitionActive ? 'animating' : ''">
                    <thead class="hidden sm:table-header-group" :class="itemsMostrar.length ? '' : 'opacity-0'">
                        <tr>
                            <th v-if="seleccionando" class="hidden md:table-cell"></th>
                            <th></th>
                            <th class="min-w-[16rem] lg:min-w-[32rem] text-left cursor-pointer"
                                @click="ordenarPor = ordenarPor == 'nombreAsc' ? 'nombreDesc' : 'nombreAsc'">Nombre
                                <span v-if="ordenarPor == 'nombreDesc'">↑</span><span
                                    v-if="ordenarPor == 'nombreAsc'">↓</span>
                            </th>
                            <th class="min-w-[8rem] cursor-pointer"
                                @click="ordenarPor = ordenarPor == 'tamañoDesc' ? 'tamañoAsc' : 'tamañoDesc'">Tamaño
                                <span v-if="ordenarPor == 'tamañoDesc'">↑</span><span
                                    v-if="ordenarPor == 'tamañoAsc'">↓</span>
                            </th>
                            <th class="min-w-[12rem] cursor-pointer"
                                @click="ordenarPor = ordenarPor == 'fechaDesc' ? 'fechaAsc' : 'fechaDesc'">Fecha <span
                                    v-if="ordenarPor == 'fechaDesc'">↑</span><span
                                    v-if="ordenarPor == 'fechaAsc'">↓</span></th>
                            <th v-if="selectors.mostrarPermisos && !mostrandoResultados" class="hidden sm:table-cell">
                                Permisos</th>
                            <th v-if="selectors.mostrarPermisos && !mostrandoResultados" class="hidden sm:table-cell">
                                Propietario</th>
                            <th v-if="mostrandoResultados || mostrarRutas || rutaActual == 'mis_archivos'"
                                class="hidden lg:table-cell text-sm">Ubicación
                            </th>
                            <th class="hidden md:table-cell"></th>
                        </tr>
                    </thead>

                    <component :is="transitionActive?TransitionGroup:'tbody'" tag="tbody" name="files">
                        <tr v-for="item in itemsMostrar" :key="item.ruta"
                            :class="item.clase + ' ' + (item.seleccionado ? '' : '') + (item.puedeLeer && (!seleccionando || esAdministrador)  ? '' : ' opacity-50 pointer-events-none')">
                            <td v-if="seleccionando" @click.prevent="toggleItem(item)"
                                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100">
                                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                                <Icon v-else icon="ph:square" />
                            </td>
                            <td class="relative w-4" v-on:touchstart="ontouchstart(item)"
                                v-on:touchend="ontouchend(item)">
                                <DiskIcon v-if="item.tipo === 'disco'" :url="item.url" class="cursor-pointer"
                                    @click="clickDisk(item, $event)" :is-link="!embed" />
                                <FolderIcon v-else-if="item.tipo === 'carpeta'"
                                    class="cursor-pointer text-4xl sm:text-xl" :private="item.privada"
                                    :owner="item.propietario && item.propietario?.usuario.id === user?.id"
                                    :url="item.url"
                                    @click="clickFolder(item, $event)" :link="!embed&&item.puedeLeer" :arrow="!!item.acceso_directo" />
                                <FileIcon v-else :url="item.url" class="cursor-pointer text-4xl sm:text-xl" @click="clickFile(item, $event)"
                                    :link="!embed&&item.puedeLeer" />
                            </td>
                            <td class="sm:hidden py-3" v-on:touchstart="ontouchstart(item)"
                                v-on:touchend="ontouchend(item)">
                                <div class="flex flex-col">
                                    <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url"
                                        v-html="nombreItem(item)" class="cursor-pointer"
                                        @click="clickDisk(item, $event)" :is-link="!embed" />
                                    <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                                        v-html="nombreItem(item)" class="cursor-pointer"
                                        :class="seleccionando ? 'pointer-events-none' : ''"
                                        @click="clickFolder(item, $event)" :is-link="!embed" />
                                    <div v-else-if="seleccionando" :title="item.nombre" v-html="nombreItem(item)" />
                                    <a v-else :href="item.url" download v-html="nombreItem(item)"
                                        :class="seleccionando ? 'pointer-events-none' : ''"
                                        @click="clickFile(item, $event)" :is-link="!embed" />

                                    <small class="w-full flex justify-between gap-2 items-center opacity-50">
                                        <span v-if="item.tipo === 'disco'">****</span>
                                        <span v-else-if="item.tipo === 'carpeta'">
                                            {{ 'archivos' in item ? plural(item.archivos + item.subcarpetas, 'elemento' ) : '' }}</span>
                                        <FileSize v-else :size="item.tamano" />
                                        <TimeAgo v-if="item.fecha_modificacion" :date="item.fecha_modificacion" />
                                    </small>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell py-3 max-w-[24rem]"
                                v-on:touchstart="ontouchstart(item)" v-on:touchend="ontouchend(item)">
                                <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url" v-html="nombreItem(item)"
                                    class="cursor-pointer py-3 hover:underline" @click="clickDisk(item, $event)"
                                    :is-link="!embed" />
                                <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                                    v-html="nombreItem(item)" class="cursor-pointer py-3 hover:underline"
                                    :class="seleccionando ? 'pointer-events-none' : ''"
                                    @click="clickFolder(item, $event)" :is-link="!embed" />
                                <span v-else-if="seleccionando" v-html="nombreItem(item)" />
                                <a v-else :href="item.url" download v-html="nombreItem(item)"
                                    class="py-3 hover:underline" :class="seleccionando ? 'pointer-events-none' : ''"
                                    @click="clickFile(item, $event)" />
                            </td>
                            <td class="hidden sm:table-cell text-center" v-on:touchstart="ontouchstart(item)"
                                v-on:touchend="ontouchend(item)">
                                <span v-if="item.tipo === 'disco'">-</span>
                                <span v-else-if="item.tipo === 'carpeta'" class="text-sm">
                                    {{ 'archivos' in item ? plural(item.archivos + item.subcarpetas, 'elemento' ) : '' }}
                                </span>
                                <FileSize v-else :size="item.tamano" class="block text-right" />
                            </td>
                            <td class="hidden sm:table-cell text-center" v-on:touchstart="ontouchstart(item)"
                                v-on:touchend="ontouchend(item)">
                                <TimeAgo v-if="item.fecha_modificacion" :date="item.fecha_modificacion"
                                    class="block text-center text-sm" />
                                <span v-else>-</span>
                            </td>
                            <td v-if="selectors.mostrarPermisos && !mostrandoResultados"
                                class="hidden sm:table-cell text-center text-sm"
                                v-on:touchstart="ontouchstart(item)" v-on:touchend="ontouchend(item)">{{
                                    item.permisos || '...' }}
                            </td>
                            <td v-if="selectors.mostrarPermisos && !mostrandoResultados"
                                class="hidden sm:table-cell text-center text-sm min-w-[10rem]"
                                v-on:touchstart="ontouchstart(item)" v-on:touchend="ontouchend(item)">
                                {{ item.propietario?.usuario.nombre || '...' }}/{{ item.propietario?.grupo.nombre ||
                                    '...'
                                }}
                            </td>
                            <td v-if="mostrandoResultados || mostrarRutas || rutaActual == 'mis_archivos'"
                                class="hidden lg:table-cell text-sm">
                                <div class="flex items-center gap-2 lg:min-w-64 xl:min-w-[500px] 2xl:min-w-[700px]">
                                    <FolderIcon :arrow="true" :url="item.carpeta" />
                                    <Link :href="item.carpeta" class="break-all">/{{ item.carpeta }}</Link>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                <Dropdown align="right" width="48" v-if="item.tipo !== 'disco'"
                                    :class="!info_cargada ||!item.puedeLeer? 'opacity-0 pointer-events-none' : ''">
                                    <template #trigger>
                                        <span class="cursor-pointer">
                                            <Icon icon="mdi:dots-vertical" class="text-xl" />
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="select-none">
                                            <div v-if="(esAdministrador || item.puedeEscribir) && !seleccionando"
                                                class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                                @click="abrirModalRenombrar(item)">
                                                <Icon icon="ph:cursor-text-duotone" />
                                                <span>Renombrar</span>
                                            </div>

                                            <div v-if="(esAdministrador || item.puedeEscribir) && !seleccionando && !item.padre"
                                                class="flex gap-x  items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                                @click="abrirEliminarModal(item)">
                                                <Icon icon="ph:trash-duotone" />
                                                <span>Eliminar</span>
                                            </div>

                                            <div v-if="(esAdministrador || item.puedeLeer) && !buscandoCarpetaDestino && !item.padre"
                                                class="flex gap-x  items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                                                @click="seleccionando = true; item.seleccionado = !item.seleccionado">
                                                <template v-if="!item.seleccionado">
                                                    <Icon icon="ph:check-fat-duotone" />
                                                    <span>Seleccionar</span>
                                                </template>
                                                <template v-else>
                                                    <Icon icon="ph:square" />
                                                    <span>Deseleccionar</span>
                                                </template>
                                            </div>

                                            <div v-if="seleccionando"
                                                class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                @click="cancelarSeleccion">
                                                <Icon icon="ph:x-square-duotone" />
                                                <span>Cancelar selección</span>
                                            </div>


                                            <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                @click.prevent="abrirModalPropiedades(item)">
                                                <Icon icon="ph:info-duotone" />
                                                <span>Propiedades</span>
                                            </div>

                                        </div>

                                    </template>
                                </Dropdown>
                            </td>
                        </tr>
                    </component>
                </table>
            </div>
            <div v-else-if="itemsMostrar.length && selectors.archivosVista === 'grid'">
                <GridFill colWidth="14rem" class="gap-4 pt-6">

                        <div v-for="item in itemsMostrar" :key="item.ruta"
                            :class="item.clase + ' ' + (item.seleccionado ? '' : 'bg-base-200') +
                            (item.puedeLeer && (!seleccionando || esAdministrador)  ? '' : ' opacity-50 pointer-events-none')">
                            <div v-if="seleccionando" @click.prevent="toggleItem(item)"
                                class="hidden md:table-cell transform scale-150 cursor-pointer opacity-70 hover:opacity-100"
                                :class="esAdministrador || item.puedeLeer ? '' : 'opacity-40 pointer-events-none'">
                                <Icon v-if="item.seleccionado" icon="ph:check-square-duotone" />
                                <Icon v-else icon="ph:square" />
                            </div>
                            <div class="flex flex-col items-center justify-center relative h-full pt-2">
                                <DiskIcon v-if="item.tipo === 'disco'" :url="item.url"
                                    class="cursor-pointer text-8xl mb-4" @click="clickDisk(item, $event)"
                                    :is-link="!embed" />
                                <FolderIcon v-else-if="item.tipo === 'carpeta'" :url="item.url" :private="item.privada"
                                    :owner="item.propietario && item.propietario?.usuario.id === user?.id"
                                    class="cursor-pointer text-8xl mb-4" :disabled="seleccionando"
                                    @click="clickFolder(item, $event)" :is-link="!embed" :arrow="!!item.acceso_directo" />
                                <a v-else-if="isImage(item.nombre)" :href="item.url" class="text-8xl mb-4" download
                                    @click="clickFile(item, $event)">
                                    <Image :src="item.url" class="overflow-hidden w-[180px] h-[120px] object-contain" />
                                </a>
                                <FileIcon v-else :url="item.url" class="cursor-pointer text-8xl mb-4"
                                    @click="clickFile(item, $event)" :is-link="!embed" />

                                <div class="text-sm text-center">
                                    <ConditionalLink v-if="item.tipo === 'disco'" :href="item.url"
                                        v-html="nombreItem(item)" class="py-1 hover:underline"
                                        @click="clickDisk(item, $event)" :is-link="!embed" />
                                    <ConditionalLink v-else-if="item.tipo === 'carpeta'" :href="item.url"
                                        v-html="nombreItem(item)" class="py-1 hover:underline"
                                        @click="clickFolder(item, $event)" :is-link="!embed" />
                                    <span v-else-if="seleccionando" v-html="nombreItem(item)" />
                                    <a v-else :href="item.url" download v-html="nombreItem(item)"
                                        @click="clickFile(item, $event)" :is-link="!embed"
                                        class="py-1 hover:underline" />
                                </div>
                                <div class="text-gray-500 text-xs">
                                    <span v-if="item.tipo === 'disco'" />
                                    <template v-else-if="item.tipo === 'carpeta'">{{ 'archivos' in item?(plural(item.archivos, 'archivo')+ ', ' +
                                        plural(item.subcarpetas,'carpeta')):'&nbsp;' }}
                                    </template>
                                    <template v-else>
                                        <FileSize :size="item.tamano" />&nbsp;
                                        <TimeAgo :date="item.fecha_modificacion" />
                                    </template>
                                </div>


                                <div class="w-full flex justify-center mt-auto">
                                    <Dropdown align="right" width="48"  v-if="item.tipo !== 'disco'"
                                     :class="!info_cargada || !item.puedeLeer? 'opacity-0 pointer-events-none' : ''">
                                        <template #trigger>
                                            <span class="my-3 btn btn-sm btn-icon bg-base-100 p-0.5">
                                                <Icon icon="mdi:dots-horizontal" class="text-xl z-20" />
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class="select-none">

                                                <div v-if="!seleccionando"
                                                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                                    @click="abrirModalRenombrar(item)">
                                                    <Icon icon="ph:cursor-text-duotone" />
                                                    <span>Renombrar</span>
                                                </div>

                                                <div v-if="!seleccionando && !item.padre"
                                                    class="flex gap-x  items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                                    @click="abrirEliminarModal(item)">
                                                    <Icon icon="ph:trash-duotone" />
                                                    <span>Eliminar</span>
                                                </div>

                                                <div v-if="(esAdministrador || item.puedeLeer) && !buscandoCarpetaDestino && !item.padre"
                                                    class="flex gap-x  items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                                                    @click="seleccionando = true; item.seleccionado = !item.seleccionado">
                                                    <template v-if="!item.seleccionado">
                                                        <Icon icon="ph:check-fat-duotone" />
                                                        <span>Seleccionar</span>
                                                    </template>
                                                    <template v-else>
                                                        <Icon icon="ph:square" />
                                                        <span>Deseleccionar</span>
                                                    </template>
                                                </div>

                                                <div v-if="seleccionando"
                                                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                    @click="cancelarSeleccion">
                                                    <Icon icon="ph:x-square-duotone" />
                                                    <span>Cancelar selección</span>
                                                </div>

                                                <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                                                    @click.prevent="abrirModalPropiedades(item)">
                                                    <Icon icon="ph:info-duotone" />
                                                    <span>Propiedades</span>
                                                </div>
                                            </div>

                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>
                </GridFill>
            </div>

            <div v-if="buscando || mostrandoResultados" class="w-full text-center h-[3rem] mt-3">
                <div v-if="buscando">Buscando...</div>
                <div v-else-if="mostrandoResultados && !resultadosBusqueda.length">No hay resultados</div>
            </div>
        </div>


        <!--  Botones de Operaciones -->
        <teleport to="body">
            <div class="fixed bottom-0 left-0 right-0 z-50 bg-base-200 p-2 flex gap-4 select-none overflow-x-auto scrollbar-hidden"
                v-if="!embed && (itemsSeleccionados.length || store.isMovingFiles || store.isCopyingFiles)"
                :seleccionando="seleccionando"
                :class="seleccionando || store.isMovingFiles || store.isCopyingFiles ? 'justify-start sm:justify-center' : 'justify-end'">

                <button v-if="store.isMovingFiles || store.isCopyingFiles"
                    class="btn btn-secondary flex gap-x items-center" @click.prevent="cancelarOperacion">
                    <Icon icon="material-symbols:close-rounded" />
                    <span>Cancelar</span>
                </button>

                <button v-if="store.isMovingFiles" class="btn btn-secondary flex gap-x items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click.prevent="moverItems"
                    title="Mover los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Mover aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <button v-else-if="store.isCopyingFiles" class="btn btn-secondary flex gap-x items-center"
                    :disabled="store.sourcePath == rutaActual || !puedeEscribir" @click.prevent="copiarItems"
                    title="Copiar los elementos seleccionados a esta carpeta">
                    <Icon icon="ph:clipboard-duotone" />
                    <span v-if="puedeEscribir">Pegar aquí</span>
                    <span v-else>No tienes permisos aquí</span>
                </button>

                <template v-else>

                    <button v-if="itemsSeleccionados.length && puedeMoverSeleccionados"
                        class="btn btn-secondary flex gap-x items-center" @click.prevent="prepararMoverItems">
                        <Icon icon="ph:scissors-duotone" /><span>Mover</span>
                    </button>

                    <button v-if="itemsSeleccionados.length" class="btn btn-secondary flex gap-x items-center"
                        @click.prevent="prepararCopiarItems">
                        <Icon icon="ph:copy-simple-duotone" /><span>Copiar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length &&  puedeBorrarSeleccionados" class="btn btn-secondary flex gap-x items-center"
                        @click.prevent="abrirEliminarModal(null)">
                        <Icon icon="ph:trash-duotone" />
                        <span>Eliminar</span>
                    </button>

                    <button v-if="itemsSeleccionados.length == 1 && puedeEliminarSeleccionados"
                        class="md:hidden btn btn-secondary flex gap-x items-center"
                        @click.prevent="abrirModalRenombrar(itemsSeleccionados[0])">
                        <Icon icon="ph:cursor-text-duotone" />
                        <span>Renombrar</span>
                    </button>

                    <button v-if="seleccionando && itemsSeleccionados.length > 0" class="btn btn-secondary"
                        @click.prevent="abrirModalPropiedades()">
                        <Icon icon="ph:info-duotone" />
                        <span>Propiedades</span>
                    </button>

                </template>
            </div>
        </teleport>



        <!-- Modal Search -->
        <Modal :show="showSearchInput" @close="showSearchInput = false" maxWidth="sm">

            <form class="p-5 flex flex-col gap-5 items-center" @submit.prevent="onSearch">
                <input ref="inputSearch" type="search" placeholder="Nombre de archivo..." v-model="buscar">

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button @click.prevent="onSearch" type="button" class="btn btn-primary btn-sm" :disabled="!buscar">
                        Buscar archivos
                    </button>

                    <button @click.prevent="showSearchInput = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </form>

        </Modal>




        <!-- Modal Upload -->
        <Modal :show="modalSubirArchivos" @close="modalSubirArchivos = false">

            <div class="p-5 flex flex-col gap-5 items-center">
                <Dropzone class="w-full" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true
                    v-on:vdropzone-sending="sendingEvent" v-on:vdropzone-success="successEvent">
                    <div class="flex flex-col items-center">
                        <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                        <span>Arrastra los archivos aquí o haz clic para subirlos</span>
                    </div>
                </Dropzone>


                <button @click.prevent="modalSubirArchivos = false" type="button" class="btn btn-neutral btn-sm">
                    Cerrar
                </button>
            </div>

        </Modal>



        <!-- Modal Crear Carpeta -->
        <Modal :show="modalCrearCarpeta" @close="modalCrearCarpeta = false" maxWidth="sm">

            <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="crearCarpeta">

                <div class="flex flex-col gap-4">
                    <label for="nombreCarpeta">Nombre de la nueva carpeta:</label>
                    <input id="nombreCarpeta" v-model="nombreCarpeta" type="text" required>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button @click.prevent="crearCarpeta" type="button" class="btn btn-primary btn-sm"
                        :disabled="creandoCarpeta">
                        <div v-if="creandoCarpeta" class="flex items-center gap-x">
                            <Spinner />
                            Creando...
                        </div>
                        <span v-else>
                            Crear Carpeta
                        </span>
                    </button>

                    <button @click.prevent="modalCrearCarpeta = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </Modal>



        <!-- Modal Propiedades -->
        <Modal :show="modalPropiedades" @close="modalPropiedades = false">
            <div class="p-5">
                <div v-for="item, index of itemsPropiedades" :key="item.url" class="pb-4"
                    :class="index > 0 ? 'pt-4 border-t border-base-200' : ''">
                    <table class="propiedades border-separate border-spacing-y-3">
                        <tbody>
                        <tr>
                            <th>Archivo </th>
                            <td>{{ item.nombre }}</td>
                        </tr>
                        <tr>
                            <th>Carpeta</th>
                            <td>{{ item.carpeta == '.' ? '' : item.carpeta }}</td>
                        </tr>
                        <tr>
                            <th>Ruta completa</th>
                            <td>/{{ item.ruta }}</td>
                        </tr>
                        <tr>
                            <th>Propietario</th>
                            <td class="flex flex-wrap gap-x items-center">
                                <span class="flex items-center gap-x" title="usuario">
                                    <Icon icon="ph:user-duotone" /> {{ item.propietario?.usuario.nombre }}
                                </span>
                                <span class="opacity-30">|</span>
                                <span class="flex items-center gap-x" title="grupo">
                                    <Icon icon="ph:users-three-duotone" /> {{ item.propietario?.grupo.nombre }}
                                </span>

                                <div v-if="item.propietario?.usuario.id == user?.id"
                                    class="badge badge-warning text-xs whitespace-nowrap">
                                    Eres el propietario</div>
                            </td>
                        </tr>
                        <tr v-if="!embed && propietarioRef">
                            <th><span v-if="propietarioRef.tipo == 'equipo'">Equipo propietario</span>
                                <span v-else>Usuario propietario</span>
                            </th>
                            <td class="flex flex-wrap gap-x items-center">
                                <Icon
                                    :icon="propietarioRef.tipo == 'equipo' ? 'ph:users-four-duotone' : 'ph:user-duotone'" />
                                <span>{{ propietarioRef.nombre }}</span>
                                <Link
                                    class="flex gap-x items-center btn btn-xs text-xs btn-neutral whitespace-nowrap"
                                    :href="propietarioRef.url" :title="tituloPropietario">
                                <span v-if="propietarioRef.tipo == 'equipo'">Ver equipo</span>
                                <span v-else>Ver usuario</span>
                                </Link>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td>
                                <TimeAgo :date="item.fecha_modificacion" />
                            </td>
                        </tr>
                        <tr>
                            <th>Nodo ID</th>
                            <td>{{ item.nodo_id }}</td>
                        </tr>
                        <tr>
                            <th class="align-top">Permisos</th>
                            <td>
                                <PermisosNodo :es-carpeta="item.tipo != 'archivo'" :permisos="item.permisos" />
                                <button v-if="esAdministrador || item.propietario?.usuario.id == user?.id"
                                    class="my-2 btn btn-xs btn-secondary text-xs"
                                    @click="abrirModalCambiarPermisos(item)">Cambiar permisos</button>
                            </td>
                        </tr>
                        <tr>
                            <th class="align-top">Acceso adicional</th>
                            <td>
                                <div v-if="item.acl && item.acl.length">
                                    <div v-for="acl of item.acl" :key="acl.id" class="flex gap-1 items-center">
                                        <PermisosAcl :acl="acl" :tipo="item.tipo" />
                                    </div>
                                </div>
                                <div v-else>
                                    No hay
                                </div>
                                <button v-if="esAdministrador || item.propietario?.usuario.id == user?.id"
                                    class="my-2 btn btn-xs btn-secondary text-xs"
                                    @click="abrirModalCambiarAcl(item)">Cambiar
                                    acceso</button>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">
                    <button @click.prevent="modalPropiedades = false" type="button" class="btn btn-neutral btn-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>



        <!-- Modal Cambiar Permisos -->
        <Modal :show="modalCambiarPermisos" @close="modalCambiarPermisos = false" maxWidth="sm">
            <div class="p-5">
                <div class="font-bold text-lg">Permisos</div>
                <form>
                    <p>/{{ itemCambiandoPermisos.ruta }}</p>
                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos de propietario:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit8" type="checkbox"
                                    v-model="permisosBits[8]"><label for="bit8">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit7" type="checkbox"
                                    v-model="permisosBits[7]"><label for="bit7">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit6" type="checkbox" v-model="permisosBits[6]"><label for="bit4">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos de grupo:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit5" type="checkbox"
                                    v-model="permisosBits[5]"><label for="bit5">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit4" type="checkbox"
                                    v-model="permisosBits[4]"><label for="bit4">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit3" type="checkbox" v-model="permisosBits[3]"><label for="bit3">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border border-solid border-neutral p-3 select-none">
                        <legend>Permisos públicos:</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit2" type="checkbox"
                                    v-model="permisosBits[2]"><label for="bit2">Leer</label></div>
                            <div class="flex gap-1 items-baseline"><input id="bit1" type="checkbox"
                                    v-model="permisosBits[1]"><label for="bit1">Escribir</label></div>
                            <div v-if="itemCambiandoPermisos.tipo == 'carpeta'" class="flex gap-1 items-baseline"><input
                                    id="bit0" type="checkbox" v-model="permisosBits[0]"><label for="bit0">Listar</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset v-if="itemCambiandoPermisos.tipo == 'carpeta'"
                        class="border border-solid border-neutral p-3 select-none">
                        <legend>Proteger contenidos (sticky bit):</legend>
                        <div class="flex gap-5">
                            <div class="flex gap-1 items-baseline"><input id="bit9" type="checkbox"
                                    v-model="permisosBits[9]"><label for="bit9">Proteger contenidos</label></div>
                        </div>
                        <small>solo los propietarios pueden modificar o eliminar sus contenidos</small>
                    </fieldset>

                    <fieldset class="mt-2 flex gap-2">
                        <label for="permi">Valor numérico:</label><input type="text" v-model="permisosNumerico"
                            class="max-w-[5rem]" @change="permisosNumericoChange" @keyup="permisosNumericoChange">
                    </fieldset>
                </form>

                <div class="py-3 flex justify-between sm:justify-end gap-5">


                    <button @click.prevent="cambiarPermisos" type="button" class="btn btn-primary btn-sm"
                        :disabled="guardandoPermisos">
                        <div v-if="guardandoPermisos" class="flex items-center gap-x">
                            <Spinner />
                            Guardando...
                        </div>
                        <span v-else>
                            Guardar
                        </span>
                    </button>

                    <button @click.prevent="modalCambiarPermisos = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </Modal>



        <!-- Modal Cambiar ACL -->
        <Modal :show="modalCambiarAcl" @close="modalCambiarAcl = false" maxWidth="md">
            <div class="p-5">
                <div class="font-bold text-lg">Acceso adicional</div>
                <form class="overflow-x-auto">
                    <p>/{{ itemCambiandoAcl.ruta }}</p>
                    <table v-if="itemCambiandoAcl?.aclEditar?.length">
                        <thead class="text-sm">
                            <tr>
                                <th></th>
                                <th>Usuario/Grupo</th>
                                <th>Leer</th>
                                <th>Escribir</th>
                                <th v-if="itemCambiandoAcl.tipo == 'carpeta'">Listar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="acl of itemCambiandoAcl.aclEditar" :key="acl.id">
                                <td :title="acl.usuario ? 'usuario' : 'grupo'">
                                    <Icon v-if="acl.usuario" icon="ph:user-duotone" />
                                    <Icon v-else icon="ph:users-three-duotone" />
                                </td>
                                <td :title="acl.usuario ? 'usuario' : 'grupo'"><span class="font-bold">{{ acl.usuario ||
                                    acl.grupo }}</span></td>
                                <td class="text-center"><input type="checkbox" v-model="acl.leer"></td>
                                <td class="text-center"><input type="checkbox" v-model="acl.escribir"></td>
                                <td v-if="itemCambiandoAcl.tipo == 'carpeta'" class="text-center"><input type="checkbox"
                                        v-model="acl.ejecutar"></td>
                                <td><button @click="eliminarAcl(acl.id)" title="eliminar acceso" class="flex">
                                        <Icon icon="ph:trash-duotone" />
                                    </button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else>
                        No hay accesos adicionales
                    </div>

                    <div class="flex gap-x my-4">
                        <button class="btn btn-xs text-xs btn-secondary" @click.prevent="abrirModalBuscarUsuario">+
                            Usuario</button>
                        <button class="btn btn-xs text-xs btn-secondary" @click.prevent="abrirModalBuscarGrupo">+
                            Grupo</button>
                    </div>

                </form>

                <div class="py-3 flex justify-between sm:justify-end gap-5">


                    <button @click.prevent="cambiarAcl" type="button" class="btn btn-primary btn-sm"
                        :disabled="guardandoAcl">
                        <div v-if="guardandoAcl" class="flex items-center gap-x">
                            <Spinner />
                            Guardando...
                        </div>
                        <span v-else>
                            Guardar
                        </span>
                    </button>

                    <button @click.prevent="modalCambiarAcl = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </Modal>



        <!-- Modal buscar usuario -->
        <Modal :show="modalBuscarUsuario" @close="modalBuscarUsuario = false" max-width="sm">
            <div class="p-5">
                <div class="font-bold text-lg">Buscar usuario</div>
                <input type="search" id="buscar_usuario"
                    class="input shadow flex-shrink-0 rounded-none border-b border-gray-500"
                    placeholder="Buscar usuario..." v-model="usuarioBuscar">

                <div class="overflow-y-auto max-h-[200px] shadow">
                    <table v-if="usuariosParaAgregar.length" class="table w-full bg-base-100 rounded-none">
                        <tbody class="divide-y">
                            <tr v-for="user of usuariosParaAgregar" :key="user.id">
                                <td>{{ user.nombre }}</td>
                                <td>
                                    <div v-if="user.acceso" class="btn bg-base-100 border-none pointer-events-none">
                                        <Icon icon="ph:check-circle-duotone" /> Ya tiene acceso
                                    </div>
                                    <div v-else class="btn" @click="agregarUsuarioAcl(user)">
                                        Seleccionar
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else-if="usuarioBuscar" class="p-2 bg-base-100">
                        No hay resultados
                    </div>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">

                    <button @click.prevent="modalBuscarUsuario = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>

                </div>
            </div>

        </Modal>



        <!-- Modal elegir grupo -->
        <Modal :show="modalBuscarGrupo" @close="modalBuscarGrupo = false" max-width="sm">
            <div class="p-5">
                <div class="font-bold text-lg">Buscar grupo</div>
                <select v-model="grupoElegido" class="select w-full border border-primary" placeholder="Elige un grupo">
                    <option v-for="grupo of gruposElegibles" :key="grupo.id" :value="grupo.id">{{ grupo.nombre }}
                    </option>
                </select>


                <div class="py-3 flex justify-between sm:justify-end gap-5">

                    <button @click.prevent="agregarGrupoAcl" type="button" class="btn btn-primary btn-sm">
                        Elegir
                    </button>

                    <button @click.prevent="modalBuscarGrupo = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>

                </div>
            </div>
        </Modal>



        <!-- Modal Renombrar Item -->
        <Modal :show="modalRenombrarItem" @close="modalRenombrarItem = false">

            <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="renombrarItem">
                <div class="flex flex-col gap-4">
                    <label for="nuevoNombre">Nuevo nombre:</label>
                    <div class="flex items-center gap-1 flex-wrap">
                        <div>{{ itemRenombrando.carpeta }}/</div>
                        <input id="nuevoNombre" v-model="nuevoNombre" type="text" required class="max-w-[32ch]">
                    </div>
                </div>

                <div class="py-3 flex justify-between sm:justify-end gap-5">

                    <button @click.prevent="renombrarItem" type="button" class="btn btn-primary btn-sm"
                        :disabled="renombrandoItem">
                        <div v-if="renombrandoItem" class="flex items-center gap-x">
                            <Spinner />
                            Renombrando...
                        </div>
                        <span v-else>
                            Renombrar
                        </span>
                    </button>

                    <button @click.prevent="modalRenombrarItem = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </Modal>



        <!-- Modal Confirmación de eliminar Archivo -->
        <ConfirmationModal :show="modalEliminarItem" @close="modalEliminarItem = false">
            <template #title>
                <div v-if="itemAEliminar">Confirmación de eliminación</div>
                <div v-else>Eliminar {{ plural(itemsSeleccionados.length, 'elemento') }}</div>
            </template>
            <template #content>
                <div v-if="itemAEliminar">
                    ¿Quieres eliminar {{ itemAEliminar.nombre }}?
                </div>
                <div v-else>
                    ¿Quieres eliminar {{ plural(itemsSeleccionados.length, 'elemento') }}?
                </div>
            </template>
            <template #footer>
                <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    @submit.prevent="eliminarArchivos">

                    <button @click.prevent="modalEliminarItem = false" type="button" class="btn btn-neutral btn-sm">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm">
                        Eliminar
                    </button>
                </form>
            </template>
        </ConfirmationModal>

        <slot />

        <Modal :show="mostrandoImagen" @close="mostrandoImagen = null" maxWidth="xl">
            <div class="bg-base-100 p-3">
                <img :src="mostrandoImagen.url + '?mw=700&mh=600'"
                    class="w-full max-h-[calc(100vh-170px)] object-contain" />

                <div class="flex pt-3 justify-between sm:justify-end gap-x flex-shrink-0">
                    <a download :href="mostrandoImagen.url" @click="mostrandoImagen = null" type="button"
                        class="btn btn-secondary flex gap-2 items-center">
                        <Icon icon="ph:download-duotone" class="text-xl" /> Descargar
                    </a>

                    <button @click.prevent="mostrandoImagen = null" type="button" class="btn btn-neutral">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>

    </div>
</template>

<script setup>
import Dropzone from 'vue2-dropzone-vue3'
import { usePage, Link } from '@inertiajs/vue3';
import useFilesOperation from '@/Stores/files';
import useSelectors from '@/Stores/selectors'
import { useDebounce } from '@vueuse/core';
import usePlayer from '@/Stores/player'
import usePermisos from '@/Stores/permisos'
import { TransitionGroup} from 'vue'

const TIEMPO_ACTIVACION_SELECCION = 1100
const TIEMPO_SELECCION_SIMPLE = 200

const permisos = usePermisos()

const esAdministrador = computed(() => !!permisos.permisos.filter(p => p == 'administrar archivos').length)

const props = defineProps({
    ruta: { type: String, required: true },
    rutaBase: { type: String, required: false, default: '' },
    items: Array,
    propietarioRef: Object,
    cargando: Boolean,
    embed: { type: Boolean, default: false },
    contentClass: String,
    rootLabel: { type: String, require: false },
    rootUrl: { type: String, require: false },
    mostrarRutas: { type: Boolean, default: false },
    mostrarMisArchivos: { type: [Boolean, String], default: true },
    modoInsertar: { type: Boolean, default: false },
});

const emit = defineEmits(['updated', 'disk', 'folder', 'file', 'insert']);

const transitionActive = ref(false)


// información más detallada de cada archivo (se carga después de mostrar el contenido de la carpeta)
const info_cargada = ref(false)
const info_archivos = ref({})

// Carga la información adicional de los contenidos de la carpeta actual
function cargarInfo() {
    info_cargada.value = false
    axios.get('/archivos_info' + '?ruta=/' + rutaActual.value)
        .then(response => {
            // info.value = response.data
            info_archivos.value = response.data
            info_cargada.value = true
        })
}

// lista de items (archivos y carpetas) que se va a mostrar
const itemsShow = ref([])

// genera la lista de items final
function calcularItems() {

    // para evitar modificación de la prop
    // https://github.com/inertiajs/inertia/issues/854#issuecomment-896089483
    const items = JSON.parse(JSON.stringify(props.items))

    const fields = ['nodo_id', 'puedeEscribir', 'puedeLeer', 'permisos', 'propietario', 'privada', 'acl', 'archivos', 'subcarpetas']
    console.log('info', info_archivos.value)
    for (const item of items) {
        const inf = info_archivos.value[item.nombre]
        if (inf) {
            for (const field of fields)
                item[field] = inf[field]
        }
    }
    console.log({items})

    itemsShow.value = items
}

calcularItems()

watch(() => props.items, () => {
    console.log('watch props.items')
    calcularItems()
    // itemsShow.value = JSON.parse(JSON.stringify(props.items))
    cargarInfo()
})

watch(info_archivos, () => {
    console.log('watch info_archivos')
    calcularItems()
})

// para reproducir audios
const player = usePlayer()

onMounted(() => {
    console.log('onmounted')
    // nextTick(() => {
        cargarInfo()
    // })

    document.addEventListener('keydown', onKeyDown);
})

onUnmounted(() => {
    document.removeEventListener('keydown', onKeyDown);
})

function onKeyDown(event) {
    //Si es Ctrl+I
    if (event.ctrlKey && event.key === 'i')
        selectors.mostrarPermisos = !selectors.mostrarPermisos
}

// estamos en la raíz?
const enRaiz = computed(() => props.items[1]?.tipo === 'disco' || props.items[0].ruta == 'mis_archivos')

// puede editar la carpeta actual?
const puedeEscribir = computed(() => esAdministrador.value || (itemsShow.value.length ? itemsShow.value[0].puedeEscribir : false))
const puedeLeer = computed(() => esAdministrador.value || (itemsShow.value.length ? itemsShow.value[0].puedeLeer : false))
const puedeMoverSeleccionados = computed(() => esAdministrador.value || itemsSeleccionados.value.find(item => item.puedeEscribir))
const puedeBorrarSeleccionados = computed(()=> puedeMoverSeleccionados.value)

// ruta actual
const rutaActual = computed(() => itemsShow.value.length ? itemsShow.value[0].ruta : '')

// otros datos
const page = usePage()
const user = computed(() => page.props?.auth?.user)

// ALGUNOS HELPERS PARA MOSTRAR DATOS

const tituloPropietario = computed(() => {
    if (!props.propietarioRef) return ''
    return 'Propietario: ' + props.propietarioRef.nombre
})


function nombreItem(item) {
    if (item.actual) return `<span class='text-neutral opacity-70'>&lt;${item.nombre}&gt;</span>`
    if (item.padre) return `<span class='text-neutral opacity-70'>&lt;arriba&gt;</span>`
    return item.nombre
}

function plural(count, label) {
    return `${count} ${label + (count != 1 ? 's' : '')}`
}

// BUSCAR ARCHIVOS

const showSearchInput = ref(false)
const inputSearch = ref(null)
const buscar = ref(null)
const mostrandoResultados = ref(false)
const resultadosBusqueda = ref([])
const buscando = ref(false)
const id_busqueda = ref(null)

function showSearch() {
    showSearchInput.value = true
    buscar.value = ""
    nextTick(() => {
        inputSearch.value.focus()
    })
}



function onSearch() {
    if (!buscar.value) {
        // cerramos el modal
        // showSearchInput.value = false
        return
    }
    showSearchInput.value = false
    buscando.value = true
    mostrandoResultados.value = true
    resultadosBusqueda.value = []
    axios('/archivos_buscar', {
        params: {
            ruta: rutaActual.value,
            nombre: buscar.value
        }
    })
        .then(response => {
            const data = response.data
            console.log({ data })
            id_busqueda.value = data.id_busqueda
            resultadosBusqueda.value = data.resultados
            buscarMasResultados()
        })
}

function buscarMasResultados() {
    if (!mostrandoResultados.value) return
    axios('/archivos_buscar?id_busqueda=' + id_busqueda.value)
        .then(response => {
            const data = response.data
            console.log({ data })
            for (const resultado of data.resultados) {
                // agregamos el resultado si acaso no estaba ya
                if (!resultadosBusqueda.value.find(item => item.ruta == resultado.ruta))
                    resultadosBusqueda.value.push(resultado)
            }
            if (data.finalizado)
                buscando.value = false // fin de la busqueda
            else
                buscarMasResultados()
        })
}

// ITEMS A MOSTRAR

const itemsMostrar = computed(() => mostrandoResultados.value ? resultadosBusqueda.value : itemsOrdenados.value)


// SELECCION

const seleccionando = ref(false)

function cancelarSeleccion() {
    seleccionando.value = false
    itemsShow.value.forEach(item => item.seleccionado = false)
}

function seleccionarTodos() {
    itemsShow.value.forEach(item => item.seleccionado = true)
}

// verifica que cuando no hay ningun item seleccionado, se termina el modo de selección
function verificarFinSeleccion() {
    if (!seleccionando.value) return
    if (screen.width >= 1024) return
    const alguno = itemsShow.value.find(item => item.seleccionado)
    if (!alguno)
        seleccionando.value = false
}

// si hay algun cambio en los items
watch(() => itemsShow, verificarFinSeleccion, { deep: true })

// EVENTOS TOUCH

const nav = useNav()
var scrollYOnTouch = -1

function ontouchstart(item) {
    console.log('touchstart')
    scrollYOnTouch = nav.scrollY
    item.touching = true
    if (seleccionando.value) {
        item.shortTouchTimer = setTimeout(() => {
            if(scrollYOnTouch != nav.scrollY) return
            item.seleccionado = !item.seleccionado
            console.log('item.seleccionado =', item.seleccionado)
        }, TIEMPO_SELECCION_SIMPLE);
    }
    else
        item.longTouchTimer = setTimeout(() => {
            if(scrollYOnTouch != nav.scrollY) {
                item.touching = false
                return
            }
            item.seleccionado = true;
            seleccionando.value = true;
            console.log('item.seleccionado2 =', item.seleccionado)

        }, TIEMPO_ACTIVACION_SELECCION); // tiempo en milisegundos para considerar un "long touch"
}

function ontouchend(item) {
    console.log('touchend')
    clearTimeout(item.longTouchTimer);
    // clearTimeout(item.shortTouchTimer);
    item.touching = false
}

function toggleItem(item) {
    console.log('toggleItem')
    if (!item.touching)
        item.seleccionado = !item.seleccionado
    item.touching = false
}

const itemsSeleccionados = computed(() => itemsShow.value.filter(item => !['.', '..'].includes(item.nombre) && item.seleccionado && !item.eliminado))


watch(() => itemsSeleccionados.value.length, (value) => {
    console.log('itemsSeleccionados.length=', value)
    if (!value)
        cancelarSeleccion()
})


// COPIAR Y MOVER ITEMS
let store = useFilesOperation()

const buscandoCarpetaDestino = computed(() => store.isMovingFiles || store.isCopyingFiles)

function prepararMoverItems() {
    seleccionando.value = false
    store.isMovingFiles = true
    store.sourcePath = rutaActual.value
    store.filesToMove = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function prepararCopiarItems() {
    seleccionando.value = false
    store.isCopyingFiles = true
    store.sourcePath = rutaActual.value
    store.filesToCopy = [...itemsSeleccionados.value.map(item => item.nombre)]
}

function copiarItems() {
    axios.post('/files/copy', {
        sourceFolder: store.sourcePath,
        targetFolder: rutaActual.value,
        items: store.filesToCopy
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al copiar los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function moverItems() {
    axios.post('/files/move', {
        sourceFolder: store.sourcePath,
        targetFolder: rutaActual.value,
        items: store.filesToMove
    }).then(response => {
        console.log({ response })
        reloadPage()
    })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al mover los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function cancelarOperacion() {
    console.log('cancelarOperacion')
    store.isMovingFiles = false
    store.isCopyingFiles = false
    store.filesToMove = []
    store.filesToCopy = []
    itemsShow.value.forEach(item => { item.seleccionado = false })
}


// SUBIR ARCHIVOS
const modalSubirArchivos = ref(false)

const dropzoneOptions = ref({
    url: '/files/upload/file', //route('files.upload.file'),
    thumbnailWidth: 150,
    maxFilesize: 50,
    headers: {
        'X-CSRF-Token': page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content,
    },
})


function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', rutaActual.value);
}


var someUploaded = ref(false)
function successEvent(file, response) {
    someUploaded.value = true
    //console.log('successEvent', props.ruta)
    //reloadPage()
}

watch(modalSubirArchivos, (value) => {
    if (value)
        someUploaded.value = false
    else if (someUploaded.value) {
        someUploaded.value = false
        // recargamos la vista
        reloadPage()
    }
})


// ORDENACION

const ordenaciones = {
    normal: 'Normal',
    fechaDesc: 'Recientes',
    fechaAsc: 'Antiguos',
    nombreAsc: 'A-Z',
    nombreDesc: 'Z-A',
    tamañoAsc: 'Pequeños',
    tamañoDesc: 'Grandes',
}


const ordenarPor = ref("normal")

const itemsOrdenados = computed(() => {
    // Separar las carpetas y los archivos en dos grupos
    var items = itemsShow.value.filter(item => !item.padre && !item.actual && !item.eliminado)

    // Mostramos "mis archivos" ?
    if (!props.mostrarMisArchivos)
        items = items.filter(item => item.tipo != 'disco' || item.nombre != 'mis_archivos')

    // carpetas y archivos
    const carpetas = items.filter(item => item.tipo === 'carpeta')
    const archivos = items.filter(item => item.tipo !== 'carpeta');

    switch (ordenarPor.value) {

        case 'normal':
            // Ordenar los archivos por fecha de modificación descendente
            carpetas.sort((a, b) => b.nombre.toUpperCase() - a.nombre.toUpperCase());
            archivos.sort((a, b) => b.fecha_modificacion - a.fecha_modificacion);
            return [...carpetas, ...archivos];

        case 'fechaAsc':
            // Ordenar los archivos por fecha de modificación ascendente
            carpetas.sort((a, b) => a.fecha_modificacion - b.fecha_modificacion);
            archivos.sort((a, b) => a.fecha_modificacion - b.fecha_modificacion);
            return [...carpetas, ...archivos];

        case 'fechaDesc':
            // Ordenar los archivos por fecha de modificación descendente
            carpetas.sort((a, b) => b.fecha_modificacion - a.fecha_modificacion);
            archivos.sort((a, b) => b.fecha_modificacion - a.fecha_modificacion);
            return [...archivos, ...carpetas];

        case 'nombreAsc':
            // Ordenar todos los elementos por nombre ascendente
            return items.sort((a, b) => a.nombre.localeCompare(b.nombre));

        case 'nombreDesc':
            // Ordenar todos los elementos por nombre descendente
            return items.sort((a, b) => b.nombre.localeCompare(a.nombre));

        case 'tamañoAsc':
            // Ordenar los archivos por tamaño ascendente
            archivos.sort((a, b) => a.tamano - b.tamano);
            return [...archivos, ...carpetas];

        case 'tamañoDesc':
            // Ordenar los archivos por tamaño descendente
            archivos.sort((a, b) => b.tamano - a.tamano);
            return [...archivos, ...carpetas];

        default:
            // Si el criterio de ordenación no coincide, devolver el listado sin cambios
            return items;
    }
});


/* function crearCarpetaKeydown(event) {
    if (event.keyCode === 27) {
        modalCrearCarpeta.value = false;
        // La tecla ESC fue presionada
    }
} */

// PROPIEDADES

const modalPropiedades = ref(false)
const itemsPropiedades = ref(null)
function abrirModalPropiedades(item) {
    itemsPropiedades.value = item ? [item] : itemsSeleccionados.value
    modalPropiedades.value = true
}

watch(modalPropiedades, (newValue) => {
    if (!newValue && permisosModificados.value) {
        // actualizamos vista de la carpeta
        reloadPage()
    }
})

// PERMISOS

const modalCambiarPermisos = ref(false)
const itemCambiandoPermisos = ref(null)
const permisosModificados = ref(false)
const permisosBits = ref([])
const permisosNumerico = ref("")
const permisosNumericoComputed = computed(() => {
    let p = 0
    for (let i = 0; i < 10; i++)
        p = p + permisosBits.value[i] * Math.pow(2, i)
    return p.toString(8)
})
const guardandoPermisos = ref(false)

watch(permisosNumericoComputed, (value) => {
    console.log('permisosBits change', value)
    permisosNumerico.value = permisosNumericoComputed.value
})

function calcularPermisosBits() {
    const octalPermisos = permisosNumerico.value // Obtener el número en octal como una cadena de texto
    const decimalPermisos = parseInt(octalPermisos, 8); // Convertir el número octal a decimal
    permisosBits.value = []; // Limpiar el array de bits
    for (let i = 0; i < 10; i++) {
        const bit = (decimalPermisos >> i) & 1; // Extraer el bit en la posición i
        permisosBits.value.push(!!bit); // Agregar el bit al array de bits
    }
}

function abrirModalCambiarPermisos(item) {
    guardandoPermisos.value = false
    itemCambiandoPermisos.value = item
    modalCambiarPermisos.value = true
    permisosNumerico.value = item.permisos
    calcularPermisosBits()
}

function permisosNumericoChange() {
    if (permisosNumerico.value.length >= 3)
        calcularPermisosBits()
}

function cambiarPermisos() {
    guardandoPermisos.value = true
    axios.post('/files/update', {
        ruta: itemCambiandoPermisos.value.ruta,
        permisos: permisosNumerico.value
    })
        .then(response => {
            console.log(response.data)
            // cierra el modal y actualiza los permisos
            itemCambiandoPermisos.value.permisos = permisosNumerico.value
            permisosModificados.value = true
            modalCambiarPermisos.value = false
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al guardar los cambios'
            alert(errorMessage)
            guardandoPermisos.value = false
        })
}


// ACL

const modalCambiarAcl = ref(false)
const itemCambiandoAcl = ref(null)
const guardandoAcl = ref(false)
// agregar usuario:
const modalBuscarUsuario = ref(false)
const usuarioBuscar = ref("")
const debouncedBuscar = useDebounce(usuarioBuscar, 800);
const usuariosEncontrados = ref([]);
const usuariosParaAgregar = computed(() => usuariosEncontrados.value
    .map(u => ({
        ...u,
        acceso: itemCambiandoAcl.value.propietario.usuario?.id == u.id  //  no es el propietario,
            || itemCambiandoAcl.value.aclEditar.find(acl => acl.user_id == u.id) // y no está ya en la lista de acceso
    })
    ))
// agregar grupo
const modalBuscarGrupo = ref(false)
const grupoElegido = ref(null)
const grupos = ref([])
const gruposElegibles = computed(() => grupos.value.filter(g => !itemCambiandoAcl.value.aclEditar.find(acl => acl.group_id == g.id)))

watch(debouncedBuscar, buscarUsuarios)

function abrirModalCambiarAcl(item) {
    itemCambiandoAcl.value = item
    if (!item.acl)
        item.acl = []
    // guardamos los valores antes de la edición
    itemCambiandoAcl.value.aclEditar = [...itemCambiandoAcl.value.acl]
    itemCambiandoAcl.value.aclEditar.forEach(acl => {
        ['leer', 'escribir', 'ejecutar'].forEach(verbo => acl[verbo] = !!acl.verbos.match(verbo))
    })
    modalCambiarAcl.value = true
    guardandoAcl.value = false
}


function eliminarAcl(id) {
    const idx = itemCambiandoAcl.value.aclEditar.findIndex(acl => acl.id == id)
    if (idx == -1) {
        alert("Hubo un error. Comunicarlo al administrador")
        return
    }
    itemCambiandoAcl.value.aclEditar.splice(idx, 1)
}

function abrirModalBuscarUsuario() {
    modalBuscarUsuario.value = true
    setTimeout(() => {
        if (modalBuscarUsuario.value)
            document.querySelector('#buscar_usuario').focus()
    }, 500)
}

function buscarUsuarios() {
    const query = debouncedBuscar.value.trim();

    if (query.length >= 3) {
        axios
            .get(route('usuarios.buscar', query))
            .then(response => {
                console.log('response', response.data)
                usuariosEncontrados.value = response.data;
            })
            .catch(error => {
                console.error(error);
            });
    }
    else usuariosEncontrados.value = []
}

function agregarUsuarioAcl(user) {
    itemCambiandoAcl.value.aclEditar.push({
        id: -10000 - user.id,
        user_id: user.id,
        leer: false,
        escribir: false,
        ejecutar: false,
        usuario: user.name
    })
    modalBuscarUsuario.value = false
    usuarioBuscar.value = ""
}


function abrirModalBuscarGrupo() {
    grupoElegido.value = null
    modalBuscarGrupo.value = true
    if (!grupos.value.length) {
        fetch(route('grupos'))
            .then(response => response.json())
            .then(response => {
                console.log({ response })
                grupos.value = response
            })
    }
}

function agregarGrupoAcl() {
    const grupo = grupos.value.find(g => g.id == grupoElegido.value)
    itemCambiandoAcl.value.aclEditar.push({
        id: -grupo.id,
        group_id: grupo.id,
        leer: false,
        escribir: false,
        ejecutar: false,
        grupo: grupo.nombre
    })
    modalBuscarGrupo.value = false
    grupoElegido.value = null
}

function cambiarAcl() {
    var newAcl = JSON.parse(JSON.stringify(itemCambiandoAcl.value.aclEditar))
    newAcl.forEach(acl => {
        acl.verbos = ['leer', 'escribir', 'ejecutar'].filter(verbo => acl[verbo]).join(',')
        delete acl.leer
        delete acl.escribir
        delete acl.ejecutar
        delete acl.usuario
        delete acl.grupo
        delete acl.updated_at
        delete acl.created_at
    })
    newAcl = newAcl.filter(acl => acl.verbos)
    guardandoAcl.value = true
    axios.post('/files/update', {
        ruta: itemCambiandoAcl.value.ruta,
        acl: newAcl
    })
        .then(response => {
            console.log({ response })
            // cierra el modal y actualiza los permisos
            itemCambiandoAcl.value.acl = response.data.acl
            permisosModificados.value = true
            modalCambiarAcl.value = false
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al guardar los cambios'
            alert(errorMessage)
            guardandoAcl.value = false
        })
}



// RENOMBRAR ITEM
const nuevoNombre = ref("")
const itemRenombrando = ref(null)
const modalRenombrarItem = ref(false)
const renombrandoItem = ref(false)

function abrirModalRenombrar(item) {
    // item.seleccionado = false // para el caso de renombrar un item seleccionado
    renombrandoItem.value = false
    itemRenombrando.value = item
    nuevoNombre.value = item.nombre
    modalRenombrarItem.value = true
    setTimeout(() => {
        if (modalRenombrarItem.value)
            document.querySelector('#nuevoNombre').focus()
    }, 500)
}

function renombrarItem() {
    itemRenombrando.value.seleccionado = false
    renombrandoItem.value = true
    axios.post('/files/rename', {
        folder: itemRenombrando.value.carpeta,
        oldName: itemRenombrando.value.nombre,
        newName: nuevoNombre.value,
    })
        .then(response => {
            console.log({ response })
            modalRenombrarItem.value = false
            const item = itemRenombrando.value // itemsComplete.find(it => it.nombre == itemRenombrando.value.nombre)
            // console.log('renombrar item', item)
            item.ruta = item.carpeta + '/' + nuevoNombre.value
            const parts = item.url.split('/')
            parts[parts.length - 1] = parts[parts.length - 1].replace(item.nombre, nuevoNombre.value)
            item.url = parts.join('/')
            item.nombre = nuevoNombre.value
            if (!props.embed)
                if (item.actual) {
                    // reemplazar la URL actual en el historial del navegador
                    router.replace(item.url);

                    // reemplazar el título de la página
                    document.title = item.ruta
                }
            // else
            // reloadPage()
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al renombrar el elemento'
            alert(errorMessage)
            renombrandoItem.value = false
        })
}

// CREAR CARPETA

const modalCrearCarpeta = ref(false)
const nombreCarpeta = ref("")
const creandoCarpeta = ref(false)

function abrirModalCrearCarpeta() {
    creandoCarpeta.value = false
    modalCrearCarpeta.value = true
    nombreCarpeta.value = ""
    setTimeout(() => {
        if (modalCrearCarpeta.value)
            document.querySelector('#nombreCarpeta').focus()
    }, 500)
}

function crearCarpeta() {
    creandoCarpeta.value = true
    if (!nombreCarpeta.value) return

    axios.put('/files/mkdir', {
        folder: rutaActual.value, name: nombreCarpeta.value
    }).then((response) => {
        console.log({ response })
        modalCrearCarpeta.value = false
        reloadPage()
    })
        .catch(err => {
            console.log({ err })
            alert(err.response.data.error)
            creandoCarpeta.value = false
        })
}


// ELIMINAR

const itemAEliminar = ref("")
const modalEliminarItem = ref(false)

function abrirEliminarModal(item) {
    itemAEliminar.value = item
    modalEliminarItem.value = true
}

function eliminarArchivos() {
    console.log('eliminarArchivos')
    if (itemAEliminar.value)
        eliminarArchivo(itemAEliminar.value)
    else {
        for (var item of itemsSeleccionados.value)
            eliminarArchivo(item);
    }
    modalEliminarItem.value = false
}

function eliminarArchivo(item) {
    console.log('eloiminar¡', item)
    const url = '/files' + ('/' + item.ruta).replace(/\/\//g, '/').replace(/%2F/g, '/')
    console.log({ url })
    return axios.delete(url)
        .then(response => {
            item.eliminado = true
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al eliminar el archivo ' + item.nombre
            alert(errorMessage)
        })
}


// TIPO DE ITEM: ES IMAGEN?
function isImage(fileName) {
    if (!fileName || (typeof fileName != 'string')) return false
    const ext = fileName.split('.').pop().toLowerCase();

    switch (ext) {
        case 'svg':
        case 'jpg':
        case 'jpeg':
        case 'webp':
        case 'png': return true;
    }
    return false
}



// VISTA DE ITEMS
const selectors = useSelectors()
if (!['lista', 'grid'].includes(selectors.archivosVista))
    selectors.archivosVista = 'lista'


console.log(selectors.value)
const toggleVista = () => {
    selectors.archivosVista = selectors.archivosVista === 'grid' ? 'lista' : 'grid';
    console.log(selectors.value)
}

function reloadPage() {
    emit('updated')
}



// copia datos de una fuente (de tipo objeto o de tipo array) a un destino, concretamente la clave k
// de forma que mantiene la reactividad
function copyData(dest, src, key) {
    // primera llamada
    if (!key) {
        for (const key in src)
            copyData(dest, src, key)
        return
    }
    // si es un objeto, copiamos con recursividad
    if (typeof src[key] === 'object') {
        console.log('copiando', key)
        if (!dest[key])
            dest[key] = {}
        for (const subkey in src[key])
            copyData(dest[key], src[key], subkey)
        return
    }

    if (src[key])
        dest[key] = src[key]
}



// MOSTRANDO IMAGEN

const mostrandoImagen = ref(null)

// EMBED


function clickDisk(item, event) {
    console.log('clickDisk', item)
    emit('disk', item)
    event.preventDefault()
}
function clickFolder(item, event) {
    console.log('clickFolder', item)
    if (seleccionando.value) {
        item.seleccionado = !item.seleccionado
        event.preventDefault()
    }
    else
        if (props.embed) {
            emit('folder', item)
            event.preventDefault()
        }
}

function clickFile(item, event) {
    console.log('clickFile', item)
    if (seleccionando.value) {
        item.seleccionado = !item.seleccionado
        event.preventDefault()
    }
    else
        if (props.embed) {
            emit('file', item)
            event.preventDefault()
        }
        else if (item.url.match(/\.(gif|png|webp|svg|jpe?g)$/i)) {
            mostrandoImagen.value = item
            event.preventDefault()
        }
        else {
            // si es un audio:
            if (player.isPlayable(item.url)) {
                player.play(item.url, item.nombre)
                event.preventDefault()
            }
        }
}

function clickBreadcrumb(item) {
    console.log('clickBreadcrumb', { item })
    if (props.embed) {
        emit('folder', { ...item, ruta: item.url })
    }
}


// EMBED

const imagenesSeleccionadas = computed(() => itemsSeleccionados.value.filter(item => item.nombre.match(/.*\.(jpe?g|webp|svg|png|gif|pcx|bmp)$/i)))

// embed
function insertarImagenes() {
    console.log('insertarImagenes', imagenesSeleccionadas.value)
    emit('images', imagenesSeleccionadas.value)
}


</script>








<style scoped>
table td,
table th {
    @apply px-2;
}

.lista {
    display: block;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

@media (min-width:768px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
}

@media (min-width:1024px) {
    .grid {
        gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}
</style>


<style>
.vue-dropzone {
    @apply bg-base-100;
    border-radius: 5px;
    border: 2px dashed rgb(0, 135, 247);
    border-image: none;
    margin-left: auto;
    margin-right: auto;
}

.vue-dropzone>.dz-preview .dz-success-mark,
.vue-dropzone>.dz-preview .dz-error-mark {
    width: unset;
    left: calc(50% - 25px);
}

/* base */
.file {
    backface-visibility: hidden;
    z-index: 1;
}

/* moving */
.animating .files-move {
    transition: all 600ms ease-in-out 50ms;
}

/* appearing */
.animating .files-enter-active {
    transition: all 400ms ease-out;
    transform: scale(0);
}

/* disappearing */
.animating .files-leave-active {
    transition: all 200ms ease-in;
    position: absolute;
    z-index: 0;
    transform: scale(0);
}

/* appear at / disappear to */
.animating .files-enter-to {
    opacity: 1;
    transform: scale(1);
}

.animating .files-leave-to {
    opacity: 0;
    transform: scale(0);
}

.propiedades th {
    text-align: left
}

.gap-x {
    @apply gap-1 xs:gap-2 sm:gap-3;
}

.btn-icon {
    @apply w-[40px] sm:w-[46px];
}
</style>
