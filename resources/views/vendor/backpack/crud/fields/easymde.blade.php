{{-- easymde_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<textarea id="editor" name="{{ $field['name'] }}" data-init-function="bpFieldInitDummyFieldElement" @include('crud::fields.inc.attributes')>{{ $field['value'] }}</textarea>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @basset('easymdeFieldStyle.css')

    {{-- How to add some CSS? --}}
    @bassetBlock('backpack/crud/fields/easymde_field-style.css')
        <style>
            .easymde_field_class {
                display: none;
            }


            <style>
    .editor-preview-side {
        top: 110px;
    }

    .editor-preview-full img,
    .editor-preview-side img {
        max-height: 70vh;
    }

    .EasyMDEContainer .CodeMirror-fullscreen {
        width: calc(100% - 60px);
        left: 60px;
        top: 110px;
    }

    .EasyMDEContainer .CodeMirror-sided {
        width: calc(50% - 60px);
    }

    .editor-toolbar.fullscreen {
        width: calc(100% - 60px);
        top: 60px;
        left: 60px;
    }




    .button-bold:before,
    .button-heading:before,
    .button-heading-1:before,
    .button-heading-2:before,
    .button-italic:before,
    .button-underline:before,
    .button-strike:before,
    .button-code:before,
    .button-link:before,
    .button-unordered-list:before,
    .button-ordered-list:before,
    .button-table:before,
    .button-image:before,
    .button-folder:before,
    .button-preview:before,
    .button-quote:before,
    .button-columns:before,
    .button-fullscreen:before,
    .button-clean-block::before {
        content: '';
        display: flex;
        width: 24px;
        height: 24px;
        background-size: 20px;
        background-position: 3px 2px;
        background-repeat: no-repeat;
    }

    .editor-toolbar button.heading-2:after {
        content: "";
    }

    .editor-toolbar button.heading-2:after {
        content: "";
    }

    .editor-toolbar button.heading-2:after {
        content: "";
    }

    .editor-toolbar:after,
    .editor-toolbar:before {
        display: block;
        content: '';
        height: 0;
    }

    .button-clean-block::before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-eraser' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cpath d='M19 19h-11l-4 -4a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9 9' /%3E%3Cpath d='M18 12.3l-6.3 -6.3' /%3E%3C/svg%3E");
    }

    .button-preview:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-eye' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Ccircle cx='12' cy='12' r='2' /%3E%3Cpath d='M2 12l1.5 2a11 11 0 0 0 17 0l1.5 -2' /%3E%3Cpath d='M2 12l1.5 -2a11 11 0 0 1 17 0l1.5 2' /%3E%3C/svg%3E");
    }

    .button-bold:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-bold' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cpath d='M7 5h6a3.5 3.5 0 0 1 0 7h-6z' /%3E%3Cpath d='M13 12h1a3.5 3.5 0 0 1 0 7h-7v-7' /%3E%3C/svg%3E");
    }

    .button-italic:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-italic' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cline x1='11' y1='5' x2='17' y2='5' /%3E%3Cline x1='7' y1='19' x2='13' y2='19' /%3E%3Cline x1='14' y1='5' x2='10' y2='19' /%3E%3C/svg%3E");
    }

    .button-underline:before {
        background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzJjM2U1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8dGV4dCBzdHlsZT0iZmlsbDogcmdiKDAsIDAsIDApOyBmb250LWZhbWlseTogQXJpYWwsIHNhbnMtc2VyaWY7IGZvbnQtc2l6ZTogMC43cHg7IHdoaXRlLXNwYWNlOiBwcmU7IiB0cmFuc2Zvcm09Im1hdHJpeCgxOC41NTU3MjMsIDAsIC0wLjI3NTY1MSwgMTYuNjEyMzIyLCAtNjcuOTgwODY1LCAtNDguNTQ0MTE3KSIgeD0iMy44ODkiIHk9IjMuNjAyIj5VPC90ZXh0PgogIDxyZWN0IHg9IjMuODg5IiB5PSIxMi4yMzUiIHdpZHRoPSI4LjEzNCIgaGVpZ2h0PSIwLjg5NiIgc3R5bGU9ImZpbGw6IHJnYigwLCAwLCAwKTsiLz4KPC9zdmc+");
    }

    .button-strike:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-strikethrough' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cline x1='5' y1='12' x2='19' y2='12' /%3E%3Cpath d='M16 6.5a4 2 0 0 0 -4 -1.5h-1a3.5 3.5 0 0 0 0 7' /%3E%3Cpath d='M16.5 16a3.5 3.5 0 0 1 -3.5 3h-1.5a4 2 0 0 1 -4 -1.5' /%3E%3C/svg%3E");
    }

    .button-code:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-code' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cpolyline points='7 8 3 12 7 16' /%3E%3Cpolyline points='17 8 21 12 17 16' /%3E%3Cline x1='14' y1='4' x2='10' y2='20' /%3E%3C/svg%3E");
    }

    .button-link:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-link' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cpath d='M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5' /%3E%3Cpath d='M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5' /%3E%3C/svg%3E");
    }

    .button-unordered-list:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-list' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cline x1='9' y1='6' x2='20' y2='6' /%3E%3Cline x1='9' y1='12' x2='20' y2='12' /%3E%3Cline x1='9' y1='18' x2='20' y2='18' /%3E%3Cline x1='5' y1='6' x2='5' y2='6.01' /%3E%3Cline x1='5' y1='12' x2='5' y2='12.01' /%3E%3Cline x1='5' y1='18' x2='5' y2='18.01' /%3E%3C/svg%3E");
    }

    .button-table:before {
        background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzJjM2U1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB4PSIyLjc4OSIgeT0iMy4yMzMiIHdpZHRoPSIxMC43OTMiIGhlaWdodD0iOS43NyIgc3R5bGU9InN0cm9rZTogcmdiKDAsIDAsIDApOyBmaWxsOiBub25lOyIvPgogIDxsaW5lIHN0eWxlPSJmaWxsOiByZ2IoMjE2LCAyMTYsIDIxNik7IHN0cm9rZTogcmdiKDAsIDAsIDApOyIgeDE9IjguMTM1IiB5MT0iMy4xODIiIHgyPSI4LjEwOSIgeTI9IjEyLjU5MyIvPgogIDxsaW5lIHN0eWxlPSJmaWxsOiByZ2IoMjE2LCAyMTYsIDIxNik7IHN0cm9rZTogcmdiKDAsIDAsIDApOyIgeDE9IjEzLjM3OCIgeTE9IjguMDQxIiB4Mj0iMy4wNzEiIHkyPSI4LjA0MSIvPgo8L3N2Zz4=");
}

