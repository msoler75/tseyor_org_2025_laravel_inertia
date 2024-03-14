<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$titulo}} - tseyor.org</title>
    <meta name="author" content="Universidad Tseyor de Granada">
    <style>
        html, body {
            font-family: 'Calibri', Helvetica, serif;
            text-align: justify;
        }
        html { margin: 3em 5em }
        table {
            border-collapse: collapse;
            margin: .4em 0;
        }
        table, th, td {
            border: 1px solid gray;
            padding: .3em;
        }
        img {
            display: block;
            margin: 2em auto;
        }
        p {
            overflow-wrap: break-word;
            hyphens: auto; /* Cambiado a 'auto' en lugar de 'manual' */
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
    </style>
</head>
    <body>

        <div class="footer">
            <span class="pagenum"></span>
        </div>

    {!! $texto !!}

    </body>

</html>
