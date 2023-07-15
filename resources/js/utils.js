export const imageUrl = (src, defaultUrl) => {
  if (!defaultUrl) defaultUrl = "/storage/profile-photos/user.png";
  if (!src) return defaultUrl;
  if (src.match(/^https?:\/\//)) return src;
  return "/storage/" + src;
};
