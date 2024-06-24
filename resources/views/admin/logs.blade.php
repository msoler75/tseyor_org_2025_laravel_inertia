@extends(backpack_view('blank'))

@section('content')
    @php
        $logsFolder = storage_path('logs');
        $archivos = ['laravel', 'jobs']; // se pone sin extensión .log
        $logs = [];
        foreach ($archivos as $archivo) {
            $logs[$archivo] = @file_get_contents($logsFolder . '/' . $archivo . '.log');
        }
        $first = true;
    @endphp

    <div style="user-select: none; margin-bottom: 10px; display: flex; gap: 10px">
        @foreach ($archivos as $archivo)
            <label><input type="radio" value="{{ $archivo }}-log" name="tipo-log"
                    @php if($first) echo 'checked'; $first = false; @endphp> {{ $archivo }}.log</label>&nbsp;
        @endforeach
    </div>
    <div id="my-logs">
        @foreach ($archivos as $archivo)
            <textarea id="{{ $archivo }}-log"
                style="font-size: .7rem; width: 100%; height: calc(100vh - 120px);  min-height: 100%; display: none">
            {{ $logs[$archivo] }}
        </textarea>
        @endforeach
    </div>

    <script>
        function ocultarTextArea() {
            var t = document.querySelectorAll("#my-logs textarea")
            for (var i = 0; i < t.length; i++) {
                t[i].style.display = 'none'
            }
        }

        function showLog(log) {
            // obtenemos el valor del radio seleccionado
            var radio = document.querySelector('input[name="tipo-log"]:checked').value;
            ocultarTextArea()
            // mostramos el textarea seleccionado
            var textarea = document.getElementById(log);
            textarea.style.display = 'block'
            textarea.scrollTop = textarea.scrollHeight;
        }

        // creamos un event listener para cuando cambie el valor del radio
        var radios = document.querySelectorAll('input[name="tipo-log"]')
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', function() {
                showLog(this.value);
            });
        }

        showLog('laravel-log')
    </script>
@endsection
