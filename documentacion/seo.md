# Sistema SEO 

## Arquitectura del Sistema

El proyecto utiliza un sistema híbrido de SEO que combina páginas Vue.js específicas con páginas almacenadas en base de datos.

### Flujo de Funcionamiento

1. **Rutas Específicas**: Laravel tiene rutas definidas para páginas específicas (ejemplo: `/biblioteca`, `/cursos`, etc.) donde cada ruta invoca normalmente un controlador que carga una vista .vue de la carpeta `resources/js/Pages/`
2. **Catch-all Route**: Al final del archivo `routes/web.php` hay una ruta catch-all que captura cualquier URL no definida:
   ```php
   Route::get('{ruta}', [PaginasController::class, 'show'])->where('ruta', '[a-z0-9\-\/\.]+')->name('pagina');
   ```

### Tipos de SEO

#### 1. SEO de Páginas Específicas
Para páginas con controladores específicos (como `LibrosController`, `EventosController`, etc.):

```php
return Inertia::render('Libros/Index', [
    'listado' => $resultados,
    'categorias' => $categorias
])
->withViewData(SEO::get('libros'));
```

#### 2. SEO de Modelos Individuales
Para páginas de detalle de modelos:

```php
return Inertia::render('Libros/Libro', [
    'libro' => $libro
])
->withViewData(SEO::from($libro));
```

#### 3. SEO de Páginas de Base de Datos
Para páginas almacenadas en la tabla `paginas`:

```php
return Inertia::render('Pagina', [
    'pagina' => $pagina
])
->withViewData(SEO::from($pagina));
```

## Clase SEO

La clase `App\Pigmalion\SEO` tiene dos métodos principales:

### SEO::get($route)
Busca una página en la tabla `paginas` por su campo `ruta`:

```php
public static function get($route)
{
    $pagina = Pagina::where('ruta', $route)->where('visibilidad', 'P')->first();

    if ($pagina)
        return [
            'seo' => new SEOData(
                title: $pagina->titulo,
                description: $pagina->descripcion,
                image: $pagina->imagen ?? config('seo.image.fallback')
            )
        ];
}
```

### SEO::from($model)
Usa los datos directamente de un modelo Eloquent:

```php
public static function from($model)
{
    return ['seo' => $model];
}
```

## Modelo Pagina

La tabla `paginas` tiene los siguientes campos relevantes para SEO:

- `titulo`: Título de la página para SEO
- `descripcion`: Meta descripción
- `imagen`: Imagen para redes sociales (añadida recientemente)
- `ruta`: URL de la página (debe coincidir con la ruta web)
- `visibilidad`: 'P' para público, 'B' para borrador

```php
protected $fillable = [
    'titulo',
    'ruta',
    'atras_ruta',
    'atras_texto',
    'descripcion',
    'texto',
    'palabras_clave',
    'imagen',
    'visibilidad'
];
```

## Cómo Funciona la Prioridad

1. **Primera prioridad**: Rutas específicas definidas en `routes/web.php`
2. **Segunda prioridad**: Catch-all que busca en la tabla `paginas`
3. **Fallback**: 404 si no encuentra nada

## Ejemplo Práctico

### Caso: Página "Biblioteca"

1. Existe una ruta específica: `Route::get('biblioteca', [PaginasController::class, 'biblioteca'])->name('biblioteca');`
2. Se renderiza la vista `Biblioteca.vue` con datos específicos del controlador
3. El SEO se obtiene con `SEO::get('biblioteca')` que busca una página con `ruta = 'biblioteca'`
4. Si existe esa página en la tabla, se usan sus datos SEO
5. Si no existe, se usa la configuración por defecto

### Caso: Página Dinámica "Acerca de"

1. No existe ruta específica para `/acerca-de`
2. La ruta catch-all captura la URL
3. `PaginasController::show()` busca una página con `ruta = 'acerca-de'`
4. Si existe, renderiza la vista `Pagina.vue` con el contenido de la base de datos
5. El SEO se obtiene con `SEO::from($pagina)`

## Configuración SEO

El archivo `config/seo.php` contiene configuraciones globales:

- `site_name`: Nombre del sitio
- `title.suffix`: Sufijo para títulos
- `title.homepage_title`: Título específico de portada
- `description.fallback`: Descripción por defecto
- `image.fallback`: Imagen por defecto
- `favicon`: Icono del sitio

## Mejores Prácticas

### Para Páginas Específicas
1. Crear entrada en tabla `paginas` con la misma `ruta` que la URL
2. Usar `SEO::get('nombre-ruta')` en el controlador
3. Rellenar campos `titulo`, `descripcion` e `imagen`

### Para Modelos Individuales
1. Asegurar que el modelo tenga campos `titulo`, `descripcion` e `imagen`
2. Usar `SEO::from($modelo)` en el controlador
3. El paquete laravel-seo usará automáticamente estos campos

### Para Páginas Dinámicas
1. Crear registro en tabla `paginas` con contenido en campo `texto`
2. La ruta catch-all se encargará automáticamente
3. El SEO se manejará con `SEO::from($pagina)`

## Campos Requeridos para SEO

Cualquier modelo que se use con `SEO::from()` debe tener:

- `titulo` o `title`: Para el título de la página
- `descripcion` o `description`: Para la meta descripción
- `imagen` o `image`: Para la imagen de redes sociales (opcional)

## Herramientas de Debug

Para verificar qué datos SEO se están enviando:

1. Inspeccionar el HTML generado y buscar las meta tags
2. Usar herramientas como Facebook Debugger o Twitter Card Validator
3. Revisar los logs de Laravel si hay errores en la clase SEO

## Migración Reciente

Se añadió el campo `imagen` a la tabla `paginas` para permitir imágenes específicas de SEO:

```sql
ALTER TABLE `paginas` ADD `imagen` VARCHAR(255) NULL AFTER `descripcion` COMMENT 'Imagen para SEO y redes sociales';
```
