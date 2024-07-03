import {getSrcUrl} from './srcutils'

const fallback_images = ["f1.jpg", "f2.jpg", "f3.jpg", "f4.jpg"];
const fallback_folder = "/almacen/medios/imagenes_contenidos_por_defecto";

export const getImageUrl = (src, defaultUrl) => {
  if (!defaultUrl) {
    const randomIndex = Math.floor(Math.random() * fallback_images.length);
    defaultUrl = fallback_folder + "/" + fallback_images[randomIndex];
  }
  if(!src) return getSrcImageUrl(defaultUrl)
  var r = getSrcImageUrl(getSrcUrl(src))
console.log('getImageUrl', {src, r})
return r
};

// guardamos los datos de las imagenes
const imagenes_cache = {};

export const getImageSize = async function (url) {
  console.log("getImageSize", url);
  url = url.replace(/^https?:\/\/[^/]+/, "");
  return new Promise(async (resolve, reject) => {
    if (imagenes_cache[url])
        return resolve(imagenes_cache[url]);
    console.log("fetch image.tamaño", url);
    return await fetch("/image_size" + "?url=" + url).then(async (r) => {
      console.log("fetch respuesta", url, r);
      const data = await r.json();
      console.log({ data });
      imagenes_cache[url] = data;
      return resolve(data);
    })
    .catch(() => reject());
  });
};

export const isFromMyDomain = function (url) {

    console.log('isFromMyDomain', url)
    const hasHost = url.match(/^https?:\/\//);

  // si es una url relativa, entonces está en mi dominio
  if (!hasHost) {
   console.log('yes')
    return true;
  }

  try {
    const urlObject = new URL(url);
    // Obtener el host de la URL
    const imageUrlHost = urlObject.hostname;

    // Obtener el host de tu sitio
    const yourSiteUrl = window?.location?.hostname;

    // Comprobar si la URL de la imagen apunta a tu host
    return imageUrlHost === yourSiteUrl;
  } catch (error) {
    console.error("Error al analizar la URL:", error);
  }

  console.log('truez')
  return true; // indeterminado
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

export const getSrcImageUrl = (url) => {
    const parts = url.split('?')
    url = parts[0]
    url =  encodeURIComponent(url)
    return url.replace(/%2F/g, '/').replace(/\(/g, '%28').replace(/\)/g, '%29') + (parts.length > 1 ? '?' + parts[1] : '')
  }
