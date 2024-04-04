const imagenes_pendientes = [];

export const getSize = async function (url) {
    return new Promise(async (resolve, reject) => {
      // primero miramos en localstorage si ya existe
      var data = localStorage.getItem("image_" + url);
      if (!data) {
        var idx = imagenes_pendientes.findIndex((x) => x.url == url);
        if (idx == -1) {
          // no existe en la lista de pendientes, eso significa que es la primera vez que se preguntan sus dimensiones
          imagenes_pendientes.push({ url, esperando: [] });
          await fetch(route("imagen.info") + "?url=" + url).then(async (r) => {
            data = await r.text();
            if (data) localStorage.setItem("image_" + url, data);
            idx = imagenes_pendientes.findIndex((x) => x == url);
            for (const callback of imagenes_pendientes[idx].esperando)
                callback(data); // avisa a los que esperan por la misma imagen
            imagenes_pendientes.splice(idx, 1);
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
      }
      if (data) resolve(JSON.parse(data));
      reject();
    });
  }

  export const clear = function() {
    imagenes_pendientes = [];
  }
