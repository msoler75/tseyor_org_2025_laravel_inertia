# Eventos: ciudad, hora de fin, lugar y contacto

**Fecha:** 2026-10-19 · **Ruta:** delegated direct (writer trigger: 6 archivos no triviales)

## Objetivo
Añadir a los eventos cuatro campos opcionales —`ciudad`, `hora_fin`, `lugar` (datos de dirección) y `contacto` (teléfono/correo)— y mostrarlos: "Ciudad, País" en `Eventos/Index`, y `lugar`/`contacto`/`hora_fin` en el bloque inicial de datos de la página del evento.

## Problema / contexto verificado
- La tabla `eventos` (BD viva `tseyor_org_2025`, verificada con `php artisan db:table eventos`) tiene 22 columnas y **no** tiene `ciudad`, `hora_fin`, `lugar` ni `contacto`. No existe migración base del esquema en el repo (el esquema vino de dumps SQL borrados), así que solo sirven migraciones *alter*.
- El frontend **ya está codificado** para `hora_fin` (`resources/js/Pages/Eventos/Evento.vue:93-102`) y `lugar` (`Evento.vue:103-112`), y `Index.vue:43/58/74` ya pasa `hora-fin` → hoy se pintan siempre vacíos.
- `Eventos/Index.vue:45/60/76` pasa `:location="contenido.pais"` → badge en `CardEvent.vue:13` muestra solo el país.
- El país (código de 2 letras) se convierte a nombre en servidor: `EventosController::index` (≈línea 73) y `show` (≈línea 104).
- Select de `index` (`EventosController.php:37`) no incluye `ciudad` ni `hora_fin`.
- Convención del proyecto para opcionales: columnas `->nullable()`, validación `nullable` en CRUD, mutador que convierte `'' → null` (`Evento::setImagenesAttribute`, `app/Models/Evento.php:62-68`) y guardas `v-if` en Vue.
- Referencia de dirección/contacto existente: modelo `Contacto` (direccion/poblacion/telefono/email).

## Decisiones de diseño
1. Cuatro columnas nuevas nullable: `ciudad` varchar(255), `hora_fin` time, `lugar` varchar(255), `contacto` varchar(255).
2. `hora_fin` en columna separada (no en `fecha_fin`): los accessores de fecha fuerzan `Y-m-d` y `hora_inicio` ya es columna `time`.
3. "Ciudad, País" se construye en el frontend de Index a partir de `ciudad` + `pais` ya convertido; sin `ciudad` → solo país (comportamiento actual intacto).
4. En la página del evento, si hay `ciudad` la fila del país muestra "Ciudad, País"; `lugar` y `contacto` filas nuevas/ya existentes en el bloque inicial.
5. `contacto` es texto libre (teléfono y/o correo), se muestra tal cual, sin parsear ni convertir en enlaces.
6. Fuera de alcance: esquema MCP (`app/MCP/Data/entities/evento.php`), geocodificación, JSON-LD.

## Tareas
- [x] T1 — Test feature en rojo: `tests/Feature/Eventos/EventosUbicacionContactoTest.php` (5 tests: Index "Madrid, País" vs solo país + `hora_fin` en props; detalle pinta `lugar`, `contacto`, `hora_fin`, `ciudad`; ausencia → no se pinta; `'' → null`).
- [x] T2 — Migración alter `2026_10_19_000001_add_ciudad_hora_fin_lugar_contacto_to_eventos_table.php` + `app/Models/Evento.php` (`$fillable` + 4 mutadores `'' → null`).
- [x] T3 — Admin `EventoCrudController.php`: `hora_fin` (time, tras `hora_inicio`), `ciudad`/`lugar`/`contacto` (text, `nullable|max:255`) cerca de `pais`.
- [x] T4 — `EventosController.php:37`: `ciudad` y `hora_fin` en el select de `index`. `Index.vue`: helper local `ubicacion()` → "Ciudad, País" en los 3 bloques.
- [x] T5 — `Evento.vue`: ciudad en la fila del país (`v-if="evento.pais || evento.ciudad"`), fila `contacto` nueva tras `lugar` (`lugar` y `hora_fin` ya codificados).
- [x] T6 — BDs migradas (dev + testing vía `--path`), tests en verde, commit `32f0b611`.

