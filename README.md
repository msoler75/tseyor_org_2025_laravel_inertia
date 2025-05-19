# 🌐 tseyor.org – Plataforma Web de Mundo Armónico Tseyor

¡Bienvenido/a! Este repositorio contiene el código fuente de la web oficial de la ONG Mundo Armónico Tseyor: [https://tseyor.org](https://tseyor.org) ✨

## 🤝 ¿Qué es este proyecto?

Este proyecto es el corazón digital de la organización, donde se publican noticias, comunicados, eventos, recursos y mucho más. Está pensado para ser mantenido y evolucionado por cualquier miembro técnico de la ONG, facilitando la continuidad y la colaboración.

- **ONG:** Mundo Armónico Tseyor
- **Propósito:** Portal web, gestión de contenidos, buscador global, automatización de publicaciones y recursos.
- **Tecnologías principales:** Laravel (backend), Inertia.js (puente), Vue 3 (frontend), MySQL/SQLite (base de datos)
- **Frontend:** Tailwind CSS 4, DaisyUI 5, editor TipTap (rich text)

## 🚀 Stack tecnológico

- **Laravel**: Framework PHP robusto para el backend y la API.
- **Inertia.js**: Conecta el backend con el frontend sin APIs REST tradicionales.
- **Vue 3**: Framework progresivo para la interfaz de usuario.
- **TNTSearch/Scout**: Buscador interno eficiente.
- **Ziggy**: Rutas de Laravel disponibles en JavaScript.

## 🛠️ Guía rápida para nuevos colaboradores

1. **Clona el repositorio y copia `.env.example` a `.env`**
2. Instala dependencias:
   - `composer install`
   - `npm install`
3. Genera la clave de la app: `php artisan key:generate`
4. Configura la base de datos en `.env` y ejecuta migraciones:
   - `php artisan migrate --seed`
5. Compila los assets:
   - `npm run dev` (desarrollo) o `npm run build` (producción)
6. ¡Listo! Accede a la web en tu entorno local.

> **Consejo:** Consulta siempre `PLANNING.md` y `TAREAS.md` para entender la arquitectura, tareas pendientes y convenciones del proyecto.

## 📁 Estructura del proyecto (resumen)

- `/app` – Lógica de negocio, modelos, controladores, servicios
- `/resources` – Vistas, componentes Vue, assets
- `/routes` – Definición de rutas web y API
- `/database` – Migraciones, seeders, factories
- `/public` – Archivos públicos y assets compilados
- `/config` – Configuración de paquetes y servicios

## 📊 Estadísticas del proyecto (a fecha 15/05/2025)

- **Modelos:** 49
- **Controladores:** 88 (incluyendo los de administración CRUD)

## ⚙️ Comandos esenciales de administración

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

### Notas técnicas y optimizaciones

- **Mejora de rendimiento Ziggy:**
  En `vendor/tightenco/ziggy/src/Ziggy.php`, en el constructor, se recomienda añadir la caché de rutas como se indica más abajo para ahorrar entre 10 y 56 ms por petición.
- **Aumentar límite de resultados en búsquedas:**
  En `TNTSearch -> SQLiteEngine.php`, en el método `loadConfig`, añade:
  ```php
  $this->maxDocs = $config['maxDocs'] ?? 500;
  ```
- **Problemas de CSRF en dev.tseyor.org:**
  Si hay errores constantes, borra todas las cookies de `.tseyor.org`.

## 📝 Buenas prácticas y contribución

- Usa ramas descriptivas y pull requests claros.
- Documenta cualquier cambio relevante en este README.

## 🔗 Enlaces útiles

- [Documentación Laravel](https://laravel.com/docs)
- [Documentación Inertia.js](https://inertiajs.com/)
- [Documentación Vue 3](https://vuejs.org/)
- [TNTSearch](https://tntsearch.dev/)
- [Ziggy](https://github.com/tighten/ziggy)

---

## 🧩 Código de optimización Ziggy sugerido

En el constructor de `vendor/tightenco/ziggy/src/Ziggy.php`:

```php
if (!static::$cache) {
    // el archivo ziggy se guarda en cache, aquí se comprueba si debe reconstruirse
    $cache_routes = base_path("bootstrap/cache/routes-v7.php");
    $cache_ziggy = base_path("bootstrap/cache/ziggy2.json");
    if (
        !file_exists($cache_ziggy) ||
        !file_exists($cache_routes) ||
        filemtime($cache_routes) > filemtime($cache_ziggy)
    ) {
        static::$cache = $this->nameKeyedRoutes();
        file_put_contents($cache_ziggy, static::$cache->toJson());
    } else {
        try {
            $ziggy_content = file_get_contents($cache_ziggy);
            static::$cache = collect(json_decode($ziggy_content, true));
        } catch (\Exception $e) {
            static::$cache = $this->nameKeyedRoutes(); // por si hubiera algun error
        }
    }
}
```

---

> Si tienes dudas, contacta con el equipo técnico o revisa la documentación interna. ¡Gracias por contribuir a la web de Tseyor! 🌱

---

## Configuración del entorno de desarrollo

Para configurar el entorno de desarrollo, sigue estos pasos:

1.  Asegúrate de tener instalado PHP 8.1 o superior, Node.js 16 o superior y Composer.
2.  Clona el repositorio: `git clone https://github.com/tseyor-org/tseyor_org_2025_laravel_inertia.git`
3.  Copia el archivo `.env.example` a `.env`: `cp .env.example .env`
4.  Genera la clave de la aplicación: `php artisan key:generate`
5.  Configura la base de datos en el archivo `.env`
6.  Ejecuta las migraciones: `php artisan migrate`
7.  Instala las dependencias de PHP: `composer install`
8.  Instala las dependencias de JavaScript: `npm install`
9.  Compila los assets: `npm run dev`
