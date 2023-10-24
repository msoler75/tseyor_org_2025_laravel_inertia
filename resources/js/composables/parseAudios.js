export const parseAudios = (json, titulo) => {
  var audios = [];
  try {
    audios = JSON.parse(json);
  } catch (err) {
    audios = json.split(",").map((x) => x.trim());
  }
  try {
    const r = [];
    const some = audios.length > 1;
    for (var idx in audios) {
      const index = parseInt(idx) + 1;
      const audio = audios[idx];
      const label = some ? `Audio ${index}` : "Audio";
      const title = some ? `${titulo} (audio ${index})` : titulo;
      r.push({ title, label, src: `/storage/${audio}` });
    }
    return r;
  } catch (err) {
    return [];
  }
};
