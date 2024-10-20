<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $titulo }} - tseyor.org</title>
    <meta name="author" content="Universidad Tseyor de Granada - tseyor.org">
    <style>
        html,
        body {
            font-family: 'Calibri', Helvetica, serif;
            text-align: justify;
        }

        html {
            margin: 3em 5.5em
        }

        table {
            border-collapse: collapse;
            margin: .4em 0;
        }

        table,
        th,
        td {
            border: 1px solid gray;
            padding: .1em;
        }

        img {
            display: block;
            margin: 2em auto;
            max-width: 100%;
            position: relative;
            clear: both;
        }

        table,
        tr,
        td,
        th {
            border: 0
        }

        p {
            overflow-wrap: break-word;
            hyphens: auto;
            /* Cambiado a 'auto' en lugar de 'manual' */
            text-wrap: pretty;
        }

        .footer {
            width: 100%;
            text-align: right;
            position: fixed;
            bottom: 0px;
        }

        .pagenum:before {
            content: counter(page);
        }


        /* pie de nota de imagenes */

        /* formato posiblemente obsoleto */
        img+br+em,
        img+em,
        p[image-note] {
            display: block;
            font-size: 90%;
            margin-top: -2.1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        img+br+em,
        img+em {
            margin-top: -1.5rem;
        }

        /* formato para pie de nota de texto actualizado */

        p[has-image] {
            display: block;
            margin: 20px auto;
            text-align: center;
        }

        p img {
            display: inline-block;
            margin: 0 auto;
            margin-bottom: 25px;
            max-height: 700px;
        }

        p[has-note] {
            text-align: center;
            font-size: 85%;
        }


        /* chat gpt recommendation */
        h1 {
            page-break-before: always;
        }

        table {
            page-break-inside: avoid;
        }
        /* no funciona */
        p {
            widows: 5;
            orphans: 5;
        }
    </style>
</head>

<body>

    <article>
        <h1>{!! $titulo !!}</h1>

        {!! $texto !!}
    </article>

    <div class="footer">
        <span class="pagenum"></span>
    </div>

</body>

</html>
