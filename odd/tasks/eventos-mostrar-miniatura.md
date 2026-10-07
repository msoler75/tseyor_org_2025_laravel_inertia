# Eventos: mostrar también la portada en la página del evento

**Fecha:** 2026-10-07 · **Ruta:** delegated direct (writer trigger: 5 archivos no triviales)

## Objetivo
Permitir que la imagen de portada (miniatura) de un evento se muestre también como imagen en la página del evento, de forma opcional por evento.

## Problema
- En admin (`EventoImagenesField.vue`), la primera imagen de la lista ordenada es la portada → se guarda en `imagen`; el resto va a `imagenes` (CSV) — `EventoCrudController.php:292-311`.
- `Evento.vue` (`displayImages`) solo pinta `imagenes` → **la portada no aparece en la página cuando hay imágenes adicionales**, aunque sí es la primera del visor (`allImages`). Incoherencia real.

## Solución elegante y robusta
Columna boolean `mostrar_miniatura` (default `false`) + checkbox en admin + condición en `displayImages`. Sin campos nuevos no hacía falta: la alternativa (centinela dentro del CSV) ensucia los datos y obliga a filtrar en cada consumidor.

## Tareas
- [x] T1 — Migración `database/migrations/2026_10_07_000001_add_mostrar_miniatura_to_eventos_table.php`: boolean `mostrar_miniatura` default false.
- [x] T2 — Modelo `app/Models/Evento.php`: añadir a `$fillable` y `$casts` (boolean).
- [x] T3 — Admin `app/Http/Controllers/Admin/EventoCrudController.php`: checkbox `mostrar_miniatura` tras el campo `imagenes`.
- [x] T4 — Frontend `resources/js/Pages/Eventos/Evento.vue`: `displayImages` incluye la portada la primera cuando `mostrar_miniatura` es true (con dedup frente a `imagenes`).
- [x] T5 — Tests `tests/Unit/EventoPortadaTest.php`: cast boolean, fillable, y comportamiento de `displayImages` (lógica extraída si es necesario para testarla).
- [x] T6 — Commit de trabajo en la rama de feature.

## Criterios de aceptación
- Con `mostrar_miniatura = 1`: la portada es la primera imagen de la página y del visor.
- Con `mostrar_miniatura = 0` o sin imágenes adicionales: comportamiento actual intacto.
- Tests en verde (salvo fallos preexistentes conocidos).

## Verificación
- `php artisan test --testsuite=Unit`
- `php artisan test --testsuite=Feature --filter Evento`

## Fallos ambientales conocidos (preexistentes)
- `tests/Unit/NodoTest.php:292` (sticky) falla en la base antes de estos cambios.

## Progreso
- [x] T1 · [ ] T2 · [ ] T3 · [ ] T4 · [ ] T5 · [ ] T6