.button-folder:before {
    background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzJjM2U1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB4PSIyLjc4OSIgeT0iNC43NjgiIHdpZHRoPSI5LjkyMyIgaGVpZ2h0PSI3LjUxOSIgc3R5bGU9InN0cm9rZTogcmdiKDAsIDAsIDApOyBzdHJva2UtbGluZWNhcDogcm91bmQ7IHN0cm9rZS1saW5lam9pbjogcm91bmQ7IGZpbGw6IHJnYigyMTYsIDIxNiwgMjE2KTsiLz4KICA8cG9seWdvbiBzdHlsZT0ic3Ryb2tlOiByZ2IoMCwgMCwgMCk7IHN0cm9rZS1saW5lY2FwOiByb3VuZDsgc3Ryb2tlLWxpbmVqb2luOiByb3VuZDsgZmlsbC1ydWxlOiBub256ZXJvOyBmaWxsOiByZ2IoMjU1LCAyNTUsIDI1NSk7IiBwb2ludHM9IjIuODQxIDEyLjM5MSAxMi44MzggMTIuMjU4IDE0LjI5OCA3LjAxOCA0LjI4NiA3LjA5NSIvPgo8L3N2Zz4=");
}
.button-image:before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-photo' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cline x1='15' y1='8' x2='15.01' y2='8' /%3E%3Crect x='4' y='4' width='16' height='16' rx='3' /%3E%3Cpath d='M4 15l4 -4a3 5 0 0 1 3 0l 5 5' /%3E%3Cpath d='M14 14l1 -1a3 5 0 0 1 3 0l 2 2' /%3E%3C/svg%3E");
    }

    .button-quote:before {
        background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzJjM2U1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8dGV4dCBzdHlsZT0iZmlsbDogcmdiKDAsIDAsIDApOyBmb250LWZhbWlseTogQmFobnNjaHJpZnQ7IGZvbnQtc2l6ZTogMC43cHg7IHdoaXRlLXNwYWNlOiBwcmU7IiB0cmFuc2Zvcm09Im1hdHJpeCgxNi4wOTI0MTMsIDAsIDAsIDE1LjY4MTUzLCAtODguMjY3Mzk1LCAtNzUuODk5NjY2KSIgeD0iNS42NzkiIHk9IjUuNTg4Ij45PC90ZXh0PgogIDxlbGxpcHNlIHN0eWxlPSJmaWxsOiByZ2IoMCwgMCwgMCk7IiBjeD0iNi4wMzciIGN5PSI2LjE1MSIgcng9IjEuNDMyIiByeT0iMS40NTgiLz4KICA8dGV4dCBzdHlsZT0iZmlsbDogcmdiKDAsIDAsIDApOyBmb250LWZhbWlseTogQmFobnNjaHJpZnQ7IGZvbnQtc2l6ZTogMC43cHg7IHdoaXRlLXNwYWNlOiBwcmU7IiB0cmFuc2Zvcm09Im1hdHJpeCgxNi4wOTI0MTMsIDAsIDAsIDE1LjY4MTUzLCAtODMuMzA1Nzg2LCAtNzUuODI5NDA3KSIgeD0iNS42NzkiIHk9IjUuNTg4Ij45PC90ZXh0PgogIDxlbGxpcHNlIHN0eWxlPSJmaWxsOiByZ2IoMCwgMCwgMCk7IiBjeD0iMTAuOTk5IiBjeT0iNi4yMjEiIHJ4PSIxLjQzMiIgcnk9IjEuNDU4Ii8+Cjwvc3ZnPg==");
    }

    .button-ordered-list:before {
        background-image: url("data:image/svg+xml,%3Csvg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-list-ol' fill='%232c3e50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' d='M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z'/%3E%3Cpath d='M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z'/%3E%3C/svg%3E");
    }


    .button-heading:before {
        background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iIzJjM2U1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB4PSIzLjc5NiIgeT0iMy4wNTEiIHdpZHRoPSIxLjY5OSIgaGVpZ2h0PSIxMC4wNjUiIHN0eWxlPSJmaWxsOiByZ2IoMCwgMCwgMCk7Ii8+CiAgPHJlY3QgeD0iNS4xMTYiIHk9IjcuMjMyIiB3aWR0aD0iNS4xIiBoZWlnaHQ9IjEuODI0IiBzdHlsZT0iZmlsbDogcmdiKDAsIDAsIDApOyIvPgogIDxyZWN0IHg9IjEwLjExNiIgeT0iMy4wNzIiIHdpZHRoPSIxLjk0IiBoZWlnaHQ9IjkuOTg2IiBzdHlsZT0iZmlsbDogcmdiKDYsIDYsIDYpOyIvPgo8L3N2Zz4=");
}

