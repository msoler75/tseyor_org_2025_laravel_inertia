# Scripts y Utilidades

La carpeta `bash/` contiene scripts esenciales para la automatización y operación del proyecto en producción. A continuación se describe el propósito de cada script y las recomendaciones de uso:

## Scripts de gestión de workers (colas Laravel)
- **worker-start.sh**: Inicia el worker de colas priorizando `default`, luego `low_priority` y `audio_processing`. Usa flock para evitar procesos duplicados. Recomendado ejecutarlo al inicio del sistema o mediante cron para asegurar que siempre haya un worker activo.
- **worker-stop.sh**: Detiene de forma ordenada el worker, esperando a que termine el trabajo actual.
- **worker-restart.sh**: Reinicia el worker llamando internamente a stop y start.
- **worker-check.sh**: Muestra el PID del worker si está activo, o 0 si no hay ninguno.
- **_start-once-worker.sh**: Ejecuta un solo job de la cola y termina. Útil para debugging o tareas puntuales (obsoleto)
- **_stop-once-worker.sh**: Detiene el worker lanzado en modo "once" (obsoleto)

## Scripts de boletines
- **boletin_enviar_pendientes.sh**: Llama al endpoint de la web para enviar boletines pendientes. Requiere un token como argumento.
- **boletin_preparar.sh**: Prepara un nuevo boletín (requiere token y periodicidad). Automatiza la generación y envío.

## Scripts de despliegue
- **release_crear.sh**: Automatiza la creación de una nueva release: clona el repo, enlaza recursos compartidos, instala dependencias y ejecuta migraciones.
- **release_establecer.sh**: Cambia el symlink `current` a una release concreta, limpia cachés y reinicia servicios.

## Renderizado SSR
- **ssr.sh**: Gestiona el servidor de renderizado SSR (start/stop/status). Usa Node y nvm.

## Recomendaciones de uso y automatización con cron
- **Definir la variable de entorno `DEPLOY_USER`** antes de ejecutar cualquier script (imprescindible en cron).
- **Permisos**: Asegúrate de que los scripts tengan permisos de ejecución (`chmod u+x bash/*.sh`).
- **Automatización recomendada**: Añade una línea en tu crontab para asegurar que el worker esté siempre activo:
  ```cron
  DEPLOY_USER=tu_usuario /home/tu_usuario/tseyor.org/current/bash/worker-start.sh
  ```
  O bien, para reiniciar periódicamente:
  ```cron
  DEPLOY_USER=tu_usuario /home/tu_usuario/tseyor.org/current/bash/worker-restart.sh
  ```
- **Boletines automáticos**: Puedes programar el envío/preparación de boletines con cron, pasando los argumentos requeridos (token, periodicidad).

## Más información
- Consulta `bash/instrucciones.md` para detalles y ejemplos de uso.
- Consulta `despliegue.md` para el flujo completo de despliegue y estructura recomendada.

### [Ver índice de documentación](./index.md)
