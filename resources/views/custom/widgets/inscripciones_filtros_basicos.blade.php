<div class="card mb-3">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="la la-filter"></i> Filtros Básicos
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ request()->url() }}" class="row" autocomplete="off">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="estado_filter">Estado:</label>
                    <select name="estado" id="estado_filter" class="form-control" onchange="reiniciarPaginacionYSubmit(this.form)" autocomplete="off">
                        <option value="">Todos los estados</option>
                        @foreach(config('inscripciones.estados') as $key => $valores)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $valores['etiqueta'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="tutor_filter">Tutor:</label>
                    <select name="tutor" id="tutor_filter" class="form-control" onchange="reiniciarPaginacionYSubmit(this.form)" autocomplete="off">
                        <option value="">Todos los tutores</option>
                        @php
                            $tutores = \App\Models\User::whereHas('inscripcionesAsignadas')->orderBy('name')->get();
                        @endphp
                        @foreach($tutores as $tutor)
                            <option value="{{ $tutor->id }}" {{ request('tutor') == $tutor->id ? 'selected' : '' }}>
                                {{ $tutor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="buscar">Buscar:</label>
                    <input type="text"
                           name="buscar"
                           id="buscar"
                           class="form-control"
                           placeholder="Nombre, email, ciudad..."
                           value="{{ request('buscar') }}"
                           autocomplete="off"
                           onkeydown="if(event.key==='Enter'){reiniciarPaginacionYSubmit(this.form); return false;}" >
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-search"></i> Filtrar
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" onclick="limpiarTodoYRedirigir()">
                        <i class="la la-refresh"></i> Limpiar
                    </button>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['estado', 'tutor', 'buscar']))
            <div class="mt-3">
                <small class="text-info">
                    <i class="la la-info-circle"></i>
                    Mostrando resultados filtrados.
                </small>
            </div>
        @endif
    </div>
</div>

<script>
function limpiarTodoYRedirigir() {
    // Limpiar formulario
    document.getElementById('estado_filter').value = '';
    document.getElementById('tutor_filter').value = '';
    document.getElementById('buscar').value = '';
    // Eliminar la clave específica de DataTables/Backpack para la tabla de inscripciones
    localStorage.removeItem('DataTables_crudTable_/admin/inscripcion');
    localStorage.removeItem('admininscripcion_list_url');
    // Redirigir a la URL base sin parámetros
    let baseUrl = window.location.pathname;
    window.location.replace(baseUrl);
}

function reiniciarPaginacionYSubmit(form) {
    // Elimina el parámetro page si existe
    let pageInput = form.querySelector('input[name="page"]');
    if(pageInput) pageInput.value = 1;
    // También eliminar de la URL si está presente
    let url = new URL(window.location.href);
    url.searchParams.set('page', 1);
    // Cambia la acción temporalmente para forzar page=1
    form.action = url.pathname + url.search.replace(/([&?])page=\d+/,'$1page=1');
    form.submit();
}
</script>
