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
                <div id="debug_info" class="mt-2" style="display: none;">
                    <small class="text-info">
                        <strong>Debug:</strong> <span id="debug_text"></span>
                    </small>
                </div>
                <button type="button" class="btn btn-link btn-sm p-0 mt-1" onclick="toggleDebug()">
                    <small>Mostrar información de debug</small>
                </button>
                <button type="button" class="btn btn-link btn-sm p-0 mt-1 ml-2" onclick="testEndpoint()">
                    <small>Probar endpoint</small>
                </button>
                <button type="button" class="btn btn-link btn-sm p-0 mt-1 ml-2" onclick="verificarCambios()">
                    <small>Verificar últimos cambios</small>
                </button>
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
                
                // Agregar información de debug si está disponible
                if (data.debug && data.debug.procesadas > 0) {
                    successMessage += `\n\n✅ Procesadas: ${data.debug.procesadas}`;
                    if (data.debug.detalles && data.debug.detalles.length > 0) {
                        successMessage += '\n📋 Detalles:';
                        data.debug.detalles.forEach(detalle => {
                            successMessage += `\n- ID ${detalle.id}: ${detalle.tutor_anterior ? `Cambió de tutor ${detalle.tutor_anterior} a ${detalle.nuevo_tutor}` : `Asignado a tutor ${detalle.nuevo_tutor}`}`;
                        });
                    }
                }
                
                if (typeof Noty !== 'undefined') {
                    new Noty({
                        text: successMessage.replace(/\n/g, '<br>'),
                        type: 'success',
                        timeout: 8000 // Más tiempo para leer los detalles
                    }).show();
                } else {
                    alert(successMessage);
                }
                
                // Mostrar información de debug si está disponible
                if (data.debug) {
                    console.log('Debug info:', data.debug);
                }
                
                // Forzar recarga completa en lugar de location.reload()
                window.location.href = window.location.href.split('?')[0];
            } else {
                let errorMessage = 'Error: ' + data.message;
                
                // Agregar información de debug si está disponible
                if (data.debug) {
                    errorMessage += '\n\nDebug: ' + JSON.stringify(data.debug, null, 2);
                    console.error('Error details:', data.debug);
                }
                
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

function toggleDebug() {
    const debugDiv = document.getElementById('debug_info');
    if (debugDiv.style.display === 'none') {
        debugDiv.style.display = 'block';
        updateDebugInfo();
    } else {
        debugDiv.style.display = 'none';
    }
}

function updateDebugInfo() {
    const debugText = document.getElementById('debug_text');
    if (!debugText) return;

    // Buscar todos los checkboxes
    const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    const tableCheckboxes = document.querySelectorAll('table input[type="checkbox"]');
    const tbodyCheckboxes = document.querySelectorAll('table tbody input[type="checkbox"]');
    const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');

    // Información adicional sobre los checkboxes seleccionados
    let detailInfo = '';
    if (checkedCheckboxes.length > 0) {
        detailInfo = '<br><strong>Detalles de checkboxes seleccionados:</strong><br>';
        Array.from(checkedCheckboxes).forEach((cb, index) => {
            if (!cb.id.includes('bulk_select_all') && !cb.id.includes('select_all') && cb.closest('tbody')) {
                const row = cb.closest('tr');
                const firstCell = row ? row.querySelector('td:first-child') : null;
                const secondCell = row ? row.querySelector('td:nth-child(2)') : null;

                detailInfo += `Checkbox ${index + 1}: `;
                detailInfo += `value="${cb.value}" `;
                if (cb.getAttribute('data-pk')) detailInfo += `data-pk="${cb.getAttribute('data-pk')}" `;
                if (row && row.getAttribute('data-entry-id')) detailInfo += `row-data-entry-id="${row.getAttribute('data-entry-id')}" `;
                if (row && row.getAttribute('data-id')) detailInfo += `row-data-id="${row.getAttribute('data-id')}" `;
                if (firstCell) detailInfo += `primera-celda="${firstCell.textContent.trim()}" `;
                if (secondCell) detailInfo += `segunda-celda="${secondCell.textContent.trim()}" `;
                detailInfo += '<br>';
            }
        });
    }

    debugText.innerHTML = `
        Total checkboxes: ${allCheckboxes.length} |
        En tabla: ${tableCheckboxes.length} |
        En tbody: ${tbodyCheckboxes.length} |
        Seleccionados: ${checkedCheckboxes.length}
        ${detailInfo}
    `;
}

function testEndpoint() {
    console.log('Probando endpoint de asignación masiva...');
    
    fetch('{{ route("admin.inscripcion.asignar-masiva") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            inscripcion_ids: [1, 2], // IDs de prueba
            user_id: 1 // ID de prueba
        })
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del test:', data);
        alert('Test completado. Ver consola para detalles.');
    })
    .catch(error => {
        console.error('Error en test:', error);
        alert('Error en test: ' + error.message);
    });
}

function verificarCambios() {
    console.log('Verificando últimos cambios...');
    
    // Hacer una petición AJAX para obtener las últimas inscripciones modificadas
    fetch(window.location.href + '?verificar_cambios=1', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Crear un elemento temporal para parsear el HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Buscar inscripciones en la tabla
        const rows = tempDiv.querySelectorAll('table tbody tr');
        let cambiosRecientes = [];
        
        rows.forEach((row, index) => {
            if (index < 5) { // Solo los primeros 5 registros
                const cells = row.querySelectorAll('td');
                if (cells.length > 3) {
                    const id = cells[1]?.textContent?.trim();
                    const tutor = cells[cells.length - 3]?.textContent?.trim();
                    const fecha = cells[2]?.textContent?.trim();
                    
                    if (id && tutor) {
                        cambiosRecientes.push({
                            id: id,
                            tutor: tutor,
                            fecha: fecha
                        });
                    }
                }
            }
        });
        
        if (cambiosRecientes.length > 0) {
            let mensaje = 'Últimos cambios verificados:\n\n';
            cambiosRecientes.forEach(cambio => {
                mensaje += `ID ${cambio.id}: Tutor "${cambio.tutor}" - ${cambio.fecha}\n`;
            });
            alert(mensaje);
        } else {
            alert('No se pudieron verificar los cambios. Revisa la consola para más detalles.');
        }
        
        console.log('Cambios recientes encontrados:', cambiosRecientes);
    })
    .catch(error => {
        console.error('Error al verificar cambios:', error);
        alert('Error al verificar cambios: ' + error.message);
    });
}
</script>
