# TODO.md

Lista de tareas pendientes y cosas por hacer. Extraído y organizado desde `to-do.txt`.


## Automatización de inscripciones a cursos
-cuando alguien se inscribe a un curso tseyor el supervisor (configurable en .env) recibe una notificación con enlaces a gestionar esa inscripción
-al gestionar la inscripción, puede asignar ese alumno a un usuario de Tseyor que se convierte en su tutor
-ese tutor recibe por correo esa asignación, y puede, o bien aceptarla, o bien rebotarla (no puede asumir su tutoria)
-las inscripciones tienen un estado (asignado, rebotado, contactado (se le ha contactado con el alumno), en_espera (está esperando el curso), realizando_curso (está realizando el curso), abandonado (el alumno ha abandonado o no quiere hacer el curso), completado (ha completado el curso))
-si se rebota, el supervisor recibirá una nueva notificación para que asigne a otro tutor/a
-cada cierto tiempo se activará una tarea (cron) que verifique el estado de cada inscripción, y segun el estado, se le notificará a su tutor, pidiéndole información de cómo está la situación. Esa notificación tendrá un enlace para que el tutor pueda en una página poner el estado o situacion, mediante un selector y un campo de descripción, y opciones de rebote
-se guardará un historial de revisiones de cada inscripción, para saber el historial de sucesos
-las inscripciones asignadas que estén pendientes en algun sentido, caducarán automáticamente a los 6 meses (no se volverá a consultar a sus tutores asignados sobre su estado o situación) 
-el estado 'abandonado' y 'completado' son estados finales


## Limpiar historial de github
- [ ] Limpiar historial de Git con BFG Repo-Cleaner (https://rtyley.github.io/bfg-repo-cleaner/)
- Descripción: Usar BFG Repo-Cleaner para eliminar archivos grandes o información sensible del historial de Git.

## API
- Poner api.tseyor.org como API_URL en tseyor.org

## Vistas Email
- Unificar diseño de vistas para correos, notificaciones, etc

## Sección Arte de Tseyorianos
- Crear la sección de arte tseyoriana o publicaciones de la comunidad en la web
- Poner también los audios

## Remover Sanctum
- composer remove laravel/sanctum

## Boletines
- Crear cron para automatizar creación de boletines
- Ver cuantos suscriptores hay para cada tipo de boletin
- Plantearse usar https://mailrelay.com/

## Global Search
- Revisar por qué el buscador global no encuentra "com 1281", "comunicado 1281" ni "1281"

## Word a Markdown
- Comprobar que importar comunicado 1278 desde Word genera saltos de línea correctos

## Equipos
- Revisar codigo de notificaciones si podemos obtener el $user mediante $notifiable
- Ver listado de miembros completo al pulsar o ampliar en listado
- Verificar cada cierto tiempo si hay solicitudes, mandar un correo a los coordinadores
- Poder ver todos los miembros de un equipo
- Relación entre equipos y centros visible en ambas vistas
- Nuevo panel: EVENTOS relacionados con el equipo

## Admin
- Sistema para guardar datos clave (ej: url de estatutos de ONG)
- Otorgar privilegios de superadmin a alguien más y enseñarle
- Cerrar sesión desde admin no funciona

## Glosario
- Relacionar términos (ej: silencio mental -> la-desconexion)

## Instalación Muular Electrónico
- Verificar log de la base de datos
- Eliminar usuarios dados de baja
- Preparar tutorial informativo o vídeo
- Revisar correos de oficinas @tseyor.org

## Comunicados
- Corregir error en slug con espacios o puntos (ziggy routes)
- Validar que los slug no sean numéricos
- Validación de campo slug [a-z0-9\-] en todos los CRUD
- Formato {STYLE:} en comunicado 1212

## Archivos
- Máximo tamaño upload: 50mb
- Vista grid por parámetro en la url
- Considerar carpetas como álbumes
- Auto detectar carpetas con muchas imágenes
- Revisar funcionamiento sticky bit (borrar/renombrar solo propios)

## Búsqueda
- Comprobar búsqueda con y sin comillas (ej: "verdad. aumnor")

## Trait EsCategorizable
- Método getCategorias debe ser estático

## PDF
- Corregir asteriscos en el último cuento de navidad
- Corregir títulos en metadatos de libros PDF

## Gallery 3D
- Departamento de arte: https://github.com/theringsofsaturn/3D-art-gallery-threejs

## Correos
- Vincular mensajes de error de correo con invitaciones
- Panel para ver mensajes recibidos en notificaciones@tseyor.org
- Crear correos de redirección para equipos
- [ ] Crear comando Laravel para revisar rebotes ("Undelivered Mail Returned to Sender") y marcar suscriptores con error (2024-06-09)

## Invitaciones
- Comprobar posibles hackeos de invitación

## Eventos
- Mejorar imagen de Eventos y disposición visual
- Permitir varias imágenes en columna izquierda
- Carousel en vista móvil
- Mostrar equipo relacionado

## Usuario
- Espacio personal de imágenes

## Image/Intervention
- Revisar error de GeometryException (ver logs)

## Instalar IP Abuser Middleware
- https://github.com/rahulalam31/Laravel-Abuse-IP
- establecer cron de actualización de IPS

