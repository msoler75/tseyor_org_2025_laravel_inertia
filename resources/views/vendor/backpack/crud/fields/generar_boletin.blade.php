<div class="form-group">
    <button onclick="generarBoletin(event)" class="btn btn-default" type="button" class="btn btn-sm"
        style="background-color: #4CAF50; color: white; text-transform: uppercase">
        Generar boletín
    </button>
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
        var url = "{{ route('boletin.generar') }}?tipo=" + tipo;
        fetch(url)
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
