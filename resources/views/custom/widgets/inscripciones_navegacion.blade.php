<div class="card mb-3 bg-light">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="card-title mb-0">
                    <i class="la la-external-link"></i> Navegación Rápida
                </h6>
                <small class="text-muted">Accede al sistema completo de gestión de inscripciones</small>
            </div>
            <div class="col-md-4 text-right">
                <!--
                <a href="{{ route('inscripciones.dashboard') }}"
                   class="btn btn-primary btn-sm"
                   target="_blank"
                   title="Dashboard Completo de Inscripciones">
                    <i class="la la-dashboard"></i> Dashboard Principal
                </a>
            -->
                <a href="{{ route('inscripciones.mis-asignaciones') }}"
                   class="btn btn-info btn-sm ml-1"
                   target="_blank"
                   title="Mis Asignaciones">
                    <i class="la la-user"></i> Mis Asignaciones
                </a>
            </div>
        </div>
    </div>
</div>
