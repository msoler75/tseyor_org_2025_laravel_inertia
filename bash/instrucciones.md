# Scripts de creación de versiones (releases) y workers

## ¿Qué hacen estos scripts?

- **worker-start.sh**: Inicia un worker de Laravel para procesar la cola de trabajos en segundo plano, registrando logs y gestionando el proceso con un archivo de bloqueo.
- **worker-stop.sh**: Detiene de forma ordenada el worker en ejecución, esperando a que termine el trabajo actual antes de finalizar el proceso.
- **worker-restart.sh**: Reinicia el worker de la cola, notificando a Laravel y asegurando que el proceso se detenga y vuelva a iniciar correctamente.
- **worker-check.sh**: Verifica si el worker está en ejecución y muestra el PID si está activo, o 0 si no lo está.
- **_start-once-worker.sh**: Inicia un worker que procesa solo un trabajo de la cola y luego termina. Útil para ejecuciones puntuales o debugging.
- **_stop-once-worker.sh**: Detiene el worker iniciado en modo "once" enviando una señal de terminación y esperando a que finalice.
- **boletin_enviar_pendientes.sh**: Ejecuta el envío automático de boletines pendientes, llamando al comando correspondiente de Laravel para procesar los boletines que cumplen los requisitos de envío.
- **boletin_preparar.sh**: Prepara un nuevo boletín ejecutando el comando de generación y guardado, útil para automatizar la creación de boletines desde scripts o tareas programadas.
- **release_crear.sh**: Automatiza el despliegue de una nueva versión de la aplicación. Crea una carpeta de release, clona el repositorio, enlaza archivos y carpetas compartidas, instala dependencias y ejecuta migraciones.
- **release_establecer.sh**: Cambia el release activo de la aplicación, actualizando el symlink 'current' para apuntar a una versión específica desplegada previamente.
- **ssr.sh**: Inicia el servidor de renderizado SSR (Server Side Rendering) para la aplicación, permitiendo la generación de vistas en el servidor para mejorar el SEO y la experiencia de usuario.

Todos estos scripts están preparados para funcionar en entornos de producción y requieren que la variable de entorno `DEPLOY_USER` esté correctamente definida.


# Instrucciones para usar los scripts de despliegue y workers

## 0. Permisos de ejecución

Recuerda que para que se puedan ejecutar, deben tener permisos de ejecución. Para ello puedes usar el comando: 

```bash
chmod u+x bash/*.sh
```

## 1. Definir el usuario 
Antes de ejecutar cualquier script, asegúrate de definir la variable de entorno `DEPLOY_USER` con el nombre de usuario correcto del sistema donde se desplegará la aplicación.

Puedes añadir la línea `DEPLOY_USER=tu_usuario` a tu `.bashrc`, `.profile` o ejecutar manualmente antes de cada ejecución de un script:

```bash
export DEPLOY_USER=tu_usuario
```

> **Importante para CRON:**
> Si ejecutas los scripts desde **cron**, debes definir la variable `DEPLOY_USER` explícitamente en la línea del cron o en el propio archivo crontab, ya que el entorno de cron no carga variables personalizadas automáticamente.
> 
> Ejemplo en crontab:
> ```cron
> DEPLOY_USER=tu_usuario /ruta/al/script/worker-start.sh
> ```
> O bien, añade la variable al principio de tu crontab:
> ```cron
> DEPLOY_USER=tu_usuario
> * * * * * /ruta/al/script/worker-start.sh
> ```

## 2. Despliegue de una nueva release

**NOTA**: Se sugiere copiar los script `release_*` a la carpeta raiz, por ejemplo a:

`/home/$DEPLOY_USER/tseyor.org`

Ejecuta el script de despliegue:

```bash
./release_crear.sh
```

Este script:
- Crea una nueva carpeta de release en `/home/$DEPLOY_USER/tseyor.org/releases/`
- Clona el repositorio
- Crea los symlinks necesarios
- Instala dependencias y ejecuta migraciones

## 3. Uso de scripts de worker
Los scripts de worker (`worker-check.sh`, `worker-start.sh`, etc.) también requieren que la variable `DEPLOY_USER` esté definida.

Ejemplo:
```bash
bash/worker-check.sh
```

## 4. Notas
- Si la variable `DEPLOY_USER` no está definida, los scripts mostrarán un error y no continuarán.
- Nunca publiques tu usuario real en el repositorio, usa siempre la variable de entorno.
- Puedes adaptar estos scripts para otros usuarios o entornos simplemente cambiando el valor de `DEPLOY_USER`.


---

## 5. Deployment

Consulta _DEPLOYMENT.md en la raíz de este proyecto para instrucciones específicas de despliegue.
