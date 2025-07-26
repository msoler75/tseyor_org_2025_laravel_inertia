{{-- Widget de estadísticas de inscripciones --}}
@php
$stats = [
    'nuevas' => \App\Models\Inscripcion::where('estado', 'nueva')->count(),
    'asignadas' => \App\Models\Inscripcion::where('estado', 'asignada')->count(),
    'en_proceso' => \App\Models\Inscripcion::whereIn('estado', ['contactado', 'encurso'])->count(),
    'finalizadas' => \App\Models\Inscripcion::where('estado', 'finalizado')->count(),
    'no_contesta' => \App\Models\Inscripcion::where('estado', 'nocontesta')->count(),
    'no_interesado' => \App\Models\Inscripcion::where('estado', 'nointeresado')->count(),
];
@endphp

<div class="row mb-3">
    {{-- Nuevas --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card bg-warning h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-user-plus fa-2x me-3"></i>
                    <div>
                        <div class="small">Nuevas</div>
                        <div class="h4 mb-0">{{ $stats['nuevas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Asignadas --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-white bg-danger h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-user-check fa-2x me-3"></i>
                    <div>
                        <div class="text-white-50 small">Asignadas</div>
                        <div class="h4 mb-0">{{ $stats['asignadas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- En Proceso --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-white bg-info h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-comments fa-2x me-3"></i>
                    <div>
                        <div class="text-white-50 small">En Proceso</div>
                        <div class="h4 mb-0">{{ $stats['en_proceso'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Finalizadas --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-check-circle fa-2x me-3"></i>
                    <div>
                        <div class="text-white-50 small">Finalizadas</div>
                        <div class="h4 mb-0">{{ $stats['finalizadas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- No Contesta --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-white bg-secondary h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-phone-slash fa-2x me-3"></i>
                    <div>
                        <div class="text-white-50 small">No Contesta</div>
                        <div class="h4 mb-0">{{ $stats['no_contesta'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- No Interesado --}}
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-white bg-dark h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <i class="la la-times-circle fa-2x me-3"></i>
                    <div>
                        <div class="text-white-50 small">No Interesado</div>
                        <div class="h4 mb-0">{{ $stats['no_interesado'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
