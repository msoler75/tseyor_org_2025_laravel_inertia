{{-- galeria_items field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<style>
#galeria-items-table .drag-handle {
    color: #6c757d;
    transition: all 0.2s;
    padding: 8px;
    border-radius: 4px;
    text-align: center;
}
#galeria-items-table .drag-handle:hover {
    color: #007bff;
    background-color: #f8f9fa;
    transform: scale(1.1);
}
#galeria-items-table tbody tr {
    transition: background-color 0.2s;
}
#galeria-items-table tbody tr:hover .drag-handle {
    color: #007bff;
    background-color: #e9ecef;
}
#galeria-items-table.sortable-ghost {
    opacity: 0.4;
    background-color: #e9ecef;
}
#galeria-items-table tbody tr.sortable-chosen .drag-handle {
    color: #28a745;
    background-color: #d4edda;
}
</style>

<div class="galeria-items-field">
    <div class="mb-3">
        <button type="button" class="btn btn-primary btn-sm" onclick="scanGaleriaFolder({{ $entry->id ?? 'null' }})">
            🔄 Escanear Carpeta
        </button>
        <small class="text-muted ml-2">
            ↕️ Arrastra las filas usando ↕️ para reordenar los items
        </small>
    </div>

    <div class="table-responsive">
        <table id="galeria-items-table" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;"><span title="Arrastrar para reordenar">☰</span></th>
                    <th>Portada</th>
                    <th>Imagen</th>
                    <th>Archivo</th>
                    <th>Título + Descripción</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($entry) && $entry->items)
                    @foreach($entry->items->sortBy('orden') as $item)
                    <tr data-id="{{ $item->id }}">
                        <td class="drag-handle" style="cursor: move;" title="Arrastrar para reordenar">
                            ↕️
                        </td>
                        <td style="text-align: center;">
                            @if($item->nodo && !$item->nodo->es_carpeta)
                                @php
                                    $extension = strtolower(pathinfo($item->nodo->ubicacion, PATHINFO_EXTENSION));
                                    $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico']);
                                @endphp
                                @if($esImagen)
                                <input type="checkbox"
                                       name="portada_item"
                                       value="{{ $item->id }}"
                                       class="portada-checkbox"
                                       {{ $entry->imagen == $item->nodo->ubicacion ? 'checked' : '' }}
                                       onchange="seleccionarPortada(this, {{ $item->id }})">
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($item->nodo && !$item->nodo->es_carpeta)
                                <img src="{{ $item->nodo->url_archivo }}"
                                     alt="{{ basename($item->nodo->ubicacion) }}"
                                     style="max-width: 100px; max-height: 100px; object-fit: cover;">
                            @else
                                📁
                            @endif
                        </td>
                        <td>{{ $item->nodo ? basename($item->nodo->ubicacion) : 'N/A' }}</td>
                        <td>
                            <input type="hidden"
                                   name="items[{{ $item->id }}][orden]"
                                   value="{{ $item->orden ?? $loop->index + 1 }}">
                            <input type="text"
                                   name="items[{{ $item->id }}][titulo]"
                                   class="form-control form-control-sm mb-1"
                                   placeholder="Título (por defecto: nombre del archivo)"
                                   value="{{ $item->titulo }}">
                            <textarea name="items[{{ $item->id }}][descripcion]"
                                      class="form-control form-control-sm"
                                      rows="2"
                                      placeholder="Descripción">{{ $item->descripcion }}</textarea>
                        </td>
                        <td>
                            <select name="items[{{ $item->id }}][user_id]" class="form-control form-control-sm">
                                <option value="">Sin asignar</option>
                                @if(isset($field['attributes']['users']))
                                    @foreach($field['attributes']['users'] as $user)
                                    <option value="{{ $user->id }}" {{ $item->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @if(!isset($entry) || !$entry->items || $entry->items->isEmpty())
    <div class="alert alert-info">
        ℹ️ No hay items en esta galería. Use "Releer Carpeta" para escanear archivos.
    </div>
    @endif
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif

<script>
function scanGaleriaFolder(galeriaId) {
    if (!galeriaId) {
        alert('Guarde primero la galería para poder escanear la carpeta.');
        return;
    }

    if (confirm('¿Está seguro de que desea escanear la carpeta? Se actualizará la lista de items.')) {
        // Mostrar indicador de carga
        var button = event.target.closest('button');
        var originalText = button.innerHTML;
        button.innerHTML = '⏳ Escaneando...';
        button.disabled = true;

        // Hacer petición AJAX
        fetch('/admin/galeria/' + galeriaId + '/scan', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // Recargar la página para mostrar los nuevos items
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error al procesar la solicitud: ' + error.message);
        })
        .finally(() => {
            // Restaurar el botón
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Inicializar drag and drop cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    var tableBody = document.querySelector('#galeria-items-table tbody');

    if (tableBody && typeof Sortable !== 'undefined') {
        Sortable.create(tableBody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                // Actualizar los valores de orden después del drag
                updateOrdenValues();
            }
        });
    }
});

function seleccionarPortada(checkbox, itemId) {
    if (checkbox.checked) {
        // Desmarcar todos los otros checkboxes
        var checkboxes = document.querySelectorAll('.portada-checkbox');
        checkboxes.forEach(function(cb) {
            if (cb !== checkbox) {
                cb.checked = false;
            }
        });
    }
}

function updateOrdenValues() {
    var rows = document.querySelectorAll('#galeria-items-table tbody tr');
    rows.forEach(function(row, index) {
        // Buscar el input hidden de orden en esta fila
        var ordenInput = row.querySelector('input[name*="[orden]"]');
        if (ordenInput) {
            ordenInput.value = index + 1;
        }
    });
}
</script>

{{-- Incluir SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

@include('crud::fields.inc.wrapper_end')
