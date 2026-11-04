# limpiar-historial-sql

Documento de tareas ODD — espejo Engram: `odd/limpiar-historial-sql/tasks`

## Objetivo

Eliminar del historial de Git todos los artefactos de base de datos (`.sql` y backups `.sql.zip`) por ser información sensible en un repositorio público, sin perder ningún otro contenido.

## Problema / Por qué

- El repositorio es público en GitHub (`msoler75/tseyor_org_2025_laravel_inertia`).
- Aunque los `.sql` no están trackeados hoy (`.gitignore:6,15,45`), siguen accesibles en el historial para cualquier usuario malicioso.
- `TODO.md:35` solo preveía `laravel_inertia.sql`; el inventario real es mayor (ver Descubrimientos).

## Alcance autorizado

- Reescritura local del historial en una copia de trabajo (`/tmp/opencode`), sin tocar el repo original.
- `push --force` pendiente de decisión explícita del usuario (rompe clones existentes).
- Posible contacto con soporte de GitHub para purga de objetos inalcanzables.

## Inventario (evidencia del 09/10/2026)

| Artefacto | Ocurrencias | Tamaño lógico |
|---|---|---|
| `laravel_inertia.sql` (raíz) | 60 | 1792 MB |
| `comunicados.sql` (raíz) | 2 | 76 MB |
| `dfdf.sq.sql` (raíz) | 1 | 0 MB |
| `storage/backups/*.sql.zip` | 16 | ~270 MB |
| `.env` | 0 | nunca se subió |

`git count-objects -vH`: `size-pack = 418.11 MiB` antes de la limpieza.

## Plan

- [x] T1 — Inventario completo del historial (todas las ramas/tags) y clasificación sensible/no sensible — ruta: directa inline
- [x] T2 — Instalar `git-filter-repo` (script único en `/tmp/opencode/tools/`, sin tocar el sistema)
- [x] T3 — Clon espejo a `/tmp/opencode/tseyor-rewrite.git` (el repo original queda intacto = backup)
- [x] T3b — **Copia de seguridad tradicional** en `/home/dev_901/tseyor_backup_2026-10-09.zip` (8.4 MB, permisos 600) — ver Evidencia de respaldo
- [x] T4 — Reescritura quirúrgica con `git-filter-repo --invert-paths` sobre 4 rutas exactas
- [x] T5 — Verificación (ver Evidencia más abajo)
- [x] T6 — `push --force` ejecutado el 09/10/2026 en las 4 ramas. Tips nuevos: `main` 91b3e0fa, `tailwind4` bc29855f, `feat/...` dbebbe0e, `copilot/...` ecc19213 — verificados 1 a 1 contra la copia limpia. `forks_count = 0`.
- [x] T7 — `TODO.md:35` actualizado con el resultado real
- [x] T8 — **Clone local actualizado** (09/10/2026): `fetch` + `update-ref` de `main` y `feat/...` a los tips nuevos, **sin tocar el árbol de trabajo** (mismos 10 cambios sin commitear; SHA-256 de `TODO.md` y `.env` idénticos). Historial viejo preservado en `backup/pre-limpieza-sql-{main,feat,tailwind4,copilot}` (el `.zip` del usuario no contiene `.git`, así que estas ramas son la única copia local del historial antiguo). Verificado: 0 SQL alcanzable desde las ramas activas, 79 desde las de respaldo (a propósito), `main^{tree}` idéntico al del commit anterior.
- [ ] T9 — Contactar con soporte de GitHub para purgar objetos inalcanzables (siguen accesibles por SHA); borrador entregado al usuario

## Segunda fase — auditoría de datos sensibles (09/10/2026)

Auditoría del árbol actual e historial pedida por el usuario antes de enviar el ticket. Hallazgos:

- **Árbol actual limpio**: sin `.env`, claves, `.pem`, `suscripciones.txt` ni credenciales en `.md` (solo diseño).
- **`dh_xjtdnz`** (usuario del servidor): enmascarado en tips actuales pero vivo en el historial de 11 ficheros (scripts `bash/` + `to-do.txt`). Gravedad baja (sin contraseña ni IP), se elimina.
- **`msgp753@gmail.com`**: hardcodeado como `->bcc()` en 3 controladores (5 usos). Se externaliza a config/`.env` y se elimina del historial.
- **Hash bcrypt de fábrica** (`$2y$10$92IXUN...`, el "password" documentado de Laravel) en `scripts/setup-test-db.sh` ×2 para `admin@tseyor.org` / `usuario2@tseyor.org`. Se genera en runtime.

