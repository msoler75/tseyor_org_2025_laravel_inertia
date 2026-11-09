# Vulnerabilidades Aceptadas (Dependabot / npm audit)

Documento de referencia para las vulnerabilidades de dependencias que el proyecto ha decidido **aceptar conscientemente** en lugar de migrar. Se actualiza cuando se revisa el estado de seguridad de las dependencias.

Última revisión: 2026-11-04 (Dependabot: 3 alertas abiertas; `npm audit`: 0 high, 0 critical).

## Estado resumido

| Fuente | Alertas abiertas | Severidad |
|---|---|---|
| GitHub Dependabot | 3 | 2 moderate + 1 low |
| `npm audit` | 49 entradas | 47 moderate + 2 low (0 high, 0 critical) |

`npm audit` infla el conteo: cuenta **instancias de paquetes en el árbol**, no CVEs. Las 49 entradas corresponden a **8 advisories GHSA** distintos. Ver detalle abajo.

## Vulnerabilidades aceptadas (Dependabot abiertas)

### 1. `@tiptap/*` v2.27.3 → v3.31.4+ (moderate) — alerta #213
- **Advisory:** `GHSA-cp6q-959q-f8rh` — `mergeAttributes()` convierte una clave `__proto__` propia en atributos DOM ejecutables.
- **Rango:** `>=2.0.0-alpha.0 <3.30.4`.
- **Motivo de aceptación:** migración **major** que afecta al editor de texto de comunicados/noticias (`resources/js/Components/TipTapEditor.vue`). Requiere refactor de la integración con Vue 3.
- **Impacto real:** bajo en este contexto (el editor se usa para contenido propio de administradores, no entrada de usuarios anónimos), pero la migración es costosa y arriesga regresiones en el editor.
- **Plan de salida:** migrar a TipTap v3 en una tarea dedicada con tests de regresión sobre el editor.

### 2. `md-editor-v3` v4.21.3 → v7 (moderate) — alerta #229
- **Advisory:** `GHSA-3rm2-h79c-8qw6` — XSS por bypass en el renderizado de lenguaje de bloques de código (fenced code).
- **Rango:** `<=6.5.3`.
- **Motivo de aceptación:** migración **major** (v4 → v7). El usuario ha pedido explícitamente no romper el editor Markdown.
- **Impacto real:** el XSS requiere que un atacante controle el lenguaje de un bloque de código en contenido Markdown renderizado; en este proyecto el Markdown se genera por el propio equipo.
- **Plan de salida:** migrar a v7 en una tarea dedicada, validando el renderizado de todos los artículos existentes.

### 3. `deepmerge` 4.3.1 (low) — alerta #270
- **Advisory:** `GHSA-gcqx-w48m-w2h4` — prototype poisoning en `mergeObject()` de objetos devueltos.
- **Rango:** `>=4.2.0 <=4.3.1`.
- **Motivo de aceptación:** no existe parche para `deepmerge` itself. El único camino de fix es subir `vite-plugin-pwa` a v1.1.0 (major). `deepmerge` solo se usa en la toolchain de build (PWA), **no llega a runtime**.
- **Plan de salida:** actualizar `vite-plugin-pwa` a v1.1.0 cuando se revise la PWA.

## Vulnerabilidades retiradas / resueltas

- **`minimatch`** (alerta #276, `GHSA-g3hv-75wg-m249`): retirada por GitHub (estado `withdrawn`). No figuraba en esta lista como aceptada, pero tampoco es accionable.
- **`showdown` 2.1.0** (alertas #123, #209, #210): sus 3 advisories (`GHSA-rmmh-p597-ppvv`, `GHSA-cr32-g25g-vxjj`, `GHSA-22g5-r2x5-97cx`) están `dismissed` con motivo `tolerable_risk`. Ya es la última versión publicada, sin fix. ReDoS y 2 XSS en parsing de Markdown.
- **6 highs de build/dev** (`braces`, `chokidar`, `fast-glob`, `micromatch`, `unplugin-auto-import`, `unplugin-vue-components`): ya no existen; `npm audit` reporta 0 high. `unplugin-*` quedan como *moderate* vía `minimatch`.
- **`happy-dom`**, **`serialize-javascript`**, **`vite`**, **`zod`**, **`symfony/*`**, etc.: resueltos en las entregas de Dependabot (ver historial de commits).

## Por qué `npm audit` dice 49 y Dependabot dice 3

`npm audit` cuenta **instancias de paquetes en el árbol**, no CVEs. Un solo advisory se infla en cada paquete que depende del vulnerable.

Las 49 entradas corresponden a **8 advisories GHSA**:

| GHSA | Paquete sujeto | Sev. | Paquetes que npm lista por dependencia |
|---|---|---|---|
| `GHSA-cp6q-959q-f8rh` | `@tiptap/core` | moderate | 35 (`@tiptap/*`) |
| `GHSA-g3hv-75wg-m249` | `minimatch` | moderate | minimatch, filelist, jake, ejs, unplugin-*, workbox-build, trickfilm |
| `GHSA-3g3m-2fjr-6pw4` | `glob` | moderate | glob |
| `GHSA-3rm2-h79c-8qw6` | `md-editor-v3` | moderate | md-editor-v3 |
| `GHSA-rmmh-p597-ppvv` | `showdown` | moderate | showdown |
| `GHSA-cr32-g25g-vxjj` | `showdown` | moderate | showdown |
| `GHSA-22g5-r2x5-97cx` | `showdown` | moderate | showdown |
| `GHSA-gcqx-w48m-w2h4` | `deepmerge` | low | deepmerge, workbox-build, @rollup/plugin-node-resolve |

Dependabot solo alerta sobre el **paquete sujeto** del advisory, no sobre quien depende de él. De ahí la diferencia de conteo.

### Producción vs build

- **37 en producción** = 35 `@tiptap/*` + `md-editor-v3` + `showdown` (todo cliente).
- **12 solo de build** = glob, minimatch, ejs, filelist, jake, workbox-build, vite-plugin-pwa, trickfilm, unplugin-*, @rollup/plugin-node-resolve, deepmerge → **no llegan a runtime**.

## Política

- Las vulnerabilidades aceptadas se revisan en cada actualización mayor de dependencias.
- Cualquier nueva alerta high/critical **no** se acepta por defecto: debe migrarse o justificarse aquí.
- Las migraciones major (`@tiptap` v2→v3, `md-editor-v3` v4→v7) se planifican como tareas dedicadas con tests de regresión.
- `TODO.md` (sección "Dependencias") mantiene el resumen operativo; este documento es la referencia detallada.

### [Ver índice de documentación](./index.md)
