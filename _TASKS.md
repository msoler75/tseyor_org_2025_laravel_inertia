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

## Discovered During Work

- [ ] Añadir instrucciones para mover los scripts release_crear.sh y release_establecer.sh a la raíz del sitio web tras cada actualización.
- [ ] Documentar el proceso de rollback usando release_establecer.sh.
- Revisar si otras tools MCP pueden beneficiarse de este patrón de modularización.
