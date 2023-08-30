# Comandos básicos con Artisan

Este documento describe los comandos básicos con Artisan, la interfaz de línea de comandos incluida en Laravel, utilizados para configurar y administrar un sitio web.

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

Si necesitas importar comunicados u otros datos en tu sitio web, puedes utilizar el siguiente comando:

```
php artisan import:comunicados
```

## Búsquedas

Para realizar búsquedas en tu aplicación utilizando Laravel Scout, puedes utilizar los siguientes comandos:

```bash
php artisan scout:import "App\Models\Comunicado"
```

Este comando importará los comunicados al buscador.

```bash
php artisan scout:status
```

Este comando te permitirá ver el estado actual del buscador.

## Contribuciones

Si deseas contribuir a este proyecto, puedes hacerlo siguiendo estas pautas: [CONTRIBUTING.md](link-to-contributing-md).

## Licencia

Este proyecto está bajo la Licencia MIT. Puedes consultar los detalles en el archivo [LICENSE.md](link-to-license-md).
