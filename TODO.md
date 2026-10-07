# TODO.md

Lista de tareas pendientes. Revisada contra el código el 07/10/2026: se eliminaron las tareas ya completadas y el resto se ordena por prioridad.

> **Eliminadas por estar hechas:** datos clave en admin (`Setting` + CRUD), sticky bit en Archivos (con tests), espacio personal de imágenes (`mis_archivos`), comando de rebotes `check-bounced` + su cron, cron de boletines y conteo de suscriptores por tipo, imágenes múltiples en la columna izquierda de Eventos, equipo relacionado en la vista de Eventos.

---

## 🔴 P1 — Seguridad y fallos activos

### Invitaciones a equipos
- [ ] **Cambiar el sistema de generación de token**: hoy es `sha1(time().$correo)` y `sha1(time().$id)` (`app/Http/Controllers/EquiposController.php:612,684`), predecible — pasar a `Str::random(40)` o UUID
- [ ] Añadir `throttle` a `GET invitacion/{token}/aceptar|declinar` (`routes/web.php:423-424`, ahora sin límite)
- [ ] Revisar el resto del flujo de invitación por posibles abusos

### Buscador global
- [ ] **El índice de Scout no existe** (`storage/indexes/` ausente): la búsqueda global devuelve 0 resultados, por eso "com 1281" / "comunicado 1281" / "1281" no aparecen — crear/importar el índice
- [ ] Codificar la query antes de enviarla (`resources/js/GlobalSearch.vue` → `globalSearch.js:116`) y pasar la frase exacta al buscar cuando viene entre comillas

### Dependencias (GitHub Dependabot)
- [ ] **27 vulnerabilidades en la rama principal: 10 altas, 15 medias, 2 bajas** — revisar y actualizar dependencias (`composer audit` / `npm audit`): https://github.com/msoler75/tseyor_org_2025_laravel_inertia/security/dependabot

---

## 🟠 P2 — Deuda técnica

- [ ] **Limpiar historial de Git con BFG Repo-Cleaner**: hay 10 copias de `laravel_inertia.sql` de ~82 MB cada una (~820 MB); `.env` nunca se subió. Requiere `push --force` (rompe los clones existentes) → coordinar antes de ejecutar
- [ ] **Remover Sanctum**: `composer.json:23`, `routes/api.php:20,25` (`auth:sanctum`), `config/cors.php:18`
- [ ] Instalar IP Abuser Middleware (https://github.com/rahulalam31/Laravel-Abuse-IP) + cron de actualización de IPs — hoy solo existe `CheckAllowedIP`, que es una allowlist de despliegue
- [ ] Unificar diseño de vistas de correo: conviven `master-secretaria`, `master-usuario`, `notification` y `old-invitacion-equipo.blade.php` (plantilla vieja, borrar)
- [ ] API: poner `API_URL=https://api.tseyor.org` en `.env` y `.env.example` (el soporte en `config/app.php:67` ya existe)

---

## 🟡 P3 — Funcionalidades pendientes

### Equipos
- [ ] Panel de EVENTOS relacionados con el equipo (`Evento.php:152` ya apunta a `Equipo`, falta la relación inversa y la vista)
- [ ] Relación entre equipos y centros visible en ambas vistas (no existe ni la relación Eloquent `centros()`/`equipos()`)
- [ ] Comando + cron que avise por correo a los coordinadores de solicitudes pendientes (hoy solo se avisa al crear la solicitud, `EquiposController.php:961`)
- [ ] Ver el listado completo de miembros al pulsar/ampliar, también para usuarios normales (hoy `ModalMiembros` está tras el gate de coordinador, `Equipo.vue:128`)
- [ ] Notificaciones: usar `$notifiable` en `BienvenidaEquipo` y `DenegadoEquipo` (los otros dos ya lo hacen)
- [ ] Revisar si `$user` se puede obtener vía `$notifiable` en el resto de notificaciones

### Archivos
- [ ] Definir el límite de subida real: el objetivo escrito era 50 MB, pero `config/filesystems.php:87` está en **300 MB** — decidir y alinear `php.ini`/nginx
- [ ] Vista grid por parámetro en la URL
- [ ] Considerar carpetas como álbumes
- [ ] Auto detectar carpetas con muchas imágenes

### Correos
- [ ] Panel para ver mensajes **recibidos** en notificaciones@tseyor.org (mailbox-for-laravel solo captura los salientes)
- [ ] Vincular mensajes de error de correo con invitaciones
- [ ] Crear correos de redirección para equipos

### Eventos
- [ ] Mejorar imagen de Eventos y disposición visual
- [ ] Carousel en vista móvil (no hay `carousel`/`swiper` en esa página)

### Comunicados
- [ ] Corregir error en slug con espacios o puntos (ziggy routes)
- [ ] Validar que los slug no sean numéricos y añadir la regex `[a-z0-9\-]` a los 3 CRUDs que solo validan `unique` (`CentroCrud:97`, `InformeCrud:135`, `EventoCrud:112`)

### Otros
- [ ] Glosario: cargar las relaciones de términos (el mecanismo `ref_terminos` existe; falta "silencio mental → la-desconexion")
- [ ] Sección Arte de Tseyorianos: contenido real con audios (hoy `TrabajosArte.vue` es un placeholder "Próximamente")
- [ ] Boletines: plantearse usar https://mailrelay.com/
- [ ] Instalación Muular Electrónico: verificar log de la base de datos, eliminar usuarios dados de baja, preparar tutorial/vídeo, revisar correos de oficinas @tseyor.org
- [ ] Trait `EsCategorizable`: el método `getCategorias` debe ser estático (`app/Traits/EsCategorizable.php:47` + 11 call sites)

---

## 🟢 P4 — Verificaciones y mejoras menores

- [ ] Comunicado 1212: revisar restos del formato `{STYLE:}` en BD (el parser del código es `{style=...}`)
- [ ] Comunicado 1278: comprobar que la importación desde Word genera los saltos de línea correctos
- [ ] PDF: corregir asteriscos en el último cuento de navidad
- [ ] PDF: corregir títulos en metadatos de libros
- [ ] Búsqueda: comprobar búsqueda con y sin comillas (ej: "verdad. aumnor")
- [ ] Cerrar sesión desde admin: hay fixes en HEAD, falta verificarlo en runtime
- [ ] Otorgar privilegios de superadmin a alguien más y enseñarle (tarea operativa)
- [ ] Image/Intervention: revisar error de GeometryException (0 apariciones en los logs actuales; los puntos de riesgo ya están en try/catch)
- [ ] Gallery 3D: https://github.com/theringsofsaturn/3D-art-gallery-threejs
