<div class="card mb-3">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="la la-users"></i> Equipos con solicitudes pendientes
        </h6>
    </div>
    <div class="card-body">
        @php
            $equiposPendientes = \App\Models\Solicitud::whereNull('fecha_aceptacion')
                ->whereNull('fecha_denegacion')
                ->with('equipo')
                ->get()
                ->pluck('equipo')
                ->unique('id');
        @endphp

        @if($equiposPendientes->count())
            <div class="d-flex flex-wrap gap-2">
                @foreach($equiposPendientes as $equipo)
                    @php
                        $count = \App\Models\Solicitud::where('equipo_id', $equipo->id)
                            ->whereNull('fecha_aceptacion')
                            ->whereNull('fecha_denegacion')
                            ->count();
                    @endphp
                    <a href="/equipos/{{ $equipo->slug }}?solicitudes" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="la la-users"></i> {{ $equipo->nombre }}
                        <span class="badge bg-warning ms-1">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <span class="text-muted">No hay solicitudes pendientes</span>
        @endif
    </div>
</div>
