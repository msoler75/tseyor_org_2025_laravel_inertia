# Sistema de Colas de Tareas/Email

## Resumen
- Nunca se saturará el límite de Dreamhost
- Los emails críticos siempre tienen hueco
- Las invitaciones tienen prioridad sobre los boletines
- El sistema es robusto y seguro para producción

## Motivo
El sistema de colas de email está diseñado para:
- No saturar el límite de Dreamhost (100 emails/hora)
- Reservar siempre capacidad para emails críticos (formularios de contacto, notificaciones urgentes)
- Priorizar invitaciones de equipo sobre boletines masivos

## Tabla Resumen

| Cola              | Prioridad      | Clase principal                      | Límite/hora | RateLimited | Notas                                  |
|-------------------|---------------|--------------------------------------|-------------|-------------|----------------------------------------|
| default           | Alta           | InvitacionEquipoEmail                | 50*         | Sí          | Invitaciones de equipo                 |
| low_priority      | Baja           | BoletinEmail                        | 15          | Sí          | Boletines masivos                      |
| audio_processing  | Muy baja       | (otras, ej. audio)                   | 50*         | Sí          | Solo si no hay jobs más prioritarios   |
| (directo)         | Máxima         | Formularios, notificaciones, sistema | 50*         | No          | No pasan por la cola ni rate limiting  |

*El límite real es el `overall` de 50/hora para todos los jobs en cola. Los emails directos no comparten ese límite y siempre tienen hueco.

## Colas Definidas
- **default**: invitaciones de equipo (alta prioridad)
- **low_priority**: boletines masivos (baja prioridad)
- **audio_processing**: tareas de audio (muy baja prioridad)

## Prioridades
1. **Invitaciones de equipo** (default): prioridad máxima en jobs
2. **Boletines** (low_priority): solo se procesan si hay hueco
3. **Audio**: solo si no hay nada más
4. **Emails críticos** (formulario, notificaciones): van directos, nunca pasan por la cola ni por el rate limiter

## Middleware EmailRateLimited
- Ubicación: `app/Jobs/Middleware/EmailRateLimited.php`
- Limita el total de emails enviados por jobs a **50/hora** (`overall`)
- Limita boletines a **15/hora** (para dejar hueco a invitaciones)
- Si se supera el límite, el job se reencola tras un tiempo de espera
- El resto de la capacidad (50/hora) queda reservada para emails críticos directos

## Configuración en config/mail.php

El middleware EmailRateLimited utiliza la siguiente configuración en `config/mail.php`:

```php
'rate_limit' => [
    'max' => [
        'overall' => env('MAIL_RATE_LIMIT_MAX', 50), // Límite global de jobs en cola
        // 'boletin' => env('MAIL_RATE_LIMIT_MAX_BOLETIN', 50), // (opcional) Límite específico para boletines
    ],
    'minutes_waiting' => env('MAIL_RATE_LIMIT_MINUTES_WAITING', 15), // Tiempo de espera tras superar el límite (minutos)
    'window' => env('MAIL_RATE_LIMIT_WINDOW', 3600), // Ventana de tiempo para el rate limit (segundos)
],
```

- Puedes ajustar estos valores en `.env` para cambiar los límites sin tocar el código.
- El valor por defecto para `overall` es 50 emails/hora.
- El tiempo de espera por defecto tras superar el límite es 15 minutos.
- La ventana de rate limiting es de 1 hora (3600 segundos).

## Clases Afectadas
- **Afectadas por rate limiting:**
  - `App\Mail\InvitacionEquipoEmail` (cola default)
  - `App\Mail\BoletinEmail` (cola low_priority)
- **NO afectadas:**
  - Emails enviados directamente (ej: formularios de contacto, notificaciones del sistema, etc.)

## Manejo del Worker

El procesamiento de las colas se gestiona mediante scripts:

- **Iniciar worker (Linux):**
  - `bash/worker-start.sh` → Arranca el worker de colas con la configuración de prioridades. Este script comprueba que no esté ya un worker funcionando.
- **Detener worker (Linux):**
  - `bash/worker-stop.sh` → Detiene el worker de colas de forma segura.

Estos scripts aseguran que siempre se procesen primero las colas de mayor prioridad (`default`, luego `low_priority`, luego `audio_processing`).

> Es importante mantener siempre un worker activo para que las invitaciones y boletines se envíen automáticamente según las prioridades y límites definidos.

> Para ello lo mejor es instalar mediante **cron** un arranque de worker-start.sh