## Superficie de edición autorizada
- `database/migrations/2026_10_19_*_add_ciudad_hora_fin_lugar_contacto_to_eventos_table.php` (nuevo)
- `app/Models/Evento.php`
- `app/Http/Controllers/Admin/EventoCrudController.php`
- `app/Http/Controllers/EventosController.php`
- `resources/js/Pages/Eventos/Index.vue`
- `resources/js/Pages/Eventos/Evento.vue`
- `tests/Feature/Eventos/EventosUbicacionContactoTest.php` (nuevo)

## Criterios de aceptación
- Evento con `ciudad = Madrid`: el badge de Index muestra "Madrid, España". Sin `ciudad`: solo "España".
- `lugar` y `contacto` visibles en el bloque inicial de la página del evento solo cuando existen (patrón `v-if`).
- `hora_fin` editable en admin (campo time) y visible en la página (ya codificado) y en Index.
- Admin puede guardar dejando los cuatro campos vacíos (nullable, sin romper validación existente).
- Tests en verde (salvo fallos preexistentes conocidos).

## Verificación
- `DB_DATABASE=web_tseyor_org_2024_testing php artisan migrate --force` (la BD de test usa `DatabaseTransactions`, no migra sola) y `php artisan migrate` en dev.
- `php artisan test --filter Eventos`
- `php artisan test --testsuite=Unit` (regresión)

## Evidencia de verificación (2026-10-19)
- RED previo: `php artisan test --filter EventosUbicacionContactoTest` → 3 failed (`Property [evento.lugar] does not exist`, `Failed asserting that null is identical to 'Madrid'`).
- `php artisan migrate --force` (dev) → DONE; columnas confirmadas con `php artisan db:table eventos`.
- Testing BD: `migrate --force` completo falla por desync preexistente del registro `migrations` → aplicado con `--path=...2026_10_19_000001... --force` → DONE.
- `php artisan test --filter Eventos` → 5 nuevos tests en verde (78 aserciones); ver "Fallos ambientales".
- `php artisan test --testsuite=Unit` → 10 failed / 87 passed (ver "Fallos ambientales").
- `npm run build` → exit 0 (13.12s).
- Spot-check del orquestador: `php artisan test --filter EventosUbicacionContactoTest` → **5 passed**.
- Commit de trabajo: **`32f0b611`** `feat(eventos): ciudad, hora_fin, lugar y contacto opcionales` (7 archivos, 223 insertiones).
- Evaluación RDD (`review assess --base-ref main --committed-only`, untracked preexistente excluido con inventario canónico): riesgo **medium** (`executable_change` en `EventoCrudController`), 8 rutas / 303 líneas → `review_due: false`, motivo **`under_budget`**: la revisión nativa queda **pendiente en el slice** hasta que un commit posterior alcance ~400 líneas; el límite revisado NO avanza.
- Atribución de fallos verificada por el orquestador en la BD de test: 42 filas huérfanas `evento-portada%` (más antigua 2026-10-07, anterior a la sesión; la más reciente la añadió la corrida de Unit del writer) y **0** restos `ubicacion` (los tests nuevos revierten limpio).

## Fallos ambientales conocidos (preexistentes, no causados por este cambio)
- `tests/Unit/NodoTest.php:292` (sticky) falla en la base antes de estos cambios.
- `EventoToolTest::listar` falla porque `tests/Unit/EventoMiniaturaTest.php` (sin `DatabaseTransactions`) filtra fixtures `evento-portada-*` a la BD de test en cada corrida de Unit (42 filas huérfanas de 2026-10-07/08) y rompe la paginación (12 ≠ 4).
- Unit extra: `ArchivosTest`/`EquipoTest` (`mkdir` permission denied en el sandbox), `EnlaceCortoTest` ×3 (Blade cache corrupto en `storage/framework/views/9ae3a79b...`), `StorageItemTest` ×4 (APP_URL `sslip.io` vs `http://localhost`).
- La BD de test tiene el registro `migrations` desincronizado → `migrate --force` completo falla en `personal_access_tokens`; patrón correcto: migración por `--path`.

## Progreso
- [x] T1 · [x] T2 · [x] T3 · [x] T4 · [x] T5 · [x] T6

## Predicción / estrategia de entrega
- ~250 líneas autorizadas previstas (< 400) → una sola unidad de trabajo, sin encadenamiento de PRs.
