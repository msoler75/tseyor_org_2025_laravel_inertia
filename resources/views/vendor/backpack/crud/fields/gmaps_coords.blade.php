@php
    $apiKey = config('services.google_maps.apikey');
@endphp
@include('crud::fields.inc.wrapper_start')
<button id="btnGeo" disabled class="btn btn-default" type="button" class="btn btn-primary">Calcular coordenadas</button>

@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    {{-- How to add some JS to the field? --}}
    @bassetBlock('backpack/crud/fields/geomaps_coords.js')
        <script>
            var geocoder = null

            function createMapScript(options) {
                const googleMapScript = document.createElement('SCRIPT')
                if (typeof options !== 'object') {
                    throw new Error('options should  be an object')
                }

                // libraries
                /* eslint-disable no-prototype-builtins */
                if (Array.prototype.isPrototypeOf(options.libraries)) {
                    options.libraries = options.libraries.join(',')
                }
                let baseUrl = 'https://maps.googleapis.com/maps/api/js?'

                let url =
                    baseUrl +
                    Object.keys(options)
                    .map((key) => encodeURIComponent(key) + '=' + encodeURIComponent(options[key])).join('&')

                googleMapScript.setAttribute('src', url)
                googleMapScript.setAttribute('async', '')
                googleMapScript.setAttribute('defer', '')

                return googleMapScript;
            }


            function loadGoogleMaps() {
                const lib = createMapScript({
                    key: '{{$apiKey}}',
                    // libraries: 'maps,marker',
                    // v: 'beta',
                    callback: 'initMap'
                });
                document.body.appendChild(lib);
            }

            setTimeout(function() {
                loadGoogleMaps();
            }, 1500)

            function initMap() {
                geocoder = new google.maps.Geocoder();

                document.getElementById('btnGeo').addEventListener('click', function(event) {
                    event.preventDefault()
                    calcularCoordenadas()
                })

                document.getElementById('btnGeo').removeAttribute('disabled')
            }

            function calcularCoordenadas() {

                if (!geocoder) {
                    console.warn('geocoder null')
                    loadGoogleMaps()
                    return
                }
                const direccion = document.querySelector("input[name=direccion]")
                const poblacion = document.querySelector("input[name=poblacion]")
                const codigo = document.querySelector("input[name=codigo]")
                const provincia = document.querySelector("input[name=provincia]")
                const pais = document.querySelector("select[name=pais]")

                const latitud = document.querySelector("input[name=latitud]")
                const longitud = document.querySelector("input[name=longitud]")

                var address = direccion.value + ', ' + poblacion.value + ', ' + codigo.value + ' ' + provincia.value + ', ' +
                    pais.value

                    console.log('nueva dirección:', address)

                geocoder.geocode({
                        'address': address
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            const  coords = results[0].geometry.location.toJSON()
                            latitud.value = coords.lat
                            longitud.value = coords.lng
                        }
                    }
                    )
                }
        </script>
    @endBassetBlock
@endpush
