@if ($crud->hasAccess('create'))
    <input type="file" name="word_file" id="word_file" style="display: none;" accept='.doc, .docx'>
    <a href="javascript:void(0)" onclick="importUpdate(this)" class="ml-2 btn btn-secondary" data-button-type="import">
        <span class="ladda-label"><i class="la la-upload"></i> Crear desde Word</span>
    </a>
@endif

<script>
    function importUpdate(button) {
        var fileInput = document.getElementById('word_file');
        fileInput.onchange = function() {
            var file = fileInput.files[0];
            var formData = new FormData();
            formData.append('file', file);

            axios.post('/admin/comunicado/importar/crear', formData)
                .then(function (response) {
                    // Éxito en la importación, maneja la respuesta del servidor
                    console.log(response);
                    window.location.href = "/admin/comunicado/"+response.data.id+"/edit"
                })
                .catch(function (error) {
                    // Error en la importación, maneja el error
                    console.error(error);
                });
        };

        fileInput.click();
    }
</script>
