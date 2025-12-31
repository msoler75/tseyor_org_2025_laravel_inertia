@extends(backpack_view('blank'))

@section('content')

    <h1 class="mt-4">Bienvenid@ al panel de administración</h1>

    <div class="admin-dashboard">

        @can('avanzado')
            <div class="flex flex-wrap gap-5 my-12">

                <div class="rounded-xs overflow-y-auto border border-gray-500 bg-base-100 p-3">
                    <div class="font-bold text-lg mb-3">Inscripciones nuevas</div>
                    <div class="flex text-3xl justify-between items-baseline">
                        <span @if ($inscripciones_nuevas) style="color: orange" @endif>{{ $inscripciones_nuevas }}</span>
                        @if ($inscripciones_nuevas)
                            <a class="text-base text-right font-normal" href="/admin/inscripcion">Revisar</a>
                        @else
                            <i class="la la-check-circle text-green-500"></i>
                        @endif
                    </div>
                </div>

                <div class="rounded-xs overflow-y-auto border border-gray-500 bg-base-100 p-3">
                    <div class="font-bold text-lg mb-3">Tareas pendientes</div>
                    <div class="flex text-3xl justify-between items-baseline">
                        <span @if ($tareas_pendientes) style="color: orange" @endif>{{ $tareas_pendientes }}</span>
                        @if ($tareas_pendientes)
                            <i class="la la-hourglass-half text-orange-500" title="Tareas pendientes"></i>
                            <a class="text-base text-right font-normal ml-2" href="/admin/job">Revisar</a>
                        @else
                            <i class="la la-check-circle text-green-500" title="Sin tareas pendientes"></i>
                        @endif
                    </div>
                </div>

                <div class="rounded-xs overflow-y-auto border border-gray-500 bg-base-100 p-3">
                    <div class="font-bold text-lg mb-3">Tareas fallidas</div>
                    <div class="flex text-3xl justify-between items-baseline">
                        <span @if ($tareas_fallidas) style="color: orange" @endif>{{ $tareas_fallidas }}</span>
                        @if ($tareas_fallidas)
                            <a class="text-base text-right font-normal" href="/admin/job-failed">Revisar</a>
                        @else
                            <i class="la la-check-circle text-green-500"></i>
                        @endif
                    </div>
                </div>

                <div class="rounded-xs overflow-y-auto border border-gray-500 bg-base-100 p-3">
                    <div class="font-bold text-lg mb-3">Estado web</div>
                    <div class="flex gap-4 text-3xl justify-between items-baseline">
                        <span
                            @if ($en_mantenimiento) style="color: orange" @endif>{{ $en_mantenimiento ? 'En mantenimiento' : 'Funcionando' }}</span>
                        @if ($en_mantenimiento)
                            <a class="text-base text-right font-normal" href="/admin/command">Revisar</a>
                        @else
                            <i class="la la-check-circle text-green-500"></i>
                        @endif
                    </div>
                </div>

            </div>
        @endcan

        <div class="flex flex-wrap justify-between gap-5 my-12">

            @can('administrar usuarios')
                <div class="grow  rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Últimos usuarios registrados:</th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($users_creados as $user)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Ver página del usuario"
                                            href="/usuarios/{{ $user->slug ? $user->slug : $user->id }}">{{ $user->name }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                        <TimeAgo date="{{ $user->created_at }}" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="grow rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Usuarios activos:</th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($users_activos as $user)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Ver página del usuario"
                                            href="/usuarios/{{ $user->slug ? $user->slug : $user->user_id }}">{{ $user->name }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                        <span>
                                            <TimeAgo date="{{ date('Y-m-d H:i:s', $user->last_activity) }}" />
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endcan

            @canany(['administrar contenidos'])
                <div class="grow  rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Búsquedas recientes:</th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($busquedas as $busqueda)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span>{{ $busqueda['query'] }}</span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span>
                                            <TimeAgo date="{{ $busqueda['created_at'] }}" />
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcanany


            @can('administrar social')
                <div class="grow rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=6 class="font-bold mb-3 text-lg px-3 py-4">Cambios recientes en inscripciones:</th>
                            </tr>
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qué cambió</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrito</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cambio</th>
                                <th class="py-3 text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($cambios_inscripciones as $cambio)
                                @php
                                    // Normalizar valores potencialmente arrays/objetos a strings para evitar errores en Blade
                                    $actor_display = $cambio->actor_name ?? '<sistema>';
                                    if (is_array($actor_display) || is_object($actor_display)) {
                                        $actor_display = json_encode($actor_display, JSON_UNESCAPED_UNICODE);
                                    }

                                    $inscrito_display = $cambio->revisionable->nombre ?? 'Desconocido';
                                    if (is_array($inscrito_display) || is_object($inscrito_display)) {
                                        $inscrito_display = json_encode($inscrito_display, JSON_UNESCAPED_UNICODE);
                                    }

                                    // Para tutor/estado
                                    $tutor_old_display = $cambio->tutor_old_name ?? null;
                                    $tutor_new_display = $cambio->tutor_new_name ?? null;
                                    if (is_array($tutor_old_display) || is_object($tutor_old_display)) {
                                        $tutor_old_display = json_encode($tutor_old_display, JSON_UNESCAPED_UNICODE);
                                    }
                                    if (is_array($tutor_new_display) || is_object($tutor_new_display)) {
                                        $tutor_new_display = json_encode($tutor_new_display, JSON_UNESCAPED_UNICODE);
                                    }

                                    // Valores antiguos/nuevos para estado (etiquetas)
                                    $old_value = $cambio->old_value;
                                    $new_value = $cambio->new_value;
                                    if (is_array($old_value) || is_object($old_value)) $old_value = json_encode($old_value, JSON_UNESCAPED_UNICODE);
                                    if (is_array($new_value) || is_object($new_value)) $new_value = json_encode($new_value, JSON_UNESCAPED_UNICODE);
                                @endphp
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap"><TimeAgo date="{{ $cambio->created_at }}" /></td>
                                    <td class="px-3 py-2">{{ $actor_display }}</td>
                                    <td class="px-3 py-2">
                                        @if(isset($cambio->key) && $cambio->key === 'user_id')
                                            Tutor
                                        @else
                                            Estado
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $inscrito_display }}</td>
                                    <td class="px-3 py-2">
                                        @if(isset($cambio->key) && $cambio->key === 'user_id')
                                            {{-- usar los nombres normalizados desde el controlador --}}
                                            {{ $tutor_old_display ?? 'No asignado' }} <span class="mx-1">→</span> {{ $tutor_new_display ?? '' }}
                                        @else
                                            @php
                                                // Usar las etiquetas ya calculadas en el controlador si existen
                                                $oldLabel = $cambio->old_label_display ?? ($old_value ?? $cambio->old_value);
                                                $newLabel = $cambio->new_label_display ?? ($new_value ?? $cambio->new_value);
                                                if (is_array($oldLabel) || is_object($oldLabel)) $oldLabel = json_encode($oldLabel, JSON_UNESCAPED_UNICODE);
                                                if (is_array($newLabel) || is_object($newLabel)) $newLabel = json_encode($newLabel, JSON_UNESCAPED_UNICODE);
                                            @endphp
                                            {{ $oldLabel }} <span class="mx-1">→</span> {{ $newLabel }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Editar inscripción" class="btn btn-xs" href="/admin/inscripcion/{{ $cambio->revisionable->id }}/edit">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcan


            @can('administrar archivos')
                <div class="grow rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=5 class="mb-3 px-3 py-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold">Últimos archivos:</span>
                                        <a class="text-xs text-right font-normal" href="/admin/nodo">Ver todos</a>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Archivo
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ruta
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Propietario
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($archivos as $archivo)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Descargar archivo" target="_blank"
                                            href="{{ $archivo['ubicacion'] }}">{{ basename($archivo['ubicacion']) }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Ir a la carpeta"
                                            href="{{ dirname($archivo['ubicacion']) }}">{{ dirname($archivo['ubicacion']) }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Ver usuario"
                                            href="/usuarios/{{ $archivo['user']['id'] }}">{{ $archivo['user']['name'] }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcanany

            @can('administrar social')
                <div class="grow rounded-xs overflow-y-auto border border-gray-500 bg-base-100">
                    <table class="w-full divide-y divide-gray-500">
                        <thead class="bg-base-100!">
                            <tr>
                                <th colspan=5 class="mb-3 px-3 py-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold">Últimos comentarios:</span>
                                        <a class="text-xs text-right font-normal" href="/admin/comentario">Ver todos</a>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Comentario
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contenido
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-base-100! divide-y divide-gray-500">
                            @foreach ($comentarios as $comentario)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Ver página del usuario"
                                            href="/usuarios/{{ $comentario['user']['slug'] ? $comentario['user']['slug'] : $comentario['user']['id'] }}">{{ $comentario['user']['name'] }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-wrap">
                                        <a title="Ver comentario en el contenido" class="grow"
                                            href="{{ $comentario['url'] }}#comentario_{{ $comentario['id'] }}">{{ substr($comentario['texto'], 0, 64) . (strlen($comentario['texto']) > 64 ? '...' : '') }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-wrap">
                                        <a title="Ver contenido" class="grow"
                                            href="{{ $comentario['url'] }}">{{ $comentario->tituloContenido }}</a>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                        <TimeAgo date="{{ $comentario['created_at'] }}" />
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <a title="Editar comentario" class="btn btn-xs"
                                            href="/admin/comentario/{{ $comentario['id'] }}/edit">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endcan


            @can('administrar contenidos')
                <div class="card p-4 w-[37rem]">
                    <div class="font-bold mb-3 text-lg">Contenidos creados:</div>
                    <div class="grid !grid-cols-[2fr_1fr_1fr_60px]">
                        @foreach ($contenidos_creados as $contenido)
                            <a title="Ver contenido" href="{{ $contenido->url }}">{{ $contenido->titulo }}</a>
                            <span>{{ $contenido->coleccion }}</span>
                                <TimeAgo date="{{ $contenido->created_at }}" />
                            <a title="Editar contenido" class="btn btn-xs"
                                href="/admin/{{ rtrim($contenido->coleccion, 's') }}/{{ $contenido->id_ref }}/edit">Editar</a>
                        @endforeach
                    </div>
                </div>
            @endcan


            @can('administrar contenidos')
                <div class="card p-4 w-[37rem]">
                    <div class="font-bold mb-3 text-lg">Contenidos modificados:</div>
                    <div class="grid !grid-cols-[2fr_1fr_1fr_60px]">
                        @foreach ($contenidos_modificados as $contenido)
                            <a title="Ver contenido" href="{{ $contenido->url }}">{{ $contenido->titulo }}</a>
                            <span>{{ $contenido->coleccion }}</span>
                            <span>
                                <TimeAgo date="{{ $contenido->updated_at }}" />
                            </span>
                            <a title="Editar contenido" class="btn btn-xs"
                                href="/admin/{{ preg_replace("/e?s$/", '', $contenido->coleccion) }}/{{ $contenido->id_ref }}/revise">Revisar</a>
                        @endforeach
                    </div>
                </div>
            @endcan

            @can('avanzado')
                <div class="card p-4 w-full">
                    <div class="font-bold mb-3 text-lg">Actividad de los administradores:</div>
                    <div class="grid !grid-cols-[3fr_1fr_1fr_1fr_1fr_100px]">
                        @foreach ($revisiones as $revision)
                            <a title="Ver contenido"
                                href="{{ $revision->contenidoUrl }}">{{ $revision->tituloContenido ? $revision->tituloContenido : '<sin título>' }}</a>
                            <span>{{ $revision->coleccion }}</span>
                            <span>{{ $revision->operacion }}</span>
                            <span>{{ $revision->autor }}</span>
                            <span>
                                <TimeAgo date="{{ $revision->created_at }}" />
                            </span>
                            <a title="Ver revisión" class="btn btn-xs" href="{{ $revision->revisionUrl }}">Ver revisión</a>
                        @endforeach
                    </div>
                    <div class="mt-4 text-xs text-right"><a href="/admin/revision">Ver todas las revisiones</a></div>
                </div>
            @endcan

        </div>

    </div>

@endsection
