@extends(backpack_view('blank'))

@section('content')

    <div class="flex flex-wrap justify-between gap-5 my-12">

        @can('administrar usuarios')
            <div class="flex-grow  rounded overflow-y-auto border border-gray-500 bg-base-100">
                <table class="w-full divide-y divide-gray-500">
                    <thead class="!bg-base-100">
                        <tr>
                            <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Últimos usuarios registrados:</th>
                        </tr>
                    </thead>
                    <tbody class="!bg-base-100 divide-y divide-gray-500">
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

            <div class="flex-grow rounded overflow-y-auto border border-gray-500 bg-base-100">
                <table class="w-full divide-y divide-gray-500">
                    <thead class="!bg-base-100">
                        <tr>
                            <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Usuarios activos:</th>
                        </tr>
                    </thead>
                    <tbody class="!bg-base-100 divide-y divide-gray-500">
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

        <div class="flex-grow  rounded overflow-y-auto border border-gray-500 bg-base-100">
            <table class="w-full divide-y divide-gray-500">
                <thead class="!bg-base-100">
                    <tr>
                        <th colspan=2 class="font-bold mb-3 text-lg px-3 py-4">Búsquedas recientes:</th>
                    </tr>
                </thead>
                <tbody class="!bg-base-100 divide-y divide-gray-500">
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

        @can('administrar social')
        <div class="flex-grow rounded overflow-y-auto border border-gray-500 bg-base-100">
            <table class="w-full divide-y divide-gray-500">
                <thead class="!bg-base-100">
                    <tr>
                        <th colspan=5 class="mb-3 px-3 py-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">Últimos comentarios:</span>
                                <a class="text-xs text-right font-normal" href="/admin/comentario">Ver todos</a></div>
                            </th>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Comentario
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Contenido
                        </th>
                    </tr>
                </thead>
                <tbody class="!bg-base-100 divide-y divide-gray-500">
                    @foreach ($comentarios as $comentario)
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">
                        <a title="Ver página del usuario"
                            href="/usuarios/{{ $comentario['user']['slug'] ? $comentario['user']['slug'] : $comentario['user']['id'] }}">{{ $comentario['user']['name'] }}</a>
                        </td>
                        <td class="px-3 py-2 whitespace-wrap">
                        <a title="Ver comentario en el contenido" class="flex-grow"
                            href="{{ $comentario['url'] }}#comentario_{{ $comentario['id'] }}">{{ substr($comentario['texto'], 0, 64) . (strlen($comentario['texto']) > 64 ? '...' : '') }}</a>
                        </td>
                        <td class="px-3 py-2 whitespace-wrap">
                        <a title="Ver contenido" class="flex-grow"
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
                <div class="grid grid-cols-[2fr_1fr_1fr_60px]">
                    @foreach ($contenidos_creados as $contenido)
                        <a title="Ver contenido" href="{{ $contenido->url }}">{{ $contenido->titulo }}</a>
                        <span>{{ $contenido->coleccion }}</span>
                        <span>
                            <TimeAgo date="{{ $contenido->created_at }}" />
                        </span>
                        <a title="Editar contenido" class="btn btn-xs"
                            href="/admin/{{ rtrim($contenido->coleccion, 's') }}/{{ $contenido->id_ref }}/edit">Editar</a>
                    @endforeach
                </div>
            </div>
        @endcan


        @can('administrar contenidos')
            <div class="card p-4 w-[37rem]">
                <div class="font-bold mb-3 text-lg">Contenidos modificados:</div>
                <div class="grid grid-cols-[2fr_1fr_1fr_60px]">
                    @foreach ($contenidos_modificados as $contenido)
                        <a title="Ver contenido" href="{{ $contenido->url }}">{{ $contenido->titulo }}</a>
                        <span>{{ $contenido->coleccion }}</span>
                        <span>
                            <TimeAgo date="{{ $contenido->updated_at }}" />
                        </span>
                        <a title="Editar contenido" class="btn btn-xs"
                            href="/admin/{{ rtrim($contenido->coleccion, 's') }}/{{ $contenido->id_ref }}/revise">Revisar</a>
                    @endforeach
                </div>
            </div>
        @endcan

        @can('avanzado')
            <div class="card p-4 w-full">
                <div class="font-bold mb-3 text-lg">Actividad de los administradores:</div>
                <div class="grid grid-cols-[3fr_1fr_1fr_1fr_1fr_100px]">
                    @foreach ($revisiones as $revision)
                        <a title="Ver contenido" href="{{ $revision->contenidoUrl }}">{{ $revision->tituloContenido }}</a>
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

@endsection
