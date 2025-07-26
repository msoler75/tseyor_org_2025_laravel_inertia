@php
    $inscripcion = $entry ?? $crud->getCurrentEntry();
    $usuarioAsignado = $inscripcion->usuarioAsignado;
    $diasSinContacto = null;

    if ($inscripcion->ultima_notificacion || $inscripcion->fecha_asignacion) {
        $fechaReferencia = $inscripcion->ultima_notificacion ?? $inscripcion->fecha_asignacion;
        $diasSinContacto = now()->diffInDays($fechaReferencia);
    }
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="la la-info-circle"></i> Información de Seguimiento
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Tutor Asignado:</strong><br>
                @if($usuarioAsignado)
                    <i class="la la-user text-success"></i> {{ $usuarioAsignado->name }}
                    <br><small class="text-muted">{{ $usuarioAsignado->email }}</small>
                @else
                    <span class="text-muted">Sin asignar</span>
                @endif
            </div>

            <div class="col-md-6">
                <strong>Días sin contacto:</strong><br>
                @if($diasSinContacto !== null)
                    <span class="badge badge-{{ $diasSinContacto > 7 ? 'danger' : ($diasSinContacto > 3 ? 'warning' : 'success') }}">
                        {{ $diasSinContacto }} días
                    </span>
                    @if($diasSinContacto > 7)
                        <br><small class="text-danger">
                            <i class="la la-exclamation-triangle"></i> Requiere seguimiento urgente
                        </small>
                    @endif
                @else
                    <span class="text-muted">Sin datos</span>
                @endif
            </div>
        </div>

        @if($inscripcion->notas)
            <hr>
            <strong>Últimas Notas:</strong>
            <div class="mt-2 p-2 bg-light rounded">
                <small>{{ Str::limit($inscripcion->notas, 200) }}</small>
            </div>
        @endif

        <hr>
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('inscripciones.mis-asignaciones') }}"
                   class="btn btn-primary btn-sm"
                   target="_blank">
                    <i class="la la-external-link"></i> Gestionar Inscripciones
                </a>

                @if($usuarioAsignado)
                    <button type="button"
                            class="btn btn-warning btn-sm ml-2"
                            onclick="cambiarEstadoRapido({{ $inscripcion->id }}, 'contactado')">
                        <i class="la la-phone"></i> Marcar como Contactado
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function cambiarEstadoRapido(id, estado) {
    if (confirm('¿Estás seguro de cambiar el estado?')) {
        fetch(`/admin/inscripcion/${id}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ estado: estado })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado');
        });
    }
}
</script>
