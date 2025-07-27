<div class="card mb-3">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="la la-tasks"></i> Acciones Masivas
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="tutor_asignar">Asignar Tutor a Inscripciones Seleccionadas:</label>
                    <select id="tutor_asignar" class="form-control">
                        <option value="">Seleccionar tutor...</option>
                        @php
                            $tutoresActivos = \App\Models\User::whereHas('inscripcionesAsignadas')
                                ->orWhereHas('roles', function($q) {
                                    $q->whereIn('name', ['admin', 'tutor']);
                                })
                                ->orderBy('name')
                                ->get();
                        @endphp
                        @foreach($tutoresActivos as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Solo se muestran usuarios con rol de tutor o que ya tienen inscripciones asignadas</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="button"
                            class="btn btn-secondary"
                            onclick="asignarMasiva()"
                            id="btn_asignar_masiva"
                            disabled>
                        <i class="la la-user-plus"></i> Asignar Seleccionadas
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <small class="text-muted">
                    <i class="la la-info-circle"></i>
                    Selecciona inscripciones usando los checkboxes de la tabla y luego elige un tutor para asignarlas masivamente.
                </small>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de acciones masivas cargado');
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('table input[type="checkbox"]')) {
            setTimeout(updateMassiveActionButton, 100); // timeout por si el checkbox fue el de de/seleccionar todos
        }
    });
    updateMassiveActionButton();
});


function updateMassiveActionButton() {
    // Actualizar debug info si está visible
    const debugDiv = document.getElementById('debug_info');
    if (debugDiv && debugDiv.style.display !== 'none') {
        updateDebugInfo();
    }
    // Buscar checkboxes seleccionados con múltiples estrategias
    let selectedCheckboxes = [];

    const selectors = [
        'input.crud_bulk_actions_line_checkbox:checked', // Backpack bulk checkboxes seleccionados
        'table tbody input[type="checkbox"]:checked[name*="bulk_actions"]',
        'table tbody input[type="checkbox"]:checked',
        'input[type="checkbox"]:checked[data-pk]',
        '.crud-table input[type="checkbox"]:checked',
        'td input[type="checkbox"]:checked'
    ];

    for (let selector of selectors) {
        const found = document.querySelectorAll(selector);
        if (found.length > 0) {
            selectedCheckboxes = Array.from(found).filter(cb =>
                !cb.id.includes('bulk_select_all') &&
                !cb.id.includes('select_all') &&
                !cb.id.includes('check-all') &&
                cb.closest('tbody') // Solo checkboxes del cuerpo de la tabla
            );
            if (selectedCheckboxes.length > 0) break;
        }
    }

    console.log('Checkboxes seleccionados:', selectedCheckboxes.length);

    const button = document.getElementById('btn_asignar_masiva');
    if (!button) return;

    if (selectedCheckboxes.length > 0) {
        button.disabled = false;
        button.innerHTML = `<i class="la la-user-plus"></i> Asignar ${selectedCheckboxes.length} Seleccionadas`;
        button.classList.remove('btn-secondary');
        button.classList.add('btn-success');
    } else {
        button.disabled = true;
        button.innerHTML = '<i class="la la-user-plus"></i> Asignar Seleccionadas';
        button.classList.remove('btn-success');
        button.classList.add('btn-secondary');
    }
}

function asignarMasiva() {
    // Buscar checkboxes seleccionados con múltiples estrategias
    let selectedCheckboxes = [];

    const selectors = [
        'table tbody input[type="checkbox"]:checked[name*="bulk_actions"]',
        'table tbody input[type="checkbox"]:checked',
        'input[type="checkbox"]:checked[data-pk]',
        '.crud-table input[type="checkbox"]:checked',
        'td input[type="checkbox"]:checked'
    ];

    for (let selector of selectors) {
        const found = document.querySelectorAll(selector);
        if (found.length > 0) {
            selectedCheckboxes = Array.from(found).filter(cb =>
                !cb.id.includes('bulk_select_all') &&
                !cb.id.includes('select_all') &&
                !cb.id.includes('check-all') &&
                cb.closest('tbody') // Solo checkboxes del cuerpo de la tabla
            );
            if (selectedCheckboxes.length > 0) break;
        }
    }

    const tutorId = document.getElementById('tutor_asignar').value;

    if (selectedCheckboxes.length === 0) {
        alert('Selecciona al menos una inscripción usando los checkboxes de la tabla');
        return;
    }

    if (!tutorId) {
        alert('Selecciona un tutor');
        return;
    }

    // Obtener IDs de las inscripciones seleccionadas usando múltiples métodos
    const inscripcionIds = Array.from(selectedCheckboxes).map(cb => {
        // 1. Si el value es un número válido, usarlo directamente
        if (cb.value && cb.value.match(/^\d+$/)) {
            return cb.value;
        }
        // 2. data-primary-key-value (Backpack actual)
        if (cb.getAttribute('data-primary-key-value')) {
            return cb.getAttribute('data-primary-key-value');
        }
        // 3. data-pk
        if (cb.getAttribute('data-pk')) {
            return cb.getAttribute('data-pk');
        }
        // 4. Buscar en la fila (tr) atributos data-*
        const row = cb.closest('tr');
        if (row) {
            if (row.getAttribute('data-entry-id')) {
                return row.getAttribute('data-entry-id');
            }
            if (row.getAttribute('data-id')) {
                return row.getAttribute('data-id');
            }
            if (row.getAttribute('data-key')) {
                return row.getAttribute('data-key');
            }
            // Buscar el ID en diferentes celdas de la fila
            const cells = row.querySelectorAll('td');
            for (let i = 0; i < Math.min(3, cells.length); i++) {
                const cell = cells[i];
                const text = cell.textContent.trim();
                const idMatch = text.match(/^\d+$/);
                if (idMatch && parseInt(idMatch[0]) > 0) {
                    return idMatch[0];
                }
            }
            // Buscar en elementos con clase o atributos específicos
            const idElement = row.querySelector('[data-primary-key], [data-id], .entry-id');
            if (idElement) {
                const id = idElement.getAttribute('data-primary-key') ||
                           idElement.getAttribute('data-id') ||
                           idElement.textContent.trim();
                if (id && id.match(/^\d+$/)) {
                    return id;
                }
            }
        }
        return null;
    }).filter(id => id && id !== null && id !== 'undefined');

    console.log('IDs encontrados:', inscripcionIds);

    if (inscripcionIds.length === 0) {
        alert('No se pudieron obtener los IDs de las inscripciones seleccionadas. Verifica que la tabla esté cargada correctamente.');
        return;
    }

    const tutorName = document.getElementById('tutor_asignar').selectedOptions[0].text;

    if (confirm(`¿Asignar ${inscripcionIds.length} inscripciones a ${tutorName}?`)) {
        fetch('{{ route("admin.inscripcion.asignar-masiva") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                inscripcion_ids: inscripcionIds,
                user_id: tutorId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                let successMessage = data.message;
                if (typeof Noty !== 'undefined') {
                    new Noty({
                        text: successMessage.replace(/\n/g, '<br>'),
                        type: 'success',
                        timeout: 8000
                    }).show();
                } else {
                    alert(successMessage);
                }
                window.location.href = window.location.href.split('?')[0];
            } else {
                let errorMessage = 'Error: ' + data.message;
                if (typeof Noty !== 'undefined') {
                    new Noty({
                        text: errorMessage,
                        type: 'error'
                    }).show();
                } else {
                    alert(errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al asignar inscripciones');
        });
    }
}
</script>
