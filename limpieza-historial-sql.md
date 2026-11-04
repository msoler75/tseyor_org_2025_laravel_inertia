# Limpieza del historial SQL — estado y qué queda por hacer

Documento único de la operación. Detalle técnico completo en `odd/tasks/limpieza-historial-sql.md`.

Fecha: 09/10/2026 · Repo: `msoler75/tseyor_org_2025_laravel_inertia` (público)

---

## 1. Qué se hizo (ya completado)

Se eliminaron del historial de Git los dumps de base de datos que estaban commiteados:

- `laravel_inertia.sql` — 60 copias (~1.8 GB lógicos)
- `comunicados.sql` — 2 copias
- `dfdf.sq.sql`
- `storage/backups/*.sql.zip` — 16 backups de producción

Verificado antes y después: **0 ficheros valiosos perdidos** (10.967 blobs comprobados uno a uno, árboles de las ramas idénticos byte a byte).

- Se hizo `push --force` a las 4 ramas (pase 1) y el clone local se resincronizó.
- `.env` **nunca** se subió a Git.
- No hay *forks* del repo (0), así que ningún clon ajeno retiene los dumps.

### Segundo pase (mismo día): correo, usuario de servidor y hash de fábrica

Una auditoría posterior encontró 3 strings sensibles vivos en el historial (y en los tips). Se reescribió de nuevo con `--replace-text`:

| String eliminado | Dónde estaba | Reemplazo |
|---|---|---|
| `dh_xjtdnz` (usuario del servidor) | histórico de `bash/*.sh` y `to-do.txt` | `REMOVED_USER` |
| `msgp753@gmail.com` | `->bcc()` hardcodeado en 3 controladores | `removed@example.com` |
| bcrypt por defecto de Laravel (`$2y$10$92IXUN…`) | `database/factories/UserFactory.php` y `scripts/setup-test-db.sh` (¡ojo, también en su forma escapada para bash `\$2y\$10\$…`!) | bcrypt nuevo, generado por nosotros, de `password` |

El código actual también se corrigió antes del scrub (commit `f4f55851`, revisión nativa aprobada): el correo pasa por `MAIL_BCC_ARCHIVO` en `.env` y `setup-test-db.sh` genera el hash en runtime.

**Tips actuales** tras el segundo `--force`:

- `main` → `081353be840d2dcce4e9f3bcaced7b1b679945d5`
- `tailwind4` → `b1dba8cccf9f7f99bdf23abbec2ae07671d3a601`
- `feat/eventos-mostrar-miniatura` → `8ecb06eb358d429c2e3e00f672533bed04bcb5c9`
- `copilot/fix-9543f02b-9c66-454e-9aa5-cb571ac97cd1` → `8ebffe969a268e192e9200a4d79fa79133f2c782`

Verificado: **0 ocurrencias** de los 3 strings (y del prefijo `92IXUNpkj`, que cubre todas las variantes) en el barrido completo de las 10.973 blobs del historial nuevo. Los diffs de los 4 tips contienen **solo** esos reemplazos (en `main`: exactamente 1 línea, en `UserFactory.php`).

Nota: el bcrypt de reemplazo sigue siendo el hash de `password` (el comentario `// password` lo documenta), pero ya no es el default público de Laravel. Si quieres eliminar también ese literal, el siguiente paso natural es cambiar `UserFactory` a `Hash::make('password')` — commit pequeño con su propia revisión.

## 2. Copias de seguridad que existen

| Qué | Dónde | Notas |
|---|---|---|
| Backup tradicional del árbol de trabajo | `.zip` guardado por ti en lugar seguro (8.4 MB, 1284 ficheros, incluye `.env`) | No contiene `.git` |
| Historial viejo completo | Ramas `backup/pre-limpieza-sql-{main,feat,tailwind4,copilot}` en el clone local | **Única copia local del histórico antiguo** — no borrar hasta el paso 5 |

## 3. ⚠️ Lo único que queda pendiente: el ticket a soporte de GitHub

El `--force` limpió el historial visible, pero **GitHub conserva los objetos viejos en sus servidores** y hoy siguen descargables por SHA (verificado con la API: el blob de 45 MB del dump responde). GitHub **no hace esa limpieza de forma rutinaria** (su propio blog de ingeniería lo confirma) — hay que pedirla.

### Instrucciones para enviar el ticket

