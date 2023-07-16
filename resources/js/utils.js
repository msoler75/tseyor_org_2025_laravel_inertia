const fallback_images = ["f1.jpg", "f2.jpg", "f3.jpg", "f4.jpg"];
const fallback_folder = "/storage/imagenes/fallback";

export const imageUrl = (src, defaultUrl) => {
  if (!defaultUrl) {
    const randomIndex = Math.floor(Math.random() * fallback_images.length);
    defaultUrl = fallback_folder + "/" + fallback_images[randomIndex];
  }
  if (!src) return defaultUrl;
  if (src.match(/^https?:\/\//)) return src;
  return "/storage/" + src.replace(/^\/storage\//, '');
};
