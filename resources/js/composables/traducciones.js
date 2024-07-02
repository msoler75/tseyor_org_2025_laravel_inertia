const traducciones = {
    paginas: 'páginas',
    guias: 'guías estelares',
    terminos: 'glosario',
    entradas: 'blog',
    lugares: 'lugares de la galaxia',
}


export default function  traducir  (col){
    return traducciones[col] || col
}