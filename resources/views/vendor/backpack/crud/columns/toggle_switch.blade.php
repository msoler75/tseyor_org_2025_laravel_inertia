@php
    $id = $entry->id;
    $is_active = !$entry->desactivado; // ajusta según tu campo
@endphp

<div class="custom-control custom-switch">
    <input
        type="checkbox"
        class="custom-control-input"
        id="switch_{{ $id }}"
        {{ $is_active ? 'checked' : '' }}
        onchange="toggleActive({{ $id }}, this.checked)"
    >
    <label class="custom-control-label" for="switch_{{ $id }}"></label>
</div>

<script>
function toggleActive(id, status) {
    fetch(`/admin/radio-item/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ active: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            new Noty({
                type: 'success',
                text: 'Estado actualizado correctamente'
            }).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        new Noty({
            type: 'error',
            text: 'Error al actualizar el estado'
        }).show();
    });
}
</script>
