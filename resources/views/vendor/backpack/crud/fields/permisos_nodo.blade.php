{{-- permisos_nodo field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp
<label>Permisos:
<input type="text" name="{{ $field['name'] }}" id="permisosNumerico" value="{{ $field['value']}}" maxlength=4 style="max-width: 5rem" >
</label>

<!-- Campos checkbox para los bits de permisos -->
<fieldset class="border border-solid border-neutral p-3 select-none">
    <span>Permisos de propietario:</span>
    <div class="flex gap-5">
        <div class="flex gap-1 items-baseline">
            <input id="bit8" type="checkbox" onchange="actualizarPermisos()"><label for="bit8">Leer</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit7" type="checkbox" onchange="actualizarPermisos()"><label for="bit7">Escribir/Modificar</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit6" type="checkbox" onchange="actualizarPermisos()"><label for="bit6">Listar/Ejecutar</label>
        </div>
    </div>
</fieldset>

<fieldset class="border border-solid border-neutral p-3 select-none">
    <span>Permisos de grupo:</span>
    <div class="flex gap-5">
        <div class="flex gap-1 items-baseline">
            <input id="bit5" type="checkbox" onchange="actualizarPermisos()"><label for="bit5">Leer</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit4" type="checkbox" onchange="actualizarPermisos()"><label for="bit4">Escribir/Modificar</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit3" type="checkbox" onchange="actualizarPermisos()"><label for="bit3">Listar/Ejecutar</label>
        </div>
    </div>
</fieldset>

<fieldset class="border border-solid border-neutral p-3 select-none">
    <span>Permisos públicos:</span>
    <div class="flex gap-5">
        <div class="flex gap-1 items-baseline">
            <input id="bit2" type="checkbox" onchange="actualizarPermisos()"><label for="bit2">Leer</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit1" type="checkbox" onchange="actualizarPermisos()"><label for="bit1">Escribir/Modificar</label>
        </div>
        <div class="flex gap-1 items-baseline">
            <input id="bit0" type="checkbox" onchange="actualizarPermisos()"><label for="bit0">Listar/Ejecutar</label>
        </div>
    </div>
</fieldset>

<fieldset class="border border-solid border-neutral p-3 select-none">
    <span>Proteger contenidos (sticky bit):</span>
    <div class="flex gap-5">
        <div class="flex gap-1 items-baseline">
            <input id="bit9" type="checkbox" onchange="actualizarPermisos()"><label for="bit9">Sticky Bit</label>
        </div>
    </div>
</fieldset>

<script>
    function actualizarPermisos() {
        let permisosBits = [];
        for (let i = 0; i < 10; i++) {
            permisosBits[i] = document.getElementById('bit' + i).checked ? 1 : 0;
        }

        let decimalPermisos = 0;
        for (let i = 0; i < 10; i++) {
            decimalPermisos += permisosBits[i] * Math.pow(2, i);
        }

        let octalPermisos = decimalPermisos.toString(8);
        document.getElementById('permisosNumerico').value = octalPermisos;
    }

    // Inicializar los checkboxes con el valor actual
    window.addEventListener('DOMContentLoaded', function() {
        let permisosNumerico = parseInt(document.getElementById('permisosNumerico').value, 8);
        for (let i = 0; i < 10; i++) {
            document.getElementById('bit' + i).checked = (permisosNumerico >> i) & 1;
        }
    });
</script>
