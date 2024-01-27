@extends(backpack_view('blank'))

@section('content')

    <div class="flex flex-wrap gap-6 my-12">

        <div class="card p-4 w-[32rem]">
            <div class="font-bold mb-3 text-lg">Últimos usuarios registrados:</div>
            <div class="grid grid-cols-3">
                @foreach($users as $user)
                    <a href="/usuarios/{{$user['slug']? $user['slug']: $user['id']}}">{{$user['name']}} slug:{{$user['slug']}}</a>
                    <span><TimeAgo date="{{$user['created_at']}}"/></span>
                    <a class="btn btn-xs" href="/admin/user/{{ $user['id'] }}/edit">Editar</a>
                @endforeach
            </div>
        </div>

        <div class="card p-4 w-full">
            <div class="font-bold mb-3 text-lg">Últimos comentarios:</div>
            <div class="grid gap-3" style="grid-template-columns: 1fr 4fr 2fr 1fr 1fr">
                @foreach($comentarios as $comentario)
                <a href="/usuarios/{{ $comentario['user']['slug']? $comentario['user']['slug']:$comentario['user']['id'] }}">{{$comentario['user']['name']}}</a>
                <a class="flex-grow" href="{{$comentario['url']}}#comentario_{{$comentario['id']}}">{{substr($comentario['texto'], 0, 64) . (strlen($comentario['texto']) > 64 ? "..." :"")}}</a>
                <span class="flex-grow">{{$comentario->tituloContenido}}</span>
                <span><TimeAgo date="{{$comentario['created_at']}}"/></span>
                <a class="btn btn-xs" href="/admin/comentario/{{ $comentario['id'] }}/edit">Editar</a>
                @endforeach
            </div>
        </div>



    </div>

    <h1 class="text-3xl">En construcción... 🏗️</h1>
@endsection
