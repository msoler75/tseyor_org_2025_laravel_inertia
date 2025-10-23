import { getSrcUrl } from "./srcutils";

export const parseFiles = (data) => {
  if (!data) return [];
  var archivos = [];
  if (Array.isArray(data)) archivos = data;
  else if (typeof data === "string")
    try {
      archivos = JSON.parse(data);
    } catch (err) {
      console.log("parseFiles1", err);
      archivos = data.split(",").map((x) => x.trim());
    }
  else return [];

  try {
    const r = [];
    var index = 1;
    for (var archivo of archivos) {
      const idx = archivo.lastIndexOf("/");
      var filename = archivo.substring(idx + 1);
      // removemos extension
      const idx2 = filename.lastIndexOf(".");
      // gurdamos extension
      var extension = "";
      if (idx2 > 0) {
        extension = filename.substring(idx2);
        // removemos la extension
        filename = filename.substring(0, idx2);
      }
      // formato del archivo: informe1_aa16bd6f64565f9156f17a08ecadd606.docx
      // queremos remover la última parte: _aa16bd6f64565f9156f17a08ecadd606
      const idx3 = filename.lastIndexOf("_");
      if (idx3 > 0 && filename.length - idx3 == 32 + 1)
        filename = filename.substring(0, idx3);
      // agregamos la extensión
      filename += extension;
      const label = filename;
      const title = filename;
      r.push({ index, title, filename, label, src: getSrcUrl(archivo) });
      index++;
    }
    return r;
  } catch (err) {
    console.error("parseFiles2", err);
    return [];
  }
};
