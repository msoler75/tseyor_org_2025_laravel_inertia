@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">Aviso de mantenimiento</span>
            <small>Configura el anuncio de próximo mantenimiento.</small>
        </h2>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <form method="POST" action="{{ backpack_url('aviso-mantenimiento') }}">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Parsear desde email de DreamHost</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Texto del email</label>
                        <textarea name="raw_email_text" class="form-control" rows="8"
                            placeholder="Pega aquí el texto completo del email de DreamHost...">{{ $aviso['raw_email_text'] ?? '' }}</textarea>
                        <small class="form-text text-muted">
                            Pega el email y usa el botón para rellenar automáticamente las fechas mediante IA.
                        </small>
                    </div>

                    <button type="button" id="btn-parse-email" class="btn btn-outline-primary" onclick="parsearEmail()">
                        <i class="la la-magic"></i> Analizar email con IA
                    </button>
                    <span id="parse-status" class="ms-2"></span>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Datos del aviso</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="titulo" class="form-control"
                            value="{{ $aviso['titulo'] ?? '' }}"
                            placeholder="Ej: Mantenimiento programado: Actualización de servidor">
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"
                            placeholder="Descripción breve que aparece en el banner...">{{ $aviso['descripcion'] ?? '' }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Inicio (UTC)</label>
                                <input type="datetime-local" name="inicio" class="form-control"
                                    value="{{ isset($aviso['inicio']) ? \Carbon\Carbon::parse($aviso['inicio'])->format('Y-m-d\TH:i') : '' }}">
                                <small class="form-text text-muted">Fecha y hora de inicio del mantenimiento en UTC</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fin previsto (UTC)</label>
                                <input type="datetime-local" name="fin" class="form-control"
                                    value="{{ isset($aviso['fin']) ? \Carbon\Carbon::parse($aviso['fin'])->format('Y-m-d\TH:i') : '' }}">
                                <small class="form-text text-muted">Fecha y hora estimada de fin en UTC</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Zona horaria original</label>
                                <input type="text" name="zona_horaria_original" class="form-control"
                                    value="{{ $aviso['zona_horaria_original'] ?? '' }}"
                                    placeholder="Ej: PT, America/Los_Angeles">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duracion estimada</label>
                                <input type="text" name="duracion_estimada" class="form-control"
                                    value="{{ $aviso['duracion_estimada'] ?? '' }}"
                                    placeholder="Ej: aproximadamente 1 hora">
                                <small class="form-text text-muted">Se rellena automaticamente al analizar el email</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>URL más información</label>
                        <input type="text" name="url_info" class="form-control"
                            value="{{ $aviso['url_info'] ?? '' }}"
                            placeholder="/aviso-mantenimiento">
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="la la-save"></i> Guardar aviso
                    </button>

                    @if(isset($aviso['inicio']))
                        <button type="submit" name="clear" value="1" class="btn btn-outline-danger float-end"
                            onclick="return confirm('¿Borrar el aviso de mantenimiento actual?')">
                            <i class="la la-trash"></i> Quitar aviso
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Estado actual</h5>
            </div>
            <div class="card-body">
                @if(isset($aviso['inicio']))
                    @php
                        $ahora = \Carbon\Carbon::now('UTC');
                        $inicio = \Carbon\Carbon::parse($aviso['inicio']);
                        $fin = isset($aviso['fin']) ? \Carbon\Carbon::parse($aviso['fin']) : null;
                    @endphp
                    <dl>
                        <dt>Título</dt>
                        <dd>{{ $aviso['titulo'] ?? '-' }}</dd>
                        <dt>Inicio (UTC)</dt>
                        <dd>{{ $inicio->format('d/m/Y H:i') }}</dd>
                        <dt>Fin (UTC)</dt>
                        <dd>{{ $fin ? $fin->format('d/m/Y H:i') : '-' }}</dd>
                        <dt>Estado</dt>
                        <dd>
                            @if($inicio <= $ahora && $fin && $fin >= $ahora)
                                <span class="badge bg-warning">En curso</span>
                            @elseif($inicio > $ahora)
                                <span class="badge bg-info">Proximo</span>
                            @elseif($fin && $fin < $ahora)
                                <span class="badge bg-secondary">Finalizado</span>
                            @endif
                        </dd>
                        <dt>Duracion caida</dt>
                        <dd>{{ $aviso['duracion_estimada'] ?? '-' }}</dd>
                    </dl>
                @else
                    <p class="text-muted">No hay ningún aviso de mantenimiento configurado.</p>
                @endif

                <hr>
                <p class="small text-muted">
                    <strong>¿Cómo funciona?</strong><br>
                    El aviso solo aparece en el banner del sitio si la fecha actual está entre <em>Inicio</em> y <em>Fin</em> (vigente)
                    o antes de <em>Inicio</em> (próximo).<br>
                    Una vez pasada la fecha de <em>Fin</em>, el aviso desaparece automáticamente.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_scripts')
<script>
    async function parsearEmail() {
        const textarea = document.querySelector('[name="raw_email_text"]');
        const status = document.getElementById("parse-status");
        const btn = document.getElementById("btn-parse-email");

        if (!textarea || !textarea.value.trim()) {
            alert("Primero pega el texto del email de DreamHost en el campo superior.");
            return;
        }

        btn.disabled = true;
        status.innerHTML = '<span class="text-info"><i class="la la-spinner la-spin"></i> Analizando...</span>';

        try {
            const resp = await fetch('{{ backpack_url('aviso-mantenimiento/analizar-email') }}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify({ raw_text: textarea.value }),
            });

            const data = await resp.json();

            if (data.error) {
                status.innerHTML = '<span class="text-danger">Error: ' + data.error + '</span>';
                return;
            }

            const m = data.mantenimiento || {};
            if (m.titulo) document.querySelector('[name="titulo"]').value = m.titulo;
            if (m.descripcion) document.querySelector('[name="descripcion"]').value = m.descripcion;
            if (m.inicio) {
                const dt = new Date(m.inicio);
                const localStr = dt.getFullYear() + "-" +
                    String(dt.getMonth() + 1).padStart(2, "0") + "-" +
                    String(dt.getDate()).padStart(2, "0") + "T" +
                    String(dt.getHours()).padStart(2, "0") + ":" +
                    String(dt.getMinutes()).padStart(2, "0");
                document.querySelector('[name="inicio"]').value = localStr;
            }
            if (m.fin) {
                const dt = new Date(m.fin);
                const localStr = dt.getFullYear() + "-" +
                    String(dt.getMonth() + 1).padStart(2, "0") + "-" +
                    String(dt.getDate()).padStart(2, "0") + "T" +
                    String(dt.getHours()).padStart(2, "0") + ":" +
                    String(dt.getMinutes()).padStart(2, "0");
                document.querySelector('[name="fin"]').value = localStr;
            }
            if (m.zona_horaria_original) document.querySelector('[name="zona_horaria_original"]').value = m.zona_horaria_original;
            if (m.duracion_estimada) document.querySelector('[name="duracion_estimada"]').value = m.duracion_estimada;

            status.innerHTML = '<span class="text-success"><i class="la la-check"></i> Campos rellenados!</span>';
        } catch (e) {
            status.innerHTML = '<span class="text-danger">Error: ' + e.message + '</span>';
        } finally {
            btn.disabled = false;
        }
    }

    var formChanged = false;
    document.querySelector('form').addEventListener('input', function() { formChanged = true; });
    document.querySelector('form').addEventListener('submit', function() { formChanged = false; });
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) e.preventDefault();
    });
</script>
@endpush
