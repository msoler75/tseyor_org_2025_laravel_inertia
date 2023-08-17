import MarkdownIt from 'markdown-it'
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
                console.log('r1', { match, atributos })
                var values = []
                atributos.replace(/(\w+)=['"](.*?)['"]/g, (match, atributo, valor) => {
                    console.log('r2', { atributo, valor })
                    if (atributo === 'width' || atributo === 'height') {
                        values.push(`${atributo}=${valor}`)
                    }
                    return match
                })
                console.log('values', values)
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
    var md = new MarkdownIt({
        html: true,
        linkify: true
    });

    return md.render(raw_markdown)
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
