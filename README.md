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

Para realizar búsquedas en tu aplicación utilizando Laravel Scout, puedes utilizar los siguientes comandos:

```bash
php artisan scout:import "App\Models\Comunicado"
```

Este comando creará un índice de los comunicados para ser usado en el buscador específico de comunicados. 

Una vez creado el índice no es necesario hacer nada más pues ya se indexa automáticamente con los nuevos comunicados o modificación de los mismos.
 
```bash
php artisan scout:status
```

Este comando te permitirá ver el estado actual del buscador.

## Contenidos

El modelo _Contenido_ es multi-uso. 

- Es una copia de los datos base de cualquier colección (noticias, comunicados, páginas, normativas, eventos, blog...) y sirve a propósito para ver las Novedades del sitio.
- Es un índice global para el buscador general _SearchAll_

Para ello cada vez que se crea un nuevo dato de cualquier modelo que hereda de _ContenidoBaseModel_ se crea un nuevo _Contenido_ con los datos válidos para la 2 propósitos:

- Los datos esenciales para ver las nocedades de ese contenido (título, descripción, fecha)
- Los datos para indexar la búsqueda de ese contenido en el buscador general (SearchAll) con el método _getTextoBuscador_


Se puede usar estos comandos para recrear los contenidos a partir de las colecciones.

```bash
php artisan contenidos:import noticias
```

Este comando recrea todos los contenidos a partir de las noticias.

No es necesario lanzar este comando cuando se crean nuevas noticias o comunicados, o se editan, pues automáticamente el trait _EsContenido_ ya gestiona la creación o actualización automática del contenido a partir de todas los modelos implicados.

## Contribuciones

Si deseas contribuir a este proyecto, puedes hacerlo siguiendo estas pautas: [CONTRIBUTING.md](link-to-contributing-md).

## Licencia

Este proyecto está bajo la Licencia MIT. Puedes consultar los detalles en el archivo [LICENSE.md](link-to-license-md).
