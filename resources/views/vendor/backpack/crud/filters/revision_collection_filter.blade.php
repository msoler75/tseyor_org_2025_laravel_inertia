<form method="GET" class="mb-3 d-flex align-items-center gap-2">
    <label for="revisionable_type" class="form-label mb-0 text-nowrap fw-bold">Colección:</label>
    <select name="revisionable_type" id="revisionable_type" class="form-select" style="max-width: 250px;" onchange="this.form.submit()">
        <option value="">-- Todas --</option>
        @foreach ($widget['colecciones'] as $type => $label)
            <option value="{{ $type }}" {{ request('revisionable_type') == $type ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @if (request('revisionable_type'))
        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary text-nowrap">&times; Limpiar</a>
    @endif
</form>
