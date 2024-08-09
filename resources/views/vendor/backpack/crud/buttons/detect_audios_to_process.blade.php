@if ($crud->hasAccess('update'))
    <a id="btn-detect-audios" href="/admin/jobs/detect-audios-to-process" class="ml-2 btn btn-secondary"
        title="Busca en contenidos cuyos audios aún deben procesarse y encola las tareas">
        Detectar audios a procesar
    </a>
@endif
