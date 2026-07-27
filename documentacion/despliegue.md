# Despliegue

Manual detallado para el despliegue y gestión de versiones del proyecto tseyor.org

## Estructura recomendada de carpetas en producción

```
/tseyor.org
├── release_crear.sh
├── release_establecer.sh
├── shared
│   ├── .env
│   └── storage/
├── releases
│   ├── 1/
│   ├── 2/
│   └── ...
└── current -> releases/2
```

- **shared**: recursos compartidos entre releases (configuración y archivos subidos).
- **releases**: cada subcarpeta es una versión desplegada.
- **current**: enlace simbólico a la versión activa.
- **release_crear.sh** y **release_establecer.sh**: scripts de automatización (deben estar en la raíz del sitio web).

## Proceso de despliegue (nueva release)


1. **Preparar los archivos de frontend**

Desde el entorno de desarrollo ejecuta el comando `release:prepare`. Este comando crea los tres zips necesarios (node_modules, public/build y ssr.js) y los envía al servidor.

```bash
  php artisan release:prepare
```

Esto se hace así por la incapacidad actual en el hosting de ejecutar comandos `npm install` o `npm run build`.
Entonces se tienen que preparar todos los archivos necesarios en local y enviarlos al servidor.

Puedes ver el código en [DeployHelper](../app/Pigmalion/DeployHelper.php).

Los archivos .zip se almacenan en storage/install que es un recurso compartido (carpeta shared) entre todas las releases. 

*Nota*: Es importante ejecutar `php artisan release:install` desde la carpeta de la última release.

2. **Definir usuario de despliegue**
   - Exporta la variable `DEPLOY_USER` con tu usuario de hosting:
     ```bash
     export DEPLOY_USER=tu_usuario
     ```


3. **Ejecutar el script de creación de release**
   - Desde la raíz del sitio web:
     ```bash
     ./release_crear.sh
     ```
    > **Nota**: el script release_crear.sh debes copiarlo y/o actualizarlo desde la carpeta `bash` del proyecto.

   - El script realiza:
     - Crea una nueva carpeta en `releases/` con el siguiente número incremental.
     - Clona el repositorio en esa carpeta.
     - Crea symlinks a `shared/.env` y `shared/storage`.
     - Instala dependencias (`composer install`).
     - Ejecuta migraciones y otros comandos necesarios.
     - Prepara permisos y estructura de caché.
     - Ejecuta scripts de instalación de frontend.

El propio script `release_crear.sh` invocará internamente el comando Artisan `php artisan release:install` para que el servidor instale los .zip de frontend preparados con `release:prepare`.

4. **Actualizar el enlace simbólico 'current'**
   - Una vez creada la carpeta con la nueva version puedes establecer la nueva versión (cambiará el symlink de `current`):
     ```bash
     ./release_establecer.sh <número_release>
     ```
   - Esto permite también hacer rollback a cualquier versión anterior. Este script tambien elimina cache en cada cambio de versión (todas las releases comparten la cache en `shared/storage`)

5. **Acceso web**
   - El directorio público a servir es: `/tseyor.org/current/public`


## Actualizacion del frontend

Si queremos actualizar en la release actual (current) alguna parte del frontend podemos ejecutar estos comandos en local (desarrollo).

```bash
  php artisan deploy:nodemodules
  php artisan deploy:front
  php artisan deploy:ssr
```

- El comando `deploy:nodemodules` sube la carpeta node_modules desde el entorno de desarrollo porque en DreamHost no permite ejecutar `npm install`
- El comando `deploy:front` sube el contenido de la carpeta de scripts en `/public/build` ya que en DreamHost no funciona `npm run build`
- El comando `deploy:ssr` sube el archivo `ssr.js` y le hace algunos reemplazos para que las rutas funcionen.

### Rollback del frontend

Si necesitas revertir una actualización del frontend a la versión anterior, puedes usar la opción `--rollback`:

```bash
php artisan deploy:front --rollback
```

Este comando:
- Renombra la carpeta `public/build` actual a `public/build_new`
- Restaura `public/build_old` como la nueva `public/build`
- Permite recuperar rápidamente una versión anterior del frontend sin necesidad de redeploy completo

**Nota**: Solo funciona si existe una carpeta `build_old` (creada durante el último deploy).


## Notas importantes
- **Procura No editar archivos directamente en `releases`**: Si hay algún cambio importante despliega una nueva versión.
- **El archivo `.env` y la carpeta `storage` son compartidos**: cualquier cambio afecta a todas las releases.
- **Para rollback**: ejecuta `./release_establecer.sh <número_release>` para apuntar el symlink `current` a una versión anterior. Pero eso no hace rollback de migraciones de base de datos.
- **Rollback del frontend**: usa `php artisan deploy:front --rollback` para revertir cambios en el frontend sin afectar la release completa.
- **Permisos**: asegúrate de que los scripts y carpetas tengan los permisos adecuados para el usuario de despliegue.
- **Logs y workers**: revisa logs y estado de workers tras cada despliegue. Están en `shared/storage/logs`

## Opcional

- **Mejora de rendimiento Ziggy:**
  Se aplica automáticamente en cada `composer install/update` mediante el comando `php artisan ziggy:patch-cache`, que añade caché en disco a las rutas de Ziggy (ahorra 10-56 ms por request). Es idempotente: no hace nada si ya está aplicado.
- **Problemas de CSRF en dev.tseyor.org:**
  Si hay errores constantes, borra todas las cookies de `.tseyor.org`.

## Referencias
- Consulta [`configuracion_inicial.md`](./configuracion_inicial.md) para la configuración inicial.
- Consulta `bash/instrucciones.md` para detalles sobre los scripts.
- Para mantenimiento, logs, caché y seguridad consulta [`mantenimiento.md`](./mantenimiento.md)

---

Este sistema de despliegue permite cambios de versión rápidos y seguros, manteniendo la configuración y archivos subidos intactos entre releases. Utiliza siempre los scripts automatizados para evitar errores manuales y facilitar el mantenimiento.

### [Ver índice de documentación](./index.md)


