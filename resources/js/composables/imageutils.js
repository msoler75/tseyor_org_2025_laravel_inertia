const fallback_images = ["f1.jpg", "f2.jpg", "f3.jpg", "f4.jpg"];
const fallback_folder = "/almacen/medios/imagenes_contenidos_por_defecto";

export const getImageUrl = (src, defaultUrl) => {
  if (!defaultUrl) {
    const randomIndex = Math.floor(Math.random() * fallback_images.length);
    defaultUrl = fallback_folder + "/" + fallback_images[randomIndex];
  }
  if (!src) return defaultUrl;
  if (src.match(/^https?:\/\//)) return src;
  const prefix = src.match(/^\/?archivos/)
    ? ""
    : src.match(/^\/?almacen/)
    ? ""
    : "/almacen/";
  return (prefix + src).replace(/\/\//g, "/");
};

// para saber las dimensiones originales de las imagenes
var imagenes_pendientes = [];

export const getImageSize = async function (url) {
  console.log("getImageSize", url);
  const hasHost = url.match(/^https?:\/\//);
  // queremos la url relativa al sitio
  if (hasHost) url = url.replace(/^https?:\/\/[^/]+/, "");
  return new Promise(async (resolve, reject) => {
    var data = null;
    var idx = imagenes_pendientes.findIndex((x) => x.url == url);
    if (idx == -1) {
      // no existe en la lista de pendientes, eso significa que es la primera vez que se preguntan sus dimensiones
      imagenes_pendientes.push({ url, esperando: [] });
      await fetch(route("imagen.tamaño") + "?url=" + url).then(async (r) => {
        data = await r.text();
        idx = imagenes_pendientes.findIndex((x) => x == url);
        if (idx > -1) {
          for (const callback of imagenes_pendientes[idx].esperando)
            callback(data); // avisa a los que esperan por la misma imagen
          imagenes_pendientes.splice(idx, 1);
        }
      });
    } else {
      // quedamos esperando por que ya hay un proceso pendiente de obtener las dimensiones de la imagen
      data = await new Promise((resolve, reject) => {
        // agregamos una función de callback
        imagenes_pendientes[idx].esperando.push((d) => {
          resolve(d);
        });
      });
    }

    if (data) {
      try {
        const size = JSON.parse(data);
        resolve(size);
      } catch (err) {
        reject(err);
      }
    }
    reject();
  });
};
export const clearImagesPending = function () {
  imagenes_pendientes = [];
};

function canUseWebP() {
  var elem = document.createElement("canvas");
  if (!!(elem.getContext && elem.getContext("2d"))) {
    // was able or not to get WebP representation
    return elem.toDataURL("image/webp").indexOf("data:image/webp") == 0;
  }
  // very old browser like IE 8, canvas not supported
  return false;
}

var webp = null;

export const isWebPSupported = async () => {
  return new Promise(async (resolve, reject) => {
    if (webp !== null) resolve(webp);
    webp = canUseWebP();
    resolve(webp);
  });
};

/*

D:\projects\tseyor\laravel_inertia\storage\app\public\profile-photos\fXfEgu28nhgwkB537aojcblsgQ1z36dgw0kTtAlw.png


D:\projects\tseyor\laravel_inertia\storage\app/public\almacen/profile-photos/fXfEgu28nhgwkB537aojcblsgQ1z36dgw0kTtAlw.png


*/
