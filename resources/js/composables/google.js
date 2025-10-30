function createMapScript(options) {
  const googleMapScript = document.createElement("SCRIPT");
  if (typeof options !== "object") {
    throw new Error("options should  be an object");
  }

  // libraries
  if (Array.isArray(options.libraries)) {
    options.libraries = options.libraries.join(",");
  }
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

export function loadGoogleMaps(apiKey, callback, options) {
  if (typeof callback != "string")
    console.error("callback ha de ser un string");

  const defaultOptions = {
    libraries: "maps,marker",
    v: "weekly",
    callback,
    key: apiKey,
  };
  if (!options) options = {};
  options = { ...defaultOptions, ...options };

  console.log("loadGoogleMaps - window.google exists:", !!window.google);
  console.log("loadGoogleMaps - window.google.maps exists:", !!(window.google && window.google.maps));
  console.log("loadGoogleMaps - callback:", callback);
  console.log("loadGoogleMaps - window[callback] exists:", !!window[callback]);

  if (!window.google || !window.google.maps) {
    console.log("Creando script de Google Maps");
    const lib = createMapScript(options);
    document.body.appendChild(lib);
  } else {
    console.log("Google Maps ya cargado, llamando callback directamente");
    window[callback]();
  }
}
