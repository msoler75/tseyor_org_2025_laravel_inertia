# Comandos esenciales de administración

### Artisan (Laravel)

- **Actualizar rutas:**
  ```bash
  php artisan route:clear
  ```
- **Optimizar Laravel:**
  ```bash
  php artisan optimize
  ```
- **Limpiar caché:**
  ```bash
  php artisan cache:clear
  ```
- **Limpiar configuración:**
  ```bash
  php artisan config:clear
  ```
- **Búsquedas (Scout/TNTSearch):**
    
    `flush` borra el indice.
    `import` añade entradas al índice.

  ```bash
  php artisan scout:flush "App\Models\Comunicado"
  php artisan scout:import "App\Models\Comunicado"
  php artisan scout:status
  ```


### Comandos avanzados

- **Limpiar caché de imágenes:**
  ```bash
  php artisan imagecache:clear
  ```
  Limpia la caché de imágenes generadas.

- **Backup de base de datos:**
  ```bash
  php artisan db:backup
  ```
- **Generar sitemap:**
  ```bash
  php artisan sitemap:generate
  ```
- **Limpiar page-cache:**
  ```bash
  php artisan page-cache:clear
  ```


- **Gestionar inscripciones:**
  ```bash
  php artisan inscripciones:gestionar [--solo-seguimiento] [--solo-reporte]
  ```
  Gestiona el seguimiento automático de inscripciones y envía reportes.

- **Verificar duraciones de radio:**
  ```bash
  php artisan radio:verificar-duraciones [--fix] [--per-page=100] [--page=1] [--all] [--id=]
  ```
  Verifica y opcionalmente corrige las duraciones de los elementos de radio.

- **Calcular hash de archivo:**
  ```bash
  php artisan hash {file}
  ```
  Calcula el código hash de un archivo.

- **Importar contenidos:** 
  ```bash
  php artisan contenidos:import {coleccion} [--y]
  ```
  Borra todos los contenidos de la colección indicada y los regenera desde ContenidoHelper::guardarContenido. Útil para reindexar contenidos de búsqueda. La opción `--y` confirma la operación directamente sin solicitar confirmación.


### Despliegue y SSR

- **Generar rutas Ziggy:**
  ```bash
  php artisan ziggy:generate
  ```
- **Despliegue SSR:**

**Ejecutar desde localhost**

  ```bash
  php artisan deploy:ssr
  php artisan deploy:front
  php artisan deploy:nodemodules
  ```

### Comandos críticos

**Atención: no ejecutar a menos que se esté seguro de lo que se está haciendo**

- **Restaurar base de datos:**
  ```bash
  php artisan db:restore
  ```
  Restaura la base de datos desde una copia de seguridad.


### [Ver índice de documentación](./index.md)