- [x] T10 — Externalizar `msgp753@gmail.com` → `config('mail.bcc.auditoria')` + `MAIL_BCC_ARCHIVO` en `.env`/`.env.example` (3 controladores, `config/mail.php`) — delegado a writer; spot-check padre OK
- [x] T11 — `setup-test-db.sh`: generar el hash con `php -r password_hash` en runtime (quitar el literal) — idem
- [x] T12 — Commit acotado `f4f55851` (6 ficheros, 79+/62-) + ciclo nativo RDD: assess `high` (señal `shell_process`), consent `granted` por el usuario, 4 lentes capturadas inline (1 WARNING informativo en reliability, sin graves), estado `approved`, acknowledgement ejecutado → **autoridad quemada** (linaje `review-735aaaf535db247b`)
- [x] T13 — Segundo scrub (`--replace-text`) en mirror `/tmp/opencode/tseyor-rewrite2.git` + verificación completa — ver Evidencia segundo scrub
- [x] T14 — `push --force` 4 ramas + resincronizar clone local (`fetch` + `reset --keep`, árbol de trabajo y cambios sin commitear intactos)
- [x] T15 — `limpieza-historial-sql.md` actualizado (tips nuevos, tabla de reemplazos, ticket re-listo con tips y blob de ejemplo)

## Criterios de aceptación

- `git rev-list --objects --all` en la copia limpia no contiene ninguna ruta `.sql`/`.sql.zip`.
- Pase 1: el árbol de punta era byte a byte idéntico al actual. Pase 2: los tips cambian **solo** en las líneas que contienen los 3 strings reemplazados (diferencia verificada diff a diff).
- El número de commits y sus mensajes se conservan (solo cambian los SHAs).
- El repo original queda intacto hasta que el usuario autorice el push (autorizado y ejecutado en ambos pases).

## Evidencia de verificación (09/10/2026)

Ejecutada en `/tmp/opencode/tseyor-rewrite.git` contra el repo original sin tocar:

1. **Cero SQL en el historial nuevo**: `git rev-list --objects --all | grep -i '\.sql'` → vacío.
2. **Cero pérdida de contenido**: de los **10.967 blobs no-SQL** alcanzables en el historial viejo, **0 ausentes** en el nuevo (`comm -23` → 0).
3. **Árboles de punta idénticos** en `main`, `feat/eventos-mostrar-miniatura`, `copilot/fix-...` (`^{tree}` byte a byte iguales). Excepción explicada: `tailwind4` 754→738 ficheros = **exactamente los 16 SQL**, y la lista "ficheros solo en NEW" está **vacía** (nada añadido ni corrompido). Esa rama antigua tenía los dumps en su punta.
4. **18 commits podados** por quedar vacíos (todo su diff era el dump): verificados **uno a uno = 0 ficheros no-SQL**. El método se auto-validó contra un commit normal (devolvió 3). Commits: `sql`, `guias y lugares`, `sql update`×2, `bd backup`, `database`, `backpack installed`, `backup de la base de datos`, `backup database backpack permission`, `backup db`, `database crud changes`, `comunicados`, `terminos`×2, `db backup`, `db update`, `after soft deletes added`, `garbage`.
5. **Tamaño**: `size-pack` 418.11 MiB → **89.88 MiB**.
6. **Repo original intacto**: HEAD `ef063e30`, `size-pack` 418.11 MiB, estado de trabajo sin cambios → funciona como backup.

> Nota: los SHAs de TODOS los commits cambian (inevitable: un cambio en un commit ancestral reencadena los descendientes). No es pérdida; se comprueba por contenido, no por SHA.

## Evidencia post-push (09/10/2026)

- `git ls-remote --heads origin` → los 4 tips coinciden **exactamente** con la copia limpia verificada.
- `forks_count = 0` → no hay forks que retengan el historial sensible.
- **Los objetos viejos siguen en GitHub**: `gh api .../git/blobs/<sha del dump>` devuelve tamaño `47757917` y el commit viejo `84077695` responde 200 → necesario pedir la purga a soporte (T8).
- Repo original `/home/dev_901/tseyor` **no se ha tocado**: conserva el historial viejo (es el backup) y ahora está desfasado respecto a `origin` → no hacer `git pull` sin antes resolverlo.

## Evidencia de respaldo tradicional (09/10/2026)

`/home/dev_901/tseyor_backup_2026-10-09.zip` — 8.4 MB, permisos `600`, **1284 ficheros**, generado con `zipfile` (no hay `zip` en el sistema).

