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

    <select id="archivo-log-select" class="form-select mb-3 p-2 input select">
        @foreach ($archivos as $archivo)
            <option value="{{ $archivo }}" {{ $loop->first ? 'selected' : '' }}>{{ $archivo }}</option>
        @endforeach
    </select>
    <div id="my-logs">
        <textarea id="log-view" style="font-size: .7rem; width: 100%; height: calc(100vh - 120px);  min-height: 100%">
        </textarea>
    </div>

    <script>
        function loadLog(archivo) {
            console.log('loading log ' + archivo);
            var textarea = document.getElementById('log-view');
            fetch('/admin/getlog/' + archivo, { credentials: 'same-origin' })
                .then(resp => resp.json())
                .then(response => {
                    console.log({response});
                    textarea.textContent = response.content;
                    textarea.scrollTop = textarea.scrollHeight;
                })
                .catch(error => {
                    alert("Error al cargar el archivo: " + error);
                });
        }

        // Event listener for select change
        document.getElementById('archivo-log-select').addEventListener('change', function(event) {
            const archivo = event.target.value;
            loadLog(archivo);
        });

        // Load initial log
        const initialArchivo = document.getElementById('archivo-log-select').value;
        if (initialArchivo) {
            loadLog(initialArchivo);
        }
    </script>
@endsection
