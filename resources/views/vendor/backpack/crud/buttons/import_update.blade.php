@if ($crud->hasAccess('update'))
    <input type="file" name="word_file" id="word_file" style="display: none;" accept='.doc, .docx'>
    <a href="javascript:void(0)" onclick="sendWordFileToUpdate(this)" class="dropdown-item" data-button-type="import">
        <span class="ladda-label"><i class="la la-upload"></i> Actualizar desde Word</span>
    </a>
@endif

    <script>
        function sendWordFileToUpdate(button) {
            var modelo = location.href.split('/').pop()
            console.log({
                modelo,
                split: location.href.split('/')
            })

            var tr = button.closest('tr');
            const id = tr.firstChild.innerText.replace(/,/g, '') // quita la marca de miles

            if(!id)
            {
                alert("No se ha podido obtener el ID del registro a actualizar.");
                return;
            }
             console.log('id', id)
            var fileInput = document.getElementById('word_file');
            fileInput.onchange = function() {
                var file = fileInput.files[0];
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
                    textBtn.innerText = "Importando...";

                axios.post('/admin/' + modelo + '/importar/actualizar/' + id, formData)
                    .then(function(response) {
                        if (typeof response.data == 'string') {
                            alert("Hubo un error inesperado");
                        } else {
                            // Éxito en la importación, maneja la respuesta del servidor
                            console.log("import update response", response);
                            window.location.href = "/admin/" + modelo + "/" + id + "/edit"
                            //alert("Se ha actualizado el "+modelo + " con id "+id)
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