.button-heading-1:before {
    background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMWVtIiBoZWlnaHQ9IjFlbSIgdmlld0JveD0iMCAwIDE2IDE2IiBjbGFzcz0iYmkgYmktdHlwZS1oMSIgZmlsbD0iY3VycmVudENvbG9yIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxwYXRoIGQ9Ik04LjYzNyAxM1YzLjY2OUg3LjM3OVY3LjYySDIuNzU4VjMuNjdIMS41VjEzaDEuMjU4VjguNzI4aDQuNjJWMTNoMS4yNTl6bTUuMzI5IDBWMy42NjloLTEuMjQ0TDEwLjUgNS4zMTZ2MS4yNjVsMi4xNi0xLjU2NWguMDYyVjEzaDEuMjQ0eiIvPgo8L3N2Zz4=");
}

.button-heading-2:before {
    background-image: url("data:image/svg+xml,%3Csvg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-type-h2' fill='%232c3e50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7.638 13V3.669H6.38V7.62H1.759V3.67H.5V13h1.258V8.728h4.62V13h1.259zm3.022-6.733v-.048c0-.889.63-1.668 1.716-1.668.957 0 1.675.608 1.675 1.572 0 .855-.554 1.504-1.067 2.085l-3.513 3.999V13H15.5v-1.094h-4.245v-.075l2.481-2.844c.875-.998 1.586-1.784 1.586-2.953 0-1.463-1.155-2.556-2.919-2.556-1.941 0-2.966 1.326-2.966 2.74v.049h1.223z'/%3E%3C/svg%3E");
    }

    .button-columns:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-layout-columns' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Crect x='4' y='4' width='16' height='16' rx='2' /%3E%3Cline x1='12' y1='4' x2='12' y2='20' /%3E%3C/svg%3E");
    }

    .button-fullscreen:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-maximize' width='44' height='44' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath stroke='none' d='M0 0h24v24H0z'/%3E%3Cpath d='M4 8v-2a2 2 0 0 1 2 -2h2' /%3E%3Cpath d='M4 16v2a2 2 0 0 0 2 2h2' /%3E%3Cpath d='M16 4h2a2 2 0 0 1 2 2v2' /%3E%3Cpath d='M16 20h2a2 2 0 0 0 2 -2v-2' /%3E%3C/svg%3E");
    }

    .editor-preview-active pre {
        border-radius: 0.5rem;
        padding: 10px;
    }

    .editor-toolbar .easymde-dropdown,
    .editor-toolbar button {
        padding: 0 6px;
    }


        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    {{-- How to load a JS file? --}}
    @basset('easymdeFieldScript.js')

    {{-- How to add some JS to the field? --}}
    @bassetBlock('path/to/script.js')
        <script>
            const EASYMDE_CDN_JS = 'https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js'
            const EASYMDE_CDN_CSS = 'https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css';
            const TURNDOWN_CDN_JS = 'https://unpkg.com/turndown/dist/turndown.js';
            const UPLOAD_ENDPOINT = '{{ url('/api/upload/image') }}';

            const showToolbar = true;
            var jsLoaded = window.EasyMDE;

            function loadJs(callback) {
                if (jsLoaded) {
                    setTimeout(callback, 10);
                } else {

                    // LOAD JAVASCRIPT
                    var js = document.createElement('script');
                    js.src = EASYMDE_CDN_JS;
                    js.type = 'text/javascript';
                    js.onload = function() {
                        jsLoaded = true;
                        setTimeout(callback, 100);
                    };
                    document.getElementsByTagName('head')[0].appendChild(js);

                    // LOAD CSS
                    if (!document.querySelector('link[href="' + EASYMDE_CDN_CSS + '"]')) {
                        var css = document.createElement('link');
                        css.href = EASYMDE_CDN_CSS;
                        css.rel = 'stylesheet';
                        css.type = 'text/css';
                        document.getElementsByTagName('head')[0].appendChild(css);
                    }
                }
            }

            var editor_{{ $field['name'] }} = null

            // document.addEventListener('DOMContentLoaded', () => {

            jsLoaded = window.EasyMDE;

            loadJs(function() {
                // Callback function to execute after EasyMDE.js has been loaded
                console.log('EasyMDE.js has been loaded');
                const toolbarSettings = [{
                        name: 'bold',
                        action: EasyMDE.toggleBold,
                        className: 'button-bold',
                        title: 'Negritas'
                    },
                    {
                        name: 'italic',
                        action: EasyMDE.toggleItalic,
                        className: 'button-italic',
                        title: 'Italica'
                    },
                    /*{
                        name: 'underline',
                        action: EasyMDE.underline,
                        className: 'button-underline',
                        title: 'Subrayado'
                    },*/
                    {
                        name: 'strikethrough',
                        action: EasyMDE.toggleStrikethrough,
                        className: 'button-strike',
                        title: 'Tachado'
                    },
                    {
                        name: 'heading',
                        action: EasyMDE.toggleHeadingSmaller,
                        className: 'button-heading',
                        title: 'Título'
                    },
                    /* {
                        name: 'heading-1',
                        action: EasyMDE.toggleHeading1,
                        className: 'button-heading-1',
                        title: 'Título 1'
                    }, */
                    {
                        name: 'heading-2',
                        action: EasyMDE.toggleHeading2,
                        className: 'button-heading-2',
                        title: 'Título 2'
                    },
                    {
                        name: 'quote',
                        action: EasyMDE.toggleBlockquote,
                        className: 'button-quote',
                        title: 'Cita'
                    },
                    {
                        name: 'unordered-list',
                        action: EasyMDE.toggleUnorderedList,
                        className: 'button-unordered-list',
                        title: 'Lista no numerada'
                    },
                    {
                        name: 'ordered-list',
                        action: EasyMDE.toggleOrderedList,
                        className: 'button-ordered-list',
                        title: 'Lista numerada'
                    },
                    {
                        name: 'link',
                        action: EasyMDE.drawLink,
                        className: 'button-link',
                        title: 'Insertar enlace'
                    },
                    /*{
                        name: 'code',
                        action: EasyMDE.toggleCodeBlock,
                        className: 'button-code',
                        title: 'Código'
                    },*/
                    {
                        name: 'insert-table',
                        action: EasyMDE.drawTable,
                        className: 'button-table',
                        title: 'Insertar tabla'
                    },
                    {
                        name: "image-group",
                        className: "button-image",
                        title: "Insertar imagen",
                        children: [{
                                name: 'image-browse',
                                action: window.abrirMediaManager_{{ $field['name'] }},
                                className: 'button-folder',
                                title: 'Insertar imagen desde la biblioteca'
                            },
                            {
                                name: 'image',
                                action: EasyMDE.drawImage,
                                className: 'button-image',
                                title: 'Insertar Imagen'
                            },
                        ]
                    },
                    {
                        name: 'clean-block',
                        action: EasyMDE.cleanBlock,
                        className: 'button-clean-block',
                        title: 'Limpiar bloque'
                    },
                    {
                        name: 'preview',
                        action: EasyMDE.togglePreview,
                        className: 'button-preview no-disable',
                        title: 'Previsualizar'
                    },
                    {
                        name: 'side-by-side',
                        action: EasyMDE.toggleSideBySide,
                        className: 'button-columns no-disable no-mobile',
                        title: 'Edición y previsualización'
                    },
                    {
                        name: 'fullscreen',
                        action: EasyMDE.toggleFullScreen,
                        className: 'button-fullscreen no-disable no-mobile',
                        title: 'Pantalla completa'
                    }
                ];
                // ya se cargó EasyMDE
                editor_{{ $field['name'] }} = new EasyMDE({
                    element: document.querySelector("#editor"),
                    uploadImage: true,
                    // autosave: {enabled: true, uniqueId:  },
                    toolbar: showToolbar == true ? toolbarSettings : false,

                    /*shortcuts: {
                        "toggleBold": "Ctrl-N", // alter the shortcut for toggleOrderedList
                        "drawTable": "Ctrl-T", // bind Cmd-Alt-T to drawTable action, which doesn't come with a default shortcut
                    },*/

                    spellChecker: false,

                    indentWithTabs: true,
                    lineWrapping: true,
                    tabSize: 4,

                    imageUploadEndpoint: UPLOAD_ENDPOINT,
                    imageMaxSize: 1024 * 1024 * 2, // 2 MB
                    imageAccept: 'image/png, image/jpeg, image/webp',
                    imageCSRFName: '1234',
                    imagePathAbsolute: false,
                    imageTexts: {
                        sbInit: 'Adjuntar archivos arrastrando y soltando o pegando desde el portapapeles.',
                        sbOnDragEnter: 'Suelte la imagen para cargarla.',
                        sbOnDrop: 'Cargando imágenes #images_names#.',
                        sbProgress: 'Cargando #file_name#: #progress#%.',
                        sbOnUploaded: 'Imagen cargada #image_name#.'
                    },
                });



                // Verificar si Turndown está definido
                if (typeof Turndown === 'undefined') {
                    // Crear el elemento <script> para cargar Turndown
                    var scriptElement = document.createElement('script');
                    scriptElement.src = TURNDOWN_CDN_JS

                    // Agregar el elemento <script> al <head> del documento
                    document.head.appendChild(scriptElement);

                    // Esperar a que se cargue Turndown
                    scriptElement.onload = function() {
                        // Turndown se ha cargado correctamente, puedes usarlo aquí
                        console.log('Turndown cargado');
                        // Tu código que utiliza Turndown...
                    };
                } else {
                    // Turndown ya está cargado, puedes usarlo directamente
                    console.log('Turndown ya está cargado');
                    // Tu código que utiliza Turndown...
                }


                window.init_editor = (editor) => {


                    // Detecta cuando se está pegando texto en el editor
                    editor.on("beforeChange", function(instance, changeObj) {
                        if (changeObj.origin === "paste") {
                            // Obtiene el texto del portapapeles
                            var clipboardData = event.clipboardData || window.clipboardData;


                            var items = clipboardData.items;
                            for (var i = 0; i < items.length; i++) {
                                var item = items[i];
                                console.log(item.type)
                                if (item.type.indexOf("image") !== -1) {
                                    // El portapapeles contiene una imagen
                                    console.log("Se encontró una imagen en el portapapeles", item);
                                    // Puedes acceder a los datos de la imagen utilizando item.getAsFile() o item.getAsString()
                                }
                            }

                            var clipboardHtml = clipboardData.getData("text/html");

                            // Comprueba si el texto proviene de Word
                            if (isWordHtml(clipboardHtml)) {
                                // Cancela el evento de pegado predeterminado
                                event.preventDefault();

                                changeObj.cancel();

                                // Reemplaza el texto y las imágenes con Markdown
                                replaceClipboardTextWithMarkdown(editor, changeObj, clipboardHtml);
                            }
                        }
                    });

                    // Convierte el HTML a texto utilizando la API DOMParser
                    /* function htmlToText(html) {
                        var dom = new DOMParser().parseFromString(html, "text/html");
                        return dom.body.textContent;
                    } */

                    // Comprueba si el texto es de Word en formato HTML
                    function isWordHtml(html) {
                        return /class="?Mso|style="[^"]*\bmso-|style='[^']*\bmso-|w:WordDocument/i.test(html);
                    }


                    // Reemplaza el texto del portapapeles con Markdown
                    function replaceClipboardTextWithMarkdown(instance, changeObj, clipboardHtml) {
                        // Extrae el contenido del body del HTML
                        var bodyContent = clipboardHtml.match(/<body[^>]*>([\s\S]*)<\/body>/i);
                        var bodyHtml = bodyContent ? bodyContent[1] : '';

                        // Convierte el HTML del body a texto y Markdown
                        var div = document.createElement("div");
                        div.innerHTML = bodyHtml;
                        var turndownService = new TurndownService();
                        var markdown = turndownService.turndown(div.innerHTML);

                        // Elimina los comentarios HTML del Markdown
                        markdown = markdown.replace(/<!--[\s\S]*?-->/g, "");

                        if (checkLocalImages(markdown))
                            alert("No puedes pegar imágenes con texto o con otras imágenes.")
                        else
                            instance.replaceRange(markdown, changeObj.from, changeObj.to);
                    }

                    var windows = []

                    function checkLocalImages(markdown) {
                        const pattern = /file:\/\/\/.*?\.(?:jpg|jpeg|png|gif)/gi;
                        // El patrón anterior buscará cualquier texto que comience con file:///
                        // seguido de cualquier cantidad de caracteres hasta el primer . seguido de una extensión de imagen válida (jpg, jpeg, png o gif)

                        const images = markdown.match(pattern);
                        return images && !!images.length
                    }

                }

                init_editor(editor_{{ $field['name'] }}.codemirror)

            });




            window.abrirMediaManager_{{ $field['name'] }} = function() {
            blur()

            }


            function bpFieldInitDummyFieldElement(element) {
                // this function will be called on pageload, because it's
                // present as data-init-function in the HTML above; the
                // element parameter here will be the jQuery wrapped
                // element where init function was defined
                console.log(element.val());
            }
        </script>
    @endBassetBlock
@endpush
