@extends(backpack_view('blank'))

@section('content')

<h1>Comandos ARTISAN</h1>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <button command="optimize" class="btn btn-success">optimize</button>
        <button command="cache:clear" class="btn btn-success">cache:clear</button>
        <button command="view:clear" class="btn btn-success">view:clear</button>
        <button command="imagecache:clear" class="btn btn-success">imagecache:clear</button>
        <button command="migrate" class="btn btn-success">migrate</button>
        <button command="sitemap:generate" class="btn btn-success">sitemap:generate</button>
        <button command="auth:clear-resets" class="btn btn-success">auth:clear-resets</button>
        <button command="down" class="btn btn-success">Activar modo mantenimiento</button>
        <button command="up" class="btn btn-success">Desactivar modo mantenimiento</button>
        <button command="inertia:stop-ssr" class="btn btn-danger">Detener servidor SSR</button>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('button[command]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    let command = encodeURIComponent(this.getAttribute('command'));
                    axios.get('/admin/command/' + command)
                        .then(function(response) {
                            console.log({
                                response
                            })
                            swal('Command executed', response.data.output, 'success');
                        })
                        .catch(function(error) {
                            console.log(error);
                            swal('Error', error.response.data.message, 'error');
                        });
                });
            });
        });
    </script>
@endsection
