<div class="form-group">
    <button onclick="generarBoletin(event)" class="btn btn-default" type="button" class="btn btn-sm"
        style="background-color: #4CAF50; color: white; text-transform: uppercase">
        Generar boletín
    </button>
    <input type="hidden" name="boletin_token" value="{{ old('boletin_token', $field['value'] ?? '') }}">
</div>

@push('crud_fields_scripts')
<script>
    function generarBoletin(event) {
        event.preventDefault();

        var tipo = document.querySelector('select[name="tipo"]').value;
        if (tipo === "") {
            alert("Por favor, selecciona un tipo de boletín.");
            return;
        }
        var url = "{{ route('boletin.generar.contenido') }}?tipo=" + tipo;
        // Obtener el token del input oculto
        var token = document.querySelector('input[name="boletin_token"]').value;
        var headers = {};
        if (token) {
            headers['X-Boletin-Token'] = token;
        }
        fetch(url, { headers })
            .then(response => response.json())
            .then(data => {
                console.log('respuesta generar boletín:', data);
                document.querySelector('input[name="titulo"]').value = data.titulo;
                document.querySelector('input[name="mes"]').value = data.mes;
                document.querySelector('input[name="anyo"]').value = data.anyo;

                const editor = window.getTipTapEditorInstance()
                editor.commands.setContent(data.texto, {
                    emitUpdate: true
                })
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Ocurrió un error al generar el boletín.");
            });
    }
</script>
@endpush
