@if ($crud->hasAccess('create'))
    <input type="file" name="word_file" id="word_file" style="display: none;" accept='.doc, .docx'>
    <a id="btn-import-word" href="javascript:void(0)" onclick="sendWordFileToCreate(this)" class="ml-2 btn btn-secondary"
        data-button-type="import">
        <span class="ladda-label"><i id="word-import-label" class="la la-upload">
            </i> <span id="text-import-word">Crear desde Word</span></span>
    </a>
@endif

@push('after_scripts')
    <script>
        function sendWordFileToCreate(button) {
            var modelo = location.href.split('/').pop()
            var fileInput = document.getElementById('word_file');

            fileInput.onchange = function() {
                var file = fileInput.files[0];
                if (!file) return
                var formData = new FormData();
                formData.append('file', file);

                const icon = document.getElementById("word-import-label")
                const btn = document.getElementById("btn-import-word")
                const textBtn = document.getElementById("text-import-word")

                if (icon)
                    icon.setAttribute("class", "la la-spinner spin")
                if (btn)
                    btn.style.pointerEvents = "none";
                if (textBtn)
                    textBtn.innerText = "IMPORTANDO...";

                axios.post('/admin/' + modelo + '/importar/crear', formData)
                    .then(function(response) {
                        // Éxito en la importación, maneja la respuesta del servidor
                        console.log("import create response", response);
                        if (typeof response.data == 'string') {
                            alert("Hubo un error inesperado");
                        } else if (response.data.id)
                            window.location.href = '/admin/' + modelo + '/' + response.data.id + '/edit'
                        else {
                            if (response.data.result)
                                alert(response.data.result)
                            if (response.data.redirect)
                                window.location.href = response.data.redirect
                        }
                    })
                    .catch(function(error) {
                        // Error en la importación, maneja el error
                        alert(error.response.data.error)
                        console.error(error);
                    });
            };

            fileInput.click();
        }
    </script>
@endpush

<style>
    .spin {
        display: inline-block;
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
