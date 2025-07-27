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

    // Esperar a que la tabla se cargue completamente
    setTimeout(function() {
        console.log('Configurando listeners de checkboxes...');
        setupCheckboxListeners();
    }, 1000);

    // Observer para cambios dinámicos en la tabla
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                setTimeout(setupCheckboxListeners, 100);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

function setupCheckboxListeners() {
    // Buscar todos los tipos de checkboxes posibles en Backpack
    const selectors = [
        'table tbody input[type="checkbox"][name*="bulk_actions"]',
        'table tbody input[type="checkbox"]',
        'input[type="checkbox"][data-pk]',
        '.crud-table input[type="checkbox"]',
        'td input[type="checkbox"]',
        '[data-toggle="table"] input[type="checkbox"]'
    ];

    let checkboxes = [];

    // Probar cada selector hasta encontrar checkboxes
    for (let selector of selectors) {
        const found = document.querySelectorAll(selector);
        if (found.length > 0) {
            checkboxes = Array.from(found).filter(cb =>
                !cb.id.includes('bulk_select_all') &&
                !cb.id.includes('select_all') &&
                !cb.id.includes('check-all') &&
                cb.closest('tbody') // Solo checkboxes del cuerpo de la tabla
            );
            if (checkboxes.length > 0) {
                console.log(`Checkboxes encontrados usando selector: ${selector}`);
                break;
            }
        }
    }

    console.log('Checkboxes encontrados:', checkboxes.length);

    // Remover listeners anteriores y agregar nuevos
    checkboxes.forEach(checkbox => {
        checkbox.removeEventListener('change', updateMassiveActionButton);
        checkbox.addEventListener('change', updateMassiveActionButton);
    });

    // También observar el checkbox de "seleccionar todo" si existe
    const selectAllSelectors = [
        '#bulk_select_all',
        '#select_all',
        '[data-toggle="bulk-select-all"]',
        'th input[type="checkbox"]',
        '.check-all'
    ];

    let selectAllCheckbox = null;
    for (let selector of selectAllSelectors) {
        selectAllCheckbox = document.querySelector(selector);
        if (selectAllCheckbox) {
            console.log(`Checkbox "seleccionar todo" encontrado: ${selector}`);
            break;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.removeEventListener('change', updateMassiveActionButton);
        selectAllCheckbox.addEventListener('change', function() {
            setTimeout(updateMassiveActionButton, 100); // Pequeño delay para que se actualicen los otros checkboxes
        });
    }

    // Actualizar estado inicial del botón
    updateMassiveActionButton();
}

function updateMassiveActionButton() {
    // Actualizar debug info si está visible
    const debugDiv = document.getElementById('debug_info');
    if (debugDiv && debugDiv.style.display !== 'none') {
        updateDebugInfo();
    }
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
        console.log('Procesando checkbox:', cb);

        // Método 1: atributo data-pk
        if (cb.getAttribute('data-pk')) {
            console.log('ID encontrado via data-pk:', cb.getAttribute('data-pk'));
            return cb.getAttribute('data-pk');
        }

        // Método 2: value del checkbox (solo si no es un valor genérico)
        if (cb.value && cb.value !== 'on' && cb.value !== '1' && cb.value !== '0') {
            console.log('ID encontrado via value:', cb.value);
            return cb.value;
        }

        // Método 3: buscar en la fila (tr) atributos data-*
        const row = cb.closest('tr');
        if (row) {
            console.log('Fila encontrada:', row);

            // Buscar data-entry-id
            if (row.getAttribute('data-entry-id')) {
                console.log('ID encontrado via data-entry-id:', row.getAttribute('data-entry-id'));
                return row.getAttribute('data-entry-id');
            }

            // Buscar data-id
            if (row.getAttribute('data-id')) {
                console.log('ID encontrado via data-id:', row.getAttribute('data-id'));
                return row.getAttribute('data-id');
            }

            // Buscar data-key (usado por algunos CRUDs)
            if (row.getAttribute('data-key')) {
                console.log('ID encontrado via data-key:', row.getAttribute('data-key'));
                return row.getAttribute('data-key');
            }

            // Método 4: buscar el ID en diferentes celdas de la fila
            const cells = row.querySelectorAll('td');
            console.log('Celdas en la fila:', cells.length);

            for (let i = 0; i < Math.min(3, cells.length); i++) {
                const cell = cells[i];
                const text = cell.textContent.trim();
                console.log(`Texto de celda ${i}:`, text);

                // Buscar número que parezca un ID
                const idMatch = text.match(/^\d+$/);
                if (idMatch && parseInt(idMatch[0]) > 0) {
                    console.log('ID encontrado en celda:', idMatch[0]);
                    return idMatch[0];
                }
            }

            // Método 5: buscar en elementos con clase o atributos específicos
            const idElement = row.querySelector('[data-primary-key], [data-id], .entry-id');
            if (idElement) {
                const id = idElement.getAttribute('data-primary-key') ||
                           idElement.getAttribute('data-id') ||
                           idElement.textContent.trim();
                if (id && id.match(/^\d+$/)) {
                    console.log('ID encontrado en elemento específico:', id);
                    return id;
                }
            }
        }

        console.log('No se pudo obtener ID para este checkbox');
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
