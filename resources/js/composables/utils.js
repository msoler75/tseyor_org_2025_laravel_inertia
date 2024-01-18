const fallback_images = ["f1.jpg", "f2.jpg", "f3.jpg", "f4.jpg"];
const fallback_folder = "/almacen/medios/imagenes_contenidos_por_defecto";

export const getImageUrl = (src, defaultUrl) => {
  if (!defaultUrl) {
    const randomIndex = Math.floor(Math.random() * fallback_images.length);
    defaultUrl = fallback_folder + "/" + fallback_images[randomIndex];
  }
  if (!src) return defaultUrl;
  if (src.match(/^https?:\/\//)) return src;
  const prefix = src.match(/^\/?archivos/) ? '' : src.match(/^\/?almacen/) ? '':'/almacen/'
  return (prefix  + src).replace(/\/\//g, '/')
};
