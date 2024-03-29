//import MarkdownIt from 'markdown-it'
import showdown from "showdown";
import TurndownService from "turndown";
import { gfm } from "turndown-plugin-gfm";

const turndownService = new TurndownService({
  bulletListMarker: '-',
  headingStyle: 'atx',
  hr: '---'
});
turndownService.use(gfm);
turndownService.keep(['span', 'sup', 'u']);

/*

Nuestras conversiones personalizados de markdown <-> HTML
*/

export function replaceQuillEditorClasses(html) {
    return html.replace(/<(\w+)\s(?:[^>]*\s)?((class|style)="[^"]*")/g, function (element, tag) {
        const styles = [];
        // Verificar si es 'style' o 'class' y añadir al array 'styles'
        element.replace(/(?:class|style)="([^"]*)"/g, function (match, attribute) {
            if (match.includes('style=')) {
                // Desglosar estilos y añadir al array 'styles'
                attribute.split(';').forEach(style => {
                    if (style.trim() !== '') {
                        styles.push(style.trim());
                    }
                });
            } else if (match.includes('class=')) {
                // Añadir estilos correspondientes a cada clase al array 'styles'
                attribute.split(' ').forEach(cls => {
                    if (cls.startsWith('ql-')) {
                        // Lógica para mapear clases a estilos
                        // Aquí puedes definir la lógica para mapear clases a estilos
                        if (cls === 'ql-align-left') styles.push('text-align: left');
                        if (cls === 'ql-align-justify') styles.push('text-align: justify');
                        if (cls === 'ql-align-center') styles.push('text-align: center');
                        if (cls === 'ql-align-right') styles.push('text-align: right');
                        if (cls === 'ql-size-small') styles.push('font-size: .75em');
                        if (cls === 'ql-size-larger') styles.push('font-size: 1.75em');
                        if (cls === 'ql-size-huge') styles.push('font-size: 2.25em');
                    }
                });
            }
        });
        if(styles.length == 0) return '<' + tag;
        return '<' + tag + ' style="' + styles.join('; ') + '"';
    })
}

export function HtmlToMarkdown(html) {
  // convertimos cualquier clase dentro de párrafo p en un marcaje especial
  console.log('HtmlToMarkdown', {html})

  // checamos si tiene alguna classe de Quill Editor
  //if(html.match(/class="ql-/))
    //html = replaceQuillEditorClasses(html)


  // quitamos estilo de centro en imagenes solitarias (no es necesario)
  html = html.replace(
    /<p style=.text-align:\s*center\s*;?\s*.>(<img[^>]+>)<\/p>/g,
    "<p>$1</p>"
  )

  const md =  turndownService.turndown(
    html
      // reemplazamos los atributos de imagen
      .replace(/<img\s+([^>]+)>/g, (match, atributos) => {
        // console.log('r1', { match, atributos })
        var values = [];
        atributos.replace(/(\w+)=['"]?([^'"\s]+)['"]?/g, (match, atributo, valor) => {
          // console.log('r2', { atributo, valor })
          //if(atributo==="src")
          //return ""
          if (atributo === "width" || atributo === "height") {
            values.push(`${atributo}=${valor}`);
          }
          if(atributo=='style') {
            valor.split(/\s*;\s*/).forEach(style => {
              const [key, value] = style.split(/\s*:\s*/)
              if(key==='width' || key==='height')
                values.push(`${key}=${value}`);
            })
          }
          return match;
        });
        console.log('values', values)
        return match + (values.length ? `{${values.join(", ")}}` : "");
      })
      // reemplazamos los estilos de párrafo
      .replace(/<p style=["']([^>]*)["'][^>]*>/g, "$&{style=$1}")
      // eliminamos estilos innecesarios
      .replace(/{style=text-align: left;}/g, "")
      .replace('{width=auto, height=auto}', '') // removemos estilos innecesarios
  );

  console.log({md})
  return md
}

// cambia los caracteres codificados de < y > a su valor real
export function DecodeHtml(html) {
  return html.replace(/&gt;/g, ">").replace(/&lt;/g, "<");
}

export function MarkdownToHtml(raw_markdown) {
  // vamos a renderizar el markdown, y sustituimos las clases de p
  /* var md = new MarkdownIt({
        html: true,
        linkify: true
    });
    return md.render(raw_markdown)
    */

   console.log("MarkdownToHtml", {raw_markdown});
   // raw_markdown = raw_markdown.replace(/\n\s*---\s*\n/mg, '<hr/>')
  const converter = new showdown.Converter();
  converter.setFlavor("github");
  const html=  (
    converter
      .makeHtml(raw_markdown)

      // primero reemplazamos las imágenes con atributos
      .replace(/(<img[^>]*>){(\w+=[^}]+)}/g, (match, img, attributes) => {
        var values = [];
        attributes.replace(/(\w+)=([^,]+)/g, (match, atributo, valor) => {
          values.push(`${atributo}=${valor}`);
          return match;
        });
        return img.replace("<img", "<img " + values.join(" "));
      })
      // reemplazamos los párrafos con estilos
      .replace(/<p>{style=([^}]*)}/g, "<p style='$1'>")
      // quitamos los espacios sobrantes
      .replace(/<p>\s+<\/p>\n?/g, "")
      .replace(/\n/g, "")
      // centramos las imágenes solitarias
      .replace(/<p>(<img[^>]+>)<\/p>/g, "<p style='text-align: center'>$1</p>")
  );
  console.log({html})
  return html
}

export function detectFormat(text) {
  if (!text) return { format: "html", probability: 1 };

  // Contamos la cantidad de etiquetas HTML
  const htmlTagsCount = (text.match(/<(p|img|div|strong|b|i|em|a)/gi) || [])
    .length;

  // Contamos la cantidad de marcadores Markdown
  const markdownMarkersCount = (
    text.match(/^#\s+\S+|^-|^>\s+\S+|-{4,99}|\||[*\[\]`!]|\!\[|\]\(/gm) || []
  ).length;

  // Calculamos la probabilidad de que sea Markdown o HTML
  const totalMarkers = markdownMarkersCount + htmlTagsCount;
  const markdownProbability = markdownMarkersCount / (totalMarkers + 1);
  const htmlProbability = htmlTagsCount / (totalMarkers + 1);

  console.log({
    htmlTagsCount,
    markdownMarkersCount,
    totalMarkers,
    markdownProbability,
    htmlProbability,
  });

  // Establecemos un umbral de probabilidad para determinar el formato
  const threshold = 0.6;

  // Detectar si el texto está en formato Markdown
  if (
    markdownProbability >= threshold &&
    markdownProbability > htmlProbability
  ) {
    console.log("formato MARKDOWN detectado", markdownProbability);
    return { format: "md", probability: markdownProbability };
  }

  // Detectar si el texto está en formato HTML
  const htmlPattern = /<(?:"[^"]*"['"]*|'[^']*'['"]*|[^'">])+>/i;
  const containsHTMLPattern = htmlPattern.test(text);

  if (
    htmlProbability >= threshold &&
    htmlProbability > markdownProbability &&
    containsHTMLPattern
  ) {
    console.log("formato HTML detectado", htmlProbability);
    return { format: "html", probability: htmlProbability };
  }

  // Si no se detecta un formato claro, se considera ambiguo
  console.log("formato AMBIGUO");
  return { format: "ambiguous", probability: 0.5 };
}
