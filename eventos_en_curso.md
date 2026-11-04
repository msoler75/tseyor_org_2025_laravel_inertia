# Informe: evento en curso se muestra como "evento pasado"

## Síntoma

Un evento que **está ocurriendo ahora mismo** (aún no han pasado ni 2 horas desde su inicio) se muestra en la web como **"evento pasado"** en lugar de "en curso".

## Causa probable

El cálculo de "en curso / pasado / futuro" en el frontend (`esEventoEnCurso`) se hace comparando `dayjs()` (hora **local del navegador**) contra fechas que representan la hora de **Europe/Madrid**.

Hay **tres husos horarios** en juego y no se están normalizando:

1. **Navegador del usuario** → `dayjs()` devuelve la hora local del visitante.
2. **Servidor (PHP)** → `Carbon::now()`, según `config('app.timezone')`.
3. **Base de datos** → debería guardar en `Europe/Madrid`, pero es un `DATETIME` sin zona; la interpretación depende de quién lo lea.

Cuando el usuario está en un huso distinto al del evento (p. ej. Chile vs Madrid), las comparaciones se desfasan varias horas y el evento se clasifica mal. El **margen de 2 horas** de la función no cubre ese desfase.

Además, hay un **bug secundario** en la función: se usa

```js
dayjs(horaInicio, 'HH:mm')
```

sin haber registrado el plugin `customParseFormat` de dayjs, por lo que el parseo de `horaInicio` puede devolver resultados incorrectos. Esto afecta sobre todo a eventos **sin `fecha_fin` pero con `hora_inicio`** (caso típico del evento de hoy).