1. Entra en <https://support.github.com/contact> (con la cuenta propietaria del repo).
2. Tema: *security / sensitive data*.
3. **Copia y pega tal cual** el bloque de abajo (está en inglés porque es lo que espera soporte de GitHub).
4. Envía. Suelen responder en pocos días laborables.

### Borrador del ticket (copiar y pegar)

```
Subject: Request purge of unreachable objects containing sensitive data after history rewrite

Hi,

On October 9, 2026 I rewrote the history of https://github.com/msoler75/tseyor_org_2025_laravel_inertia (public repository) with git-filter-repo to remove database dumps that were committed by mistake, then force-pushed all branches.

Current branch tips after the rewrite:
- main -> 081353be840d2dcce4e9f3bcaced7b1b679945d5
- tailwind4 -> b1dba8cccf9f7f99bdf23abbec2ae07671d3a601
- feat/eventos-mostrar-miniatura -> 8ecb06eb358d429c2e3e00f672533bed04bcb5c9
- copilot/fix-9543f02b-9c66-454e-9aa5-cb571ac97cd1 -> 8ebffe969a268e192e9200a4d79fa79133f2c782

The removed files were full MySQL dumps of a production database (user emails, hashed passwords, private content):
- laravel_inertia.sql
- comunicados.sql
- storage/backups/*.sql.zip (16 backup files)

A second filter-repo pass on the same day (same force-push) additionally replaced three sensitive strings across the entire history:
- a private server username used in historical shell scripts,
- a personal email address that was hardcoded as a mail BCC in old controllers,
- the well-known Laravel default UserFactory bcrypt hash (present in both literal and bash-escaped forms).

Example blob from the pre-rewrite objects that still contains the default hash: f83891d716dc01426cb83526363ad19c38f0ed36

First changed commits (pre-rewrite hashes):
- laravel_inertia.sql: 754c1c6b28b845a0e59e27522939ece3800c45c6 (2023-06-14)
- comunicados.sql: 506204ef8d420f6bc5c7fe7676502e97286fa7e0 (2024-04-13)
- storage/backups/*.sql.zip: aae0f22819bc3fc57a382a867a312380804f8417 (2024-01-27)

Affected pull requests: 1 (PR #1, merged). The repository has 0 forks.

The old objects are still retrievable by SHA after the force-push (verified via the API).

This is personal data of third parties (user emails and password hashes) in a public repository, so rotating credentials is not sufficient mitigation. Could you please:
- dereference/delete any affected PR references,
- run a server-side garbage collection to expunge the unreachable objects,
- and remove cached views.

Thank you.
```

## 4. Tras la respuesta de soporte: verificación

Cuando te confirmen la purga, avísame y ejecuto la comprobación. Manualmente sería:

```bash
gh api repos/msoler75/tseyor_org_2025_laravel_inertia/git/blobs/<sha-del-dump> --jq .size
# También el blob del hash por defecto (segundo pase):
gh api repos/msoler75/tseyor_org_2025_laravel_inertia/git/blobs/f83891d716dc01426cb83526363ad19c38f0ed36 --jq .size
# Esperado tras la purga: 404 Not Found en ambas
# Mientras responda con un tamaño, los datos siguen ahí
```

## 5. Limpieza final (solo cuando la purga esté confirmada)

Hasta entonces, **no** ejecutar nada de esto:

```bash
# 1) Borrar el historial viejo del disco local (79 referencias SQL)
git branch -D backup/pre-limpieza-sql-main backup/pre-limpieza-sql-feat \
               backup/pre-limpieza-sql-tailwind4 backup/pre-limpieza-sql-copilot
git reflog expire --expire=now --all && git gc --prune=now --aggressive

# 2) Verificar que el disco ya no contiene los dumps
git rev-list --objects --all | grep -ci '\.sql'   # esperado: 0
```

## 6. Avisos importantes

- **Clones antiguos**: cualquiera que tenga un clon previo al 09/10/2026 puede volver a subir los dumps sin querer con un `git pull` + `push`. Si hay colaboradores, deben re-clonar o limpiar su clon (instrucciones: <https://github.com/newren/git-filter-repo> → *Make sure other copies are cleaned up*).
- El clone local de este proyecto quedó desfasado el 09/10 con cada `--force` y se resincronizó sin `reset --hard` (la última vez con `fetch` + `reset --keep`, que preserva los cambios sin commitear).
- Mientras existan las ramas `backup/pre-limpieza-sql-*`, los dumps **siguen en tu disco**. Es intencionado hasta tener la confirmación de GitHub.
