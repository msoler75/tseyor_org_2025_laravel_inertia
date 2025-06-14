# TASKS.md

## Tareas actuales
- [x] Mejorar y detallar el manual de despliegue en _DEPLOYMENT.md (15/05/2025)
- [x] Migrar putty.cmd a putty.ps1 usando variables en .env para mayor seguridad (15/05/2025)
- [ ] Terminar la portada del sitio web (17/05/2025)
- [x] Crear script bash boletin_generar.sh para lanzar boletín vía CURL con token y periodicidad (19/05/2025)
- [x] Implementar estructura inicial de servidor MCP y tools para comunicados, entradas y noticias (11/06/2025)
- [x] Refactorizar ComunicadosTool y NoticiasTool siguiendo los principios de EntradasTool (11/06/2025)
- [ ] Migrar endpoint /mcp para usar opgginc/laravel-mcp-server manteniendo tokens y tools actuales, sin eliminar archivos previos MCP (13/06/2025)
- [x] Todas las tools MCP usan capabilities.php para description, inputSchema y annotations (13/06/2025)
- [x] Crear tools MCP para modelo Audio: listar, ver, crear, editar, eliminar y ver campos (13/06/2025)
- [x] Crear tools MCP para modelo Centro: listar, ver, crear, editar, eliminar y ver campos (13/06/2025)
- [x] Crear tools MCP para modelo Contacto: listar, ver, crear, editar, eliminar y ver campos (13/06/2025)
- [x] Crear tools MCP para todos los modelos de App\Models (Audio, Centro, Contacto, Equipo, Evento, Grupos, Guia, Informe, Libro, Lugar, Meditacion, Normativa, Pagina, Psicografia, Sala, Termino, Tutorial, Video): listar, ver, crear, editar, eliminar y ver campos, considerando lógica especial en los CrudControllers correspondientes. (13/06/2025) Se seguirán los preceptos comentados a continuación:

### Detalles y reglas para la creación de nuevas Tools MCP para modelos

1. **Estructura base:**
   - Cada Tool debe basarse en la estructura y principios de las Tools existentes en ComunicadosTools (herencia de BaseTool, manejo de errores, logging, etc).

2. **Operaciones de Listado y Visualización:**
   - Para listar y ver un modelo, la Tool debe llamar al método correspondiente del Controller público (por ejemplo, `CentrosController::index` o `AudiosController::index` para listar, y `show` para ver).
   - Se debe procesar la respuesta de Inertia usando `fromInertiaToArray` para devolver los datos en formato array.
   - En el método `description()` o en capabilities/campos, se deben documentar los parámetros opcionales de búsqueda o filtrado que acepte el listado (por ejemplo, `buscar`, `categoria`, etc). Para ello se puede consultar el controlador correspondiente.

3. **Operaciones de Crear, Editar y Eliminar:**
   - Para crear, editar o eliminar, la Tool debe usar directamente Eloquent y, si existe lógica especial, debe apoyarse en el CrudController correspondiente en `App\Http\Controllers\Admin` (por ejemplo, `AudioCrudController`).
   - Si el modelo implementa SoftDelete, la Tool debe usar `withTrashed()` y permitir borrado lógico y definitivo (`forceDelete`). Todas las Tool deben comportase de forma muy similar en este aspecto.
   - Se debe validar el token MCP y los permisos necesarios antes de realizar la operación.

4. **Campos y capacidades:**
   - Cada Tool de campos debe devolver la definición de campos desde `campos.php`.
   - Si existen opciones adicionales de filtrado o búsqueda en los listados, deben estar documentadas en el método `description()` o capabilities.
   - Se debe actualizar capabilities.php para que consten todas las nuevas tool o las que falten.

5. **Consistencia y modularidad:**
   - Seguir la convención de nombres y estructura de carpetas de las Tools existentes.
   - Usar logging y manejo de errores consistente.

6. **Referencia:**
   - Tomar como referencia las Tools de `ComunicadosTools` para la estructura, validaciones y manejo de respuestas.

7. **Otros:**
   - Revisar si el modelo tiene relaciones o lógica especial en el CrudController y reflejarlo en la Tool si es relevante.
   - Documentar claramente cualquier parámetro especial o comportamiento adicional en la Tool.
