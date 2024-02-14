
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