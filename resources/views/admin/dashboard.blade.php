@extends(backpack_view('blank'))

@section('content')

    <div class="flex flex-wrap justify-between gap-5 my-12">

        @can('administrar usuarios')
            <div class="card p-4 w-[24rem]">
                <div class="font-bold mb-3 text-lg">Últimos usuarios registrados:</div>
                <div class="w-full grid grid-cols-[1fr_1fr]">
                    @foreach ($users_creados as $user)
                        <a title="Ver página del usuario"
                            href="/usuarios/{{ $user->slug ? $user->slug : $user->id }}">{{ $user->name }}</a>
                        <span>
                            <TimeAgo date="{{ $user->created_at }}" />
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="card p-4 w-[24rem]">
                <div class="font-bold mb-3 text-lg">Usuarios activos:</div>
                <div class="w-full grid grid-cols-[1fr_1fr]">
                    @foreach ($users_activos as $user)
                        <a title="Ver página del usuario"
                            href="/usuarios/{{ $user->slug ? $user->slug : $user->user_id }}">{{ $user->name }}</a>
                        <span>
                            <TimeAgo date="{{ date('Y-m-d H:i:s', $user->last_activity) }}" />
                        </span>
                    @endforeach
                </div>
            </div>
        @endcan

        <div class="card p-4 w-[20rem]">
            <div class="font-bold mb-3 text-lg">Búsquedas recientes:</div>
            <div class="grid grid-cols-[2fr_1fr]">
                @foreach ($busquedas as $busqueda)
                    <span>{{$busqueda['query']}}</span>
                    <span><TimeAgo date="{{ $busqueda['created_at'] }}" /></span>
                @endforeach
            </div>
        </div>

        @can('administrar social')
            <div class="card p-4 w-full">
                <div class="font-bold mb-3 text-lg">Últimos comentarios:</div>
                <div class="grid grid-cols-[1fr_4fr_2fr_1fr_60px] gap-3">
                    @foreach ($comentarios as $comentario)
                        <a title="Ver página del usuario"
                            href="/usuarios/{{ $comentario['user']['slug'] ? $comentario['user']['slug'] : $comentario['user']['id'] }}">{{ $comentario['user']['name'] }}</a>
                        <a title="Ver comentario en el contenido" class="flex-grow"
                            href="{{ $comentario['url'] }}#comentario_{{ $comentario['id'] }}">{{ substr($comentario['texto'], 0, 64) . (strlen($comentario['texto']) > 64 ? '...' : '') }}</a>
                        <a title="Ver contenido" class="flex-grow"
                            href="{{ $comentario['url'] }}">{{ $comentario->tituloContenido }}</a>
                        <span>
                            <TimeAgo date="{{ $comentario['created_at'] }}" />
                        </span>
                        <a title="Editar comentario" class="btn btn-xs"
                            href="/admin/comentario/{{ $comentario['id'] }}/edit">Editar</a>
                    @endforeach
                </div>
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
