//import MarkdownIt from 'markdown-it'
import showdown from 'showdown'
import TurndownService from 'turndown'
import { gfm } from 'turndown-plugin-gfm'

const turndownService = new TurndownService({ bulletListMarker: '-', headingStyle: 'atx' })
turndownService.use(gfm)
turndownService.keep(['span'])

/*

Nuestras conversiones perosnalizadas de markdown <-> HTML
*/

export function HtmlToMarkdown(html) {
    // convertimos cualquier clase dentro de párrafo p en un marcaje especial
    // console.log('HtmlToMarkdown', html)
    return turndownService.turndown(html

        // reemplazamos los atributos de imagen
        .replace(/<img\s+([^>]+)>/g,
            (match, atributos) => {
                // console.log('r1', { match, atributos })
                var values = []
                atributos.replace(/(\w+)=['"](.*?)['"]/g, (match, atributo, valor) => {
                    // console.log('r2', { atributo, valor })
                    if (atributo === 'width' || atributo === 'height') {
                        values.push(`${atributo}=${valor}`)
                    }
                    return match
                })
                // console.log('values', values)
                return match + (values.length ? `{${values.join(', ')}}` : '')
            })
             // reemplazamos los estilos de párrafo
    .replace(/<p style=["']([^>]*)["'][^>]*>/g, '$&{style=$1}')

            )
}

// cambia los caracteres codificados de < y > a su valor real
export function DecodeHtml(html) {
    return html.replace(/&gt;/g, '>').replace(/&lt;/g, '<')
}

export function MarkdownToHtml(raw_markdown) {
    // vamos a renderizar el markdown, y sustituimos las clases de p
    /* var md = new MarkdownIt({
        html: true,
        linkify: true
    });
    return md.render(raw_markdown)
    */

    console.log('MarkdownToHtml', raw_markdown)
    const converter = new showdown.Converter()
    converter.setFlavor('github')
    return converter.makeHtml(raw_markdown)

    // primero reemplazamos las imágenes con atributos
    .replace(/(<img[^>]*>){(\w+=[^}]+)}/g, (match, img, attributes) => {
            console.log('r1', {match, attributes})
            var values = []
            attributes.replace(/(\w+)=([^,]+)/g, (match, atributo, valor) => {
                console.log('r2', {match, atributo, valor})
                values.push(`${atributo}=${valor}`)
                return match
            })
            console.log({values})
            return img.replace('<img', '<img '+values.join(' '))
        })
    // reemplazamos los párrafos con estilos
    .replace(/<p>{style=([^}]*)}/g, "<p style='$1'>")
    // quitamos los espacios sobrantes
    .replace(/<p>\s+<\/p>\n?/g, '').replace(/\n/g, '')


}



export function detectFormat(text) {
    if(!text) return { format: "html", probability: 1 };
    
    // Contamos la cantidad de etiquetas HTML
    const htmlTagsCount = (text.match(/<\/?[a-z][a-z0-9]*\b[^>]*>/gi) || []).length;

    // Contamos la cantidad de marcadores Markdown
    const markdownMarkersCount = (text.match(/^#\s+\S+|^-|^>\s+\S+|-{4,99}|\||[*\[\]`!]|\!\[|\]\(/gm) || []).length;

    // Calculamos la probabilidad de que sea Markdown o HTML
    const totalMarkers = markdownMarkersCount + htmlTagsCount;
    const markdownProbability = markdownMarkersCount / (totalMarkers + 1);
    const htmlProbability = htmlTagsCount / (totalMarkers + 1);

    console.log({htmlTagsCount,markdownMarkersCount,  totalMarkers, markdownProbability,htmlProbability })

    // Establecemos un umbral de probabilidad para determinar el formato
    const threshold = 0.6;

    // Detectar si el texto está en formato Markdown
    if (markdownProbability >= threshold && markdownProbability > htmlProbability) {
        console.log('formato MARKDOWN detectado', markdownProbability)
      return { format: "md", probability: markdownProbability };
    }

    // Detectar si el texto está en formato HTML
    const htmlPattern = /<(?:"[^"]*"['"]*|'[^']*'['"]*|[^'">])+>/i;
    const containsHTMLPattern = htmlPattern.test(text);

    if (htmlProbability >= threshold && htmlProbability > markdownProbability && containsHTMLPattern) {
        console.log('formato HTML detectado', htmlProbability)
      return { format: "html", probability: htmlProbability };
    }

    // Si no se detecta un formato claro, se considera ambiguo
    console.log('formato AMBIGUO')
    return { format: "ambiguous", probability: 0.5 };
  }
