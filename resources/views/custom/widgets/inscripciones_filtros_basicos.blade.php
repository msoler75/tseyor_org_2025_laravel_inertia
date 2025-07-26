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
                    <select name="estado" id="estado_filter" class="form-control" onchange="this.form.submit()" autocomplete="off">
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
                    <select name="tutor" id="tutor_filter" class="form-control" onchange="this.form.submit()" autocomplete="off">
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
                           placeholder="Nombre o email..."
                           value="{{ request('buscar') }}"
                           autocomplete="off">
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
                    <button type="button" onclick="limpiarTodoYRedirigir()" class="btn btn-link p-0 text-primary" style="text-decoration: underline;">Ver todos</button>
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
    
    // Limpiar solo claves específicas relacionadas con filtros (no todo el storage)
    localStorage.removeItem('crud_filters');
    localStorage.removeItem('inscripcion_filters');
    localStorage.removeItem('backpack_filters');
    localStorage.removeItem('admin_filters');
    sessionStorage.removeItem('crud_filters');
    sessionStorage.removeItem('inscripcion_filters');
    sessionStorage.removeItem('backpack_filters');
    sessionStorage.removeItem('admin_filters');
    
    // Usar replace para no mantener en el historial
    window.location.replace('/admin/inscripcion');
}
</script>
