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
- **Importar datos:**
  ```bash
  php artisan import:comunicados
  php artisan import:paginas
  ```
- **Búsquedas (Scout/TNTSearch):**
  ```bash
  php artisan scout:import "App\Models\Comunicado"
  php artisan scout:status
  ```
- **Contenidos espejo:**
  ```bash
  php artisan contenidos:import noticias
  ```
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

### Despliegue y SSR

- **Generar rutas Ziggy:**
  ```bash
  php artisan ziggy:generate
  ```
- **Despliegue SSR:**
  ```bash
  php artisan deploy:ssr
  php artisan deploy:front
  php artisan deploy:nodemodules
  ```


### [Ver índice de documentación](./index.md)
