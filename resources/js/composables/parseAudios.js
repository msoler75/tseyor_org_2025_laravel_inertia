export const parseAudios = (json, titulo) => {
    try {
        const audios = JSON.parse(json)
        const r = []
        const some = audios.length > 1
        for (var idx in audios) {
            const audio = audios[idx]
            const label = some ? `Audio ${idx}` : 'Audio'
            const title = some ? `${titulo} (${idx})` : titulo
            r.push({ title, label, src: `/storage/${audio}` })
        }
        return r
    }
    catch (err) {
        return []
    }
}
