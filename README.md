# Comandos básicos con Artisan

Este documento describe los comandos básicos con Artisan, la interfaz de línea de comandos incluida en Laravel, utilizados para configurar y administrar un sitio web.

## Actualizar rutas

Si se crean o se cambian las rutas (routes) en web.php o api.php se puede usar estos comandos:

```
php artisan route:clear
```

## Optimizar laravel

Realiza varias tareas de optimización, como la generación de clases de contenedor optimizadas, la recopilación de rutas en caché y la eliminación de rutas y vistas no utilizadas:

```
php artisan optimize
```

## Limpiar la caché

El siguiente comando se utiliza para eliminar la caché del sitio:

```
php artisan cache:clear
```

## Limpiar la configuración

Si no se detecta el archivo .env o se han realizado cambios en la configuración, puedes utilizar el siguiente comando para limpiar la configuración en caché:

```
php artisan config:clear
```

## Importar datos

Si necesitas importar comunicados u otros datos en tu sitio web, puedes utilizar estos comandos:

```
php artisan import:comunicados
```

```
php artisan import:paginas
```

## Búsquedas

Cuando un modelo utiliza el trait _Searchable_, se crea automáticamente un índice con los nuevos datos o cuando se modifican los existentes.

Si ya tienes datos en un modelo antes de agregar el trait o si deseas recrear por completo el índice de un modelo, ejecuta el siguiente comando:

```bash
php artisan scout:import "App\Models\Comunicado"
```

Puedes ver el estado actual del buscador:
 
```bash
php artisan scout:status
```

## Contenidos

El modelo _Contenido_ es multi-uso. 

- Es una copia o "espejo" de los datos básicos de muchos otros modelos (noticias, comunicados, páginas, normativas, eventos, blog...) y sirve para listar las Novedades del sitio.
- Es un índice global para el buscador general _SearchAll_

Para ello cada vez que se crea o se actualiza un registro de cualquier modelo (que herede de _ContenidoBaseModel_) automáticamente se crea o actualizar un _Contenido_ "espejo" que contiene:

- Los datos esenciales (título, descripción, fecha, imagen, colección) para listar las novedades del sitio de una sola vez.
- Además del título, el texto o palabras clave para el buscador general (SearchAll) con el método _getTextoContenidoBuscador_


Los contenidos se generan y actualizan de forma automática, pero se puede usar este comando para recrear los contenidos de algún modelo en concreto:

```bash
php artisan contenidos:import noticias
```

Este comando recrea todos los contenidos "espejo" de las noticias.

# Deploy

Recordar ejecutar:

```bash
php artisan ziggy:generate
```

Para mejorar el rendimiento:

## Cambios en Response de Inertia

Mejoras en el código.

vendor\tightenco\ziggy\src\Ziggy.php en el método _constructor, se coloca este código (ahorro de 10 a 56 ms):
```
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

# SQLiteEngine

Para superar el límite de 500 en los resultados de busqueda, ir al método loadConfig de TNTSearch -> SQLiteEngine.php y añadir

```
$this->maxDocs = $config['maxDocs'] ?? 500;
```

# Base de datos

# Crear un backup de la base de datos

```bash
php artisan db:backup
```


# Sitemap

```bash
php artisan sitemap:generate
```

# Page-cache

Since the responses are cached to disk as static files, any updates to those pages in your app will not be reflected on your site. To update pages on your site, you should clear the cache with the following command:

```bash
php artisan page-cache:clear
```
