@if ($crud->hasAccess('update'))
    <input type="file" name="word_file" id="word_file" style="display: none;" accept='.doc, .docx'>
    <a href="javascript:void(0)" onclick="sendWordFileToUpdate(this)" class="dropdown-item" data-button-type="import">
        <span class="ladda-label"><i class="la la-upload"></i> Actualizar desde Word</span>
    </a>
@endif

<script>

    function sendWordFileToUpdate(button) {
        var modelo = location.href.split('/').pop()
        var elem = button.parentNode
        while(elem.tagName!="TR")
            elem = elem.parentNode
        const id = elem.firstChild.firstChild.innerText
        // console.log('id', id)
        var fileInput = document.getElementById('word_file');
        fileInput.onchange = function() {
            var file = fileInput.files[0];
            var formData = new FormData();
            formData.append('file', file);

            axios.post('/admin/'+modelo+'/importar/actualizar/'+id, formData)
                .then(function (response) {
                    // Éxito en la importación, maneja la respuesta del servidor
                    // console.log(response);
                    window.location.href = "/admin/"+modelo+"/"+id+"/edit"
                    //alert("Se ha actualizado el "+modelo + " con id "+id)
                })
                .catch(function (error) {
                    // Error en la importación, maneja el error
                    alert(error.response.data.error)
                    console.error(error);
                });
        };

        fileInput.click();
    }
</script>