- **Incluye**: todo el árbol de trabajo actual (con las modificaciones sin commitear), `.env` (imprescindible para recuperar credenciales) e imágenes (121, casi todas iconos de `public/` que la web necesita — excluirlas no ahorraba nada, solo rompía el proyecto).
- **Excluye**: `.git` (420 MB; el historial pre-limpieza sigue en el repo original), `node_modules` (459 MB), `vendor` (149 MB), `storage` (1.1 GB), `*.sql`/`*.sql.zip`, logs y cachés de build.
- **Verificación**: `testzip` OK · 0 entradas `.sql` · 0 entradas de `.git`/`node_modules`/`vendor`/`storage` · 0 ficheros con tamaño distinto al del disco · `TODO.md` y `.env` con SHA-256 idénticos al disco.

## Evidencia segundo scrub (09/10/2026)

Mirror `/tmp/opencode/tseyor-rewrite2.git` (clon espejo del local; ramas `backup/*` y `refs/remotes/*` eliminadas del mirror antes de reescribir; solo las 4 ramas activas).

1. **Reemplazos** (`--replace-text`, fichero `/tmp/opencode/replacements.txt`, 4 líneas):
   - `dh_xjtdnz` → `REMOVED_USER`
   - `msgp753@gmail.com` → `removed@example.com`
   - `$2y$10$92IXUN…` → `$2y$10$QZokGCM0…` (bcrypt de `password` con sal nueva — el factory sigue funcional)
   - `\$2y\$10\$92IXUN…` → misma variante escapada (**imprescindible**: `setup-test-db.sh` histórico usaba `$` escapado para bash; el primer pase no lo cubrió y quedaron 2 ocurrencias → segundo pase correctivo)
2. **Verificación**: barrido de las **10.973 blobs** con `cat-file --batch` + `grep -a` para `dh_xjtdnz`/`msgp753`/`92IXUNpkj` → **0 ocurrencias**; `git log --all -S` → 0/0/0; paths `.sql` → 0.
3. **Diffs de punta** (viejo vs nuevo, objeto a objeto): `main` = 1 línea (UserFactory hash), `feat` = 8, `tailwind4` = 24, `copilot` = 6 — todas y solo reemplazos de los 3 strings.
4. **Tips finales**: `main` 081353be, `tailwind4` b1dba8cc, `feat/...` 8ecb06eb, `copilot/...` 8ebffe96 — `push --force` OK en las 4; clone local con `fetch` + `reset --keep` (mismos 6 modificados + 5 sin trackear de antes; `UserFactory.php` del árbol de trabajo ya contiene el hash nuevo); 0 secrets en los 4 tips locales.
5. **Tamaño**: `size-pack` 90.08 MiB (sin cambios significativos respecto al pase 1).

## Verificación prevista

- `git rev-list --objects --all | grep -i sql` → vacío (salvo `.gitignore` si se conserva)
- `git rev-parse <rama>^{tree}` old vs new → igual para todas las ramas/tags
- `git rev-list --count --all` old vs new → igual
- `git diff --stat <old-main> <new-main>` → vacío

## Descubrimientos

- 09/10/2026: además de los `.sql` de raíz ya conocidos, el historial contiene 16 backups `storage/backups/*.sql.zip` de producción (~270 MB) que el TODO no mencionaba.
- El historial también contiene `storage/indexes/comunicados.index` (69 MB) — **no sensible**, se conserva.
- BFG está sin mantenimiento y requiere Java (no instalado); se usa `git-filter-repo` (estándar actual, script único sin dependencias).
- La auditoría inicial dio los tips por "limpios": **fue incorrecto para el hash** — `UserFactory.php`@main traía el bcrypt por defecto de Laravel, y los tips de `tailwind4` (11 ficheros) y `copilot` (4 ficheros) también arrastraban los 3 strings. Los tips NO equivalen a árbol limpio; verificar con `git grep` por ref.
- `--replace-text` es literal: un hash bcrypt dentro de strings de bash dobles aparece como `\$2y\$10\$…` y NO matchea la forma sin escapar. Tras el pase 1 quedaron 2 ocurrencias en `setup-test-db.sh` → detectadas con barrido de blobs (no con `-S`), corregidas añadiendo la variante escapada al fichero de reemplazos y re-ejecutando `--force`.
- Ciclo RDD completo sobre `f4f55851`: assess `high` (ruido `shell_process`), START+consent del usuario, 4 lentes inline (el usuario optó por hacerlas él mismo en el padre por el bug de OpenCode free-tier que bloquea a los subagentes `review-*` con `bash: deny`), 1 WARNING (BCC condicional sin test), `approved`, acknowledgement con token de 64 hex → autoridad quemada. Schema de findings: `id` opcional con prefijo por lente `R1-`/`R2-`/`R3-`/`R4-`; `proof_refs` con path **sin** sufijo de línea.
