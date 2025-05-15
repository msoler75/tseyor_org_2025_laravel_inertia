# Instrucciones para usar los scripts de despliegue y workers

## 1. Definir el usuario de despliegue
Antes de ejecutar cualquier script, asegúrate de definir la variable de entorno `DEPLOY_USER` con el nombre de usuario correcto del sistema donde se desplegará la aplicación:

```bash
export DEPLOY_USER=tu_usuario
```
Puedes añadir esta línea a tu `.bashrc`, `.profile` o ejecutarla manualmente antes de cada despliegue.

## 2. Despliegue de una nueva release
Ejecuta el script de despliegue:

```bash
bash/release_crear.sh
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

## ¿Qué hacen estos scripts?

- **release_crear.sh**: Automatiza el despliegue de una nueva versión de la aplicación. Crea una carpeta de release, clona el repositorio, enlaza archivos y carpetas compartidas, instala dependencias y ejecuta migraciones.
- **worker-start.sh**: Inicia un worker de Laravel para procesar la cola de trabajos en segundo plano, registrando logs y gestionando el proceso con un archivo de bloqueo.
- **worker-stop.sh**: Detiene de forma ordenada el worker en ejecución, esperando a que termine el trabajo actual antes de finalizar el proceso.
- **worker-restart.sh**: Reinicia el worker de la cola, notificando a Laravel y asegurando que el proceso se detenga y vuelva a iniciar correctamente.
- **worker-check.sh**: Verifica si el worker está en ejecución y muestra el PID si está activo, o 0 si no lo está.
- **_start-once-worker.sh**: Inicia un worker que procesa solo un trabajo de la cola y luego termina. Útil para ejecuciones puntuales o debugging.
- **_stop-once-worker.sh**: Detiene el worker iniciado en modo "once" enviando una señal de terminación y esperando a que finalice.

Todos estos scripts están preparados para funcionar en entornos de producción y requieren que la variable de entorno `DEPLOY_USER` esté correctamente definida.

## 5. Deployment

Consulta _DEPLOYMENT.md en la raíz de este proyecto para instrucciones específicas de despliegue.
