@extends(backpack_view('blank'))

@section('content')
    @php
        $logsFolder = storage_path('logs');
        // list files in folder
        $archivos = [];
        foreach (glob($logsFolder . '/*.log') as $archivo) {
            $archivo = basename($archivo);
            $archivos[] = $archivo;
        }
        usort($archivos, function($a, $b){
            if($a==$b) return 0;
            $weightA = 0;
            $weightB = 0;
            if(preg_match("/laravel.*/", $a))
            $weightA = $a=='laravel.log' ? -2 : -1;
            if(preg_match("/laravel.*/", $b))
            $weightB = $b=='laravel.log' ? -2 : -1;
            return $weightA - $weightB;
        })
    @endphp

    <div style="user-select: none; margin-bottom: 10px; display: flex; gap: 10px">
        @foreach ($archivos as $archivo)
            <label><input type="radio" value="{{ $archivo }}" name="archivo-log"> {{ $archivo }}</label>&nbsp;
        @endforeach
    </div>
    <div id="my-logs">
        <textarea id="log-view" style="font-size: .7rem; width: 100%; height: calc(100vh - 120px);  min-height: 100%">
        </textarea>
    </div>

    <script>
        function showLog(archivo) {
            // obtenemos el valor del radio seleccionado
            var radio = document.querySelector('input[name="archivo-log"]');
            radio.click()
        }

        // creamos un event listener para cuando cambie el valor del radio
        var radios = document.querySelectorAll('input[name="archivo-log"]')
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('click', function(event) {
                const archivo = event.target.value
                console.log('click en archivo ' + archivo)
                // mostramos el textarea seleccionado
                var textarea = document.getElementById('log-view');
                fetch('/admin/getlog/' + archivo)
                    .then(resp => resp.json())
                    .then(response => {
                        console.log({response})
                        textarea.textContent = response.content
                        textarea.scrollTop = textarea.scrollHeight;
                    })
                    .catch(error => {
                        alert("Error al cargar el archivo: " + error);
                    })
            });
        }

        showLog('laravel.log')
    </script>
@endsection
