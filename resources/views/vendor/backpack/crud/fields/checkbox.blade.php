{{-- checkbox field — override local: checkbox más grande y hint debajo (no al lado) --}}
{{-- El JS (bpFieldInitCheckbox) se mantiene idéntico al original de Backpack: --}}
{{-- es el que sincroniza el input oculto con 0/1 y hace pasar la validación.  --}}

@php
  $field['value'] = old_empty_or_null($field['name'], '') ?? $field['value'] ?? $field['default'] ?? '';
  $field['attributes']['class'] = $field['attributes']['class'] ?? 'bp-field-checkbox__input';
  $wrapper_class = $field['wrapper']['class'] ?? 'form-group';
  $field['wrapper']['class'] = $wrapper_class;
@endphp

@include('crud::fields.inc.wrapper_start')
    @include('crud::fields.inc.translatable_icon')
      <div class="bp-field-checkbox">
        <div class="bp-field-checkbox__control">
          <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] }}">
          <input type="checkbox"
            data-init-function="bpFieldInitCheckbox"

            @if ((bool)$field['value'])
              checked="checked"
            @endif

            @if (isset($field['attributes']))
              @foreach ($field['attributes'] as $attribute => $value)
                {{ $attribute }}="{{ $value }}"
              @endforeach
            @endif
            >
          <label class="bp-field-checkbox__label">{!! $field['label'] !!}</label>
        </div>

        {{-- HINT: debajo del checkbox, no al lado, para que respire el label --}}
        @if (isset($field['hint']))
            <p class="help-block bp-field-checkbox__hint">{!! $field['hint'] !!}</p>
        @endif
      </div>
@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

    {{-- FIELD CSS --}}
    @push('crud_inline_css')
        @bassetBlock('backpack-custom/bp-field-checkbox.css')
        <style>
          /* Checkbox más grande: Tabler no ofrece una variante -lg, así que se
             escala con transform conservando el área de click y el foco. */
          .bp-field-checkbox__input {
            width: 1.35rem !important;
            height: 1.35rem !important;
            margin-top: 0.1rem;
            cursor: pointer;
            flex-shrink: 0;
          }
          /* El label acompaña el tamaño del checkbox y queda pulsable */
          .bp-field-checkbox__control {
            display: flex;
            align-items: center;
            gap: 0.6rem;
          }
          .bp-field-checkbox__label {
            font-weight: 500;
            margin-bottom: 0;
            cursor: pointer;
            font-size: 1rem;
            line-height: 1.35rem;
          }
          /* Hint debajo, con sangría para alinearse con el label */
          .bp-field-checkbox__hint {
            margin-left: 0;
            margin-top: 0.4rem;
            margin-bottom: 0;
            font-size: 0.875rem;
          }
        </style>
        @endBassetBlock
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        @bassetBlock('backpack/crud/fields/checkbox-field.js')
        <script>
            function bpFieldInitCheckbox(element) {
                var hidden_element = element.siblings('input[type=hidden]');
                var id = 'checkbox_'+Math.floor(Math.random() * 1000000);

                // make sure the value is a boolean (so it will pass validation)
                if (hidden_element.val() === '') hidden_element.val(0).trigger('change');

                // set unique IDs so that labels are correlated with inputs.
                // El label ya no es hermano directo del input (está dentro de
                // .bp-field-checkbox__control), así que se busca por clase.
                element.attr('id', id);
                element.closest('.bp-field-checkbox__control')
                    .find('label').attr('for', id);

                // set the default checked/unchecked state
                // if the field has been loaded with javascript
                if (hidden_element.val() != 0) {
                  element.prop('checked', 'checked');
                } else {
                  element.prop('checked', false);
                }

                hidden_element.on('CrudField:disable', function(e) {
                  element.prop('disabled', true);
                });
                hidden_element.on('CrudField:enable', function(e) {
                  element.removeAttr('disabled');
                });

                // when the checkbox is clicked
                // set the correct value on the hidden input
                element.change(function() {
                  if (element.is(":checked")) {
                    hidden_element.val(1).trigger('change');
                  } else {
                    hidden_element.val(0).trigger('change');
                  }
                })
            }
        </script>
        @endBassetBlock
    @endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
