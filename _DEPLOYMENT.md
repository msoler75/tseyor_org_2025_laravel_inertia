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

1. **Definir usuario de despliegue**
   - Exporta la variable `DEPLOY_USER` con tu usuario de hosting:
     ```bash
     export DEPLOY_USER=tu_usuario
     ```
2. **Ejecutar el script de creación de release**
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
     - (Opcional) Ejecuta scripts de frontend si están habilitados.

3. **Actualizar el enlace simbólico 'current'**
   - Una vez creada la carpeta con la nueva version puedes establecer la nueva versión (cambiará el symlink de `current`):
     ```bash
     ./release_establecer.sh <número_release>
     ```
   - Esto permite también hacer rollback a cualquier versión anterior.

4. **Acceso web**
   - El directorio público a servir es: `/tseyor.org/current/public`

## Notas importantes
- **Procura No editar archivos directamente en `releases`**: Si hay algún cambio importante despliega una nueva versión.
- **El archivo `.env` y la carpeta `storage` son compartidos**: cualquier cambio afecta a todas las releases.
- **Para rollback**: ejecuta `./release_establecer.sh <número_release>` para apuntar el symlink `current` a una versión anterior.
- **Permisos**: asegúrate de que los scripts y carpetas tengan los permisos adecuados para el usuario de despliegue.
- **Limpieza de cachés**: tras cada cambio de release, limpia cachés de views y rutas si es necesario.
- **Logs y workers**: revisa logs y estado de workers tras cada despliegue. Están en `shared/storage/logs`

## Referencias
- Consulta `_SETUP.md` para la configuración inicial.
- Consulta `bash/instrucciones.md` para detalles sobre los scripts.

---

Este sistema de despliegue permite cambios de versión rápidos y seguros, manteniendo la configuración y archivos subidos intactos entre releases. Utiliza siempre los scripts automatizados para evitar errores manuales y facilitar el mantenimiento.
