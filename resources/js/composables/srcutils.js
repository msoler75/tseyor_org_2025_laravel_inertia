
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
    if(url.match(/^https:\/\/www.youtube.com\/embed\//))
        return url
    const u = url.match(/(?:https:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube-nocookie\.com\/embed\/)([^&]+)/);
    if (u && u[1]) {
        const videoId = u[1];
        return 'https://www.youtube.com/embed/' + videoId
    }
    else {
        return url
    }
  }




