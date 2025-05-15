# TODO.md

Lista de tareas pendientes y cosas por hacer. Extraído y organizado desde `to-do.txt`.

## General
- Poner api.tseyor.org como API_URL en tseyor.org

## Sección Arte de Tseyorianos
- Crear esa sección en la web
- Poner también los audios

## Remover Sanctum
- composer remove laravel/sanctum

## Boletines
- Plantearse usar https://mailrelay.com/

## Global Search
- Revisar por qué el buscador global no encuentra "com 1281", "comunicado 1281" ni "1281"

## Word a Markdown
- Comprobar que importar comunicado 1278 desde Word genera saltos de línea correctos

## Equipos
- Ver listado de miembros completo al pulsar o ampliar en listado
- Verificar cada cierto tiempo si hay solicitudes, mandar un correo a los coordinadores
- Invitaciones: permitir reenviar invitación caducada
- Poder ver todos los miembros de un equipo
- Relación entre equipos y centros visible en ambas vistas
- Nuevo panel: EVENTOS relacionados con el equipo
- Testar funcionamiento y permisos de acceso al crear un equipo
- Testar creación de informe del equipo

## DiskUtil
- Reemplazar/quitar DiskUtil por StorageItem (tal vez dejar DiskUtil::ensureDirExists)

## Admin
- Sistema para guardar datos clave (ej: url de estatutos de ONG)
- Dashboard: añadir cuadro con avisos de estado del queue-worker y tareas fallidas
- Otorgar privilegios de superadmin a alguien más y enseñarle
- Buscar usuario por email
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

## IP Abuser Middleware
- https://github.com/rahulalam31/Laravel-Abuse-IP

## URL
- En móvil, compartir página solo comparte tseyor.xyz

## Blog
- Usar categoría no funciona en la paginación
