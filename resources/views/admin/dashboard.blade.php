@extends(backpack_view('blank'))

@section('content')

    <div class="flex flex-wrap gap-6 my-12">

        <div class="card p-4 w-[24rem]">
            <div class="font-bold mb-3 text-lg">Últimos usuarios registrados:</div>
            <div class="grid grid-cols-2">
                @foreach($users as $user)
                <span>{{$user['name']}}</span><span><TimeAgo date="{{$user['created_at']}}"/></span>
                @endforeach
            </div>
        </div>



    </div>

    <h1 class="text-3xl">En construcción... 🏗️</h1>
@endsection
