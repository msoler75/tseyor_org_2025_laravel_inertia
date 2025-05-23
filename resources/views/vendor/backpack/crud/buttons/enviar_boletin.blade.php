@php
    $enviado = $entry->enviado ?? false;
@endphp
@if(!$enviado)
    <a href="{{ route('boletin.enviar', $entry->getKey()) }}"
       class="btn btn-sm btn-link"
       data-url="{{ route('boletin.enviar', $entry->getKey()) }}"
       data-suscriptores="{{ $entry->numeroSuscriptores ?? 0 }}"
       onclick="enviarBoletin(this); return false;">
        <i class="la la-paper-plane"></i> Enviar Boletín
    </a>

    @once
    <script>
    function enviarBoletin(el, event) {
        console.log('enviarBoletin');
        const url = el.getAttribute('data-url');
        const suscriptores = el.getAttribute('data-suscriptores') || 0;
        if(!confirm(`Se mandará el boletín a ${suscriptores} suscriptores. ¿Quieres mandarlo ahora?`)) {
            return;
        }
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                data = {};
            }
            if (response.ok && data.success) {
                alert(data.message || 'Boletín enviado correctamente');
            } else {
                alert((data && data.message) ? data.message : 'Error al enviar el boletín');
            }
        })
        .catch(error => {
            alert('Error al enviar el boletín');
        });
    }
    </script>
    @endonce
@endif
