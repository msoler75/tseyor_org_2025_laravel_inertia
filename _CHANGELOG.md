### Tareas realizadas

- **10 de mayo de 2025**:
  - Creado el modelo `Boletin` y la clase `BoletinEmail` y se comienza a programar la suscripción a boletines mediante los modelos `Suscriptor`, y añadidas las rutas
  - Mejorado el servicio `EmailRateLimiter` para evitar problemas de concurrencia y validar configuraciones.
  - Eliminado el control de ratio de envío en `InvitacionEquipoEmail`, delegando esta funcionalidad a la clase padre `LimitedMailable`.
  - Añadido el método `failed` en `InvitacionEquipoEmail` para manejar errores de envío.
  - Movido el uso de `Queueable` y `SerializesModels` a la clase padre `LimitedMailable` y eliminado de las clases hijas `InvitacionEquipoEmail` y `BoletinEmail`.

- **11 de mayo de 2025**:
  - Configurado MailHog como servidor SMTP falso para pruebas en `LimitedMailableTest`.
  - Añadido soporte para iniciar la imagen Docker de MailHog en los tests.
  - Añadido test para `EmailRateLimiter` verificando límites de envío y tiempos de espera.
  - Seguimos mejorando `Boletin` y `BoletinEmail`
  - Vistas de Boletin para configurarlo, darse de baja, etc.
  - Añadido el método `suscribir` en `SuscriptorController` con validaciones mejoradas para correos electrónicos.
  - Creada la vista `Config.vue` para configurar la suscripción al boletín.
  - Añadido soporte para manejar tokens en las rutas de configuración y desuscripción.
  - Actualizado el componente `Suscribe.vue` para manejar mensajes de éxito y error al suscribirse.
  - Corregido el error de migración duplicada en `CreateBoletinesTable`.
  - Creado el middleware `RateLimited` para limitar la tasa de procesamiento de trabajos en cola utilizando Redis.

### Tareas pendientes

