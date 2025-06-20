# TASKS.md

## Tareas actuales
- [x] Mejorar y detallar el manual de despliegue en _DEPLOYMENT.md (15/05/2025)
- [x] Migrar putty.cmd a putty.ps1 usando variables en .env para mayor seguridad (15/05/2025)
- [ ] Terminar la portada del sitio web (17/05/2025)
- [x] Crear script bash boletin_generar.sh para lanzar boletín vía CURL con token y periodicidad (19/05/2025)
- [x] Implementar estructura inicial de servidor MCP y tools para comunicados, entradas y noticias (11/06/2025)
- [x] Refactorizar ComunicadosTool y NoticiasTool siguiendo los principios de EntradasTool (11/06/2025)
- [x] Migrar endpoint /mcp para usar opgginc/laravel-mcp-server manteniendo tokens y tools actuales, sin eliminar archivos previos MCP (13/06/2025)
- [ ] Todas las tools MCP usan capabilities.php para description, inputSchema y annotations (13/06/2025)
- [x] Crear tools MCP para todos los modelos de App\Models (Audio, Centro, Contacto, Equipo, Entrada, Evento, Grupos, Guia, Informe, Libro, Lugar, Meditacion, Noticia, Normativa, Pagina, Psicografia, Sala, Termino, Tutorial, Video): listar, ver, crear, editar, eliminar y ver campos. (13/06/2025) Se seguirán los preceptos comentados a continuación:
- [x] Refactorizar hooks de tools MCP: Crear, Editar, Eliminar, Listar, Buscar para usar onCrear, onEditar, onEliminar, onListar, onBuscar en BaseModelTools (18/06/2025)
- [x] Actualizar la gestión de permisos y la sintaxis de $required en las Tools MCP, simplificar la lógica de permisos, y asegurar que los tests de archivos (crear, editar, eliminar, permisos) pasen correctamente. Ajustar la respuesta de edición de archivos/carpeta para que los tests sean consistentes y no fallen por claves ausentes. (19/06/2025)
- [x] Añadir tests para tools MCP de Evento: listar, ver, crear, editar, eliminar (16/06/2025)
- [x] Crear test para las nuevas tool MCP para todos los modelos de App\Models faltantes (Audio, Centro, Contacto, Equipo, Entrada, Grupos, Guia, Informe, Libro, Lugar, Meditacion, Noticia, Normativa, Pagina, Psicografia, Sala, Termino, Tutorial, Video): listar, ver, crear, editar, eliminar y ver campos,  (14/06/2025) Se seguirá el mismo esquema que ComunicadosToolTest
- [x] Unificar paginación en todos los Controllers: usar static $ITEMS_POR_PAGINA y $page de request en index con paginate, como en LibrosController, ComunicadosController y PaginasController (16/06/2025)
- [x] Crear sección para que usuarios autenticados puedan generar y ver su token JWT MCP desde el área de perfil (18/06/2025)
- [x] Permitir subida de archivos binarios (ejemplo PDF) en la tool MCP 'archivo' (onCrear) (20/06/2025)



