@php
    $apiKey = config('services.google_maps.apikey');
@endphp
@include('crud::fields.inc.wrapper_start')
<button id="btnGeo" disabled class="btn btn-default" type="button" class="btn btn-primary">Calcular coordenadas</button>

<div id="map" style="width: 400px; height: 400px; margin-top: 5px"></div>

@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    {{-- How to add some JS to the field? --}}
    @bassetBlock('backpack/crud/fields/geomaps_coords.js')
        <script>
            // es una copia de la función en @/composables/google.js:

            const apiKey = '{{ $apiKey }}'
            var geocoder = null
            var marker = null

            function createMapScript(options) {
                const googleMapScript = document.createElement("SCRIPT");
                let baseUrl = "https://maps.googleapis.com/maps/api/js?";

                let url =
                    baseUrl +
                    Object.keys(options)
                    .map(
                        (key) =>
                        encodeURIComponent(key) + "=" + encodeURIComponent(options[key])
                    )
                    .join("&");

                googleMapScript.setAttribute("src", url);
                googleMapScript.setAttribute("async", "");
                googleMapScript.setAttribute("defer", "");

                return googleMapScript;
            }

            function loadGoogleMaps( /*apiKey, callback, */ options) {
                const defaultOptions = {
                    libraries: "maps,marker",
                    v: "weekly",
                    callback: "initMap",
                    key: apiKey,
                };
                if (!options) options = {};
                options = {
                    ...defaultOptions,
                    ...options
                };

                if (!window.google || !window.google.maps) {
                    const lib = createMapScript(options);
                    document.body.appendChild(lib);
                } else window[callback]();
            }


            var map = null
            document.addEventListener('DOMContentLoaded', function() {
                loadGoogleMaps();
            })


            function initMap() {
                console.log('initMap')
                // Inicializar el mapa
                map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: 0,
                        lng: 0
                    },
                    // queremos un zoom a nivel de país:
                    zoom: 4
                });

                const latitud = document.querySelector("input[name=latitud]")
                const longitud = document.querySelector("input[name=longitud]")

                if(latitud.value&&longitud.value)
                    setMarker({lat: Number(latitud.value), lng: Number(longitud.value)})

            // habilitamos el botón de calcular coordenadas

                geocoder = new google.maps.Geocoder();

                document.getElementById('btnGeo').addEventListener('click', function(event) {
                    event.preventDefault()
                    calcularCoordenadas()
                })

                document.getElementById('btnGeo').removeAttribute('disabled')


            }

            function setMarker(position) {
                // Crear el marcador en el mapa
                if (!marker)
                    marker = new google.maps.Marker({
                        position,
                        map
                    });
                else {
                    marker.setPosition(position)
                }
                map.panTo(position)
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
                        const coords = results[0].geometry.location.toJSON()
                        latitud.value = coords.lat
                        longitud.value = coords.lng

                        setMarker(coords)
                    }
                })
            }
        </script>
    @endBassetBlock
@endpush
