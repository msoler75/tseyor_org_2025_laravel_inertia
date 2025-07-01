export const getSrcUrl = (src) => {
  if (!src) return src;
  if (src.match(/^https?:\/\//)) return src;
  const prefix = src.match(/^\/?archivos/)
    ? ""
    : src.match(/^\/?almacen/)
    ? ""
    : "/almacen/";
  return (prefix + src).replace(/\/\//g, "/");
};

export const getEmbedYoutube = (url) => {
    if(!url) return url
  if (url.match(/^https:\/\/www.youtube.com\/embed\//)) return url;
  const u = url.match(
    /(?:https:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube-nocookie\.com\/embed\/)([^&]+)/
  );
  if (u && u[1]) {
    const videoId = u[1];
    return "https://www.youtube.com/embed/" + videoId;
  } else {
    return url;
  }
};

export const getMyDomain = function () {
  const myDomain =
    typeof window !== "undefined"
      ? window.location.origin
      : process.env.APP_URL;
  return myDomain;
};

let URLLib = {
    URL: typeof window !== 'undefined'
        ? window.URL
        : globalThis.URL
};

function getURLModule() {
    return URLLib; // Acceso directo sin promesas
}

export const belongsToCurrentDomain = async function (url) {
    console.log("belongsToCurrentDomain", url);
    const hasHost = url.match(/^https?:\/\//);
    if (!hasHost) return true;

    try {
        const { URL } = await getURLModule();
        const urlObj = new URL(url);
        const currentDomain = getMyDomain();
        return urlObj.hostname === new URL(currentDomain).hostname;
    } catch (error) {
        console.error("Error parsing URL:", error);
        return false;
    }
};

