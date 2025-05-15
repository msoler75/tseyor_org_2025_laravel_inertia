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

- **12 de mayo de 2025**:
  - Mejoras en la suscripción al boletín desde la cuenta de usuario: integración de la vista parcial `BoletinSuscripcion.vue` en el perfil y ajustes en controladores y modelos relacionados.
  - Ajustes en la relación entre usuario y suscripción al boletín, validaciones y mensajes de éxito/error en la UI.
  - Refactorización de middlewares y servicios de email: renombrado y mejoras en el control de ratio de envío, registro de errores de envío en logs.
  - Ampliación del campo email en la base de datos para boletines.
  - Correcciones menores en la administración de usuarios y suscriptores.

- **13 de mayo de 2025**:
  - (Sin cambios relevantes registrados en el repositorio)

- **14 de mayo de 2025**:
  - Mejoras en la gestión de sesiones y configuración para almacenamiento en archivos.
  - Cambios visuales en componentes de navegación (`NavTabs.vue`, `NavBar.vue`).
  - Refactorización y limpieza de scripts de SSR y comandos relacionados, creación de `ssr.sh` y adaptación para Windows.
  - Mejoras en CORS y configuración de la API, creación de store para API y ajustes en stores de usuario e imágenes.
  - Eliminación de backups antiguos y scripts obsoletos.
  - Ajustes en vistas y componentes para comentarios y navegación.

- **15 de mayo de 2025**:
  - Documentación ampliada: creación de archivos de arquitectura, planificación, contribución, despliegue y seguridad.
  - Refactorización y migración de archivos de tareas y changelog.
  - Mejoras y correcciones en el modelo `Boletin` y migración de listado de suscriptores desde Mailchimp.
  - Ajustes en el email de boletín y comandos de despliegue.
  - Limpieza de archivos y eliminación de scripts y configuraciones obsoletas.


