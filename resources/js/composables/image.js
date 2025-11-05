import {getSrcUrl, belongsToCurrentDomain} from './srcutils'

const fallback_images = ["f1.jpg", "f2.jpg", "f3.jpg", "f4.jpg"];
const fallback_folder = "/almacen/medios/imagenes_contenidos_por_defecto";

export const getImageUrl = (src, defaultUrl) => {
  if (!defaultUrl) {
    const randomIndex = Math.floor(Math.random() * fallback_images.length);
    defaultUrl = fallback_folder + "/" + fallback_images[randomIndex];
  }
  if(!src) return getSrcImageUrl(defaultUrl)
  var r = getSrcImageUrl(getSrcUrl(src))
// console.log('getImageUrl', {src, r})
return r
};

// OBTENER INFORMACION (dimensiones) DE UNA IMAGEN
// Cache en memoria para optimización de performance (solo cliente)
// Estas variables son seguras en SSR porque solo se usan cuando typeof window !== 'undefined'
const imagenes_cache = {};
const imagenes_pendientes = [];

export const getImageSize = async function (url) {
  // console.log("getImageSize", url);
  url = url.replace(/^https?:\/\/[^/]+/, "");

  return new Promise(async (resolve, reject) => {
    // Verificar si la imagen está en caché
    if (imagenes_cache[url]) {
      return resolve(imagenes_cache[url]);
    }

    // Verificar si hay una consulta pendiente para esta URL
    const idx = imagenes_pendientes.findIndex((x) => x.url === url);
    if (idx !== -1) {
      // Hay una consulta pendiente, esperamos su resultado
      return new Promise((res) => {
        imagenes_pendientes[idx].esperando.push(res);
      }).then(resolve).catch(reject);
    }

    if(typeof window === 'undefined')
        return reject(error);

    // No hay consulta pendiente, iniciamos una nueva
    imagenes_pendientes.push({ url, esperando: [] });

    try {
      const response = await axios.get(`${getApiUrl()}/image_size`, {
        params: { url }
      });

      const data = response.data;
      // console.log("Respuesta recibida para", url, data);

      // Guardar en caché
      imagenes_cache[url] = data;

      // Resolver esta promesa y todas las pendientes
      resolve(data);
      const pendingIdx = imagenes_pendientes.findIndex((x) => x.url === url);
      imagenes_pendientes[pendingIdx].esperando.forEach(callback => callback(data));
      imagenes_pendientes.splice(pendingIdx, 1);

    } catch (error) {
      // Limpiar las promesas pendientes en caso de error
      const pendingIdx = imagenes_pendientes.findIndex((x) => x.url === url);
      if (pendingIdx !== -1) {
        imagenes_pendientes[pendingIdx].esperando.forEach(callback => callback(null));
        imagenes_pendientes.splice(pendingIdx, 1);
      }

      reject(error);
    }
  });
};


function canUseWebP() {
    // console.log('canUseWebP')
    var elem = document.createElement("canvas");
  if (!!(elem.getContext && elem.getContext("2d"))) {
    // was able or not to get WebP representation
    const result  = elem.toDataURL("image/webp").indexOf("data:image/webp") == 0;
    // console.log('canUseWebP ended')
    return result
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

export const getSrcImageUrl = (url) => {
    const parts = url.split('?')
    url = parts[0]
    url = decodeURIComponent(url)
    return url.replace(/ /g, '%20').replace(/\(/g, '%28').replace(/\)/g, '%29') + (parts.length > 1 ? '?' + parts[1] : '')
  }



  export const clear = function() {
    imagenes_pendientes.splice(0, imagenes_pendientes.length);
  }

  var imagesInfo = []

  export const saveImagesInfo = function(_imagesInfo) {
    imagesInfo = _imagesInfo ? _imagesInfo : []
  }

  export const getImageInfo = function(url) {
    // console.log('getImageInfo', url)
    return imagesInfo[url]
  }
