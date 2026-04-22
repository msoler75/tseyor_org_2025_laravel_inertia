<!DOCTYPE html>
<html>
<head>
    <style>
        html, body {
            background: #2d3768;
            color: #bbb;
        }
        body {
            padding: 15px;
        }
        </style>
    <title>Redirigiendo...</title>
</head>
<body>
    <form id="redirectForm" action="{{ $url }}" method="POST">
        <input type="hidden" name="token" value="{{ $token }}">
    </form>
    <script>
        // when document is ready, submit the form
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('redirectForm').submit();
        });
    </script>
</body>
</html>
