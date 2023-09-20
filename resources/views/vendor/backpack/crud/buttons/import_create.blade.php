@if ($crud->hasAccess('create'))
    <input type="file" name="word_file" id="word_file" style="display: none;" accept='.doc, .docx'>
    <a href="javascript:void(0)" onclick="sendWordFileToCreate(this)" class="ml-2 btn btn-secondary"
        data-button-type="import">
        <span class="ladda-label"><i id="my-icon-button" class="la la-upload">
            </i> Crear desde Word</span>
    </a>
@endif

<script>
    function sendWordFileToCreate(button) {
        var modelo = location.href.split('/').pop()
        var fileInput = document.getElementById('word_file');
        const icon = document.getElementById("my-icon-button")

        fileInput.onchange = function() {
            var file = fileInput.files[0];
            if(!file) return
            var formData = new FormData();
            formData.append('file', file);

            icon.setAttribute("class", "la la-spinner spin")

            axios.post('/admin/' + modelo + '/importar/crear', formData)
                .then(function(response) {
                    // Éxito en la importación, maneja la respuesta del servidor
                    // console.log(response);
                    if (response.data.id)
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

<style>
.spin {
    display: inline-block;
    animation: spin 1s infinite linear;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
