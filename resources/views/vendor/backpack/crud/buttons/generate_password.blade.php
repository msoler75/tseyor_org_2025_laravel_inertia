@if ($crud->hasAccess('update'))
    <a onclick="generateUserPassword({{ $entry->getKey() }})" class="dropdown-item">
        <i class="la la-key me-2 text-primary"></i>
        Generar contraseña</a>
@endif

@push('after_scripts')
    <script>
        if (!window.generateUserPassword)
            window.generateUserPassword = (user_id) => {
                console.log('generateUserPassword')
                event.preventDefault()

                axios.post('/admin/user/new-password', {
                        _token: '{{ csrf_token() }}',
                        user_id
                    })
                    .then(function(response) {
                        if (typeof response.data == 'string') {
                            swal("¡Error!",
                                "No se ha podido generar la contraseña. Por favor informa al administrador", "error"
                            );

                        } else {

                            swal("Nueva contraseña para " + response.data.user + ':',
                                    response.data.password, "success")
                                .then((value) => {
                                    swal(`Se ha notificado al usuario`);
                                })
                        }
                    })
            }
    </script>
@endpush
