# temas-filosofia — Grid de temas: portada + /filosofia/temas

## Objetivo
Que las páginas temáticas (históricamente "Descubre") aparezcan agrupadas en `/filosofia/temas` y que la portada muestre el grid de tarjetas de temas de la filosofía.

## Problema / Evidencia
- `Pagina.filosofia` está en `0` en TODAS las filas (0/41 en local, y el sitio vivo `164.68.107.151.sslip.io` = este host devuelve `paginasPorCategoria: []` y `paginasFilosofia: 0`).
- `PaginasController::temas()` filtra `where('filosofia', true)` → vacío. `portada()` filtra `filosofia=true` + LIKE hardcode (`%rayo%|%sociedades%|%confederacion%|%observacion%`) → vacío.
- La migración `2026_07_14_000001` solo agrega la columna con `default(false)`; nadie la puso en `1` (el checkbox existe en Admin/PaginaCrudController).
- Contenido: solo 2/8 slugs temáticos existen en DB (`confederacion-de-mundos-habitados-de-la-galaxia`, `el-rayo-sincronizador`), ambos `descubre=1, filosofia=0, orden=NULL`. Los back links `atras_ruta='filosofia/temas'` ya están puestos por migración. 6 slugs dan 404 también en este host; 4 de ellos existen en tseyor.org (`la-autoobservacion`, `las-sociedades-armonicas` + los 2 ya citados), 4 no existen en ningún lado (`salto-cuantico`, `especializacion`, `retroalimentacion`, `espejos`).

## Alcance autorizado
Arreglo de código + datos pedido explícitamente por el usuario ("vamos a arreglar las páginas Descubre"). NO incluye crear contenido faltante (decisión del usuario, pendiente).

## Tareas
- [x] T1 — Migración de datos: `filosofia=true` y `orden` fijo para los 8 slugs temáticos (solo donde exista la fila; idempotente). Orden: el-rayo=10, las-sociedades=20, confederacion=30, la-autoobservacion=40, resto 50+. — Evidencia: `2026_10_21_000001_marcar_paginas_filosofia_temas` aplicada con `--path --force` en dev y test DB (DONE); re-ejecución = "Nothing to migrate"; en dev marcó las 2 filas existentes (el-rayo orden=10, confederacion orden=30).
- [x] T2 — `PaginasController`: (a) portada: reemplazar el LIKE hardcode por `filosofia=true` + `orderBy('orden')` + `limit(4)` (el hint del admin ya documenta `orden` como "Orden de aparición en portada"); (b) `temas()`: los slugs fuera del mapa en vez de descartarse van a un grupo «Otros temas». — Evidencia: LIKE cerrado eliminado de `portada()`; grupo «Otros temas» (icono `ph:compass-duotone`) añadido tras el bucle de categorías en `temas()`.
- [x] T3 — Test de feature que fija el comportamiento: con filas marcadas, `GET /filosofia/temas` devuelve categorías no vacías; la portada devuelve el top-4 por `orden`. — Evidencia: `tests/Feature/FilosofiaTemasTest.php` (DatabaseTransactions); RED pre-T2 observado: 2 failed, 1 passed («Otros temas» null; paginasFilosofia `[]`); GREEN post-T2: 3 passed (51 assertions).
- [x] T4 — Verificación (migrate + tests + tinker de conteos) y work-unit commit en la rama activa `feature/eventos-ubicacion-contacto`. — Evidencia: tinker dev = `filosofia=true count: 2`, portada query devuelve el-rayo + confederacion; `php artisan test --filter FilosofiaTemas` → 3 passed; commit work-unit con migración + controlador + test + este documento.

## Criterios de aceptación
- `GET /filosofia/temas` lista las páginas temáticas existentes agrupadas (hoy: 2 en «El cambio de era»).
- La portada muestra tarjetas en el grid (mínimo las 2 existentes; sube a 4 cuando exista el contenido).
- Tests verdes; `php artisan migrate` idempotente.

## Pendiente de decisión del usuario
- Contenido faltante: importar/crear `la-autoobservacion` y `las-sociedades-armonicas` (existen en tseyor.org) y decidir sobre `salto-cuantico`, `especializacion`, `retroalimentacion`, `espejos` (no existen en ningún lado).

## Registro de rutas
- Ruta elegida: delegated direct (writer trigger: 2+ ficheros no triviales: migración + controlador + test).

## Registro RDD
- 2026-10-21 — commit `bbd2e957`; assess (base c2516834, committed-only, untracked excluido): risk **medium** (`executable_change` en PaginasController), `review_due=false` (**under_budget**, 236 líneas en el rango, 4 commits). Frontera revisada no avanza; queda pendiente en el slice hasta llegar a ~400 líneas.
- Verificación: RED→GREEN observado (`FilosofiaTemas`: 2 failed → 3 passed, 51 aserciones); migrate idempotente; spot-check del padre: `php artisan test --filter FilosofiaTemas` 3 passed; verificación en vivo sslip.io: `/filosofia/temas` → grupo «El cambio de era» con 2 páginas; portada → 2 tarjetas.
